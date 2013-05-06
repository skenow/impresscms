<?php

class action_system_module_CheckIfNotSupported
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
        $criteria = new icms_db_criteria_Item('dirname', $this->dirname);
        //$criteria->setLimit(1);
        $count = $module_handler->getCount($criteria);
        if ($count > 0)
            return $response->error(sprintf(_MD_AM_ALEXISTS, $this->dirname));        
    }
    
}