<?php

class action_system_module_GetConfig extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING);

        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
        $module = & $module_handler->getByDirname($this->dirname);
        $module->registerClassPath();

        $configs = $module->getInfo('config');
        if (!is_array($configs))
            $configs = array();
        if ($module->getVar('hascomments') != 0) {
            include_once ICMS_INCLUDE_PATH . '/comment_constants.php';
            $configs[] = array(
                'name' => 'com_rule',
                'title' => '_CM_COMRULES',
                'description' => '',
                'formtype' => 'select',
                'valuetype' => 'int',
                'default' => 1,
                'options' => array(
                    '_CM_COMNOCOM' => XOOPS_COMMENT_APPROVENONE,
                    '_CM_COMAPPROVEALL' => XOOPS_COMMENT_APPROVEALL,
                    '_CM_COMAPPROVEUSER' => XOOPS_COMMENT_APPROVEUSER,
                    '_CM_COMAPPROVEADMIN' => XOOPS_COMMENT_APPROVEADMIN
                )
            );
            $configs[] = array(
                'name' => 'com_anonpost',
                'title' => '_CM_COMANONPOST',
                'description' => '',
                'formtype' => 'yesno',
                'valuetype' => 'int',
                'default' => 0,
            );
        }
        if ($module->getVar('hasnotification') != 0) {

            // Main notification options
            include_once ICMS_INCLUDE_PATH . '/notification_constants.php';
            $options = array(
                '_NOT_CONFIG_DISABLE' => XOOPS_NOTIFICATION_DISABLE,
                '_NOT_CONFIG_ENABLEBLOCK' => XOOPS_NOTIFICATION_ENABLEBLOCK,
                '_NOT_CONFIG_ENABLEINLINE' => XOOPS_NOTIFICATION_ENABLEINLINE,
                '_NOT_CONFIG_ENABLEBOTH' => XOOPS_NOTIFICATION_ENABLEBOTH,
            );
            $configs[] = array(
                'name' => 'notification_enabled',
                'title' => '_NOT_CONFIG_ENABLE',
                'description' => '_NOT_CONFIG_ENABLEDSC',
                'formtype' => 'select',
                'valuetype' => 'int',
                'default' => XOOPS_NOTIFICATION_ENABLEBOTH,
                'options' => $options,
            );
            // Event-specific notification options
            // FIXME: doesn't work when update module... can't read back the array of options properly...  " changing to &quot;
            $options = array();
            $notification_handler = icms::handler('icms_data_notification');
            $categories = & $notification_handler->categoryInfo('', $module->getVar('mid'));
            foreach ($categories as $category) {
                $events = & $notification_handler->categoryEvents($category['name'], FALSE, $module->getVar('mid'));
                foreach ($events as $event) {
                    if (!empty($event['invisible'])) {
                        continue;
                    }
                    $option_name = $category['title'] . ' : ' . $event['title'];
                    $option_value = $category['name'] . '-' . $event['name'];
                    $options[$option_name] = $option_value;
                }
            }
            $configs[] = array(
                'name' => 'notification_events',
                'title' => '_NOT_CONFIG_EVENTS',
                'description' => '_NOT_CONFIG_EVENTSDSC',
                'formtype' => 'select_multi',
                'valuetype' => 'array',
                'default' => array_values($options),
                'options' => $options
            );
        }
        
        $response->add('config', $configs);
    }

}