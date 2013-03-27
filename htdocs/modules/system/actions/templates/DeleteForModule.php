<?php

class action_system_templates_DeleteForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $tplfile_handler = icms::handler('icms_view_template_file');
        $templates =& $tplfile_handler->find(NULL, 'module', $this->module_id);
        $tcount = count($templates);
        if ($tcount > 0) {
                $response->msg(_MD_AM_TEMPLATES_DELETE);
                for ($i = 0; $i < $tcount; $i++) {
                        if (!$tplfile_handler->delete($templates[$i])) {
                                $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_TEMPLATE_DELETE_FAIL . '</span>',
                                $templates[$i]->getVar('tpl_file') , '<strong>' . icms_conv_nr2local($templates[$i]->getVar('tpl_id')) . '</strong>'));
                        } else {
                            $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TEMPLATE_DELETED,
                                        '<strong>' . icms_conv_nr2local($templates[$i]->getVar('tpl_file')) . '</strong>',
                                        '<strong>' . icms_conv_nr2local($templates[$i]->getVar('tpl_id')) . '</strong>'
                                        ));
                        }
                }
        }
    }
    
}