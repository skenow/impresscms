<?php

class action_system_autotasks_DeleteForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $response->msg(_MD_AM_AUTOTASKS_DELETE);
	$atasks_handler = &icms_getModuleHandler('autotasks', 'system');
	$criteria = new icms_db_criteria_Compo();
	$criteria->add(new icms_db_criteria_Item('sat_type', 'addon/' . $this->dirname));
	$atasks_handler->deleteAll($criteria);
    }
    
}