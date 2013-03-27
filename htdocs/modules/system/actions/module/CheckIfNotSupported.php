<?php

class action_system_module_CheckIfNotSupported
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $r2 = new icms_action_Response();
        $r2->addModuleAction('module/getCompatibility', $this->toArray(), 'system');
        $ret = $r2->toArray();
        if (!isset($ret['module_compatibility']) || empty($ret['module_compatibility']))
            $response->error('This module is not supported!');
    }
    
}