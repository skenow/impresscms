<?php

class action_system_module_Activate
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
         $this->initVar('module_id', self::DTYPE_INTEGER, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->get($this->module_id);
        
        $response->addModuleAction('templates/ClearCache', array('module_id' => $this->module_id), 'system');
        
        $module->setVar('isactive', 1);
        if (!$module->store())
            return $response->error($module->getErrors());

	$icms_block_handler = icms_getModuleHandler('blocks', 'system');
	$blocks =& $icms_block_handler->getByModule($module->getVar('mid'));
	$bcount = count($blocks);
	for ($i = 0; $i < $bcount; $i++) {
		$blocks[$i]->setVar('isactive', 1);
		$icms_block_handler->insert($blocks[$i]);
	}
        
        $response->msg(sprintf(_MD_AM_OKACT, "<strong>" . $module->getVar('name') . "</strong>"));
    }
    
}