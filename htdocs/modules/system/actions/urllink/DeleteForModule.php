<?php

class action_system_urllink_DeleteForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $urllink_handler = icms::handler('icms_data_urllink');
        $urllink_handler->deleteAll(icms_buildCriteria(array("mid" => $this->module_id)));
    }
    
}