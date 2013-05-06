<?php

class action_system_module_CheckIfIsSystemModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        if ($this->dirname == 'system') 
            return $response->error(_MD_AM_SYSNO);
    }
    
}