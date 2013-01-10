<?php

/**
 * Handler for alternatives
 */
class icms_alternative_Handler extends icms_ipf_Handler {    
    
    protected static $alts = array();
    protected static $changed = array();

    public function __construct(&$db) {
        parent::__construct($db, 'alternative', 'alt_id', 'name', 'default', 'icms');
    }

    public function find($type, $name, $area ) {
        if (!isset(self::$alts[$type])) {
            $file_cache = new icms_cache_File('system', 'alternative', $type);
            self::$alts[$type] = $file_cache->read(array(), false);
        }
        if (!isset(self::$alts[$type][$name])) {
            $this->updateCache($type, $name);
            if (!isset(self::$alts[$type][$name]))
                return null;
        }
        $area = md5(http_build_query($_GET));
     /*   foreach (icms::$user->getGroups() as $groupid) 
            if (isset(self::$alts[$type][$name][$area][intval($groupid)]))
                 return self::$alts[$type][$name][$area][intval($groupid)];*/
    }
    
    protected function getDataForCaching($type, $name) {        
        $ndata = $this->getDataForNode($type, $name);
        
        
        
        if ((self::$alts = $file_cache->read(null, false)) === null)
            self::$alts = array();
        
        return self::$alts;
    }
    
    public function updateCache($type, $name) {
        if (!isset(self::$alts[$type])) {
            $file_cache = new icms_cache_File('system', 'alternative', $type);
            self::$alts[$type] = $file_cache->read(array(), false);
        }
        self::$alts[$type][$name] = $this->getRealDataForNode($type, $name);
        self::$changed[$type] = true;
    }
    
    protected function getRealDataForNode($type, $name) {
        $criteria = new icms_db_criteria_SQLItem('name = %s AND type = %d', $name, $type);
        $criteria->setLimit(1);
        $item = $this->getObjects($criteria);
        if (isset($item[0]))
            $item = $item[0];
        else {
            $packages_handler = icms::handler('icms_package');
            $criteria = new icms_db_criteria_SQLItem('alt_name = %s AND type = %d', $name, $type);
            $criteria->setLimit(1);
            $package = $packages_handler->getObjects($criteria);
            if (isset($package[0])) {
                $package = $package[0]->getVar('name');
            } else {
                $packages_handler->update($type, self::UPDATE_LOCAL);
                $package = $packages_handler->getObjects($criteria);
                if (isset($package[0]))
                    $package = $package[0]->getVar('name');
                else
                    $package = null;
            }
            if ($package !== null) {
                $item = $this->create();
                $item->setVar('name', $name);
                $item->setVar('type', $type);
                $item->setVar('default', $package);
                $item->store(true);
            } else {
                $item = null;
            }
        }
        if ($item === null)
            return null;
        $data = array(
            'default' => $item->getVar('default')
        );
        foreach ($item->getRules(false) as $rule)
            $data[md5($rule->getVar('when_area'))][(int)$rule->getVar('when_group')] = $rule->getVar('then');
        return $data;
    }
    
    protected function afterSave(icms_alternative_Object &$obj) {
        $this->updateCache($obj->getVar('type'), $obj->getVar('name'));  
    }

}