<?php

class ZariliaDatabaseExtention {

   /**
     * ZariliaDatabaseExtention::setPrefix()
     *
     * @param mixed $value
     * @return
     */
    function setPrefix( $value ) {
         $this->prefix = $value;
    }


    /**
     * ZariliaDatabaseExtention::prefix()
     *
     * @param string $tablename
     * @return
     */
     function prefix( $tablename = null ) {
		 global $zariliaOption, $cpConfig, $zariliaConfig;
		 if ($tablename===null) return ZAR_DB_PREFIX;
		 if (isset($cpConfig['tables'][$tablename])) {
			 switch ($cpConfig['tables'][$tablename]) {
				 case 1:
					 return $zariliaOption['siteprefix']. "_$tablename";
				 default:
		 			 return ZAR_DB_PREFIX. "_$tablename";
			 }
		 } else {
			 return isset($zariliaConfig['lang_code'])?$zariliaOption['siteprefix'].'_'.$zariliaConfig['lang_code']."_$tablename":$zariliaOption['siteprefix']. "_$tablename";
		 }
     }
}

class ZariliaDatabaseFactory {

	function &getDatabaseConnection() {
		static $db =  null;
		if (!$db) {
            require_once ZAR_ROOT_PATH . '/class/adodb_lite/adodb.inc.php';
			$db = ADONewConnection(ZAR_DB_TYPE);
			if ( !($result = $db->Connect(ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS, ZAR_DB_NAME)) ) {
			    trigger_error( "Database error: could not connect", E_USER_ERROR );
			}
		}
		return $db;
	}

}

?>