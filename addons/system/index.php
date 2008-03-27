<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:57 catzwolf Exp $
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
$zariliaOption['isAdmin'] = true;
include "../../mainfile.php";
/*if ( !function_exists( 'zarilia_cleanRequestVars' ) ) {
    header( 'location: ../../install' );
    die();
}*/
$_lang = ( !empty( $zariliaConfig['language'] ) ) ? $zariliaConfig['language'] : 'english';

require_once ZAR_ROOT_PATH . "/include/cp_functions.php";
include_once ZAR_ROOT_PATH . "/kernel/addon.php";
if ( is_object( $zariliaUser ) ) {
    $zariliaAddon = &ZariliaAddon::getByDirname( "system" );
    if ( !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
        redirect_header( ZAR_URL . "/", 1, _NOPERM );
        exit();
    }
} else {
    redirect_header( ZAR_URL . "/", 1, _NOPERM );
    exit();
}

$error = null;
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'default', XOBJ_DTYPE_TXTBOX );
$fct = zarilia_cleanRequestVars( $_REQUEST, 'fct', 'cpanel', XOBJ_DTYPE_TXTBOX );
chdir("admin/$fct");
// $_PHP_SELF = zarilia_getenv( 'PHP_SELF' );
/*crappy hack to get to mail users from her when sending mail from users and finusers*/
if ( ( isset( $_POST['fct'] ) && $_POST['fct'] == 'users' ) && ( isset( $_POST['op'] ) && $_POST['op'] == 'mailusers' ) ) {
    $fct = 'mailusers';
    $op = 'default';
}
if ( isset( $fct ) && $fct != '' ) {
    if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/zarilia_version.php" ) ) {
        require_once ZAR_ROOT_PATH . "/addons/system/language/" . $_lang . "/admin/preferences.php";
        if ( $fct != 'preferences' ) {
            if ( file_exists( ZAR_ROOT_PATH . "/addons/system/language/" . $_lang . "/admin/" . $fct . ".php" ) ) {
                require_once @ZAR_ROOT_PATH . "/addons/system/language/" . $_lang . "/admin/" . $fct . ".php";
            }
        }
        require ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/zarilia_version.php";
        $category = array( 'ZAR_SYSTEM_HOME' => 'cpanel',
            'groups' => 'ZAR_SYSTEM_GROUP', 'users' => 'ZAR_SYSTEM_USER', 'preferences' => 'ZAR_SYSTEM_PREF', 'addonsadmin' => 'ZAR_SYSTEM_ADDON', 'blocksadmin' => 'ZAR_SYSTEM_BLOCK', 'findusers' => 'ZAR_SYSTEM_FINDU', 'mailusers' => 'ZAR_SYSTEM_MAILU', 'images' => 'ZAR_SYSTEM_MEDIA', 'media' => 'ZAR_SYSTEM_MEDIA',
            'avatars' => 'ZAR_SYSTEM_AVATAR', 'userrank' => 'ZAR_SYSTEM_URANK', 'smilies' => 'ZAR_SYSTEM_SMILE', 'comments' => 'ZAR_SYSTEM_COMMENT', 'tplsets' => 'ZAR_SYSTEM_TPLSET', 'agever' => 'ZAR_SYSTEM_AGEVER', 'userprofiles' => 'ZAR_SYSTEM_PROFILES', 'coreinfo' => 'ZAR_SYSTEM_COREINFO', 'languages' => 'ZAR_SYSTEM_LANGUAGE',
            'events' => 'ZAR_SYSTEM_EVENTS', 'mimetypes' => 'ZAR_SYSTEM_MIMETYPES', 'developer' => 'ZAR_SYSTEM_DEVELOPERS', 'cpanel' => 'ZAR_SYSTEM_CPANEL', 'multilanguage' => 'ZAR_SYSTEM_MULTILANG', 'menus' => 'ZAR_SYSTEM_MENUS', 'section' => 'ZAR_SYSTEM_SECTION',
            'category' => 'ZAR_SYSTEM_CATEGORY', 'static' => 'ZAR_SYSTEM_STATIC', 'rss' => 'ZAR_SYSTEM_RSS', 'errors' => 'ZAR_SYSTEM_ERRORS', 'content' => 'ZAR_SYSTEM_CONTENT', 'security' => 'ZAR_SYSTEM_SECURITY', 'trash' => 'ZAR_SYSTEM_TRASH', 'contest' => 'ZAR_SYSTEM_CONTEST',
            'streaming' => 'ZAR_SYSTEM_STREAMING', 'developers' => 'ZAR_SYSTEM_DEVELOPERS', 'ms_ml' =>  'ZAR_SYSTEM_COREINFO');
        if ( array_key_exists( $fct, $category ) || $zariliaAddon->getVar( 'mid' ) > 1 ) {
            $groups = &$zariliaUser->getGroups();
            $sysperm_handler = &zarilia_gethandler( 'groupperm' );
            $permissions = $sysperm_handler->checkRight( 'system_admin', constant( $category[$fct] ), $groups, $zariliaAddon->getVar( 'mid' ) );
            if ( in_array( ZAR_GROUP_ADMIN, $groups ) && $permissions == true ) {
                if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/index.php" ) ) {
                    require_once ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/index.php";
                    exit();
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, 'WARNING: Could not find the specific system file required' );
                }
            } else {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, 'Notice: You do not have permission to view this area!' );
            }
        } else {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, 'Notice: Selected admin addon category or version incorrect' );
        }
    } else {
        $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, 'Maybe not a addon? zarilia_version.php does not exist!' );
    }
} else {
    $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, 'Notice: Selected Admin Addons Does Not Exist!' );
}
/*
* If on error
*/
if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
//    cp_showErrors( 'Warning: System Error', $heading = '', $description = '', $image = '', $errno = '', $errstr = '' );
     zarilia_cp_header();
     zarilia_admin_menu( '', "Warning: System Error", $op );
     $GLOBALS['zariliaLogger']->sysRender();
     zarilia_cp_footer();
     exit();
}

?>