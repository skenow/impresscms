<?php

/**
 * Description of SetFavorite
 *
 * @author mekdrop
 */
class mod_content_GetContent
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('content_id', self::DTYPE_INTEGER, 0, false);
        parent::__construct($params);
    }
    
    public function exec(icms_collection_Response &$response) {
        
        $content_content_handler = icms_getModuleHandler('content');
        $content = $content_content_handler->getContents(0, 1, false, false, $this->content_id);
        
        if (!$content)
            return $response->error('Content not found!');
            
        $response->add('content', current($content));
    }
    
}