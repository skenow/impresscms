<?php
/**
 * Class for dealing with direct responses
 *
 * @category            ICMS
 * @author              Raimondas Rimkevičius <i.know@mekdrop.name>
 */
class icms_collection_Response
    extends icms_collection_Simple {
    
        /**
         * Returned response will be in JSON format
         */
        const FORMAT_JSON = 0;
        
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
        
        public $renderFormat = self::FORMAT_JSON;
        
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
                return $this->error(sprintf('Action "%s" for "%s" module doesn\'t exists', $action, serialize($module)));
            
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
            switch ($this->renderFormat) {
                case self::FORMAT_JSON:
                    return json_encode($data);
                break;
                default:
                    trigger_error('Unsupported rendering format');
                    return null;
            }
        }
        
        /**
         * Magic function to convert object to string
         *
         * @return string 
         */
        public function __toString() {
            return $this->render();
        }
      
}