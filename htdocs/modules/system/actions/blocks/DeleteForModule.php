<?php

class action_system_blocks_DeleteForModule extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);

        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        // delete blocks and block template files
        $icms_block_handler = icms::handler('icms_view_block');
        $block_arr = & $icms_block_handler->getByModule($this->module_id);
        $tplfile_handler = icms::handler('icms_view_template_file');
        if (is_array($block_arr)) {
            $bcount = count($block_arr);
            $response->msg(_MD_AM_BLOCKS_DELETE);
            for ($i = 0; $i < $bcount; $i++) {
                if (!$icms_block_handler->delete($block_arr[$i])) {
                    $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_BLOCK_DELETE_FAIL . '</span>', '<strong>' . $block_arr[$i]->getVar('name') . '</strong>', '<strong>' . icms_conv_nr2local($block_arr[$i]->getVar('bid')) . '</strong>'));
                } else {
                    $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_BLOCK_DELETED, '<strong>' . $block_arr[$i]->getVar('name') . '</strong>', '<strong>' . icms_conv_nr2local($block_arr[$i]->getVar('bid')) . '</strong>'
                    ));
                }
                if ($block_arr[$i]->getVar('template') != '') {                    
                    $templates = & $tplfile_handler->find(NULL, 'block', $block_arr[$i]->getVar('bid'));
                    $btcount = count($templates);
                    if ($btcount > 0) {
                        for ($j = 0; $j < $btcount; $j++) {
                            if (!$tplfile_handler->delete($templates[$j])) {
                                $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_BLOCK_TMPLT_DELETE_FAILED . '</span>', $templates[$j]->getVar('tpl_file'), '<strong>' . icms_conv_nr2local($templates[$j]->getVar('tpl_id')) . '</strong>'
                                ));
                            } else {
                                $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_BLOCK_TMPLT_DELETED, '<strong>' . $templates[$j]->getVar('tpl_file') . '</strong>', '<strong>' . icms_conv_nr2local($templates[$j]->getVar('tpl_id')) . '</strong>'
                                ));                                
                            }
                        }
                    }
                    unset($templates);
                }
            }
        }
    }

}