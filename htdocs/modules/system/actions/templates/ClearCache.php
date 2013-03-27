<?php

class action_system_templates_ClearCache
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER, false);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        if ($this->module_id > 0) {
            icms_view_Tpl::template_clear_module_cache($this->module_id);
            $response->msg('Cached templates for module cleared');
        } else {
            $tpl = new icms_view_Tpl();
            $tpl->clear_all_cache();
            $response->msg('All cached templates cleared');
        }        
    }
    
}