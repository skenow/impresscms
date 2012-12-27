<?php

/**
 * Collection to store temporary objects
 *
 * @author mekdrop
 */
class icms_collection_Global
    extends icms_collection_Base {
    
    private static $data = array();
    
    protected $name = '';    
    
    /**
     * Gets specified instance of collection
     *
     * @param type $name
     * 
     * @return self 
     */
    public static function getInstance($name = 'default') {
        return new self($name);
    }
    
    /**
     * Constructor
     *
     * @param string $name Name of namespace in global collection
     */
    public function __construct($name = 'default') {
        $this->name = $name;
    }
    
    /**
     * Add item to collection
     *
     * @param string $key
     * @param string $value 
     */
    public function add($key, $value) {
        self::$data[$this->name][$key] = $value;
    }
    
    /**
     * Check if key in collection exists
     *
     * @param string $key
     * 
     * @return bool 
     */
    public function keyExists($key) {
        return isset(self::$data[$this->name][$key]);
    }
    
    /**
     * Get item from collection
     *
     * @param string $key
     * 
     * @return mixed 
     */
    public function get($key) {
        return self::$data[$this->name][$key];
    }
    
    /**
     * Clear collection
     */
    public function clear() {
        unset(self::$data[$this->name]);
    }
    
    /**
     * Converts object to array
     */
    public function toArray() {
        return self::$data[$this->name];
    }
    
}