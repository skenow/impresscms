<?php

class action_system_groupperm_AddForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);
        $this->initVar('groups', self::DTYPE_LIST);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        // retrieve all block ids for this module
        $icms_block_handler = icms::handler('icms_view_block');
        $blocks =& $icms_block_handler->getByModule($this->module_id, FALSE);
        $response->msg(_MD_AM_PERMS_ADDING);
        $gperm_handler = icms::handler('icms_member_groupperm');
        foreach ($this->groups as $mygroup) {
                if ($gperm_handler->checkRight('module_admin', 0, $mygroup))
                    $response->addModuleAction('groupperm/Insert', array('name' => 'module_admin', 'itemid' => $this->module_id, 'groupid' => $mygroup), 'system');
                $response->addModuleAction('groupperm/Insert', array('name' => 'module_read', 'itemid' => $this->module_id, 'groupid' => $mygroup), 'system');
                
                foreach ($blocks as $blc)
                        $response->addModuleAction('groupperm/Insert', array('name' => 'block_read', 'itemid' => $blc, 'groupid' => $mygroup), 'system');
        }
    }
    
}