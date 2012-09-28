<?php


/**
 * Handles actions
 *
 * @author mekdrop
 */
class icms_action_Handler {
    
    private $db;
    
    public $output_format = icms_collection_Response::FORMAT_JSON;
    
    protected $response;
    
    const PARAM_ACTION = 'icms_action';
    const PARAM_PARAMS = 'icms_params';
    const PARAM_CONTROL = 'icms_control';
    const PARAM_MODULE = 'icms_module';
    const PARAM_DUMMY = 'icms_dummy_value';
    
    public function __get($name) {
        switch ($name) {
            case 'db':
            case 'response':
                return $this->$name;
            break;
        }
    }
    
    public function __construct(&$db) {
        $this->db = $db;
        $this->db->allowWebChanges = true;
        icms::$logger->disableRendering();
        $this->response = new icms_collection_Response();// icms_collection_Response::getInstance();
    }
    
    public function includeHeadersInResponse() {
        $this->response->add('system_headers', getallheaders());
        $this->response->add('$_GET', $_GET);
        $this->response->add('$_POST', $_POST);
        $this->response->add('$_FILES', $_FILES);
        $this->response->add('$_SERVER', $_SERVER);
    }
    
    public function includeLoggingInfoInResponse() {
        $logger = icms_core_Logger::instance();
        $log_data = array();
        foreach (array('queries', 'extra', 'errors', 'deprecated') as $name) {            
            foreach (array_map('array_filter', $logger->$name) as $item) {
                if (count($item) == 1)
                    $log_data[$name][] = current($item);
                else 
                    $log_data[$name][] = $item;
            }
        }            
        
        $this->response->add('system_log', $log_data);
    }
    
    public function render() {
        switch ($this->output_format) {
            case icms_collection_Response::FORMAT_JSON:
            default:
                //header('Content-Type: application/json');
                header('Content-Type: text/plain');
            break;
        }
        header('Content-Type: text/plain');
        echo $this->response->render($this->output_format);
    }
    
    public function getControlAction($control, $action, $params = array()) {
        $file = ICMS_CONTROLS_PATH . '/' . $control . '/actions/' . $action . '.php';
        if (!file_exists($file))
            return null;
        include_once $file;
        $i = strpos($control, '/');
        if (!$i) {
            $author = 'icms';
            $name = $control;
        } else {
            $author = substr($control, 0, $i);
            $name = substr($control, $i + 1);
        }
        
        if (!isset($params[self::PARAM_PARAMS]))
            $params[self::PARAM_PARAMS] = array();                
        $class = sprintf('\ImpressCMS\Controls\%s\%s\Actions\%s', $author, ucfirst($name), $action);
        if (!class_exists($class))
            return null;

        $icms_params = $params[self::PARAM_PARAMS];
        unset($params[self::PARAM_PARAMS]);
        if (isset($params[self::PARAM_DUMMY]))
            unset($params[self::PARAM_DUMMY]);
        
        $instance = new $class($params);
        
        if (isset($params['icms-control-instance']) && ($params['icms-control-instance'] instanceof icms_controls_Base)) {
            $instance->control = $params['icms-control-instance'];
        } else {
            $control_handler = new icms_controls_Handler();
            $instance->control = $control_handler->make($control, $icms_params);
        }
        
        return $instance;
    }
    
    public function getModuleAction($module, $action, $params = array()) {
        $file = ICMS_MODULES_PATH . '/' . $module . '/actions/' . $action . '.php';
        if (!file_exists($file))
            return null;
        icms_loadLanguageFile($module, 'actions');
        include_once $file;
        $class = 'action_' . $module . '_' . $action;
        if (!class_exists($class))
            return null;
        $instance = new $class($params);
        return $instance;
    }        
    
    public function getSystemAction($action, $params = array()) {
        return $this->getModuleAction('system', $action, $params);
    }
    
    public function hasActions($params) {
        return isset($params[self::PARAM_ACTION]);
    }
    
    private static $i = 0;
    
    public function execActionFromArray($params) {
        if (!isset($params[self::PARAM_ACTION]))
            Throw new Exception('Unknown action!');
        if (is_array($params[self::PARAM_ACTION]) && isset($params[self::PARAM_ACTION][0])) { 
            if ((bool)count(array_filter(array_keys($params[self::PARAM_ACTION]), 'is_string'))) 
                 Throw new Exception('Bad params supplied!');
            $count = count($params[self::PARAM_ACTION]);
            $keys = array_keys($params);
            for($i = 0; $i < $count; $i++) {
                $cparams = array();
                foreach ($keys as $key)
                    if (isset($params[$key][$i]))
                        $cparams[$key] = $params[$key][$i];
                $this->execActionFromArray($cparams);
            }
            return;
        }
        $action = $params[self::PARAM_ACTION];
        unset($params[self::PARAM_ACTION]);
        $tmp_response = new icms_collection_Response();
        if (isset($params[self::PARAM_CONTROL])) {
            $control = $params[self::PARAM_CONTROL];            
            unset($params[self::PARAM_CONTROL]);  
            $tmp_response->addControlAction($control, $action, $params);            
        } else {
            if (!isset($params[self::PARAM_MODULE]))
                $module = null;
            else {
                $module = $params[self::PARAM_MODULE];
                unset($params[self::PARAM_MODULE]);
            }
            $tmp_response->addModuleAction($action, $params, $module);
        }
        $this->response->add(self::$i++, $tmp_response->toArray());
    }
    
    
}