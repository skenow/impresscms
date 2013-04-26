<?php

class action_system_module_Update
    extends action_system_actionscript_Make {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        
        $params = $this->toArray();
        foreach (array(
                'module/GetID',
                'module/GetCurrentName', 
                'module/GetVersion', 
                'db/GetReservedTables', 
                'module/GetGroups', 
                'module/GetConfig', 
                'module/GetBlocks', 
                'module/GetAutotasks', 
                'module/GetTemplates',                
            ) as $action)
                $this->execDataGateringAction($action, $params, 'system');
        
        $this->addToGateredDataCollection('event', 'update');
        $this->addToGateredDataCollection('dirname', $this->dirname);
        
        foreach (array(
                'templates/ClearCache', 
                'module/ReloadInfo', 
                'templates/RebuildForModule', 
                'autotasks/RebuildForModule', 
                'blocks/RebuildForModule', 
                'config/RebuildForModule', 
                'db/UpdateByIPFStructure', 
                'module/ExecNFOEvent') as $name)
            $response->addModuleAction($name, $params, 'system');
        
        $response->msg(sprintf(_MD_AM_OKUPD, '<strong>' . $module->getVar('name') . '</strong>'));
    }    
    
}