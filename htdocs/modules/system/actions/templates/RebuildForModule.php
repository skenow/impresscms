<?php

class action_system_templates_RebuildForModule extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('templates', self::DTYPE_ARRAY);
        $this->initVar('module_id', self::DTYPE_INTEGER);
        $this->initVar('dirname', self::DTYPE_INTEGER);
        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        global $icmsConfig;
        
        $response->msg(_MD_AM_MOD_DATA_UPDATED);
        $tplfile_handler = & icms::handler('icms_view_template_file');
        $deltpl = & $tplfile_handler->find('default', 'module', $this->module_id);
        $delng = array();
        $xoopsDelTpl = new icms_view_Tpl();
        if (is_array($deltpl)) {            
            // clear cache files
            $xoopsDelTpl->clear_cache(NULL, 'mod_' . $this->dirname);
            // delete template file entry in db
            $dcount = count($deltpl);
            for ($i = 0; $i < $dcount; $i++) {
                if (!$tplfile_handler->delete($deltpl[$i])) {
                    $delng[] = $deltpl[$i]->getVar('tpl_file');
                }
            }
        }

        if (count($this->templates) > 0) {
            $response->msg(_MD_AM_MOD_UP_TEM);
            foreach ($this->templates as $tpl) {
                $tpl['file'] = trim($tpl['file']);
                if (!in_array($tpl['file'], $delng)) {
                    $tpldata = & $this->getSource($this->dirname, $tpl['file']);
                    $tplfile = & $tplfile_handler->create();
                    $tplfile->setVar('tpl_refid', $this->module_id);
                    $tplfile->setVar('tpl_lastimported', 0);
                    $tplfile->setVar('tpl_lastmodified', time());
                    if (preg_match("/\.css$/i", $tpl['file'])) {
                        $tplfile->setVar('tpl_type', 'css');
                    } else {
                        $tplfile->setVar('tpl_type', 'module');
                    }
                    $tplfile->setVar('tpl_source', $tpldata, TRUE);
                    $tplfile->setVar('tpl_module', $this->dirname);
                    $tplfile->setVar('tpl_tplset', 'default');
                    $tplfile->setVar('tpl_file', $tpl['file'], TRUE);
                    $tplfile->setVar('tpl_desc', $tpl['description'], TRUE);
                    if (!$tplfile_handler->insert($tplfile)) {
                        $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">'
                                . _MD_AM_TEMPLATE_INSERT_FAIL . '</span>', '<strong>' . $tpl['file'] . '</strong>'));
                    } else {
                        $newid = $tplfile->getVar('tpl_id');
                        $response->msg(sprintf('&nbsp;&nbsp;<span>' . _MD_AM_TEMPLATE_INSERTED . '</span>', '<strong>' . $tpl['file'] . '</strong>', '<strong>' . $newid . '</strong>'));
                        if ($icmsConfig['template_set'] == 'default') {
                            if (!$xoopsDelTpl->template_touch($newid)) {
                                $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">'
                                        . _MD_AM_TEMPLATE_RECOMPILE_FAIL . '</span>', '<strong>' . $tpl['file'] . '</strong>'));
                            } else {
                                $response->msg(sprintf('&nbsp;&nbsp;<span>' . _MD_AM_TEMPLATE_RECOMPILED . '</span>', '<strong>' . $tpl['file'] . '</strong>'));
                            }
                        }
                    }
                    unset($tpldata);
                } else {
                    $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_TEMPLATE_DELETE_FAIL . '</span>', $tpl['file']));
                }
            }
        }
    }
    
    /**
     * Reads template from file
     *
     * @param	string	$dirname	Directory name of the module
     * @param	string	$template	Name of the template file
     * @param	boolean	$block		Are you trying to retrieve the template for a block?
     */
    public function getSource($dirname, $template, $block = FALSE) {
        $ret = '';
        if ($block) {
            $path = ICMS_MODULES_PATH . '/' . $dirname . '/templates/blocks/' . $template;
        } else {
            $path = ICMS_MODULES_PATH . '/' . $dirname . '/templates/' . $template;
        }
        if (!file_exists($path)) {
            return $ret;
        } else {
            $lines = file($path);
        }
        if (!$lines) {
            return $ret;
        }
        $count = count($lines);
        for ($i = 0; $i < $count; $i++) {
            $ret .= str_replace("\n", "\r\n", str_replace("\r\n", "\n", $lines[$i]));
        }
        return $ret;
    }

}