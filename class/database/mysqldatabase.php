<?php
// $Id: mysqldatabase.php,v 1.5 2007/04/24 09:33:20 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
/**
 *
 * @package kernel
 * @subpackage database
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
include_once ZAR_ROOT_PATH . "/class/database/database.php";
/**
 * ZariliaMySQLDatabase
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2005
 * @version $Id: mysqldatabase.php,v 1.5 2007/04/24 09:33:20 catzwolf Exp $
 * @access public
 */
class ZariliaMySQLDatabase extends ZariliaDatabase {
    var $dbcon;
    // var $_path = '';
    /**
     * ZariliaMySQLDatabase::connect()
     *
     * @param boolean $selectdb
     * @return
     */
    function connect( $selectdb = true, $show_error = false ) {
        if ( !extension_loaded( 'mysql' ) ) {
            $this->displayError( 'mysql' );
            exit();
        }

        if ( ZAR_DB_PCONNECT ) {
            $this->dbcon = @mysql_pconnect( ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS );
        } else {
            $this->dbcon = @mysql_connect( ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS );
        }
		/**
         */
        if ( !$this->dbcon ) {
            @mysql_close( $this->dbcon );
            trigger_error( mysql_error() . mysql_errno(), E_USER_ERROR );
            $this->logger->addQuery( 'SQL Error', mysql_error(), mysql_errno() );
            return false;
        }

        /**
         */
        if ( $selectdb == true ) {
            if ( !mysql_select_db( ZAR_DB_NAME ) ) {
                @mysql_close( $this->dbcon );
                $this->logger->addQuery( 'SQL Error', mysql_error(), mysql_errno() );
                return false;
            }
        }
        return true;
    }

    /**
     * ZariliaMySQLDatabase::genId()
     *
     * @param  $sequence
     * @return int
     */
    function genId( $sequence ) {
        return 0; // will use auto_increment
    }

    /**
     * ZariliaMySQLDatabase::getInsertId()
     *
     * @return
     */
    function getInsertId( $value = null ) {
        return @mysql_insert_id( $this->dbcon );
    }

    /**
     * ZariliaMySQLDatabase::fetchRow()
     *
     * @param  $result
     * @return
     */
    function fetchRow( $result ) {
        return @mysql_fetch_row( $result );
    }

    /**
     * ZariliaMySQLDatabase::fetchArray()
     *
     * @param  $result
     * @return
     */
    function fetchArray( $result ) {
        return @mysql_fetch_assoc( $result );
    }

    /**
     * ZariliaMySQLDatabase::fetchBoth()
     *
     * @param  $result
     * @return
     */
    function fetchBoth( $result ) {
        return @mysql_fetch_array( $result, MYSQL_BOTH );
    }

    /**
     * ZariliaMySQLDatabase::fetchObject()
     *
     * @param  $result
     * @return
     */
    function fetchObject( $result ) {
        return @mysql_fetch_object( $result );
    }

    /**
     * ZariliaMySQLDatabase::getRowsNum()
     *
     * @param  $result
     * @return
     */
    function getRowsNum( $result ) {
        return @mysql_num_rows( $result );
    }

    /**
     * ZariliaMySQLDatabase::getAffectedRows()
     *
     * @return
     */
    function getAffectedRows() {
        return @mysql_affected_rows( $this->dbcon );
    }

    /**
     * ZariliaMySQLDatabase::close()
     *
     * @return
     */
    function close() {
        @mysql_close( $this->dbcon );
    }

    /**
     * will free all memory associated with the result identifier result.
     *
     * @param resource $ query result
     * @return bool TRUE on success or FALSE on failure.
     */
    function freeRecordSet( $result ) {
        return @mysql_free_result( $result );
    }

    /**
     * Returns the text of the error message from previous MySQL operation
     *
     * @return bool Returns the error text from the last MySQL function, or '' (the empty string) if no error occurred.
     */
    function error() {
        return htmlspecialchars( @mysql_error() );
    }

    /**
     * Returns the numerical value of the error message from previous MySQL operation
     *
     * @return int Returns the error number from the last MySQL function, or 0 (zero) if no error occurred.
     */
    function errno() {
        return @mysql_errno();
    }

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     *
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    function quoteString( $str ) {
        // Stripslashes
        if ( is_array( $str ) ) {
            foreach( $str as $_element ) {
                $_newvar[] = $this->quoteString( $_element );
            }
            return $_newvar;
        }

        if ( get_magic_quotes_gpc() ) {
            $str = stripslashes( $str );
        }
        // Quote if not integer
        if ( !is_numeric( $str ) ) {
            if ( function_exists( 'mysql_real_escape_string' ) ) {
                $str = mysql_real_escape_string( $str );
            } elseif ( function_exists( 'mysql_escape_string' ) ) {
                $str = mysql_escape_string( $str );
            } else {
                $str = addslashes( $str );
            }
            $str = "'" . $str . "'";
        }
        return $str;
    }

    /**
     * perform a query on the database
     *
     * @param string $sql a valid MySQL query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    function queryF( $sql, $limit = 0, $start = 0 ) {
        return $this->query( $sql, $limit, $start );
    }

    function &query( $sql, $limit = 0, $start = 0 ) {
        global $_GLOBALS;

        if ( strtolower( substr( trim( $sql ), 0, 6 ) ) == "select" ) {
            if ( !empty( $limit ) ) {
                if ( empty( $start ) ) {
                    $start = 0;
                }
                $sql = $sql . ' LIMIT ' . ( int )$start . ', ' . ( int )$limit;
            }
        }
        // if ( $_GLOBALS['db_proxy'] == 0 )
        // {
        // echo $sql."<br>";
        $result = @mysql_query( $sql, $this->dbcon );
        // }
        // else
        // {
        /**
         * $sql = ltrim( $sql );
         * if ( strtolower( substr( $sql, 0, 6 ) ) == 'select' )
         * {
         * $result = &mysql_query( $sql, $this -> dbcon );
         * }
         * else
         * {
         * echo "Cannot update via this method";
         * $this -> logger -> addQuery( $sql, 'Database update not allowed during processing of a GET request', 0 );
         * return false;
         * }
         * }
         * //
         */
        if ( $result ) {
            $this->logger->addQuery( $sql );
            return $result;
        } else {
            $this->logger->addQuery( $sql, $this->error(), $this->errno() );
            $result = false;
            return $result;
        }
    }

    /**
     * Optimize table
     */
    function optimize( $table ) {
        $this->query( 'ANALYZE TABLE $table;
            ' );
        $this->query( 'OPTIMIZE TABLE $table;
            ' );
    }

    /**
     * Get field name
     *
     * @param resource $result query result
     * @param int $ numerical field index
     * @return string
     */
    function getFieldName( $result, $offset ) {
        return mysql_field_name( $result, $offset );
    }

    /**
     * Get field type
     *
     * @param resource $result query result
     * @param int $offset numerical field index
     * @return string
     */
    function getFieldType( $result, $offset ) {
        return mysql_field_type( $result, $offset );
    }

    /**
     * Get number of fields in result
     *
     * @param resource $result query result
     * @return int
     */
    function getFieldsNum( $result ) {
        return mysql_num_fields( $result );
    }

    /**
     * Note from here MYSQL Information for development purposes only
     */
    function explain( $sql = '' ) {
        $temp = $sql;
        $sql = "EXPLAIN $sql";

        if ( !$cur = $this->query( $sql ) ) {
            return null;
        }
        $first = true;

        $buf = "<table cellspacing=\"1\" cellpadding=\"2\" border=\"0\" bgcolor=\"#000000\" align=\"center\">";
        while ( $row = mysql_fetch_assoc( $cur ) ) {
            if ( $first ) {
                $buf .= "<tr>";
                foreach ( $row as $k => $v ) {
                    $buf .= "<th bgcolor=\"#ffffff\">$k</th>";
                }
                $buf .= "</tr>";
                $first = false;
            }
            $buf .= "<tr>";
            foreach ( $row as $k => $v ) {
                $buf .= "<td bgcolor=\"#ffffff\">$v</td>";
            }
            $buf .= "</tr>";
        }
        $buf .= "</table><br />&nbsp;";
        mysql_free_result( $cur );

        $sql = $temp;
        return "<div style=\"background-color:#FFFFCC\" align=\"left\">$buf</div>";
    }

    function getStatus( $htmlOutput = false ) {
        $res = mysql_query( "show status", $this->dbcon );
        while ( list( $key, $value ) = mysql_fetch_array( $res ) )
        $sql[$key] = $value;
        if ( $htmlOutput == true ) {
            print_r_html( $sql );
        } else {
            return $sql;
        }
    }

    function getLastInfo( $linkid = null ) {
        $strInfo = $linkid ? mysql_info( $linkid ) : mysql_info();

        $return = array();
        ereg( "Records: ([0-9]*)", $strInfo, $records );
        ereg( "Duplicates: ([0-9]*)", $strInfo, $dupes );
        ereg( "Warnings: ([0-9]*)", $strInfo, $warnings );
        ereg( "Deleted: ([0-9]*)", $strInfo, $deleted );
        ereg( "Skipped: ([0-9]*)", $strInfo, $skipped );
        ereg( "Rows matched: ([0-9]*)", $strInfo, $rows_matched );
        ereg( "Changed: ([0-9]*)", $strInfo, $changed );

        $return['records'] = $records[1];
        $return['duplicates'] = $dupes[1];
        $return['warnings'] = $warnings[1];
        $return['deleted'] = $deleted[1];
        $return['skipped'] = $skipped[1];
        $return['rows_matched'] = $rows_matched[1];
        $return['changed'] = $changed[1];
        return $return;
    }

    function getServerInfo( $info = false ) {
        if ( $info == true ) {
            return printf( "MySQL server version: %s\n", mysql_get_server_info() );
        } else {
            return mysql_get_server_info();
        }
    }

    function getProtoInfo( $info = false ) {
        if ( $info == true ) {
            return printf( "MySQL protocol version: %s\n", mysql_get_proto_info() );
        } else {
            return mysql_get_proto_info();
        }
    }

    function getClientInfo( $info = false ) {
        if ( $info == true ) {
            return printf( "MySQL client info: %s\n", mysql_get_client_info() );
        } else {
            return mysql_get_client_info();
        }
    }

    function getHostInfo( $info = false ) {
        if ( $info == true ) {
            return printf( "MySQL host info: %s\n", mysql_get_host_info() );
        } else {
            return mysql_get_host_info();
        }
    }

    function getListFields( $tablename = '' ) {
        $ret = '';
        $sql = "SHOW columns FROM " . $this->prefix( $tablename );
        $result = $this->query( $sql );
        if ( !$result ) {
            // echo "Error: ".$this -> error();
            return false;
        }
        if ( mysql_num_rows( $result ) > 0 ) {
            while ( $myrow = mysql_fetch_object( $result ) ) {
                $ret[] = $myrow;
            }
        }
        return $ret;
    }

    function table_exists( $tablename ) {
        $result = $this->query( "SELECT 1 FROM " . $this->prefix( $tablename ) . " LIMIT 0" );
        return ( $result ) ? 1 : 0;
    }

    /**
     * ZariliaMySQLDatabase::mysqlPing()
     *
     * @return
     */
    function mysqlPing() {
        if ( !mysql_ping( $this->dbcon ) ) {
            exit( 'MySql connection lost' );
        }
        return true;
    }
}

?>