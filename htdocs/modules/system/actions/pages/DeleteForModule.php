<?php

class action_system_pages_DeleteForModule
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('module_id', self::DTYPE_INTEGER);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        $page_handler = icms::handler('icms_data_page');
        $criteria = new icms_db_criteria_Compo(new icms_db_criteria_Item('page_moduleid', $this->module_id));
        $pages = $page_handler->getCount($criteria);

        if ($pages > 0) {
                $pages = $page_handler->getObjects($criteria);
                $response->msg(_MD_AM_SYMLINKS_DELETE);
                foreach ($pages as $page) {
                        if (!$page_handler->delete($page)) 
                                return $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_SYMLINK_DELETE_FAIL . '</span>', $page->getVar('page_title'),  '<strong>'. $page->getVar('page_id') . '</strong>'));
                         $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_SYMLINK_DELETED, '<strong>' . $page->getVar('page_title') . '</strong>', '<strong>' . $page->getVar('page_id') . '</strong>'));
                }
        }  
    }
    
}