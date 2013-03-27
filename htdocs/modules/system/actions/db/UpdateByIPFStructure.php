<?php

class action_system_db_UpdateByIPFStructure 
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING);
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->getByDirname($this->dirname);
	$module->registerClassPath();
        
        $is_IPF = $module->getInfo('object_items');
        if (!empty($is_IPF)) {
                $icmsDatabaseUpdater = icms_db_legacy_Factory::getDatabaseUpdater();
                $icmsDatabaseUpdater->moduleUpgrade($module, TRUE);
                $response->msg($icmsDatabaseUpdater->_messages);
        }
    }
    
}