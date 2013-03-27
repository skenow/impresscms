<?php

class action_system_module_GetCompatibility
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $compat = $this->getCompatibility();
        if (!$compat)
            $response->error('Unknown module type');
        $response->add('module_compatibility', $compat);
    }
    
    /**
     * Autodetecting compatibility for module
     * 
     * @return null|string
     */
    public function getCompatibility() {
        $path = ICMS_MODULES_PATH . '/' . $this->dirname;
        if (!is_dir($path))
            return null;
        if (file_exists($path . '/icms_version.php'))
            return 'icms';
        if (file_exists($path . '/xoops_version.php'))
            return 'xoops';
        if (file_exists($path . '/version.json'))
            return 'icms2';
        return null;
    }
    
}