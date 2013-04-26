<?php

class action_system_module_ChangeName
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER, true);
        $this->initVar('name', self::DTYPE_INTEGER, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->get($this->module_id);
	$module->setVar('name', $this->name);
        if (!$module->store())
            $response->error($module->getErrors());
        else
            $response->msg(sprintf(_MD_AM_OKORDER, "<strong>" . $this->name . "</strong>"));
    }
    
}