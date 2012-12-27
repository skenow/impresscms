<?php

/**
 * Description of Simple
 *
 * @author mekdrop
 */
class icms_collection_Simple
    extends icms_collection_Base {
    
    private $_data = array();    
    
    /**
     * Add item to collection
     *
     * @param string $key
     * @param string $value 
     */
    public function add($key, $value) {
        if ($this->keyExists($key))
            Throw new Exception('Key '.$key.' already exists!');
        $this->_data[$key] = $value;
    }
    
    public function remove($key) {
        unset ($this->_data[$key]);
    }
    
    /**
     * Check if key in collection exists
     *
     * @param string $key
     * 
     * @return bool 
     */
    public function keyExists($key) {
        return isset($this->_data[$key]);
    }
    
    /**
     * Get item from collection
     *
     * @param string $key
     * 
     * @return mixed 
     */
    public function get($key) {
        return $this->_data[$key];
    }
    
    /**
     * Clear collection
     */
    public function clear() {
        $this->_data = array();
    }
    
    /**
     * Converts collection to array
     * 
     * @return array
     */
    public function toArray() {
        return $this->_data;
    }
    
    /**
     * Modify added value to response
     *
     * @param mixed $var        Variable to modify
     * @param mixed $value      Value to change
     */
    public function __set($var, $value) {
        if (isset($this->_data[$var]))
           $this->_data[$var] = $value;
        else
            Throw new Exception($var . ' var not added!');
    }
    
    /**
     * Import data into collection
     * 
     * @param mixed $data Data to import into object
     */
    public function import(&$data) {
        if (is_object($data)) {
            if (($data instanceof icms_collection_Simple) || method_exists($data, 'toArray'))  {
                $arx = $data->toArray();
            } elseif (method_exists($data, 'toResponse')) {
                $arx = $data->toResponse;
                if ($data instanceof icms_collection_Response)
                    $arx = $arx->toArray();
                else
                    $arx = (array)$data;
            } else {
                $arx = (array)$data;
            }
        } elseif (is_array($data)) {
            $arx = $data;
        } else {
            $name = '#@$' . microtime(true);
            while($this->keyExists($name))
                $name = '#@$' . microtime(true);
            $arx[$name] = $data;
        }
        $this->_data = $this->mergeRecursive($this->_data, $arx);
    } 
    
    private function mergeRecursive(&$array1, &$array2) {
        $keys1 = array_keys($array1);
        $keys2 = array_keys($array2);
        $ret = array();
        foreach(array_intersect($keys1, $keys2) as $key)
             if (is_array($array1[$key]))
                 $ret[$key] = $this->mergeRecursive($array1[$key], $array2[$key]);
             else
                 $ret[$key] = $array2[$key];
        foreach(array_diff($keys1, $keys2) as $key)
             $ret[$key] = $array1[$key];
        foreach(array_diff($keys2, $keys1) as $key)
             $ret[$key] = $array2[$key];
        return $ret;
    }
    
}