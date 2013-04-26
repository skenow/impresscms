<?php
/**
 * Cached persistable Object Handlder
 * @category	ICMS
 * @package		Ipf
 * @since		1.1
 * @author              Raimondas Rimkevicius <i.know@mekdrop.name>
 * @todo		Properly name the vars using the naming conventions
 */
abstract class icms_cache_Handler extends icms_ipf_Handler {   
    
        protected $cachePath = '';
    
        public function __construct($db, $itemname, $keyname, $idenfierName, $summaryName, $modulename, $table = null) {
            $this->cachePath = ICMS_CACHE_PATH . '/objects/' . get_class($this) . '/'; 
            parent::__construct($db, $itemname, $keyname, $idenfierName, $summaryName, $modulename, $table);
        }

	/**
	 * retrieve a {@link icms_ipf_Object}
	 *
	 * @param mixed $id ID of the object - or array of ids for joint keys. Joint keys MUST be given in the same order as in the constructor
	 * @param bool $as_object whether to return an object or an array
	 * @return mixed reference to the {@link icms_ipf_Object}, FALSE if failed
	 */
	public function &get($id, $as_object = true, $debug = false, $criteria = false) {
		if (!$criteria) {
			$criteria = new icms_db_criteria_Compo();
		}
		if (is_array($this->keyName)) {
			for ($i = 0; $i < count($this->keyName); $i++) {
				/**
				 * In some situations, the $id is not an INTEGER. icms_ipf_ObjectTag is an example.
				 * Is the fact that we removed the intval() represents a security risk ?
				 */
				//$criteria->add(new icms_db_criteria_Item($this->keyName[$i], ($id[$i]), '=', $this->_itemname));
				$criteria->add(new icms_db_criteria_Item($this->keyName[$i], $id[$i], '=', $this->_itemname));
			}
		} else {
			//$criteria = new icms_db_criteria_Item($this->keyName, intval($id), '=', $this->_itemname);
			/**
			 * In some situations, the $id is not an INTEGER. icms_ipf_ObjectTag is an example.
			 * Is the fact that we removed the intval() represents a security risk ?
			 */
			$criteria->add(new icms_db_criteria_Item($this->keyName, $id, '=', $this->_itemname));
		}
		$criteria->setLimit(1);
                $sql = '';
                $filename = $this->getCacheFileName($criteria, $sql);
            
                if (file_exists($filename)) {
                    $ret = include($filename);
                } else {
                    $ret = parent::get($id, false, $debug, $criteria);
                    $this->cacheData($filename, $ret);
                }
                
                if ($as_object) {
                    $obj = $this->create(false);
                    $obj->assignVars($ret);
                    $ret = $obj;
                }                    

		return $ret;
	}         
        
        protected function getCacheFileName(icms_db_criteria_Element $criteria = null, $sql = false) {
                if (!$sql)
                    $sql = '';
                $where_sql = (!$criteria)?'_default_':$criteria->render();
                $where_sql = (!$sql)?$where_sql:$sql.' WHERE'.$where_sql;
                $sql_part = (strlen($where_sql) > 40)?(substr($where_sql, 0, 37).'...'):$where_sql;                
                $filename = $this->cachePath . $sql_part . sha1($where_sql) . '.php';
                return $filename;
        }
        
        protected function cacheData($filename, &$data) {
            if (!file_exists($this->cachePath))
               mkdir($this->cachePath, 0777, true);
            file_put_contents($filename, '<?php return ' . var_export($data, true) . ';');
        }
        
        protected function clearCache() {
            $dir = opendir($this->cachePath);
            while(false !== ($file = readdir($dir))) {
                if ($file == '.')
                    continue;
                if ($file == '..')
                    continue;
                $file = $this->cachePath . '/' . $file;
                unlink($file);
            }
            closedir($dir);
        }

	/**
	 * retrieve objects from the database
	 *
	 * @param object $criteria {@link icms_db_criteria_Element} conditions to be met
	 * @param bool $id_as_key use the ID as key for the array?
	 * @param bool $as_object return an array of objects?
	 *
	 * @return array
	 */
	public function getObjects($criteria = null, $id_as_key = false, $as_object = true, $sql = false, $debug = false) {            
            
                $filename = $this->getCacheFileName($criteria, $sql);
            
                if (file_exists($filename)) {
                    $ret = include($filename);
                } else {
                    $ret = parent::getObjects($criteria, false, null, $sql, $debug);
                    $this->cacheData($filename, $ret);
                }                
                
                if ($id_as_key) {
                    $ret2 = array();
                    foreach ($ret as $k => $v)
                        $ret2[$v[$this->keyName]] = $v;
                    $ret = $ret2;
                    unset($ret2);
                }
                
                if ($as_object) {
                    foreach ($ret as $k => $v) {
                        $obj = & new $this->className($this, $v);
                        $ret[$k] = &$obj;
                    }
                }                
                
		return $ret;
	}

	/**
	 * query the database with the constructed $criteria object
	 *
	 * @param string $sql The SQL Query
	 * @param object $criteria {@link icms_db_criteria_Element} conditions to be met
	 * @param bool $force Force the query?
	 * @param bool $debug Turn Debug on?
	 *
	 * @return array
	 */
	public function query($sql, $criteria, $force = false, $debug = false) {
            
                if (in_array(substr(strtolower($sql), 0, 6), array('replac', 'delete', 'insert', 'create', 'update'))) {
                    $ret = parent::query($sql, $criteria, $force, $debug);
                    $this->clearCache();
                } else {
                    $filename = $this->getCacheFileName($criteria, $sql);
            
                    if (file_exists($filename)) {
                        $ret = include($filename);
                    } else {
                        $ret = parent::query($sql, $criteria, $force, $debug);
                        $this->cacheData($filename, $ret);
                    }            
                }
                return $ret;
	}
        
	/**
	 * Retrieve a list of objects as arrays - DON'T USE WITH JOINT KEYS
	 *
	 * @param object $criteria {@link icms_db_criteria_Element} conditions to be met
	 * @param int   $limit      Max number of objects to fetch
	 * @param int   $start      Which record to start at
	 *
	 * @return array
	 */
	public function getList($criteria = null, $limit = 0, $start = 0, $debug = false) {
		if ($criteria == null) {
                    $criteria = new icms_db_criteria_Compo();
		}
                if ($limit > 0)
                    $criteria->setLimit($limit);
                if ($start > 0)
                    $criteria->setStart($start);

		if ($criteria->getSort() == '') {
			$criteria->setSort($this->getIdentifierName());
		}

		$sql = 'SELECT ' . (is_array($this->keyName) ? implode(', ', $this->keyName) : $this->keyName) ;
		if (!empty($this->identifierName)) {
			$sql .= ', ' . $this->getIdentifierName();
		}
		$sql .= ' FROM '.$this->table . " AS " . $this->_itemname;
                
                $filename = $this->getCacheFileName($criteria, $sql);
            
                if (file_exists($filename)) {
                    $ret = include($filename);
                } else {
                    $ret = parent::getList($criteria, $limit, $start, $debug);
                    $this->cacheData($filename, $ret);
                }
		
		return $ret;
	}

	/**
	 * count objects matching a condition
	 *
	 * @param object $criteria {@link icms_db_criteria_Element} to match
	 * @return int count of objects
	 */
	public function getCount($criteria = null) {
            
                $field = "";
		$groupby = false;
		if (isset($criteria) && is_subclass_of($criteria, 'icms_db_criteria_Element')) {
			if ($criteria->groupby != "") {
				$groupby = true;
				$field = $criteria->groupby . ", "; //Not entirely secure unless you KNOW that no criteria's groupby clause is going to be mis-used
			}
		}
		/**
		 * if we have a generalSQL, lets used this one.
		 * This needs to be improved...
		 */
		if ($this->generalSQL) {
			$sql = $this->generalSQL;
			$sql = str_replace('SELECT *', 'SELECT COUNT(*)', $sql);
		} else {
			$sql = 'SELECT ' . $field . 'COUNT(*) FROM ' . $this->table . ' AS ' . $this->_itemname;
		}
            
                $filename = $this->getCacheFileName($criteria, $sql);
            
                if (file_exists($filename)) {
                    $ret = include($filename);
                } else {
                    $ret = parent::getList($criteria, $limit, $start, $debug);
                    $this->cacheData($filename, $ret);
                }
            
		
                return $ret;
	}

	/**
	 * delete an object from the database
	 *
	 * @param object $obj reference to the object to delete
	 * @param bool $force
	 * @return bool FALSE if failed.
	 */
	public function delete(&$obj, $force = false) {
                $this->clearCache();
                return parent::delete($obj, $force);		
	}

	/**
	 * insert a new object in the database
	 *
	 * @param object $obj reference to the object
	 * @param bool $force whether to force the query execution despite security settings
	 * @param bool $checkObject check if the object is dirty and clean the attributes
	 * @return bool FALSE if failed, TRUE if already present and unchanged or successful
	 */
	public function insert(&$obj, $force = false, $checkObject = true, $debug = false) {
                $this->clearCache();
                return parent::insert($obj, $force, $checkObject, $debug);
	}

	/**
	 * Change a value for objects with a certain criteria
	 *
	 * @param   string  $fieldname  Name of the field
	 * @param   string  $fieldvalue Value to write
	 * @param   object  $criteria   {@link icms_db_criteria_Element}
	 *
	 * @return  bool
	 **/
	public function updateAll($fieldname, $fieldvalue, $criteria = null, $force = false) {
		$this->clearCache();
		return parent::updateAll($fieldname, $fieldvalue, $criteria, $force);
	}

	/**
	 * delete all objects meeting the conditions
	 *
	 * @param object $criteria {@link icms_db_criteria_Element} with conditions to meet
	 * @return bool
	 */

	public function deleteAll($criteria = NULL) {
                $this->clearCache();
                return parent::deleteAll($criteria);		
	}

	/**
	 *
	 * @param $object
	 */
	public function updateCounter($object) {
		 $this->clearCache();
                 return parent::updateCounter($object);
	}

}

