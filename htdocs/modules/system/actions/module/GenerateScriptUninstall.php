<?php

class action_system_module_Uninstall
    extends action_system_actionscript_Make {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        
        $_SESSION['sys_action_key'] = sha1(microtime(true));
        
        $params = $this->toArray();
        $this->execDefaultAction('module/CheckIfIsSystemModule', $params, 'system');
        $this->execDefaultAction('module/CheckIfItIsStartPage', $params, 'system');
        
        $this->execDataGateringAction('db/GetReservedTables', array(), 'system');
        $this->execDataGateringAction('module/GetID', $params, 'system');
        $this->addToGateredDataCollection('event', 'uninstall');
        $this->addToGateredDataCollection('dirname', $this->dirname);
        $this->addToGateredDataCollection('sys_action_key', $_SESSION['sys_action_key']);
        
        foreach (array(
                       'templates/ClearCache',
                       'module/ExecNFOEvent',
                       'module/Delete',
                       'pages/DeleteForModule', 
                       'templates/DeleteForModule', 
                       'urllink/DeleteForModule', 
                       'file/DeleteForModule', 
                       'groupperm/DeleteForModule', 
                       'blocks/DeleteForModule', 
                       'db/DropTables',
                       'comments/DeleteForModule',
                       'notifications/DeleteForModule',
                       'autotasks/DeleteForModule',
                       'config/DeleteForModule'
                        ) as $action)
                $this->execDefaultAction ($action, $params, 'system');
        
        $this->writeMessage(sprintf(_MD_AM_OKUNINS, "<strong>" . $module->getVar('name') . "</strong>"));
    }           
    
}