<?php

/**
 * Class for dealing with direct responses
 *
 * @category            ICMS
 * @author              Raimondas Rimkevičius <i.know@mekdrop.name>
 */
class icms_action_Response {

    /**
     * Get's main response instance
     * 
     * @return self 
     */
    public static function getInstance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
            define('ICMS_HAS_RESPONSE', true);
        }
        return $instance;
    }

    public $renderFormat = 'json';

    /**
     * Import module action results into response 
     *
     * @param string $action    Action name
     * @param array $params     Associate array with action params
     * @param string $module    Module from which action to call. If not specified use same module as currently running
     */
    public function addModuleAction($action, $params = array(), $module = null) {

        if ($module === null)
            if (is_object(icms::$module))
                $module = icms::$module->getVar('dirname');
            else
                $module = 'system';

        $handler = icms::handler('icms_action');
        $instance = $handler->getModuleAction($module, $action, $params);
        if (!$instance)
            return $this->error(sprintf('Action "%s" for "%s" module doesn\'t exists', $action, $module));

        try {
            $instance->exec($this);
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Imports control action results into response
     *
     * @param string $control   Control name
     * @param string $action    Action name
     * @param array $params     Action params
     */
    public function addControlAction($control, $action, $params = array()) {

        if ($control instanceof \icms_action_base_Control) {
            $type = $control->control->getType();
            $params['icms-control-instance'] = $control->control;
        } elseif ($control instanceof \icms_controls_Base) {
            $type = $control->getType();
            $params['icms-control-instance'] = $control;
        } else
            $type = $control;

        $handler = icms::handler('icms_action');
        $instance = $handler->getControlAction($type, $action, $params);
        if (!$instance)
            return $this->error(sprintf('Action "%s" for "%s" control doesn\'t exists', $action, $control));

        try {
            $instance->exec($this);
            $changed = $instance->control->getChangedVars();
            if (!empty($changed)) {
                $key = icms_action_base_Control::RESPONSE_KEY_CHANGED_PROPERTIES;
                if (!isset($this->$key))
                    $this->add(icms_action_base_Control::RESPONSE_KEY_CHANGED_PROPERTIES, array());
                $changes = $instance->control->toArray();
                $changed = array_flip($changed);
                foreach (array_keys($changes) as $var)
                    if (!isset($changed[$var]))
                        unset($changes[$var]);
                $data = $this->$key;
                $data[$instance->control->getVar('id')] = $changes;
                $this->$key = $data;
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->add('isOk', true);
    }

    /**
     * Adds error message in to response
     *
     * @param string/array $message     Error message or array with error messages
     */
    public function error($message) {
        if (is_array($message)) {
            $this->add('errors', $message);
        } else {
            if ($this->keyExists('error')) {
                $error = $this->error;
                $this->remove('error');
                return $this->error(array($error, $message));
            } else
                $this->add('error', $message);
        }
        $this->isOk = false;
    }

    /**
     * Adds special message to describe response
     *
     * @param string $message     Message to show in response
     */
    public function msg($message) {
        if (is_array($message)) {
            $this->add('messages', $message);
        } else {
            if ($this->keyExists('message')) {
                $msg = $this->message;
                $this->error(array($msg, $message));
            } else
                $this->add('message', $message);
        }
    }

    /**
     * Renders response
     * 
     * @return string 
     */
    public function render() {
        $data = $this->toArray();
        if ($this->renderFormat == 'json') {
            if (!headers_sent())
                header('Content-Type: application/json');
            return json_encode($data);
        } else {
            $class = 'icms_action_rformat_' . $this->renderFormat;
            if (!class_exists($class, true)) {
                trigger_error('Unsupported rendering format');
                return null;
            }
            $instance = new $class();
            if (!($instance instanceof icms_action_IFormat)){
                trigger_error('Unsupported rendering format');
                return null;
            }
            if (!headers_sent())
                header('Content-Type: ' .  $instance->getContentType());
            return $instance->render($data);
        }        
    }

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
        return $this->render();
    }

    private $_data = array();

    /**
     * Add item to collection
     *
     * @param string $key
     * @param string $value 
     */
    public function add($key, $value) {
        if ($this->keyExists($key))
            Throw new Exception('Key ' . $key . ' already exists!');
        $this->_data[$key] = $value;
    }

    /**
     * Removes item from response
     * 
     * @param string $key
     */
    public function remove($key) {
        unset($this->_data[$key]);
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
            if (($data instanceof icms_collection_Simple) || method_exists($data, 'toArray')) {
                $arx = $data->toArray();
            } elseif (method_exists($data, 'toResponse')) {
                $arx = $data->toResponse;
                if ($data instanceof self)
                    $arx = $arx->toArray();
                else
                    $arx = (array) $data;
            } else {
                $arx = (array) $data;
            }
        } elseif (is_array($data)) {
            $arx = $data;
        } else {
            $name = '#@$' . microtime(true);
            while ($this->keyExists($name))
                $name = '#@$' . microtime(true);
            $arx[$name] = $data;
        }
        $this->_data = $this->mergeRecursive($this->_data, $arx);
    }

    private function mergeRecursive(&$array1, &$array2) {
        $keys1 = array_keys($array1);
        $keys2 = array_keys($array2);
        $ret = array();
        foreach (array_intersect($keys1, $keys2) as $key)
            if (is_array($array1[$key]))
                $ret[$key] = $this->mergeRecursive($array1[$key], $array2[$key]);
            else
                $ret[$key] = $array2[$key];
        foreach (array_diff($keys1, $keys2) as $key)
            $ret[$key] = $array1[$key];
        foreach (array_diff($keys2, $keys1) as $key)
            $ret[$key] = $array2[$key];
        return $ret;
    }

    /**
     * Adds data from file
     * 
     * @param string $key       Key where add data
     * @param string $file      File from where You need to read data
     * @param String $format    File format
     */
    public function addFile($key, $file, $format = 'json') {
        $contents = file_get_contents($file);
        switch ($format) {
            case 'json':
                $this->add($key, json_decode($contents, true));
                break;
            case 'unserialize':
                $this->add($key, unserialize($contents));
                break;
            default:
                $this->add($key, $contents);
                break;
        }
    }

}