<?php

/**
 * This class is used as base class for all collections
 *
 * @author mekdrop
 */
abstract class icms_collection_Base {
    
    abstract function keyExists($key);
    abstract function get($key);
    abstract function add($key, $value);
    abstract function clear();
    abstract function toArray();
     
    /**
     * Does same sas keyExists
     *
     * @param string $name
     * 
     * @return bool 
     */
    public function __isset($name) {
        return $this->keyExists($name);
    }
    
    /**
     * Does same as get function
     *
     * @param string $name
     * 
     * @return mixed
     */
    public function __get($name) {
        return $this->get($name);
    }
    
    /**
     * Convert object to string
     *
     * @return string 
     */
    public function __toString() {
        return get_class($this);
    }
    
    
    
}