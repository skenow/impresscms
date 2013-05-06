<?php

/**
 * Installs module
 * 
 * @property string $dirname Dirname of module
 */
class action_system_module_Install
    extends action_system_actionscript_Make {   
    
    /**
     * Constructor
     * 
     * @param array $params
     */
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, true);
        
        parent::__construct($params);
    }
    
    /**
     * Executes action
     * 
     * @param \icms_action_Response $response
     */
    public function exec(\icms_action_Response &$response) {
        
        $params = $this->toArray();
        $this->execDefaultAction('module/CheckIfNotSupported', $params, 'system');
        $this->execDefaultAction('module/CheckIfNotAlreadyInDB', $params, 'system');
        $this->writeMessage(_MD_AM_INSTALLING . $module->getInfo('name'));
        //$this->writeMessage('<strong>'._VERSION . ':</strong> ' . icms_conv_nr2local($module->getInfo('version')));                
        $this->writeMessage('');                 
       
        foreach (array(
                        'module/Insert', 
                        'db/GetReservedTables', 
                        'module/GetGroups', 
                        'module/GetConfig', 
                        'module/GetSQLFile', 
                        'module/GetBlocks', 
                        'modules/GetAutotasks', 
                        'modules/GetTemplates'
                ) as $action)
                    $this->execDataGateringAction($action, $params, 'system');
        
        $this->addToGateredDataCollection('event', 'install');
        $this->addToGateredDataCollection('dirname', $this->dirname);
        
        $this->renameCollectionKey('sql_file', 'file');
        $this->execDefaultAction('db/RunSQLFile', $params, 'system');
        $this->unsetCollectionKey('file');                          
            
        foreach (array('db/UpdateByIPFStructure', 'module/ExecNFOEvent', 'templates/ClearCache', 'blocks/AddForModule', 'groupperm/AddForModule', 'config/AddForModule', 'autotasks/AddForModule', 'templates/AddForModule') as $name)
            $this->execDefaultAction($name, $params, 'system');
        
        $this->writeMessage(sprintf(_MD_AM_OKINS, "<strong>" . $module->getVar('name') . "</strong>"));
        $this->makeScript($response);
    }    
    
    /**
     * Deletes created errors
     */
    public function deleteCreatedTables() {
        foreach ($this->created_tables as $ct)
            $db->query("DROP TABLE " . $db->prefix($ct));
        $this->created_tables = array();
    }    
    
}