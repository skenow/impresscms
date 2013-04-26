<?php

class action_system_autotasks_AddForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('autotasks', self::DTYPE_ARRAY);
        $this->initVar('dirname', self::DTYPE_STRING);
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        if (count($this->collection) > 0)
            foreach ($this->collection as $taskID => $taskData)
                $response->addModuleAction('autotasks/Insert', array_merge($taskData, array('addon_id' => $taskID, 'dirname' => $this->dirname)), 'system');
    }
    
}