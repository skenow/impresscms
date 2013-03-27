<?php

class action_system_module_Insert
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, '', true);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
        $module = $module_handler->create();
        $module->loadInfoAsVar($this->dirname);
	$module->registerClassPath();
        $module->setVar('weight', 1);
        if (!$module->store())
            return $response->error(sprintf(_MD_AM_DATA_INSERT_FAIL, '<strong>' . $module->getVar('name') . '</strong>'));
        $response->add('module_id', $module->getVar('mid'));
    }
    
}