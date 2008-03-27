<?php
// $Id: pgsqldatabase.php,v 1.1 2007/03/16 02:40:48 catzwolf Exp $
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
class XoopsPgsqlDatabase extends ZariliaDatabaseFactory {
    /**
     * Database connection
     *
     * @var resource
     */
    var $conn;

    /**
     * connect to the database
     *
     * @param bool $selectdb select the database now?
     * @return bool successful?
     */
    function connect( $selectdb = true ) {
        $conn_string = 'host=' . XOOPS_DB_HOST . ' port=' . XOOPS_DB_PORT . ' dbname=' . XOOPS_DB_NAME;
        if ( XOOPS_DB_USER != "" ) {
            $conn_string .= ' user=' . XOOPS_DB_USER;
        }
        if ( XOOPS_DB_PASS != "" ) {
            $conn_string .= ' password=' . XOOPS_DB_PASS;
        }
        if ( XOOPS_DB_PCONNECT == 1 ) {
            $this->conn = @pg_pconnect( $conn_string );
        } else {
            $this->conn = @pg_connect( $conn_string );
        }

        if ( !$this->conn ) {
            $this->logger->addQuery( $conn_string . " : Connection failed." );
            return false;
        }
        return true;
    }

    /**
     * generate an ID for a new row
     *
     * @param string $sequence name of the sequence from which to get the next ID
     * @return int
     */
    // function genId($table)
    // function genId($table,$column)
    function genId( $sequence ) {
        $sql = sprintf( "SELECT nextval('%s'::text)", $sequence );
        $result = $this->queryF( $sql, 1 );
        if ( !$result ) {
            return false;
        }
        if ( pg_num_rows( $result ) == 0 ) {
            return 1;
        }
        // $num = @pg _fetch_result( $result, 0, 0 );
        return @pg_fetch_result( $result, 0, 0 );
    }

    /**
     * Get a result row as an enumerated array
     *
     * @param resource $result
     * @return array
     */
    function fetchRow( $result, $row = null ) {
        if ( $row == null ) {
            return @pg_fetch_row( $result );
        } else {
            return @pg_fetch_row( $result, $row );
        }
    }

    /**
     * Fetch a result row as an associative array
     *
     * @return array
     */
    function fetchArray( $result, $row = null ) {
        if ( $row == null ) {
            return @pg_fetch_array( $result );
        } else {
            return @pg_fetch_array( $result, $row, PGSQL_ASSOC );
        }
    }

    /**
     * Get the oid assigned to tuple from the previous INSERT operation
     *
     * @return int
     */
    // function getInsertId()
    function getInsertId( $table ) {
        // return mysql_insert_id($this->conn);
        // No function. {pg_last_oid($result):It seems that this is different}
        $sql = "SELECT * FROM " . $table;
        $result = $this->queryF( $sql, 1 );
        if ( !$result ) {
            return false;
        }
        $field_name = pg_field_name( $result, 0 );
        // $sql = "SELECT currval('".$table."_".$field_name."_seq')";
        // $result = $this->queryF($sql);
        $sql = "SELECT " . $field_name . " FROM " . $table . " ORDER BY " . $field_name . " DESC";
        $result = $this->queryF( $sql, 1 );
        if ( !$result ) {
            return false;
        }
        // return $result;
        return @pg_fetch_result( $result, 0, $field_name );
    }

    /**
     * Get number of rows in result
     *
     * @param resource $ query result
     * @return int
     */
    function getRowsNum( $result ) {
        // return @mysql _num_rows($result);
        return @pg_num_rows( $result );
    }

    /**
     * Get number of influenced tuples
     *
     * @return int
     */
    function getAffectedRows( $result ) {
        // return mysql_affected_rows($this->conn);
        return pg_affected_rows( $result );
    }

    /**
     * Close Pgsql connection
     */
    function close() {
        // mysql_close($this->conn);
        pg_close( $this->conn );
    }

    /**
     * will free all memory associated with the result identifier result.
     *
     * @param resource $ query result
     * @return bool TRUE on success or FALSE on failure.
     */
    function freeRecordSet( $result ) {
        // return mysql_free_result($result);
        return pg_free_result( $result );
    }

    /**
     * Returns the text of the error message from previous Pgsql operation
     *
     * @return bool Returns the error text from the last Pgsql function.
     */
    function error( $result ) {
        // return @mysql _error();
        return @pg_result_error( $result );
    }

    /**
     * Returns the numerical value of the query status from previous Pgsql operation
     *
     * @return int Returns the error status from the last Pgsql function.
     */
    function errno( $result ) {
        // return @mysql _errno();
        return @pg_result_status( $result );
    }

    /**
     * Returns escaped string text with single quotes around it to be safely stored in database
     *
     * @param string $str unescaped string text
     * @return string escaped string text with single quotes around
     */
    function quoteString( $str ) {
        $str = "'" . pg_escape_string( stripslashes( $str ) ) . "'";
        // $str = "'".str_replace('\\"', '"', addslashes($str))."'";
        return $str;
    }

    /**
     * perform a query on the database
     *
     * @param string $sql a valid Pgsql query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    function &queryF( $sql, $limit = 0, $start = 0 ) {
        if ( !empty( $limit ) ) {
            if ( empty( $start ) ) {
                $start = 0;
            }
            // $sql = $sql. ' LIMIT '.(int)$start.', '.(int)$limit;
            $sql = $sql . ' LIMIT ' . ( int )$limit . ' OFFSET ' . ( int )$start;
        }
        // $result =& mysql_query($sql, $this->conn);
        $result = @pg_query( $this->conn, $sql );
        if ( $result ) {
            $this->logger->addQuery( $sql );
            return $result;
        } else {
            // $this->logger->addQuery($sql, $this->error(), $this->errno());
            $this->logger->addQuery( $sql, $this->error( $result ), $this->errno( $result ) );
            $ret = false;
            return $ret;
        }
    }

    /**
     * perform a query
     *
     * This method is empty and does nothing! It should therefore only be
     * used if nothing is exactly what you want done! ;-)
     *
     * @param string $sql a valid Pgsql query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @abstract
     */
    function &query( $sql, $limit = 0, $start = 0 ) {
    }

    /**
     * perform queries from SQL dump file in a batch
     *
     * @param string $file file path to an SQL dump file
     * @return bool FALSE if failed reading SQL file or TRUE if the file has been read and queries executed
     */
    function queryFromFile( $file ) {
        if ( false !== ( $fp = fopen( $file, 'r' ) ) ) {
            include_once XOOPS_ROOT_PATH . '/class/database/sqlutility.php';
            $sql_queries = trim( fread( $fp, filesize( $file ) ) );
            SqlUtility::splitMySqlFile( $pieces, $sql_queries );
            foreach ( $pieces as $query ) {
                // [0] contains the prefixed query
                // [4] contains unprefixed table name
                $prefixed_query = SqlUtility::prefixQuery( trim( $query ), $this->prefix() );
                if ( $prefixed_query != false ) {
                    $this->query( $prefixed_query[0] );
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Get field name
     *
     * @param resource $result query result
     * @param int $ numerical field index
     * @return string
     */
    function getFieldName( $result, $offset ) {
        return pg_field_name( $result, $offset );
    }

    /**
     * Get field type
     *
     * @param resource $result query result
     * @param int $offset numerical field index
     * @return string
     */
    function getFieldType( $result, $offset ) {
        return pg_field_type( $result, $offset );
    }

    /**
     * Get number of fields in result
     *
     * @param resource $result query result
     * @return int
     */
    function getFieldsNum( $result ) {
        return pg_num_fields( $result );
    }

    function strLimit( $start, $end ) {
        return( "LIMIT " . intval( $start ) . " OFFSET " . intval( $end ) );
    }
}

/**
 * Safe Connection to a PostgreSQL database.
 *
 * @author Masanori Igarashi <igarashi@iganet.jp>
 * @copyright copyright (c) 2000-2004 XOOPS.org
 * @package kernel
 * @subpackage database
 */
class XoopsPgsqlDatabaseSafe extends XoopsPgsqlDatabase {
    /**
     * perform a query on the database
     *
     * @param string $sql a valid Pgsql query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if successful
     * or TRUE if successful and no result
     */
    function &query( $sql, $limit = 0, $start = 0 ) {
        $ret = &$this->queryF( $sql, $limit, $start );
        return $ret;
    }
}

/**
 * Read-Only connection to a PostgreSQL database.
 *
 * This class allows only SELECT queries to be performed through its
 * {@link query()} method for security reasons.
 *
 * @author Masanori Igarashi <igarashi@iganet.jp>
 * @copyright copyright (c) 2000-2004 XOOPS.org
 * @package kernel
 * @subpackage database
 */
class XoopsPgsqlDatabaseProxy extends XoopsPgsqlDatabase {
    /**
     * perform a query on the database
     *
     * this method allows only SELECT queries for safety.
     *
     * @param string $sql a valid Pgsql query
     * @param int $limit number of records to return
     * @param int $start offset of first record to return
     * @return resource query result or FALSE if unsuccessful
     */
    function &query( $sql, $limit = 0, $start = 0 ) {
        $ret = false;
        $sql = ltrim( $sql );
        if ( strtolower( substr( $sql, 0, 6 ) ) == 'select' ) {
            // if (preg_match("/^SELECT.*/i", $sql)) {
            $ret = &$this->queryF( $sql, $limit, $start );
        }
        $this->logger->addQuery( $sql, 'Database update not allowed during processing of a GET request', 0 );
        return $ret;
    }
}

?>
