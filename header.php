<?php
// $Id: header.php,v 1.4 2007/05/05 11:10:55 catzwolf Exp $
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
// Start of the Page
include_once ZAR_ROOT_PATH . '/class/zariliablock.php';

global $zariliaLogger, $zariliaConfig, $zariliaUser, $zariliaUserIsAdmin, $zariliaAddon;

$zariliaLogger->stopTime( 'Addon Load Time' );
$zariliaLogger->startTime( 'Zarilia Output Time' );

$zariliaOption['theme_use_smarty'] = 1;
/*
* include Smarty template engine and initialize it
*/
require_once ZAR_ROOT_PATH . '/class/template.php';
$zariliaTpl = new ZariliaTpl();
$zariliaTpl->zarilia_setCaching( 2 );
$zariliaTpl->zarilia_setDebugging( $zariliaLogger->getbugLevel( 'smarty' ) );
$zariliaTpl->addMeta( 'content-type', 'text/html; charset=' . _CHARSET, true );
$zariliaTpl->addMeta( 'content-language', _LANGCODE, true );
/**
 * Assign Meta Tags
 */
$config_handler = &zarilia_gethandler( 'config' );
$criteria = new CriteriaCompo( new Criteria( 'conf_modid', 0 ) );
$criteria->add( new Criteria( 'conf_catid', ZAR_CONF_METAFOOTER ) );
$criteria->add( new Criteria( 'conf_name', 'meta_%', 'LIKE' ) );
$config = $config_handler->getConfigs( $criteria, true );
foreach ( array_keys( $config ) as $i ) {
    if ( $config[$i]->getVar( 'conf_name' ) == 'meta_footer' ) {
        $zariliaTpl->assign( 'zarilia_meta_footer', $config[$i]->getConfValueForOutput() );
    } else {
        $zariliaTpl->addMeta( $config[$i]->getVar( 'conf_name' ), htmlspecialchars( $config[$i]->getConfValueForOutput(), ENT_QUOTES ) );
    }
}
/**
 * Assign Shortcut icon
 */
$zariliaTpl->addIcon( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/favicon.ico', 'SHORTCUT ICON', '' );
/**
 * assign Zarilia JS
 */
$zariliaTpl->addScript( ZAR_URL . '/include/javascript/zarilia.js' );
//$zariliaTpl->addScript( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/js/JSCookMenu.js' );
//$zariliaTpl->addScript( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/js/pngfix.js' );
//$zariliaTpl->addScript( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/js/ThemeOffice/theme.js' );
//$zariliaTpl->addCss( ZAR_THEME_URL . "/" . $zariliaConfig['theme_set'] . '/css/style.css' );
//$zariliaTpl->addCss( ZAR_THEME_URL . "/" . $zariliaConfig['theme_set'] . '/js/ThemeOffice/user_theme.css' );
/**
 * get all blocks and assign to smarty
 */
$zariliablock = new ZariliaBlock();
$block_arr = array();
$_userGroups = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;


if ( is_object( $zariliaUser ) ) {
    $_userGroups = $zariliaUser->getGroups();
    $zariliaTpl->assign( array( 'zarilia_isuser' => true, 'zarilia_userid' => $zariliaUser->getVar( 'uid' ), 'zarilia_uname' => $zariliaUser->getVar( 'uname' ), 'zarilia_isadmin' => $zariliaUserIsAdmin ) );
} else {
    $_userGroups = ZAR_GROUP_ANONYMOUS;
    $zariliaTpl->assign( array( 'zarilia_isuser' => false, 'zarilia_isadmin' => false ) );
}
if ( is_object( $zariliaAddon ) && $zariliaAddon->getVar( 'mid' ) > 1 ) {
    $_isAddon = $zariliaAddon->getVar( 'mid' );
    $_statement = ( preg_match( "/index\.php$/i", zarilia_getenv( 'PHP_SELF' ) ) && $zariliaConfig['startpage'] == $zariliaAddon->getVar( 'dirname' ) ) ? true : false;
    $zariliaTpl->addTitle( htmlspecialchars( $zariliaConfig['sitename'] ) . " " . $zariliaAddon->getVar( 'name' ) );
} else {
    $_isAddon = 0;
    $zariliaTpl->addTitle( htmlspecialchars( $zariliaConfig['sitename'] ) . " " . htmlspecialchars( $zariliaConfig['slogan'], ENT_QUOTES ) );
    $_statement = ( !empty( $zariliaOption['show_cblock'] ) ) ? true : false;
}
if ( is_object( $zariliaAddon ) && $zariliaAddon->getVar( 'name' ) ) {
    $zariliaTpl->assign( 'zarilia_pagetitle', $zariliaAddon->getVar( 'name' ) );
}

$show_center = ( in_array( @$zariliaOption['pagetype'], array( 'user', 'notification' ) ) ) ? false : true;
if ( $show_center == true ) {
    $block_arr = &$zariliablock->getAllByGroupAddon( $_userGroups, $_isAddon, $_statement , ZAR_BLOCK_VISIBLE );
} else {
    $block_arr = &$zariliablock->getAllByGroupAddonSides( $_userGroups, $_isAddon, $_statement , ZAR_BLOCK_VISIBLE );
}

foreach ( array_keys( $block_arr ) as $i ) {
    /*
 	 * rendering block
 	 */
  //  if ( $block_arr[$i]->getVar( 'liveupdate' ) ) {
//        $bcontent = $block_arr[$i]->getAsLiveUpdateObj();
//    } else {
        $bcontent = $block_arr[$i]->getRenderedBlockContent();
//    }

    if ( ( $bcontent == '' ) || ( $bcontent == null ) || ( $bcontent == false ) ) continue;

    switch ( $block_arr[$i]->getVar( 'side' ) ) {
        case ZAR_SIDEBLOCK_LEFT:
            if ( !isset( $show_lblock ) ) {
                $zariliaTpl->assign( 'zarilia_showlblock', 1 );
                $show_lblock = 1;
            }
            $zariliaTpl->append( 'zarilia_lblocks', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;

        case ZAR_CENTERBLOCK_LEFT:
            if ( !isset( $show_cblock ) ) {
                $zariliaTpl->assign( 'zarilia_showcblock', 1 );
                $show_cblock = 1;
            }
            $zariliaTpl->append( 'zarilia_clblocks', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;

        case ZAR_CENTERBLOCK_RIGHT:
            if ( !isset( $show_cblock ) ) {
                $zariliaTpl->assign( 'zarilia_showcblock', 1 );
                $show_cblock = 1;
            }
            $zariliaTpl->append( 'zarilia_crblocks', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;

        case ZAR_CENTERBLOCK_CENTER:
            if ( !isset( $show_cblock ) ) {
                $zariliaTpl->assign( 'zarilia_showcblock', 1 );
                $show_cblock = 1;
            }
            $zariliaTpl->append( 'zarilia_ccblocks', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;

        case ZAR_CENTERBLOCKDOWN_LEFT:
            if ( !isset( $show_cblock_down ) ) {
                $zariliaTpl->assign( 'zarilia_showcdblock', 1 );
                $show_cblock_down = 1;
            }
            $zariliaTpl->append( 'zarilia_clblocks_down', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;

        case ZAR_CENTERBLOCKDOWN_RIGHT:
            if ( !isset( $show_cblock_down ) ) {
                $zariliaTpl->assign( 'zarilia_showcdblock', 1 );
                $show_cblock_down = 1;
            }
            $zariliaTpl->append( 'zarilia_crblocks_down', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;

        case ZAR_CENTERBLOCKDOWN_CENTER:
            if ( !isset( $show_cblock_down ) ) {
                $zariliaTpl->assign( 'zarilia_showcdblock', 1 );
                $show_cblock_down = 1;
            }
            $zariliaTpl->append( 'zarilia_ccblocks_down', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;

        case ZAR_SIDEBLOCK_RIGHT:
            if ( !isset( $show_rblock ) ) {
                $zariliaTpl->assign( 'zarilia_showrblock', 1 );
                $show_rblock = 1;
            }
            $zariliaTpl->append( 'zarilia_rblocks', array( 'title' => $block_arr[$i]->getVar( 'title' ), 'content' => $bcontent ) );
            break;
    }
    unset( $bcontent );
}
unset( $block_arr );

if ( !isset( $show_lblock ) ) {
    $zariliaTpl->assign( 'zarilia_showlblock', 0 );
}
if ( !isset( $show_rblock ) ) {
    $zariliaTpl->assign( 'zarilia_showrblock', 0 );
}
if ( !isset( $show_cblock ) or $show_center == false ) {
    $zariliaTpl->assign( 'zarilia_showcblock', 0 );
}
if ( !isset( $show_cblock_down ) ) {
    $zariliaTpl->assign( 'zarilia_showcdblock', 0 );
}

//unset( $_SESSION['user'] );
if (( !isset( $_SESSION['user']['menu'] ) ) ||(@$_REQUEST['debug'] == 'rebuild') ){
    $menu_handler = &zarilia_gethandler( 'menus' );
    $menu_handler->displayTopMenu();
} 

$zariliaTpl->assign_by_ref( 'system_menu', $_SESSION['user']['menu'] );
$zariliaTpl->assign_by_ref( 'system_footermenu', $_SESSION['user']['footermenu'] );

if ( zarilia_getenv( 'REQUEST_METHOD' ) != 'POST' && !empty( $zariliaAddon ) && !empty( $zariliaConfig['addon_cache'][$zariliaAddon->getVar( 'mid' )] ) ) {
    $zariliaTpl->zarilia_setCaching( 2 );
    $zariliaTpl->zarilia_setCacheTime( $zariliaConfig['addon_cache'][$zariliaAddon->getVar( 'mid' )] );
    if ( !isset( $zariliaOption['template_main'] ) ) {
        $zariliaCachedTemplate = 'db:system_dummy.html';
    } else {
        $zariliaCachedTemplate = 'db:' . $zariliaOption['template_main'];
    }
    // generate safe cache Id

    $zariliaCachedTemplateId = 'mod_' . $zariliaAddon->getVar( 'dirname' ) . '|' . md5( isset($_SERVER['REQUEST_URI'])?str_replace( ZAR_URL, '', $_SERVER['REQUEST_URI'] ):'');
    if ( $zariliaTpl->is_cached( $zariliaCachedTemplate, $zariliaCachedTemplateId ) ) {
        $zariliaLogger->addExtra( $zariliaCachedTemplate, $zariliaConfig['addon_cache'][$zariliaAddon->getVar( 'mid' )] );
        $zariliaTpl->assign( 'zarilia_contents', $zariliaTpl->fetch( $zariliaCachedTemplate, $zariliaCachedTemplateId ) );
        $zariliaTpl->zarilia_setCaching( 0 );
        if ( !headers_sent() ) {
            header ( 'Content-Type:text/html; charset=' . _CHARSET );
        }
        $zariliaTpl->display( ZAR_THEME_PATH . '/' . $zariliaConfig['theme_set'] . '/theme.html' );
        $zariliaLogger->render();
        exit();
    }
} else {
    $zariliaTpl->zarilia_setCaching( 0 );
}
if ( !isset( $zariliaOption['template_main'] ) ) {
    $zariliaTheme['thename'] = $zariliaConfig['theme_set'];
    ob_start();
}
$zariliaLogger->stopTime( 'Zarilia Output Time' );
$zariliaLogger->startTime( 'Addon Output Time' );

?>