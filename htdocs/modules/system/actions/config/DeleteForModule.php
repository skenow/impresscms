<?php

class action_system_config_DeleteForModule extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);

        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        $config_handler = icms::handler('icms_config');
        $configs = & $config_handler->getConfigs(new icms_db_criteria_Item('conf_modid', $this->module_id));
        $confcount = count($configs);
        if ($confcount > 0) {
            $response->msg(_MD_AM_CONFIGOPTIONS_DELETE);
            for ($i = 0; $i < $confcount; $i++) {
                if (!$config_handler->deleteConfig($configs[$i])) {
                    $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_CONFIGOPTION_DELETE_FAIL . '</span>', '<strong>' . icms_conv_nr2local($configs[$i]->getVar('conf_id')) . '</strong>'));
                } else {
                    $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_CONFIGOPTION_DELETED, '<strong>' . icms_conv_nr2local($configs[$i]->getVar('conf_id')) . '</strong>'));
                }
            }
        }
    }

}