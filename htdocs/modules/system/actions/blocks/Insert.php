<?php

/**
 * @property string $dirname    Dirname for module
 * @property string $file       File for template
 * @property int $ref_id     ID of module
 * @property string $description Description of template
 * @property string $type       Type of template
 */
class action_system_blocks_Insert extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('file', self::DTYPE_STRING, false);
        $this->initVar('show_func', self::DTYPE_STRING, false);
        $this->initVar('options', self::DTYPE_ARRAY, false);
        $this->initVar('edit_func', self::DTYPE_STRING, false);
        $this->initVar('name', self::DTYPE_STRING, '');
        $this->initVar('dirname', self::DTYPE_STRING, '');
        $this->initVar('template', self::DTYPE_STRING, '');
        $this->initVar('blockkey', self::DTYPE_STRING, '');

        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        if (!empty($this->file) || empty($this->show_func))
            return $response->error('Error in blocks configuration!');

        $blockHandler = icms::handler('icms_view_block');
        $block = $blockHandler->create();
        
        foreach (array('options', 'edit_func') as $func)
            if (!empty($this->$func))
                $block->setVar($func, $this->$func);        
                
        $block->setVar('func_num', $this->blockkey);
        $block->setVar('name', trim($this->name));
        $block->setVar('title', trim($this->name));
        $block->setVar('content', '');
        $block->setVar('side', 1);
        $block->setVar('weight', 0);
        $block->setVar('visible', false);
        $block->setVar('block_type', 'M');
        $block->setVar('c_type', 'H');
        $block->setVar('isactive', true);
        $block->setVar('dirname', $this->dirname);
        $block->setVar('func_file', trim($this->file));
        $block->setVar('show_func', trim($this->show_func));
        $block->setVar('template', trim($this->template));
        $block->setVar('bcachetime', 0);
        $block->setVar('last_modified', time());
        if (!$block->store())
            return $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_BLOCKS_ADD_FAIL . '</span>', '<strong>' . $block['name'] . '</strong>', '<strong>' . icms::$xoopsDB->error() . '</strong>'));
        $response->msg(sprintf(_MD_AM_BLOCK_ADDED, '<strong>' . $block->getVar('name') . '</strong>', '<strong>' . icms_conv_nr2local($block->getVar('bid')) . '</strong>'));
        if (isset($block['template']) && trim($block['template']) != '')
            $response->addModuleAction('templates/Insert', array(
                'file' => trim($block['template']),
                'description' => $block['description'],
                'ref_id' => $block->getVar('bid'),
                'dirname' => $this->dirname,
                'type' => 'block'
                    ), 'system');
        $sql = sprintf('INSERT INTO %s (block_id, module_id, page_id) VALUES (%d, 0, 1)', icms::$xoopsDB->prefix('block_module_link'), $block->getVar('bid'));
        icms::$xoopsDB->query($sql);
    }

}