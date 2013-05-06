<?php
/**
 *Default interface for other formats
 * 
 * @author mekdrop
 */
interface icms_action_IFormat {
    
    public function getContentType();
    public function render(Array &$data);
        
}

