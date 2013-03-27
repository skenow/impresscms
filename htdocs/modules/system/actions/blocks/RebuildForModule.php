<?php

class action_system_blocks_RebuildForModule extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('blocks', self::DTYPE_ARRAY);
        $this->initVar('module_id', self::DTYPE_INTEGER);
        $this->initVar('dirname', self::DTYPE_INTEGER);
        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        global $icmsConfig;

        $response->msg(_MD_AM_MOD_REBUILD_BLOCKS);
        $count = count($this->blocks);
         $xoopsDelTpl = new icms_view_Tpl();
         $tplfile_handler = & icms::handler('icms_view_template_file');
        if ($count > 0) {
            $showfuncs = array();
            $funcfiles = array();
            foreach ($this->blocks as $i => $block) {
                if (isset($block['show_func']) && $block['show_func'] != '' && isset($block['file']) && $block['file'] != '') {
                    $editfunc = isset($block['edit_func']) ? $block['edit_func'] : '';
                    $showfuncs[] = $block['show_func'];
                    $funcfiles[] = $block['file'];
                    $template = $content = '';
                    if ((isset($block['template']) && trim($block['template']) != '')) {
                        $content = & xoops_module_gettemplate($this->dirname, $block['template'], TRUE);
                    }
                    if (!$content) {
                        $content = '';
                    } else {
                        $template = $block['template'];
                    }
                    $options = '';
                    if (!empty($block['options'])) {
                        $options = $block['options'];
                    }
                    $sql = "SELECT bid, name FROM " . icms::$xoopsDB->prefix('newblocks')
                            . " WHERE mid='" . (int) $this->module_id
                            . "' AND func_num='" . (int) $i
                            . "' AND show_func='" . addslashes($block['show_func'])
                            . "' AND func_file='" . addslashes($block['file']) . "'";
                    $fresult = icms::$xoopsDB->query($sql);
                    $fcount = 0;
                    while ($fblock = icms::$xoopsDB->fetchArray($fresult)) {
                        $fcount++;
                        $sql = "UPDATE " . icms::$xoopsDB->prefix("newblocks")
                                . " SET name='" . addslashes($block['name'])
                                . "', edit_func='" . addslashes($editfunc)
                                . "', content='', template='" . $template
                                . "', last_modified=" . time()
                                . " WHERE bid='" . (int) $fblock['bid'] . "'";
                        $result = icms::$xoopsDB->query($sql);
                        if (!$result) {
                            $response->error(sprintf('&nbsp;&nbsp;' . _MD_AM_UPDATE_FAIL, $fblock['name']));
                        } else {
                            $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_BLOCK_UPDATED, '<strong>' . $fblock['name'] . '</strong>', '<strong>' . icms_conv_nr2local($fblock['bid']) . '</strong>'));
                            if ($template != '') {
                                $tplfile = & $tplfile_handler->find('default', 'block', $fblock['bid']);
                                if (count($tplfile) == 0) {
                                    $tplfile_new = & $tplfile_handler->create();
                                    $tplfile_new->setVar('tpl_module', $this->dirname);
                                    $tplfile_new->setVar('tpl_refid', (int) $fblock['bid']);
                                    $tplfile_new->setVar('tpl_tplset', 'default');
                                    $tplfile_new->setVar('tpl_file', $block['template'], TRUE);
                                    $tplfile_new->setVar('tpl_type', 'block');
                                } else {
                                    $tplfile_new = $tplfile[0];
                                }
                                $tplfile_new->setVar('tpl_source', $content, TRUE);
                                $tplfile_new->setVar('tpl_desc', $block['description'], TRUE);
                                $tplfile_new->setVar('tpl_lastmodified', time());
                                $tplfile_new->setVar('tpl_lastimported', 0);
                                if (!$tplfile_handler->insert($tplfile_new)) {
                                    $response->msg(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">'
                                            . _MD_AM_TEMPLATE_UPDATE_FAIL . '</span>', '<strong>' . $block['template'] . '</strong>'));
                                } else {
                                    $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TEMPLATE_UPDATED, '<strong>' . $block['template'] . '</strong>'));
                                    if ($icmsConfig['template_set'] == 'default') {
                                        if (!$xoopsDelTpl->template_touch($tplfile_new->getVar('tpl_id'))) {
                                            $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">'
                                                    . _MD_AM_TEMPLATE_RECOMPILE_FAIL . '</span>', '<strong>' . $block['template'] . '</strong>'));
                                        } else {
                                            $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TEMPLATE_RECOMPILED, '<strong>' . $block['template'] . '</strong>'));
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($fcount == 0) {
                        $newbid = icms::$xoopsDB->genId(icms::$xoopsDB->prefix('newblocks') . '_bid_seq');
                        $block_name = addslashes($block['name']);
                        /* @todo properly handle the block_type when updating the system module */
                        $sql = "INSERT INTO " . icms::$xoopsDB->prefix("newblocks")
                                . " (bid, mid, func_num, options, name, title, content, side, weight, visible, block_type, c_type, isactive, dirname, func_file, show_func, edit_func, template, bcachetime, last_modified) VALUES ('"
                                . (int) $newbid . "', '" . (int) $this->module_id . "', '" . (int) $i . "', '" . addslashes($options) . "', '" . $block_name . "', '" . $block_name . "', '', '1', '0', '0', 'M', 'H', '1', '" . addslashes($this->dirname) . "', '" . addslashes($block['file']) . "', '" . addslashes($block['show_func']) . "', '" . addslashes($editfunc) . "', '" . $template . "', '0', '" . time() . "')";
                        $result = icms::$xoopsDB->query($sql);
                        if (!$result) {
                            $response->error(sprintf('&nbsp;&nbsp;' . _MD_AM_CREATE_FAIL, $block['name']));
                        } else {
                            if (empty($newbid)) {
                                $newbid = icms::$xoopsDB->getInsertId();
                            }
                            $groups = & icms::$user->getGroups();
                            $gperm_handler = icms::handler('icms_member_groupperm');
                            foreach ($groups as $mygroup) {
                                $bperm = & $gperm_handler->create();
                                $bperm->setVar('gperm_groupid', (int) $mygroup);
                                $bperm->setVar('gperm_itemid', (int) $newbid);
                                $bperm->setVar('gperm_name', 'block_read');
                                $bperm->setVar('gperm_modid', 1);
                                if (!$gperm_handler->insert($bperm)) {
                                    $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_BLOCK_ACCESS_FAIL . '</span>', '<strong>' . $newbid . '</strong>', '<strong>' . $mygroup . '</strong>'));
                                } else {
                                    $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_BLOCK_ACCESS_ADDED, '<strong>' . $newbid . '</strong>', '<strong>' . $mygroup . '</strong>'));
                                }
                            }

                            if ($template != '') {
                                $tplfile = & $tplfile_handler->create();
                                $tplfile->setVar('tpl_module', $this->dirname);
                                $tplfile->setVar('tpl_refid', (int) $newbid);
                                $tplfile->setVar('tpl_source', $content, TRUE);
                                $tplfile->setVar('tpl_tplset', 'default');
                                $tplfile->setVar('tpl_file', $block['template'], TRUE);
                                $tplfile->setVar('tpl_type', 'block');
                                $tplfile->setVar('tpl_lastimported', 0);
                                $tplfile->setVar('tpl_lastmodified', time());
                                $tplfile->setVar('tpl_desc', $block['description'], TRUE);
                                if (!$tplfile_handler->insert($tplfile)) {
                                    $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_TEMPLATE_INSERT_FAIL . '</span>', '<strong>' . $block['template'] . '</strong>'));
                                } else {
                                    $newid = $tplfile->getVar('tpl_id');
                                    $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TEMPLATE_INSERTED, '<strong>' . $block['template'] . '</strong>', '<strong>' . $newid . '</strong>'));
                                    if ($icmsConfig['template_set'] == 'default') {
                                        if (!$xoopsDelTpl->template_touch($newid)) {
                                            $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_TEMPLATE_RECOMPILE_FAIL . '</span>', '<strong>' . $block['template'] . '</strong>'));
                                        } else {
                                            $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TEMPLATE_RECOMPILED, '<strong>' . $block['template'] . '</strong>'));
                                        }
                                    }
                                }
                            }
                            $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_BLOCK_CREATED, '<strong>' . $block['name'] . '</strong>', '<strong>' . $newbid . '</strong>'));
                            $sql = "INSERT INTO " . icms::$xoopsDB->prefix('block_module_link')
                                    . " (block_id, module_id, page_id) VALUES ('"
                                    . (int) $newbid . "', '0', '1')";
                            icms::$xoopsDB->query($sql);
                        }
                    }
                }
            }

            $icms_block_handler = icms::handler('icms_view_block');
            $block_arr = $icms_block_handler->getByModule($this->module_id);
            foreach ($block_arr as $block) {
                if (!in_array($block->getVar('show_func'), $showfuncs) || !in_array($block->getVar('func_file'), $funcfiles)) {
                    $sql = sprintf("DELETE FROM %s WHERE bid = '%u'", icms::$xoopsDB->prefix('newblocks'), (int) $block->getVar('bid'));
                    if (!icms::$xoopsDB->query($sql)) {
                        $response->msg(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_BLOCK_DELETE_FAIL . '</span>', '<strong>' . $block->getVar('name') . '</strong>', '<strong>' . $block->getVar('bid') . '</strong>'));
                    } else {
                        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_BLOCK_DELETED, '<strong>' . $block->getVar('name') . '</strong>', '<strong>' . $block->getVar('bid') . '</strong>'));
                        if ($block->getVar('template') != '') {
                            $tplfiles = & $tplfile_handler->find(NULL, 'block', $block->getVar('bid'));
                            if (is_array($tplfiles)) {
                                $btcount = count($tplfiles);
                                for ($k = 0; $k < $btcount; $k++) {
                                    if (!$tplfile_handler->delete($tplfiles[$k])) {
                                        $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_BLOCK_TMPLT_DELETE_FAILED . '</span>', '<strong>' . $tplfiles[$k]->getVar('tpl_file') . '</strong>', '<strong>' . $tplfiles[$k]->getVar('tpl_id') . '</strong>'));
                                    } else {
                                        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_BLOCK_TMPLT_DELETED, '<strong>' . $tplfiles[$k]->getVar('tpl_file') . '</strong>', '<strong>' . $tplfiles[$k]->getVar('tpl_id') . '</strong>'));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

}