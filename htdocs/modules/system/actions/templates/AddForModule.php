<?php

class action_system_templates_ImportCollection 
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('templates', self::DTYPE_ARRAY);
        $this->initVar('module_id', self::DTYPE_INTEGER);
        $this->initVar('dirname', self::DTYPE_INTEGER);
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $response->msg(sprintf(_MD_AM_MOD_DATA_INSERT_SUCCESS, '<strong>' . icms_conv_nr2local($this->module_id) . '</strong>'));
        foreach ($this->templates as $tpl)
            $response->addModuleAction('templates/Insert', array(
                'file' => $tpl['file'],
                'description' => $tpl['description'],
                'ref_id' => $this->module_id,
                'dirname'   => $this->dirname,
                'type' => 'module'
            ), 'system');
    }
    
}