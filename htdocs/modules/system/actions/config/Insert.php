<?php

/**
 * @property string $dirname    Dirname for module
 * @property string $file       File for template
 * @property int $ref_id     ID of module
 * @property string $description Description of template
 * @property string $type       Type of template
 */
class action_system_config_Insert extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER, false);
        $this->initVar('name', self::DTYPE_STRING, false);
        $this->initVar('title', self::DTYPE_STRING, false);
        $this->initVar('description', self::DTYPE_STRING, false);
        $this->initVar('formtype', self::DTYPE_STRING, '');
        $this->initVar('valuetype', self::DTYPE_STRING, '');
        $this->initVar('default', self::DTYPE_STRING, '');
        $this->initVar('order', self::DTYPE_INTEGER, 0);
        $this->initVar('options', self::DTYPE_ARRAY, '');

        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        $config_handler = icms::handler('icms_config');
        $confobj = & $config_handler->createConfig();
        $confobj->setVar('conf_modid', $this->module_id);
        $confobj->setVar('conf_catid', 0);
        $confobj->setVar('conf_name', $this->name);
        $confobj->setVar('conf_title', $this->title, TRUE);
        $confobj->setVar('conf_desc', $this->description, TRUE);
        $confobj->setVar('conf_formtype', $this->formtype);
        $confobj->setVar('conf_valuetype', $this->valuetype);
        $confobj->setConfValueForInput($this->default, TRUE);
        $confobj->setVar('conf_order', $this->order);
        $confop_msgs = '';
        if (count($this->options) > 0) {
            foreach ($this->options as $key => $value) {
                $confop = & $config_handler->createConfigOption();
                $confop->setVar('confop_name', $key, TRUE);
                $confop->setVar('confop_value', $value, TRUE);
                $confobj->setConfOptions($confop);
                $confop_msgs .= sprintf('<br />&nbsp;&nbsp;&nbsp;&nbsp;' . _MD_AM_CONFIGOPTION_ADDED, '<strong>' . $key . '</strong>', '<strong>' . $value . '</strong>');
                unset($confop);
            }
        }
        $order++;
        if ($config_handler->insertConfig($confobj) !== FALSE) {
            $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_CONFIG_ADDED . $confop_msgs, '<strong>' . $this->name . '</strong>'));
        } else {
            $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_CONFIG_ADD_FAIL . '</span>', '<strong>' . $this->name . '</strong>'));
        }
    }

}