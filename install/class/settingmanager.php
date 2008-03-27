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
/**
 * setting manager for ZARILIA installer
 *
 * @author Haruki Setoyama <haruki@planewave.org>
 * @version $Id: settingmanager.php,v 1.3 2007/04/12 14:16:37 catzwolf Exp $
 * @access public
 */
class setting_manager
{
    var $database;
    var $dbhost;
    var $dbuname;
    var $dbpass;
    var $dbname;
    var $prefix;
    var $db_pconnect;
    var $root_path;
    var $zarilia_url;
    var $default_zarilia_url;
    var $default_root_path;

    function setting_manager( $post = false )
    {
        if ( $post )
        {
            $this->readPost();
        }
        else
        {
            $this->database = 'mysql';
            $this->dbhost = 'localhost';
            $this->db_pconnect = 0;

            $this->root_path = str_replace( "\\", "/", getcwd() ); // "
            $this->default_root_path = $this->root_path = str_replace( "/install", "", $this->root_path );			
            $filepath = ( ! empty( $_SERVER['REQUEST_URI'] ) ) ? dirname( $_SERVER['REQUEST_URI'] ) : dirname( $_SERVER['SCRIPT_NAME'] );
            $filepath = str_replace( "\\", "/", $filepath ); // "
            $filepath = str_replace( "/install", "", $filepath );
            if ( substr( $filepath, 0, 1 ) == "/" )
            {
                $filepath = substr( $filepath, 1 );
            }
            if ( substr( $filepath, -1 ) == "/" )
            {
                $filepath = substr( $filepath, 0, -1 );
            }
            $protocol = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? 'https://' : 'http://';
            $this->default_zarilia_url = $this->zarilia_url = ( !empty( $filepath ) ) ? $protocol . $_SERVER['HTTP_HOST'] . "/" . $filepath : $protocol . $_SERVER['HTTP_HOST'];
            $this->prefix = $this->generatePrefix();
        }
    }

    function readPost()
    {
        if ( isset( $_POST['database'] ) )
            $this->database = stripslashes( $_POST['database'] );
        if ( isset( $_POST['dbhost'] ) )
            $this->dbhost = stripslashes( $_POST['dbhost'] );
        if ( isset( $_POST['dbuname'] ) )
            $this->dbuname = stripslashes( $_POST['dbuname'] );
        if ( isset( $_POST['dbpass'] ) )
            $this->dbpass = stripslashes( $_POST['dbpass'] );
        if ( isset( $_POST['dbname'] ) )
            $this->dbname = stripslashes( $_POST['dbname'] );
        if ( isset( $_POST['prefix'] ) )
            $this->prefix = stripslashes( $_POST['prefix'] );
        if ( isset( $_POST['db_pconnect'] ) )
            $this->db_pconnect = intval( $_POST['db_pconnect'] ) > 0 ? 1 : 0;
        if ( isset( $_POST['root_path'] ) )
            $this->root_path = stripslashes( $_POST['root_path'] );
        if ( isset( $_POST['zarilia_url'] ) )
            $this->zarilia_url = stripslashes( $_POST['zarilia_url'] );
    }

    function readConstant()
    {
        if ( defined( 'ZAR_DB_TYPE' ) )
            $this->database = ZAR_DB_TYPE;
        if ( defined( 'ZAR_DB_HOST' ) )
            $this->dbhost = ZAR_DB_HOST;
        if ( defined( 'ZAR_DB_USER' ) )
            $this->dbuname = ZAR_DB_USER;
        if ( defined( 'ZAR_DB_PASS' ) )
            $this->dbpass = ZAR_DB_PASS;
        if ( defined( 'ZAR_DB_NAME' ) )
            $this->dbname = ZAR_DB_NAME;
        if ( defined( 'ZAR_DB_PREFIX' ) )
        {
            if ( $this->prefix !== '' )
            {
                $this->prefix = ZAR_DB_PREFIX;
            }
        }
        if ( $this->prefix == '' )
        {
            $this->prefix = $this->generatePrefix();
        }

        if ( defined( 'ZAR_DB_PCONNECT' ) )
            $this->db_pconnect = intval( ZAR_DB_PCONNECT ) > 0 ? 1 : 0;
        if ( defined( 'ZAR_ROOT_PATH' ) )
        {
            if ( ZAR_ROOT_PATH != '' )
            {
                $this->root_path = ZAR_ROOT_PATH;
            }
        }
        if ( $this->root_path == '' )
        {
            chdir( '..' );
            $this->root_path = getcwd();
            chdir( 'install' );
        }
        if ( defined( 'ZAR_URL' ) )
        {
            if ( ZAR_URL != '' )
            {
                $this->zarilia_url = ZAR_URL;
            }
        }
        if ( ( $this->zarilia_url == '' ) || ( $this->zarilia_url == 'http://' ) || ( $this->zarilia_url == 'https://' ) )
        {
            $this->zarilia_url = urldecode( substr( $_SERVER['HTTP_REFERER'], 0, - strlen( 'install/index.php' )-1 ) );
        }
    }

    function checkData()
    {
        $ret = '';
        $error = array();

        if ( empty( $this->dbhost ) )
        {
            $error[] = sprintf( _INSTALL_L57, _INSTALL_L27 );
        }
        if ( empty( $this->dbname ) )
        {
            $error[] = sprintf( _INSTALL_L57, _INSTALL_L29 );
        }
        if ( empty( $this->prefix ) )
        {
            $error[] = sprintf( _INSTALL_L57, _INSTALL_L30 );
        }
        if ( empty( $this->root_path ) )
        {
            $error[] = sprintf( _INSTALL_L57, _INSTALL_L55 );
        }
        if ( empty( $this->zarilia_url ) )
        {
            $error[] = sprintf( _INSTALL_L57, _INSTALL_L56 );
        }

        if ( !empty( $error ) )
        {
            foreach ( $error as $err )
            {
                $ret .= "<p><span style='color:#ff0000;'><b>" . $err . "</b></span></p>\n";
            }
        }
        return $ret;
    }

    function editform()
    {
        $ret = "<table width='100%' border='0' cellpadding='2' cellspacing='1'>
                <tr>
                    <th colspan='2'></th>
                </tr>
                <tr valign='top' align='left'>
                    <td class='head'>
                        <b>" . _INSTALL_L51 . "</b><br />
                        <span style='font-size:85%; font-weight: normal;'>" . _INSTALL_L66 . "</span>
                    </td>
                    <td class='even'>
                        <select size='1' name='database' id='database'>";
        $dblist = $this->getDBList();
        foreach( $dblist as $val )
        {
            $ret .= "<option value='$val'";
            if ( $val == $this->database ) $ret .= " selected='selected'";
            $ret .= "'>$val</option>";
        }
        $ret .= "</select>
                    </td>
                </tr>";
        $ret .= $this->editform_sub( _INSTALL_L27, _INSTALL_L67, 'dbhost', htmlSpecialChars( $this->dbhost ) );
        $ret .= $this->editform_sub( _INSTALL_L28, _INSTALL_L65, 'dbuname', htmlSpecialChars( $this->dbuname ) );
        $ret .= $this->editform_sub( _INSTALL_L52, _INSTALL_L68, 'dbpass', htmlSpecialChars( $this->dbpass ) );
        $ret .= $this->editform_sub( _INSTALL_L29, _INSTALL_L64, 'dbname', htmlSpecialChars( $this->dbname ) );
        $ret .= $this->editform_sub( _INSTALL_L30, _INSTALL_L63, 'prefix', htmlSpecialChars( $this->prefix ) );
        $ret .= "<tr valign='top' align='left'>
                    <td class='head'>
                        <b>" . _INSTALL_L54 . "</b><br />
                        <span style='font-size:85%; font-weight: normal;'>" . _INSTALL_L69 . "</span>
                    </td>
                    <td class='even'>
                        <input type='radio' name='db_pconnect' value='1'" . ( $this->db_pconnect == 1 ? " checked='checked'" : "" ) . " />" . _INSTALL_L23 . "
                        <input type='radio' name='db_pconnect' value='0'" . ( $this->db_pconnect != 1 ? " checked='checked'" : "" ) . " />" . _INSTALL_L24 . "
                    </td>
                </tr>
                ";
        $ret .= $this->editform_sub( _INSTALL_L55, _INSTALL_L59, 'root_path', htmlSpecialChars( $this->root_path ),  $this->default_root_path );
        $ret .= $this->editform_sub( _INSTALL_L56, _INSTALL_L58, 'zarilia_url', htmlSpecialChars( $this->zarilia_url ),  $this->default_zarilia_url );
        $ret .= "</table>";
        return $ret;
    }

    function editform_sub( $title, $desc, $name, $value, $dvalue = null )
    {
        return "<tr valign='top' align='left'>
                    <td class='head'>
                        <b>" . $title . "</b><br />
                        <span style='font-size:85%; font-weight: normal;'>" . $desc . "</span>
                    </td>
                    <td class='even'>
                        <input type='text' name='" . $name . "' id='" . $name . "' size='30' maxlength='100' value='" . htmlspecialchars( $value ) . "' />
						".(($dvalue)?"<input type='button' onclick='document.getElementById(\"$name\").value=\"$dvalue\";' value='<--' />":"")."
                    </td>
                </tr>";
    }

    function confirmForm()
    {
        $yesno = empty( $this->db_pconnect ) ? _INSTALL_L24 : _INSTALL_L23;
        $ret = "<table width='100%' border='0' cellpadding='2' cellspacing='1'>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L51 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->database ) . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L27 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->dbhost ) . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L28 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->dbuname ) . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L52 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->dbpass ) . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L29 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->dbname ) . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L30 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->prefix ) . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L54 . "</b></td>
                        <td class='even'>" . $yesno . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L55 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->root_path ) . "</td>
                    </tr>
                    <tr>
                        <td class='head'><b>" . _INSTALL_L56 . "</b></td>
                        <td class='even'>" . htmlSpecialChars( $this->zarilia_url ) . "</td>
                    </tr>
                </table>
            <input type='hidden' name='database' value='" . htmlSpecialChars( $this->database ) . "' />
            <input type='hidden' name='dbhost' value='" . htmlSpecialChars( $this->dbhost ) . "' />
            <input type='hidden' name='dbuname' value='" . htmlSpecialChars( $this->dbuname ) . "' />
            <input type='hidden' name='dbpass' value='" . htmlSpecialChars( $this->dbpass ) . "' />
            <input type='hidden' name='dbname' value='" . htmlSpecialChars( $this->dbname ) . "' />
            <input type='hidden' name='prefix' value='" . htmlSpecialChars( $this->prefix ) . "' />
            <input type='hidden' name='db_pconnect' value='" . intval( $this->db_pconnect ) . "' />
            <input type='hidden' name='root_path' value='" . htmlSpecialChars( $this->root_path ) . "' />
            <input type='hidden' name='zarilia_url' value='" . htmlSpecialChars( $this->zarilia_url ) . "' />
            ";
        return $ret;
    }

    function getDBList()
    {
        return getDirList('../class/adodb_lite/adodbSQL_drivers/');//array( 'mysql' );
        // $dirname = '../class/database/';
        // $dirlist = array();
        // if (is_dir($dirname) && $handle = opendir($dirname)) {
        // while (false !== ($file = readdir($handle))) {
        // if ( !preg_match("/^[.]{1,2}$/",$file) ) {
        // if (strtolower($file) != 'cvs' && is_dir($dirname.$file) ) {
        // $dirlist[$file] = strtolower($file);
        // }
        // }
        // }
        // closedir($handle);
        // asort($dirlist);
        // reset($dirlist);
        // }
        // return $dirlist;
    }

    function generatePrefix()
    {
        $makepass = '';
        $syllables = array( "er", "in", "tia", "wol", "fe", "pre", "vet", "jo", "nes", "al", "len", "son", "cha", "ir", "ler", "bo", "ok", "tio", "nar", "sim", "ple", "bla", "ten", "toe", "cho", "co", "lat", "spe", "ak", "er", "po", "co", "lor", "pen", "cil", "li", "ght", "wh", "at", "the", "he", "ck", "is", "mam", "bo", "no", "fi", "ve", "any", "way", "pol", "iti", "cs", "ra", "dio", "sou", "rce", "sea", "rch", "pa", "per", "com", "bo", "sp", "eak", "st", "fi", "rst", "gr", "oup", "boy", "ea", "gle", "tr", "ail", "bi", "ble", "brb", "pri", "dee", "kay", "en", "be", "se" );
        srand( ( double )microtime() * 1000000 );
        for ( $count = 1; $count <= 4; $count++ )
        {
            if ( rand() % 10 == 1 )
            {
                $makepass .= sprintf( "%0.0f", ( rand() % 50 ) + 1 );
            }
            else
            {
                $makepass .= sprintf( "%s", $syllables[rand() % 62] );
            }
        }
        return $makepass;
    }
}

?>