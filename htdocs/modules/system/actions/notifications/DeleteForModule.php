<?php

class action_system_notifications_DeleteForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        // delete notifications if any        
        $notification_handler = icms::handler('icms_data_notification');
        if (!$notification_handler->unsubscribeByModule ($this->module_id)) {
            $response->error('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_NOTIFICATION_DELETE_FAIL .'</span>');
        } else {
            $response->msg('&nbsp;&nbsp;' . _MD_AM_NOTIFICATION_DELETED);
        }
    }
    
}