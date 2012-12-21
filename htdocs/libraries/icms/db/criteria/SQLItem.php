<?php
/**
 * A single criteria
 *
 * @category	ICMS
 * @package     Database
 * @subpackage  Criteria
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 */
class icms_db_criteria_SQLItem extends icms_db_criteria_Element {
    
        protected $sql, $params;

	/**
	 * Constructor
	 *
	 * @param   string  $sql
	 * @param   string  $value
	 * @param   string  $operator
	 **/
	public function __construct($sql) {
            $this->sql = $sql;
            $this->params = array_slice(func_get_args(), 1);
	}
        
        /**
         * @author MelTraX from PHP.net
         * @link http://lt.php.net/manual/en/function.sprintf.php#86835
         */
        protected function parsePrintfParameters($string) { 
            $valid = '/^(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxXat])/'; 
            $originalString = $string; 

            $result = array(); 
            while(strlen($string)) { 
                if(!$string = preg_replace('/^[^%]*/', '', $string)) 
                    break; 

                if(preg_match($valid, $string, $matches)) { 
                    $result[] = $matches[0]; 
                    $string = substr($string, strlen($matches[0])); 
                } else { 
                    Throw new Exception(sprintf('"%s" has an error near "%s".', $originalString, $string));
                    return NULL; 
                } 
            } 
            return $result; 
        }

	/**
	 * Make a sql condition string
	 *
	 * @return  string
	 **/
	public function render() {
            $sql = $this->sql;

            $params = $this->parsePrintfParameters($sql);            
            $true_params = $this->params;
            $tp_count = count($true_params);
            if (count($params) > 0) {
                $db = method_exists(icms::$db, 'quote')?icms::$db:icms::$xoopsDB;
                foreach ($params as $n => $param) {
                    $l = strpos($param, '$');
                    $i = (!$l)?$n:(intval(substr($param, 1, $l)) - 1);
                    $c = substr($param, -1);
                    if ($c == '%')
                        continue;
                    if (!in_array($c, array('s', 'a', 't'))) {
                        if (!$l) {
                            $nr = '%' . strval($n + 1) . '$' . $c;
                            $sql = preg_replace('/'.$param.'/', $nr, $sql, 1);
                        }
                        continue;
                    }                       
                    $nr = '%' . ($tp_count + 1) . '$s';
                    $sql = preg_replace('/'.$param.'/', $nr, $sql, 1);
                    switch ($c) {
                        case 's':
                            $true_params[$tp_count++] = $db->quote($this->params[$i]);
                        break;
                        case 'a':                            
                            $true_params[$tp_count++] = implode(',', array_map(array($db, 'quote'), $this->params[$i]));
                        break;
                        case 't':
                            $true_params[$tp_count++] = '`' . $this->params[$i] . '`';
                        break;
                    }
                }
            } 

            return vsprintf($sql, $true_params);
	}

	/**
	 * Make a SQL "WHERE" clause
	 *
	 * @return	string
	 */
	public function renderWhere() {
		$cond = $this->render();
		return empty($cond) ? '' : "WHERE $cond";
	}
}

