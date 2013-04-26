<?php

class action_system_db_RunSQLFile
    extends icms_action_base_Module {
    
    public function __construct($params = array()) {
        $this->initVar('reserved_tables', self::DTYPE_LIST);
        $this->initVar('file', self::DTYPE_STRING);
        
        parent::__construct($params);
    }
    
    
    public function exec(\icms_action_Response &$response) {
        
        if (!file_exists($this->file))
            return $response->error(sprintf(_MD_AM_SQL_NOT_FOUND, '<strong>' . $this->file . '</strong>'));
        
	$response->msg(sprintf(_MD_AM_SQL_FOUND, '<strong>' . $this->file . '</strong>'));
        
        $sql_query = trim(file_get_contents($this->file));
        icms_db_legacy_mysql_Utility::splitSqlFile($pieces, $sql_query);
        
        $this->created_tables = array();
        foreach ($pieces as $piece) {
            // [0] contains the prefixed query
            // [4] contains unprefixed table name
            $prefixed_query = icms_db_legacy_mysql_Utility::prefixQuery($piece, $db->prefix());
            if (!$prefixed_query) {
                $response->error("<strong>$piece</strong>" . _MD_SQL_NOT_VALID);
                break;
            }
            
            // check if the table name is reserved
            if (!in_array($prefixed_query[4], $this->reserved_tables)) {
                // not reserved, so try to create one
                if (!$db->query($prefixed_query[0])) {
                    $response->error($db->error());
                    break;
                } else {
                    if (!in_array($prefixed_query[4], $this->created_tables)) {
                        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_TABLE_CREATED,
                        '<strong>' . $db->prefix($prefixed_query[4]) . '</strong>'));
                        $this->created_tables[] = $prefixed_query[4];
                    } else
                        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_DATA_INSERT_SUCCESS, '<strong>' . $db->prefix($prefixed_query[4]) . '</strong>'));
                }
            } else {
                // the table name is reserved, so halt the installation
                $response->error(sprintf(_MD_AM_RESERVED_TABLE, '<strong>' . $prefixed_query[4] . '</strong>'));
            }
        }                
    }
    
}