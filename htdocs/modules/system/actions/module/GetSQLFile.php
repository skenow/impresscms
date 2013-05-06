<?php

class action_system_module_GetVersion
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->getByDirname($this->dirname);
	$module->registerClassPath();
        
        $response->add('sql_file', $module->getInfo('sqlfile'));
    }        
    
}