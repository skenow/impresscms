<?php

class action_system_comments_DeleteForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $response->msg(_MD_AM_COMMENTS_DELETE);        
        $comment_handler = icms::handler('icms_data_comment');
        if (!$comment_handler->deleteByModule($this->module_id)) {
                $response->error('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_COMMENT_DELETE_FAIL . '</span>');
        } else {
                $response->msg('&nbsp;&nbsp;' . _MD_AM_COMMENT_DELETED);
        }      
    }
    
}