<?php

/**
 * Base class for caching
 *
 * @author mekdrop
 */
abstract class icms_cache_Base {    
    
    /**
     * How long to cache?
     *
     * @var int 
     */
    public $time = 0;
    
    /**
     * Location for cached data
     *
     * @var string 
     */
    public $location = '';
    
    /**
     * Constructor
     * 
     * @param string $module    Module for caching
     * @param string $type      Cache type
     * @param string $area      Area of cached data
     */
    public function __construct($module, $type, $area = 'default') {
        if (is_array($area))
            $a = implode('/', array_map('urlencode', $area));        
        else
            $a = urlencode($area);
        $this->location = $module . '/' . $a;
    }    
    
    abstract public function read($default = null, $autosave = true);
    abstract public function write($data);
    abstract public function needUpdate();    
    
    /**
     * Gets private class variable
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function __get($name) {
        return $this->$name;
    }
    
}