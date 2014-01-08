<?php

/**
 * Class for dealing with direct responses
 *
 * @category            ICMS
 * @author              Raimondas Rimkevičius <i.know@mekdrop.name>
 */
class icms_action_Response {
    
    const NTYPE_MESSAGE = 0;
    const NTYPE_ERROR = 1;
    const NTYPE_WARNING = 2;
    const NTYPE_LOG = 4;
    
    const MODE_AUTO = 0;
    const MODE_REPLACE = 1;
    const MODE_UPDATE = 2;
    
    const OPT_NONE = 0;
    const OPT_RENDERTIME = 1;
    const OPT_MODEASTEXT = 2;    
    
    /**
     * Stores base data for response
     *
     * @var array
     */
    protected $_baseData = array(
        'isOK'  => true,
        'data'  => array(),
        'mode'  => self::MODE_AUTO
    );

    /**
     * Get's main response instance
     * 
     * @return icms_action_Response 
     */
    public static function getInstance() {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
            define('ICMS_HAS_RESPONSE', true);
        }
        return $instance;
    }
    
    /**
     * Returns if everything is ok with response
     * 
     * @return bool
     */
    public function isOK() {
        return $this->_baseData['isOK'];
    }
        
    /**
     * Switch response mode
     * 
     * @param int $mode
     * 
     * @throws Exception
     */
    public function switchMode($mode) {
        if (($this->_baseData['mode'] == self::MODE_AUTO) && ($mode != self::MODE_AUTO)) {
            $this->_baseData['mode'] = $mode;            
        } elseif ($this->_baseData['mode'] != $mode) {
            Throw new Exception(sprintf('Can\'t switch from %s to %s mode in response',$this->_baseData['mode'], $mode));
        }
    }
    
    /**
     * Returns current mode
     * 
     * @return int
     */
    public function getMode() {
        return $this->_baseData['mode'];
    }

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
        elseif (!$instance->checkIfRightsAreOK($this))
            return;

        try {
            //$this->_baseData['hashes'][] = $module . '_' . $action . '_' . md5(json_encode($params));
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
            //$this->_baseData['hashes'][] = $module . '_' . $action . '_' . md5(json_encode($params));
            $instance->exec($this);
            $changed = $instance->control->getChangedVars();
            if (!empty($changed)) {                
                $changes = $instance->control->toArray();
                $changed = array_flip($changed);
                foreach (array_keys($changes) as $var)
                    if (!isset($changed[$var]))
                        unset($changes[$var]);                
                $this->_baseData[icms_action_base_Control::RESPONSE_KEY_CHANGED_PROPERTIES][$instance->control->getVar('id')] = $changes;
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Adds error message in to response
     *
     * @param string/array $message     Error message or array with error messages
     */
    public function error($message) {
        $this->notify($message, self::NTYPE_ERROR);
        $this->_baseData['isOK'] = false;
    }

    /**
     * Adds special message to describe response
     *
     * @param string $message     Message to show in response
     */
    public function msg($message) {
        $this->notify($message);
    }
    
    /**
     * Adds messages into to the response
     * 
     * @param string/array $message     Message text
     * @param int $type                 NTYPE_* constant as message type
     */
    public function notify($message, $type = self::NTYPE_MESSAGE) {
        if (is_array($message)) {
            foreach ($message as $msg)
                $this->notify($msg, self::NTYPE_ERROR);
            return;
        }           
        $this->_baseData['messages'][] = array(
            'time' => time(), 
            'type' => $type, 
            'message' => $message
        );
    }
    
    public function getBaseData($name) {
        return isset($this->_baseData[$name])?$this->_baseData[$name]:null;
    }
    
    public function setBaseData($name, $value) {
        $this->_baseData[$name] = $value;
    }
    
    /**
     * Adds only changes from object to response
     * 
     * @param icms_properties_Handler $object       Object from where to fetch changes
     * @param string $path                          Path to store this in array
     */
    public function addObjectChanges(icms_properties_Handler &$object, $path, $include_keys = array(), $include_values = array()) {        
        
	
		
		$temp = &$this->_baseData['data'];
		
		foreach(explode('/', $path) as $key)
		{
            $temp = &$temp[$key];
        }
		
		
		$values = $temp;
		
        $changed = $object->getChangedValues($include_keys, false);
	
	
		if (empty($changed) == false)
		{	
		    $this->switchMode(self::MODE_UPDATE);
        
			foreach($changed as $key => $value)
			{
				$temp[$key] = $value;
			}
        }
		
		if ($include_values != null && empty($include_values) == false)
		{
			foreach($include_values as $key => $value)
			{
				$temp[$key] = $value;
			}
		}
    }
    
    
    /**
     * Adds arrays changed to response
     * 
     * @param array $data           Object from where to fetch changes
     * @param string $path          Path to store this in array
     */
    public function addArrayChanges(Array &$data, $path) {
        $temp = &$this->_baseData['data'];
        foreach(explode('/', $path) as $key) {
            $temp = &$temp[$key];
        }
        $temp = $data;
    }
    
    /**
     * Get's mode as string
     * 
     * @param int
     * 
     * @return string
     */
    public function getModeString($mode) {
        switch ($this->_baseData['mode']) {
            case self::MODE_UPDATE:
                return 'update';
            case self::MODE_REPLACE:
            case self::MODE_AUTO:
            default:
                return 'replace';
        }
    }

    /**
     * Renders response
     * 
     * @param string $renderFormat  Specifies rendering format
     * @param array $renderOptions  Array with rendering options
     * 
     * @return string
     */
    public function render($renderFormat = 'json', $renderOptions = array()) {
        $data = $this->_baseData;
        if (isset($data['data']) && empty($data['data']))
            unset($data['data']);
        
        if (isset($renderOptions['renderTime']) && $renderOptions['renderTime']) {
            $data['time'] = time();
            unset($renderOptions['renderTime']);
        }
        
        if (isset($renderOptions['renderModeAsText']) && $renderOptions['renderModeAsText']) {
            $data['mode'] = $this->getModeString($data['mode']);
            if (isset($data['responses'])) {
                foreach ($data['responses'] as $i => $response) {
                    $data['responses'][$i]['mode'] = $this->getModeString($response['mode']);
                }
            }
            unset($renderOptions['renderModeAsText']);
        } else {
            if (isset($data['responses']))
                foreach ($data['responses'] as $i => $response)
                    if ($response['mode'] == self::MODE_AUTO)
                        unset($data['responses'][$i]['mode']);
            if ($data['mode'] == self::MODE_AUTO)
                unset($data['mode']);
        }
        
        $class = 'icms_action_format_' . $renderFormat;
        if (!class_exists($class, true)) {
            trigger_error('Unsupported '.$renderFormat.' rendering format');
            return null;
        }
        $instance = new $class();
        if (!($instance instanceof icms_action_IFormat)){
            trigger_error('Unsupported rendering format');
            return null;
        }
        if (!headers_sent())
            header('Content-Type: ' .  $instance->getContentType());
        
        return $instance->render($data, $renderOptions);        
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
    
    private function prepareArray(&$array) {
        $ret = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $this->prepareArray($value);
            } elseif (is_object($value)) {
                $this->prepareObject($value);                
            }
            $ret[$key] = $value;
        }
        $value = $ret;
    }
    
    private function prepareObject(&$object) {
        if (is_callable(array($object, 'toArray'))) {
            $object = $object->toArray();
        } else {
             $value = (array)$object;
             $this->prepareArray($value);
             $object = $value;
        }
    }

    /**
     * Add item to collection
     *
     * @param string $key
     * @param string $value 
     */
    public function add($key, $value)
	{
		if (is_object($value))
		{
            $this->prepareObject($value);
        }
		elseif (is_array($value))
        {
			$this->prepareArray($value);
        }
		
        $temp = &$this->_baseData['data'];
        
		if (strpos($key, '/') === false)
		{
            $temp = &$temp[$key];
		}
		else
		{
			foreach(explode('/', $key) as $path_key)
			{
	            $temp = &$temp[$path_key];
	        }	
		}
		
        if (isset($temp))
		{
            switch ($this->_baseData['mode'])
			{
                case self::MODE_UPDATE:
                    $temp = array_merge_recursive($temp, $value);
                break;
                default:
                    $temp = $value;   
            }
        }
		else
		{
            $temp = $value;
        }                    
    }   

    /**
     * Removes item from response
     * 
     * @param string $key
     */
    public function remove($key) {
        unset($this->_baseData['data'][$key]);
    }

    /**
     * Check if key in collection exists
     *
     * @param string $key
     * 
     * @return bool 
     */
    public function keyExists($key) {
        return isset($this->_baseData['data'][$key]);
    }

    /**
     * Get item from collection
     *
     * @param string $key
     * 
     * @return mixed 
     */
    public function get($key) {
        return $this->_baseData['data'][$key];
    }

    /**
     * Clear collection
     */
    public function clear() {
        $this->_baseData['data'] = array();
    }

    /**
     * Converts collection to array
     * 
     * @return array
     */
    public function toArray() {
        $data =  $this->_baseData;
        if (isset($data['data']) && empty($data['data']))
            unset($data['data']);
        return $data;
    }

    /**
     * Modify added value to response
     *
     * @param mixed $var        Variable to modify
     * @param mixed $value      Value to change
     */
    public function __set($var, $value) {
        if (isset($this->_baseData['data'][$var]))
            $this->_baseData['data'][$var] = $value;
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
        $this->_baseData['data'] = $this->mergeRecursive($this->_baseData['data'], $arx);
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