<?php

class action_system_blocks_AddForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('blocks', self::DTYPE_ARRAY);
        $this->initVar('dirname', self::DTYPE_STRING);
        parent::__construct($params);
    }
    
    public function exec(\icms_action_Response &$response) {
        $response->msg(_MD_AM_BLOCKS_ADDING);        
        foreach ($this->blocks as $blockkey => $block)
            $response->addModuleAction('blocks/Insert', array_merge($block, array('dirname' => $this->dirname, 'blockkey' => $blockkey)), 'system');
    }
    
}