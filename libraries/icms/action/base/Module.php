<?php
/**
 * Special action executed when doing request. Returns data for application but not for user
 *
 * @author mekdrop
 */
abstract class icms_action_base_Module
    extends icms_ipf_Properties {
    
    /**
     * Constructor
     *
     * @param array $params Array with keys used to set current action properties
     */
    public function __construct($params = array()) {
        $this->load($params);
        foreach ($params as $key => $value)
            if (isset($this->$key))
                $this->setVarInfo($key, 'changed', true);
    }
    
    /**
     * This is called when action is executed
     */
    abstract function exec(icms_collection_Response &$response);
    
}