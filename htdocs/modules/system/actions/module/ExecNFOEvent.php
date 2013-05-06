<?php

class action_system_module_ExecNFOEvent
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING);
        $this->initVar('event', self::DTYPE_STRING);
        $this->initVar('version', self::DTYPE_STRING);
        $this->initVar('dbversion', self::DTYPE_STRING);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $module_handler = icms::handler('icms_module');
	$module =& $module_handler->getByDirname($this->dirname);
	$module->registerClassPath();
        
        // execute module specific install script if any
	$install_script = $module->getInfo('on' . ucfirst($this->event));
        if (empty($install_script))
            return;
        
	$ModName = ($module->getInfo('modname') != '') ? trim($module->getInfo('modname')) : $this->dirname;
        $file = ICMS_MODULES_PATH . '/' . $this->dirname . '/' . trim($install_script);
        if (!file_exists($file))
            return $response->error('Install file not found');
        
        switch ($this->getCompat()) {
            case 'xoops':
                $func = 'xoops_module_'.$this->event.'_' . $ModName;
                break;
            case 'icms':
                $func = 'icms_module_'.$this->event.'_' . $ModName;
                break;
        }
        
        if (!function_exists($func)) {
            if (!($lastmsg = $func($module, $this->version, $this->dbversion))) {
                    $response->error(sprintf(_MD_AM_FAIL_EXEC, '<strong>' . $func . '</strong>'));
            } else {
                    $response->msg($module->messages);
                    $response->msg(sprintf(_MD_AM_FUNCT_EXEC, '<strong>' . $func . '</strong>'));
                    if (is_string($lastmsg))
                            $response->msg($lastmsg);
            }
        }
    }
    
    public function getCompat() {
        $r2 =  new icms_action_Response();
        $r2->addModuleAction('module/GetCompatibility', array('dirname' => $this->dirname), 'system');
        $rez = $r2->toArray();
        return isset($rez['module_compatibility'])?$rez['module_compatibility']:null;
    }
    
}