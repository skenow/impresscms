<?php

abstract class action_system_actionscript_Make
    extends icms_action_base_Module {
    
    private $_actions = array();
    
    protected function writeMessage($msg) {
        $this->_actions[] = array(
            'type' => 'msg',
            'params' => compact('msg')
        );
    }
    
    protected function execDataGateringAction($action, $params, $module) {
        $this->_actions[] = array(
            'type' => 'data_gatering_action',
            'params' => compact('action', 'params', 'module')
        );
    }
    
    protected function execDefaultAction($action, $params, $module) {
        $this->_actions[] = array(
            'type' => 'default_action',
            'params' => compact('action', 'params', 'module')
        );
    }
    
    protected function addToGateredDataCollection($key, $value) {
        $this->_actions[] = array(
            'type' => 'add_to_collection',
            'params' => compact('key', 'value')
        );
    }
    
    protected function renameCollectionKey($key, $new_name) {
        $this->_actions[] = array(
            'type' => 'rename_collection_key',
            'params' => compact('key', 'new_name')
        );
    }
    
    protected function unsetCollectionKey($key) {
        $this->_actions[] = array(
            'type' => 'unset_collection_key',
            'params' => compact('key')
        );
    }
    
    protected function makeScript(icms_action_Response &$response) {
        $response->add('actions', $this->_actions);
    }
    
}