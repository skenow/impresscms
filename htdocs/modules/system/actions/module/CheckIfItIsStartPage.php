<?php

class action_system_module_CheckIfItIsStartPage
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, true);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        global $icmsConfig;        
        
        if ($this->dirname == $icmsConfig['startpage'])
            return true;
        
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->getByDirname($this->dirname);
	$module->registerClassPath();
        $module_id = $module->getVar('mid');
        
        $member_handler = icms::handler('icms_member');
        $grps = $member_handler->getGroupList();
        foreach ($grps as $k => $v) {
                $stararr = explode('-', $icmsConfig['startpage'][$k]);
                if (count($stararr) > 0) {
                        if ($module_id == $stararr[0])
                            return $response->error(_MD_AM_STRTNO);
                }
        }
        if (in_array($this->dirname, $icmsConfig['startpage']))
            $response->error(_MD_AM_STRTNO);        
    }
    
}