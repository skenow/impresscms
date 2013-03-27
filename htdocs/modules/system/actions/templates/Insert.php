<?php

/**
 * @property string $dirname    Dirname for module
 * @property string $file       File for template
 * @property int $ref_id     ID of module
 * @property string $description Description of template
 * @property string $type       Type of template
 */
class action_system_templates_Insert
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('dirname', self::DTYPE_STRING, false);
        $this->initVar('file', self::DTYPE_STRING, false);
        $this->initVar('ref_id', self::DTYPE_INTEGER, false);
        $this->initVar('description', self::DTYPE_STRING, false);
        $this->initVar('type', self::DTYPE_STRING, false);
        
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $tplfile_handler =& icms::handler('icms_view_template_file');
        $tpl = new icms_view_Tpl();
        $tplfile = $tplfile_handler->create();
        $tpldata = $this->getSource($this->dirname, $this->file, ($this->getVar('type') == 'block'));
        $tplfile->setVar('tpl_source', $tpldata);
        $tplfile->setVar('tpl_refid', $this->ref_id);
        $tplfile->setVar('tpl_tplset', 'default');
        $tplfile->setVar('tpl_file', $this->file);
        $tplfile->setVar('tpl_desc', $this->description, TRUE);
        $tplfile->setVar('tpl_module', $this->dirname);
        $tplfile->setVar('tpl_lastmodified', time());
        $tplfile->setVar('tpl_lastimported', 0);
        $tplfile->setVar('tpl_type', $this->type);
        if (!$tplfile_handler->insert($tplfile)) {
                return $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_TEMPLATE_INSERT_FAIL . '</span>',
                        '<strong>' . $tpl['file'] . '</strong>'));
        } else {
                $newtplid = $tplfile->getVar('tpl_id');
                $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TEMPLATE_INSERTED,
                        '<strong>' . $this->file . '</strong>', '<strong>' . $newtplid . '</strong>'));

                // generate compiled file
                if (!$tpl->template_touch($newtplid))
                        $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_TEMPLATE_COMPILE_FAIL . '</span>',
                                '<strong>' . $this->file . '</strong>', '<strong>' . $newtplid . '</strong>'));
                else
                        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TEMPLATE_COMPILED, '<strong>' . $this->file . '</strong>'));
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