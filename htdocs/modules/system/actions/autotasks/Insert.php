<?php

class action_system_autotasks_Insert
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('enabled', self::DTYPE_BOOLEAN, true);
        $this->initVar('repeat', self::DTYPE_INTEGER, 0);
        $this->initVar('interval', self::DTYPE_INTEGER, 60);
        $this->initVar('onfinish', self::DTYPE_STRING, '');
        $this->initVar('name', self::DTYPE_STRING, '', true);
        $this->initVar('code', self::DTYPE_STRING, '', true);
        $this->initVar('dirname', self::DTYPE_STRING, '', true);
        $this->initVar('addon_id', self::DTYPE_INTEGER, 0, true);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $atasks_handler = &icms_getModuleHandler('autotasks', 'system');
        $task = &$atasks_handler->create();
        foreach ($this->toArray() as $key => $value)
            $task->setVar('sat_' . $key, $value);
        $task->setVar('sat_type', 'addon/' . $this->dirname);        
        if (!($atasks_handler->insert($task)))
            return $response->error(_MD_AM_AUTOTASK_FAIL);
        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_AUTOTASK_ADDED, '<strong>' . $this->name . '</strong>'));
    }
    
}