<?php

class action_system_module_ReloadInfo
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING);
        $this->initVar('module_name', self::DTYPE_STRING);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->getByDirname($this->dirname);
	$module->registerClassPath();
        
        $module->loadInfoAsVar($dirname);	
        
        if (!empty($this->module_name))
            $module->setVar('name', $this->module_name);
        
        if (!$module->store(true))
            return $response->error(sprintf('<p>' . _MD_AM_UPDATE_FAIL . '</p>', $module->getVar('name')));
    }        
    
}