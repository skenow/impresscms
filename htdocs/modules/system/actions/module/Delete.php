<?php

class action_system_module_Delete
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, '', true);
        $this->initVar('sys_action_key', self::DTYPE_STRING, '', true);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
                
        if ($_SESSION['sys_action_key'] != $this->sys_action_key)
            return $response->error('Bad auth key');
        
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->getByDirname($this->dirname);
	$module->registerClassPath();
        
        if (!$module_handler->delete($module))
            return $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_DELETE_FAIL . '</span>', $module->getVar('name')));
    }
    
}