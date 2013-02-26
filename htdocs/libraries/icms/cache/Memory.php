<?php

/**
 * Cache some data in memory
 *
 * @author mekdrop
 */
class icms_cache_Memory
    extends icms_cache_Base {
    
    private static $connection = null;
    private static $type = 0;

    public function __construct($module, $type, $area = 'default') {
        parent::__construct($module, $type, $area);
        $this->location = sha1($this->location) . '-' . strlen($this->location);
        if (self::$type === 0) {
            if (class_exists('Memcached')) {
                self::$type = 1;
                self::$connection = new Memcached();
                foreach ($icmsConfig['memcached_servers'] as $i => $server) {
                    $server = explode(':', $server);
                    if (!isset($server[1]))
                        $server[1] = 11211;
                    self::$connection->addServer($server[0], $server[1], $i);
                }
            } elseif (class_exists('Memcache')) {
                self::$type = 2;
                self::$connection = new Memcache();
                foreach ($icmsConfig['memcached_servers'] as $i => $server) {
                    $server = explode(':', $server);
                    if (!isset($server[1]))
                        $server[1] = 11211;
                    self::$connection->addServer($server[0], $server[1], true, $i);
                }
            } else {                
                self::$type = -1;
                Throw new Exception('Can\t connect');
            }
        }
    }
    
    /**
     * Does this cache needs update?
     * 
     * @return bool
     */
    public function needUpdate() {
        switch (self::$type) {
            case 2:
            case 1:
                if (self::$connection->get($this->location) === false)
                    return true;
            break;            
            case -1:
                Throw new Exception('Can\'t connect');
            break;
        }
        return false;
    }
    
    /**
     * Write cached data
     * 
     * @param mixed $data
     */
    public function write($data) {
        switch (self::$type) {
            case 1:
                self::$connection->set($this->location, serialize($data), $this->time);
            break;
            case 2:
                self::$connection->set($this->location, serialize($data), MEMCACHE_COMPRESSED, $this->time);
            break;
            case -1:
                Throw new Exception('Can\'t connect');
            break;
        }
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
        switch (self::$type) {
            case 1:
                $ret = self::$connection->get($this->location);
            break;
            case 2:
                $ret = self::$connection->get($this->location);
            break;
            case -1:
                Throw new Exception('Can\'t connect');
            break;
        }
        if (!is_string($ret)) {
            $whatToSave = is_callable($default)?call_user_func($default):$default;        
            if ($autosave)
                $this->write($whatToSave, $autosave);
            return $whatToSave;
        } else {
            return unserialize($ret);
        }
    }
    
}