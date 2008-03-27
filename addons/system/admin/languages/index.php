<?php
// $Id: index.php,v 1.2 2007/04/21 09:42:27 catzwolf Exp $
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
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( 'Access Denied' );
}

require_once 'admin_menu.php';
include_once ZAR_ROOT_PATH . '/class/class.menubar.php';
include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

require_once ZAR_ROOT_PATH . '/addons/system/admin/languages/functions.php';

$opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0, XOBJ_DTYPE_INT );
$addonid = zarilia_cleanRequestVars( $_REQUEST, 'addonid', 1 );
$categoryid = zarilia_cleanRequestVars( $_REQUEST, 'categoryid', 1 );
$fileid = zarilia_cleanRequestVars( $_REQUEST, 'fileid', 1 );
$files = zarilia_cleanRequestVars( $_REQUEST, 'files', null );

zarilia_cp_header();
switch ( $op ) {
    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . '/addons/system/admin/' . $fct . '/admin_help.php' ) ) {
            @include ZAR_ROOT_PATH . '/addons/system/admin/' . $fct . '/admin_help.php';
        }
        break;

    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 3 );
        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'savetranslation':
        $string = zarilia_cleanRequestVars( $_REQUEST, 'item', '', XOBJ_DTYPE_TXTBOX );
        switch ( $opt ) {
            case 2:
                $addon_handler = &zarilia_gethandler( 'addon' );
                $addon_list = &$addon_handler->getDirList();
                $fileid = zarilia_cleanRequestVars( $_REQUEST, 'fileid', 0 );
                $path = ZAR_ROOT_PATH . '/addons/' . $addon_list[$addonid] . '/language';
                $files = GetFiles( "$path/english" );
                $file = $files[$fileid];
                unset( $files );
                SaveVars( $string, $path, $file );
                redirect_header( ZAR_URL . "/addons/system/index.php?fct=languages&amp;op=translate&amp;opt=$opt&fileid=$fileid&addonid=$addonid", 2, _DBUPDATED , false );
                break;
            case 1:
                $fileid = zarilia_cleanRequestVars( $_REQUEST, 'fileid', 1 );
                $path = ZAR_ROOT_PATH . '/language';
                $files = GetFiles( "$path/english" );
                $file = $files[$fileid];
                unset( $files );
                SaveVars( $string, $path, $file );
                redirect_header( ZAR_URL . "/addons/system/index.php?fct=languages&amp;op=translate&amp;opt=$opt&fileid=$fileid", 2, _DBUPDATED , false );
                break;
            case 0:
                break;
        }
        break;

    case 'translating':
        $menu_handler->render( 2 );
        include_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";
        $string = zarilia_cleanRequestVars( $_REQUEST, 'item', '', XOBJ_DTYPE_TXTBOX );
        $form = new ZariliaThemeForm( sprintf( _MD_AM_TRANSLATING_TXT, $string ), 'modifystring', ZAR_URL . '/addons/system/index.php', 'post' );
        $_GET['op'] = 'savetranslation';
        foreach ( $_GET as $key => $value )
        $form->addElement( new ZariliaFormHidden( $key, $value ), false );
        switch ( $opt ) {
            case 2:
                $addon_handler = &zarilia_gethandler( 'addon' );
                $addon_list = &$addon_handler->getDirList();
                $fileid = zarilia_cleanRequestVars( $_REQUEST, 'fileid', 0 );
                $addonid = zarilia_cleanRequestVars( $_REQUEST, 'addonid', 1 );
                $path = ZAR_ROOT_PATH . '/addons/' . $addon_list[$addonid] . '/language';
                $files = GetFiles( "$path/english" );
                ParseList( $form, $path, $files[$fileid], $string );
                unset( $files );
                unset( $fileid );
                unset( $addonid );
                break;
            case 1:
                $fileid = zarilia_cleanRequestVars( $_REQUEST, 'fileid', 0 );
                $path = ZAR_ROOT_PATH . '/language';
                $files = GetFiles( "$path/english" );
                ParseList( $form, $path, $files[$fileid], $string );
                unset( $files );
                unset( $fileid );
                break;
            case 0:
                break;
        }
        $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
        $form->display();
        break;

    case 'translate':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
		$menu_handler->render( 2 );
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', '', XOBJ_DTYPE_TXTBOX );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['length'] = zarilia_cleanRequestVars( $_REQUEST, 'length', 25 );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        if ( isset( $_GET['start'] ) ) {
            unset( $_GET['start'] );
        }
        $nav['url'] = '';
        foreach( $_GET as $key => $value ) {
            $nav['url'] .= "$key=$value&";
        }
        $nav['url'] = htmlentities( substr( $nav['url'], 0, -1 ) );
        $op = array( 'index.php?fct=languages&amp;op=translate' => 'Edit strings', 'index.php?fct=languages&amp;op=import' => 'Import', 'index.php?fct=languages&amp;op=export' => 'Export' );

        zarilia_admin_menu( _MD_AD_ACTION_BOX, $op );
        $tabbar = new ZariliaTabMenu( $opt );
        $tabbar->addTabArray( getTabs() );
        $tabbar->renderStart();
        echo '<table width="100%" cellpadding="2" cellspacing="1" class="outer"><tr><td>';
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'key', '180px', 'left' );
        $tlist->AddHeader( 'text', '', 'left' );
        $tlist->AddHeader( 'ACTION', '5px', 'center' );
        $tlist->setPrefix( '_MD_AM_' );
        switch ( $opt ) {
            case 2:
                $addonid = zarilia_cleanRequestVars( $_REQUEST, 'addonid', 1 );
                $fileid = zarilia_cleanRequestVars( $_REQUEST, 'fileid', 0 );
                $addon_handler = &zarilia_gethandler( 'addon' );
                $addon_list = &$addon_handler->getDirList();
                $files = array();
                if ( count( $addon_list ) < 1 ) {
                    $param1 = 'disabled="disabled" style="disabled: true"';
                    $addon_list[] = _CP_AM_LANGNOADDONS;
                    $param2 = 'disabled="disabled" style="disabled: true"';
                    $files[] = _CP_AM_LANGNOFILES;
                } else {
                    $param1 = "";
                    $files = GetFiles( ZAR_ROOT_PATH . '/addons/' . $addon_list[$addonid] . '/language/english' );
                    if ( count( $files ) < 1 ) {
                        $param2 = 'disabled="disabled" style="disabled: true"';
                        $files[] = _CP_AM_LANGNOFILES;
                    } else {
                        $param2 = "";
                    }
                }
                echo "<br />\n<form op='index.php' method='get'>\n<input type='hidden' name='fct' value='languages' />\n<div>" . _MD_AM_ADDON . ": ";
                zarilia_getSelection( $addon_list, $addonid, "addonid", 1, 0, false, false, "$param1 onchange=\"location='" . ZAR_URL . "/addons/system/index.php?fct=languages&amp;op=translate&amp;opt=$opt&amp;addonid='+this.options[this.selectedIndex].value\"" );
                echo " " . _MD_AM_FILE . ": ";
                zarilia_getSelection( $files, $fileid, "fileid", 1, 0, false, false, "$param2 onchange=\"location='" . ZAR_URL . "/addons/system/index.php?fct=languages&amp;op=translate&amp;opt=$opt&amp;addonid=$addonid&amp;fileid='+this.options[this.selectedIndex].value\"" );
                echo "</div></form><br />";
                if ( ( $param1 == "" ) && ( $param2 == "" ) ) {
                    $data = GetLangData( ZAR_ROOT_PATH . '/addons/' . $addon_list[$addonid] . '/language/english/' . $files[$fileid], ZAR_URL . '/addons/system/index.php?' . str_replace( 'op=translate', 'op=translating', $nav['url'] ) );
                } else {
                    $data = array();
                }
                $tlist->import( array_slice( $data, $nav['start'], $nav['length'] ) );
                break;

            case 1:
                $fileid = zarilia_cleanRequestVars( $_REQUEST, 'fileid', 0 );
                $files = GetFiles( ZAR_ROOT_PATH . '/language/english' );
                if ( count( $files ) < 1 ) {
                    $param = 'disabled="disabled" style="disabled: true';
                    $files[] = _CP_AM_LANGNOFILES;
                } else {
                    $param = '';
                }
                echo "<br />\n<form op='index.php' method='get'>\n<input type='hidden' name='fct' value='languages' />\n<div>" . _MD_AM_FILE . ": ";
                zarilia_getSelection( $files, $fileid, "fileid", 1, 0, false, false, "$param onchange=\"location='" . ZAR_URL . "/addons/system/index.php?fct=languages&amp;opt=$opt&amp;op=translate&amp;fileid='+this.options[this.selectedIndex].value\"" );
                echo "</div></form><br />";
                if ( $param == "" ) {
                    $data = GetLangData( ZAR_ROOT_PATH . '/language/english/' . $files[$fileid], ZAR_URL . '/addons/system/index.php?' . str_replace( 'op=translate', 'op=translating', $nav['url'] ) );
                } else {
                    $data = array();
                }
                $tlist->import( array_slice( $data, $nav['start'], $nav['length'] ) );
                break;

            default:
                $categories = array( _MD_AM_CAT_BLOCKS, _MD_AM_CAT_MENUS, _MD_AM_CAT_ADDONS );
                echo "<br />\n<form op='index.php' method='get'>\n<input type='hidden' name='fct' value='languages' />\n<div>" . _MD_AM_CATEGORY . ": ";
                zarilia_getSelection( $categories, $categoryid, "categoryid", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/index.php?fct=languages&amp;opt=$opt&amp;op=translate&amp;fileid='+this.options[this.selectedIndex].value\"" );
                echo "</div></form><br />";
                $data = GetLangData( ZAR_ROOT_PATH . '/language/english/' . $files[$fileid], ZAR_URL . '/addons/system/index.php?' . str_replace( 'op=translate', 'op=translating', $nav['url'] ) );
                $tlist->import( array_slice( $data, $nav['start'], $nav['length'] ) );
                break;
        }
        $tlist->render();
        zarilia_pagnav( count( $data ), $nav['length'], $nav['start'], 'start', 1, 'index.php?' . $nav['url'] );
        echo '</td></tr></table>';
        break;

    case 'list':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $menu_handler->render( 1 );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'id', '10%', 'center', true );
        $tlist->AddHeader( 'sn', '40%', 'left', true );
        $tlist->AddHeader( 'fn', '40%', 'left', true );
        $tlist->AddHeader( 'ACTION', '10%', 'center', false );
        $tlist->setPrefix( '_MD_AM_' );
        $tlist->setPath( 'op=' . $op );
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'type', $opt + 1 ) );
        foreach ( $zariliaEvents->getObjects( $criteria ) as $event )
        $tlist->add(
            array( _MD_AM_EVENTOWNER => getUserDesc( $event->getVar( 'uid' ) ),
                _MD_AM_EVENTCONDITION => getConditionDesc( $event->getVar( 'condition' ), $event->getVar( 'type' ) ),
                _MD_AM_EVENTCOMMENT => getCommentDesc( $event->getVar( 'comment' ) ),
                _AM_ACTION => '<a href="' . ZAR_URL . '/addons/system/index.php?fct=events&amp;opt=3&item=' . $event->getVar( 'id' ) . '"><img  src="' . ZAR_URL . '/images/small/delete.png" /></a>',
                _MD_AM_EVENTID => $event->getVar( 'id' )
                ) );
        $tlist->render();
        break;

    case 'index':
    default:
        zarilia_cp_header();
        $menu_handler->render( 0 );
        break;
}
zarilia_cp_footer();

?>