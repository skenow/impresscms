<?php

/**
 * Cache some data in files
 *
 * @author mekdrop
 */
class icms_cache_File
  extends icms_cache_Base {
    
    /**
     * Filename of cached file
     *
     * @var string 
     */
    private $filename;    
    
    /**
     * Constructor
     * 
     * @param string $module    Module for caching
     * @param string $type      Cache type
     * @param string $area      Area of cached data (can be also used as other params)
     */
    public function __construct($module, $type, $area = 'default') {        
        parent::__construct($module, $type, $area);
        $this->filename = $this->path . '/' . $this->location . '.json';
    }        
    
    /**
     * Does this cache needs update?
     * 
     * @return bool
     */
    public function needUpdate() {
        return ((!file_exists($filename)) || (($time > 0) && (abs(filemtime($this->filename) - time()) > $this->time)));
    }
    
    /**
     * Write cached data
     * 
     * @param mixed $data
     */
    public function write($data) {
        if (!file_exists($this->path))
            mkdir(dirname($this->path), 0777, true);
        file_put_contents($this->filename, json_encode($data), LOCK_EX);
    }
    
    /**
     * Read cached data
     * 
     * @param mixed $default  Default data to return if there is no cached
     * @param bool $autosave  Do we need save if we don't find data ?
     * 
     * @return mixed
     */
    public function read($default = null, $autosave = true) {
        if (!$this->needUpdate())
            return json_decode(file_get_contents($this->filename), true);
        $whatToSave = is_callable($default)?call_user_func($default):$default;        
        if ($autosave)
            $this->write($whatToSave, $autosave);
        return $whatToSave;
    }
    
}