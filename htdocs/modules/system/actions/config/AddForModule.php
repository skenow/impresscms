<?php

class action_system_config_AddForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('config', self::DTYPE_ARRAY);
        $this->initVar('module_id', self::DTYPE_ARRAY);
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $response->msg(_MD_AM_CONFIG_ADDING);						
        $order = 0;
        foreach ($this->collection as $config)
            $response->addModuleAction('config/Insert', array_merge($config, array('module_id' => $this->module_id, 'order' => $order++)), 'system');
    }
    
}