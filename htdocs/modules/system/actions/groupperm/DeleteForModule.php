<?php

class action_system_groupperm_DeleteForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        // delete permissions if any
        $response->msg(_MD_AM_GROUPPERM_DELETE);
        $gperm_handler = icms::handler('icms_member_groupperm');
        if (!$gperm_handler->deleteByModule($this->module_id)) {
                $response->error('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_GROUPPERM_DELETE_FAIL . '</span>');
        } else {
                $response->msg('&nbsp;&nbsp;' . _MD_AM_GROUPPERM_DELETED);
        }
    }
    
}