<?php

class action_system_module_Deactivate
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        global $icmsConfig;
        
        $module_handler = icms::handler('icms_module');
        $module =& $module_handler->get($this->module_id);
        if (!$module)
            return $response->error('Module not found!');
        
        if ($module->getVar('dirname') == 'system')
            return $response->error(_MD_AM_SYSNO);
        elseif ($module->getVar('dirname') == $icmsConfig['startpage'])
            return $response->error(_MD_AM_STRTNO);
        
        $module->setVar('isactive', 0);
        
        $response->addModuleAction('templates/ClearCache', array('module_id' => $this->module_id), 'system');
        
        $member_handler = icms::handler('icms_member');
        $grps = $member_handler->getGroupList();
        foreach ($grps as $k => $v) {
                $stararr = explode('-', $icmsConfig['startpage'][$k]);
                if (count($stararr) > 0) {
                        if ($module->getVar('mid') == $stararr[0]) 
                                return $response->error(_MD_AM_STRTNO);
                }
        }
        
        if (in_array($module->getVar('dirname'), $icmsConfig['startpage']))
                return $response->error(_MD_AM_STRTNO);
        
        if (!$module->store())
            return $response->error($module->getErrors());	
        
        $icms_block_handler = icms_getModuleHandler('blocks', 'system');
        $blocks =& $icms_block_handler->getByModule($this->module_id);
        $bcount = count($blocks);
        for ($i = 0; $i < $bcount; $i++) {
                $blocks[$i]->setVar('isactive', FALSE);
                $icms_block_handler->insert($blocks[$i]);
        }
        
        $response->msg(sprintf(_MD_AM_OKDEACT, "<strong>" . $module->getVar('name') . "</strong>"));              
    }
    
}