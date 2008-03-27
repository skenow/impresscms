<?php
// $Id: language.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Xlanguage: eXtensible Language Management For Zarilia               //
// Copyright (c) 2004 Zarilia China Community                      //
// <http://www.zarilia.org.cn/>                             //
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// //
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// //
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// //
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: D.J.(phppp) php_pp@hotmail.com                                    //
// URL: http://www.zarilia.org.cn                                              //
// ------------------------------------------------------------------------- //
include_once( ZAR_ROOT_PATH . "/class/zarilialists.php" );
include_once( ZAR_ROOT_PATH . '/addons/system/admin/multilanguage/vars.php' );
include_once( ZAR_ROOT_PATH . '/addons/system/admin/multilanguage/functions.php' );

class Blanguage extends ZariliaObject {
    var $isBase;

    function Blanguage() {
        $this->db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $this->table = $this->db->prefix( "language_base" );
        $this->initVar( 'lang_id', XOBJ_DTYPE_INT );
        $this->initVar( 'lang_desc', XOBJ_DTYPE_TXTBOX );
        $this->initVar( 'lang_name', XOBJ_DTYPE_TXTBOX );
        $this->initVar( 'lang_code', XOBJ_DTYPE_TXTBOX );
        $this->initVar( 'lang_charset', XOBJ_DTYPE_TXTBOX );
        $this->initVar( 'lang_image', XOBJ_DTYPE_TXTBOX );
        $this->initVar( 'weight', XOBJ_DTYPE_INT );
    }

    function prepareVars() {
        foreach ( $this->vars as $k => $v ) {
            $cleanv = $this->cleanVars[$k];
            switch ( $v['data_type'] ) {
                case XOBJ_DTYPE_TXTBOX:
                case XOBJ_DTYPE_TXTAREA:
                case XOBJ_DTYPE_SOURCE:
                case XOBJ_DTYPE_EMAIL:
                    $cleanv = ( $v['changed'] )?$cleanv:'';
                    if ( !isset( $v['not_gpc'] ) || !$v['not_gpc'] ) {
                        $cleanv = $this->db->qstr( $cleanv );
                    }
                    break;
                case XOBJ_DTYPE_INT:
                    $cleanv = ( $v['changed'] )?intval( $cleanv ):0;
                    break;
                case XOBJ_DTYPE_ARRAY:
                    $cleanv = ( $v['changed'] )?$cleanv:serialize( array() );
                    break;
                case XOBJ_DTYPE_STIME:
                case XOBJ_DTYPE_MTIME:
                case XOBJ_DTYPE_LTIME:
                    $cleanv = ( $v['changed'] )?$cleanv:0;
                    break;

                default:
                    break;
            }
            $this->cleanVars[$k] = &$cleanv;
            unset( $cleanv );
        }
        return true;
    }

    function setBase() {
        $this->isBase = true;
    }

    function isBase() {
        return $this->isBase;
    }

    function getCheckbox( $id = null ) {
        $ret = '<input type="checkbox" value="1" name="checkbox[' . $this->getVar( $id ) . ']" />';
        return $ret;
    }
}

class Zarilialanguage extends Blanguage {
    function Zarilialanguage() {
        $this->Blanguage();
        $this->table = $this->db->prefix( "language_ext" );
        $this->initVar( 'lang_base', XOBJ_DTYPE_TXTBOX );
        $this->isBase = false;
    }

    function getCheckbox( $id = null ) {
        $ret = '<input type="checkbox" value="1" name="checkbox[' . $this->getVar( $id ) . ']" />';
        return $ret;
    }
}

class ZariliaLanguageHandler extends ZariliaObjectHandler {
    var $cached_config;

    function loadConfig() {
        $this->cached_config = &xlanguage_loadConfig( $this );
    }

    /**
     * ZariliaLanguageHandler::get()
     *
     * @param  $id
     * @param boolean $isBase
     * @return
     */
    function &get( $id, $isBase = true ) {
        $id = intval( $id );
        if ( !$id ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'No Id found' );
            return false;
        }
        $prefix = ( $isBase == true ) ? "language_base" : "language_ext";
        $sql = 'SELECT * FROM ' . $this->db->prefix( $prefix ) . ' WHERE lang_id=' . $id;
		$result = $this->db->Execute( $sql );
        $array = $result->FetchRow();
        if ( !is_array( $array ) || count( $array ) == 0 ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'ERROR: Selected item was not found in the database' );
            return false;
        }
        $lang = &$this->create( false, @$isBase );
        $lang->assignVars( $array );
        if ( $isBase ) {
            $lang->setBase();
        }
        return $lang;
    }

    /**
     * ZariliaLanguageHandler::get()
     *
     * @param  $id
     * @param boolean $isBase
     * @return
     */
    function &getFirst() {
        $sql = 'SELECT * FROM ' . $this->db->prefix( 'language_base' ) . ' LIMIT 0,1';
		$result = $this->db->Execute( $sql );
        $array = $result->FetchRow();
        if ( !is_array( $array ) || count( $array ) == 0 ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'ERROR: Selected item was not found in the database' );
            return false;
        }
        $lang = &$this->create( false, @$isBase );
        $lang->assignVars( $array );
        return $lang;
    }

    function &getByName( $name ) {
        if ( empty( $name ) || preg_match( "/[^a-zA-Z0-9\_\-]/", $name ) ) {
			$false = false;
            return $false;
        }
        $isBase = false;
        $sql = 'SELECT * FROM ' . $this->db->prefix( 'language_base' ) . ' WHERE lang_name=\'' . $name . '\'';
        $result = $this->db->Execute( $sql );
        $array = $result->FetchRow();
        if ( !is_array( $array ) || count( $array ) == 0 ) {
            $sql = 'SELECT * FROM ' . $this->db->prefix( 'language_ext' ) . ' WHERE lang_name=\'' . $name . '\'';
            $result = $this->db->Execute( $sql );
            $array = &$result->FetchRow();
            if ( !is_array( $array ) || count( $array ) == 0 ) {
                $false = false;
                return $false;
            }
        } else {
            $isBase = true;
        }
        if ( empty( $array ) ) {
            $false = false;
            return $false;
        }
        $lang = &$this->create( false, @$isBase );
        $lang->assignVars( $array );
        if ( !isset( $array['lang_base'] ) ) {
            $lang->setBase();
        }
        return $lang;
    }

    function &getAll( $isBase = true ) {
        $prefix = ( $isBase ) ? 'language_base' : 'language_ext';
        $ret = array();
        $sql = 'SELECT * FROM ' . $this->db->prefix( $prefix );
        $result = $this->db->Execute( $sql );
        while ( $myrow = $result->FetchRow() ) {
            $lang = &$this->create( false, $isBase );
            $lang->assignVars( $myrow );
            $ret[$myrow['lang_name']] = $lang;
            unset( $lang );
        }
        return $ret;
    }

    function &getAllList() {
        $baseArray = &$this->getAll();
        $extArray = &$this->getAll( false );
        $ret = array();
        $count = 0;
        if ( is_array( $baseArray ) && count( $baseArray ) > 0 ) {
            foreach( $baseArray as $base ) {
                $count++;
                $ret[$base->getVar( 'lang_name' )]['base'] = $base;
                unset( $base );
            }
        }
        if ( is_array( $extArray ) && count( $extArray ) > 0 ) {
            foreach( $extArray as $ext ) {
                $count++;
                $ret[$ext->getVar( 'lang_base' )]['ext'][] = $ext;
                unset( $ext );
            }
        }
        $ret['count'] = $count;
        return $ret;
    }

    function &create( $isNew = true, $isBase = true ) {
        if ( $isBase == true ) {
            $lang = new Blanguage();
            $lang->setBase();
        } else {
            $lang = new Zarilialanguage();
        }
        if ( $isNew ) {
            $lang->setNew();
        }
        return $lang;
    }

    function insert( &$lang ) {
        if ( !$lang->isDirty() ) {
            return true;
        }
        if ( !$lang->cleanVars() ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Vars Not clean' );
            return false;
        }

        $lang->prepareVars();
        foreach ( $lang->cleanVars as $k => $v ) {
            ${$k} = $v;
        }

        if ( $lang->isNew() ) {
            if ( $lang->isBase() ) {
                $var_array = array( "lang_id", "lang_name", "lang_desc", "lang_image", "weight", "lang_code", "lang_charset" );
            } else {
                $var_array = array( "lang_id", "lang_name", "lang_desc", "lang_image", "weight", "lang_code", "lang_charset", "lang_base" );
            }
            $lang_id = $this->db->genId( $lang->table . "_lang_id_seq" );
            foreach( $var_array as $var ) {
                $val_array[] = ${$var};
            }
            $sql = "INSERT INTO " . $lang->table . " (" . implode( ",", $var_array ) . ") VALUES (" . implode( ",", $val_array ) . ")";
            if ( !$result = $this->db->Execute( $sql ) ) {
   				$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
                return false;
            }
            if ( $lang_id == 0 ) $lang_id = $this->db->getInsertId();
            $lang->setVar( 'lang_id', $lang_id );
        } else {
            if ( $lang->isBase() ) {
                $var_array = array( "lang_name", "lang_desc", "lang_image", "weight", "lang_code", "lang_charset" );
            } else {
                $var_array = array( "lang_name", "lang_desc", "lang_image", "weight", "lang_code", "lang_charset", "lang_base" );
            }
            $set_array = array();
            foreach( $var_array as $var ) {
                $set_array[] = "$var = " . ${$var};
            }
            $set_string = implode( ',', $set_array );
            $sql = "UPDATE " . $lang->table . " SET " . $set_string . " WHERE lang_id = " . $lang->getVar( 'lang_id' );
            if ( !$result = $this->db->Execute( $sql ) ) {
   				$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
                return false;
            }
        }
        xlanguage_createConfig( $this );
        return $lang->getVar( 'lang_id' );
    }

    function delete( &$lang ) {
        $sql = "DELETE FROM " . $lang->table . " WHERE lang_id= " . $lang->getVar( 'lang_id' );
        if ( !$result = $this->db->Execute( $sql ) ) {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        xlanguage_createConfig( $this );
        return true;
    }

    function &getZariliaLangList() {
        return ZariliaLists::getLangList();
    }
}

?>