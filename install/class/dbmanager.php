<?php
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
require_once ZAR_ROOT_PATH . '/class/logger.php';
require_once ZAR_ROOT_PATH . '/class/database/sqlutility.php';

/**
 * database manager for ZARILIA installer
 *
 * @author Haruki Setoyama <haruki@planewave.org>
 * @version $Id: dbmanager.php,v 1.3 2007/04/12 14:16:37 catzwolf Exp $
 * @access public
 */
class db_manager {
    var $s_tables = array();
    var $f_tables = array();
    var $db;

    function db_manager() {
//		require_once ZAR_ROOT_PATH . '/class/adodb_lite/adodb-errorhandler.inc.php';
		require_once ZAR_ROOT_PATH . '/class/adodb_lite/adodb.inc.php';
		$this->db = ADONewConnection(ZAR_DB_TYPE);
    }

    function isConnectable() {
		$rez =( @$this->db->Connect(ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS, ZAR_DB_NAME) != false ) ? true : false;
		if (!$rez) {
			switch ($this->db->ErrorNo()) {
				case 1049:
					return true;
				break;
			}
		}
		return $rez;
    }

    function dbExists() {
		$rez =( @$this->db->Connect(ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS, ZAR_DB_NAME) != false ) ? true : false;
		if (!$rez) {
			switch ($this->db->ErrorNo()) {
				case 1049:
					return false;
				break;
				default:
					return true;
			}
		}
		return $rez;
    }

    function createDB() {
		$this->db->createdatabase = true;
		return $this->dbExists();
//		$this->db->Connect(ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS, ZAR_DB_NAME);
//        $result = $this->db->Execute( "CREATE DATABASE " . ZAR_DB_NAME );
  //      return ( $result != false ) ? true : $this->db->errno();
    }

    function queryFromFile( $sql_file_path ) {
        $tables = array();
		if ( !file_exists( $sql_file_path ) ) {
			return false;
        }
		$sql_query = trim( fread( fopen( $sql_file_path, 'r' ), filesize( $sql_file_path ) ) );
        //echo $sql_query."<br />";
		SqlUtility::splitMySqlFile( $pieces = null, $sql_query );
   		$this->db->Connect(ZAR_DB_HOST, ZAR_DB_USER, ZAR_DB_PASS, ZAR_DB_NAME);
		$content = '';
        foreach ( $pieces as $piece ) {
            $piece = trim( $piece );
            // [0] contains the prefixed query
            // [4] contains unprefixed table name
            $prefixed_query = SqlUtility::prefixQuery( $piece, $this->db );
            if ( $prefixed_query != false ) {
                $table = $this->db->prefix( $prefixed_query[4] );
                if ( $prefixed_query[1] == 'CREATE TABLE' ) {
					$this->db->Execute( $prefixed_query[0] );
                    if ( $this->db->ErrorNo() < 1) {
                        if ( ! isset( $this->s_tables['create'][$table] ) ) {
                            $content .= _OKIMG;// . sprintf( _INSTALL_L45, "<b>$key</b>" ) . "<br />\n";
                            $this->s_tables['create'][$table] = 1;
                        }
                    } else {
                        if ( ! isset( $this->f_tables['create'][$table] ) ) {
                            $this->f_tables['create'][$table] = 1;
                        }
                    }
                } elseif ( $prefixed_query[1] == 'INSERT INTO' ) {
					$this->db->Execute( $prefixed_query[0] );
                    if ( $this->db->ErrorNo() < 1) {
                        if ( ! isset( $this->s_tables['insert'][$table] ) ) {
                            $this->s_tables['insert'][$table] = 1;
                        } else {
                            $this->s_tables['insert'][$table]++;
                        }
                    } else {
                        if ( ! isset( $this->f_tables['insert'][$table] ) ) {
                            $this->f_tables['insert'][$table] = 1;
                        } else {
                            $this->f_tables['insert'][$table]++;
                        }
                    }
                } elseif ( $prefixed_query[1] == 'ALTER TABLE' ) {
					$this->db->Execute( $prefixed_query[0] );
                    if ( $this->db->ErrorNo() < 1) {
                        if ( ! isset( $this->s_tables['alter'][$table] ) ) {
                            $this->s_tables['alter'][$table] = 1;
                        }
                    } else {
                        if ( ! isset( $this->s_tables['alter'][$table] ) ) {
                            $this->f_tables['alter'][$table] = 1;
                        }
                    }
                } elseif ( $prefixed_query[1] == 'DROP TABLE' ) {
					$this->db->Execute( $prefixed_query[0] );
                    if ( $this->db->ErrorNo() < 1) {
                        if ( ! isset( $this->s_tables['drop'][$table] ) ) {
                            $this->s_tables['drop'][$table] = 1;
                        }
                    } else {
                        if ( ! isset( $this->s_tables['drop'][$table] ) ) {
                            $this->f_tables['drop'][$table] = 1;
                        }
                    }
                }
            }
        }
        return true;
    }

    function report() {
        $reports = array();
        if ( isset( $this->s_tables['create'] ) ) {
            foreach( $this->s_tables['create'] as $key => $val ) {
                $reports[] = _OKIMG . sprintf( _INSTALL_L45, "<b>$key</b>" );
            }
        }
        if ( isset( $this->s_tables['insert'] ) ) {
            foreach( $this->s_tables['insert'] as $key => $val ) {
                $reports[] = _OKIMG . sprintf( _INSTALL_L119, $val, "<b>$key</b>" );
            }
        }
        if ( isset( $this->s_tables['alter'] ) ) {
            foreach( $this->s_tables['alter'] as $key => $val ) {
                $reports[] = _OKIMG . sprintf( _INSTALL_L133, "<b>$key</b>" );
            }
        }
        if ( isset( $this->s_tables['drop'] ) ) {
            foreach( $this->s_tables['drop'] as $key => $val ) {
                $reports[] = _OKIMG . sprintf( _INSTALL_L163, "<b>$key</b>" );
            }
        }
        if ( isset( $this->f_tables['create'] ) ) {
            foreach( $this->f_tables['create'] as $key => $val ) {
                $reports[] = _NGIMG . sprintf( _INSTALL_L118, "<b>$key</b>" );
            }
        }
        if ( isset( $this->f_tables['insert'] ) ) {
            foreach( $this->f_tables['insert'] as $key => $val ) {
                $reports[] = _NGIMG . sprintf( _INSTALL_L120, $val, "<b>$key</b>" );
            }
        }
        if ( isset( $this->f_tables['alter'] ) ) {
            foreach( $this->f_tables['alter'] as $key => $val ) {
                $reports[] = _NGIMG . sprintf( _INSTALL_L134, "<b>$key</b>" );
            }
        }
        if ( isset( $this->f_tables['drop'] ) ) {
            foreach( $this->f_tables['drop'] as $key => $val ) {
                $reports[] = _NGIMG . sprintf( _INSTALL_L164, "<b>$key</b>" );
            }
        }
        return $reports;
    }

    function query( $sql ) {
        $this->db->connect();
        return $this->db->Execute( $sql );
    }

    function prefix( $table ) {
        $this->db->connect();
        return $this->db->prefix( $table );
    }

    function fetchArray( $ret ) {
        $this->db->connect();
        return $this->db->fetchArray( $ret );
    }

    function insert( $table, $query ) {
        $this->db->connect();
        $table = $this->db->prefix( $table );
        $query = 'INSERT INTO ' . $table . ' ' . $query;
		if ( !$this->db->Execute( $query ) ) {
			if ( !isset( $this->f_tables['insert'][$table] ) ) {
                $this->f_tables['insert'][$table] = 1;
            } else {
                $this->f_tables['insert'][$table]++;
            }
            return false;
        } else {
            if ( !isset( $this->s_tables['insert'][$table] ) ) {
                $this->s_tables['insert'][$table] = 1;
            } else {
                $this->s_tables['insert'][$table]++;
            }
            return $this->db->getInsertId();
        }
	}

    function isError() {
        return ( isset( $this->f_tables ) ) ? true : false;
    }

    function deleteTables( $tables ) {
        $deleted = array();
        $this->db->connect();
        foreach ( $tables as $key => $val ) {
            if ( ! $this->db->Execute( "DROP TABLE " . $this->db->prefix( $key ) ) ) {
                $deleted[] = $ct;
            }
        }
        return $deleted;
    }

    function tableExists( $table ) {
        $table = trim( $table );
        $ret = false;
        if ( $table != '' ) {
            $this->db->connect();
            $sql = 'SELECT * FROM ' . $this->db->prefix( $table );
            $ret = ( false != $this->db->Execute( $sql ) ) ? true : false;
        }
        return $ret;
    }

    function UserExists() {
        $ret = 0;
        if ( $table != '' ) {
            $this->db->connect();
            $sql = 'SELECT count(*) FROM ' . $this->db->prefix( 'users' );
            $ret = $this->db->getRowsNum( $this->db->Execute( $sql ) );
        }
        return $ret;
    }
}

?>