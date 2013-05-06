<?php
/**
 * Special action executed when doing request. Returns data for application but not for user
 *
 * @author mekdrop
 */
abstract class icms_action_base_Module
    extends icms_properties_Handler {
    
    const SR_NOTHING = 0;
    const SR_LOGIN = 1;
    const SR_NOLOGIN = 2;
    
    protected $special_requirements = self::SR_NOTHING;
    
    /**
     * Constructor
     *
     * @param array $params Array with keys used to set current action properties
     */
    public function __construct($params = array()) {
        foreach ($params as $key => $value)
            if (isset($this->_vars[$key])) {
                $this->_values[$key] = $this->cleanVar($key, $this->_vars[$key][parent::VARCFG_TYPE], $value);
                $this->_vars[$key][parent::VARCFG_CHANGED] = true;
            }
    }
    
    /**
     * This is called when action is executed
     */
    abstract function exec(icms_action_Response &$response);
    
    /**
     * Check if this action has any special requirement
     * 
     * @param mixed $requirement
     * 
     * @return bool
     */
    public function checkSR($requirement) {
        if (!is_int($requirement)) 
            $requirement = constant('icms_action_base_Module::SR_' . strtoupper($requirement));
        return $this->special_requirements && $requirement == $requirement;
    }       
    
}