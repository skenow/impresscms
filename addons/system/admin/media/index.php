<?php
// $Id: index.php,v 1.2 2007/04/21 09:42:31 catzwolf Exp $
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
require_once "admin_menu.php";
/**
 *
 * @version $Id: index.php,v 1.2 2007/04/21 09:42:31 catzwolf Exp $
 * @copyright 2006
 */
require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
require_once ZAR_ROOT_PATH . '/class/class.permissions.php';

$media_handler = &zarilia_gethandler( 'media' );
$media_cat_handler = &zarilia_gethandler( 'mediacategory' );
$groupperm_handler = &zarilia_gethandler( 'groupperm' );

$zariliaMediaConfig = &$config_handler->getConfigsByCat( ZAR_CONF_MEDIA );
$media_id = zarilia_cleanRequestVars( $_REQUEST, 'media_id', 0 );
$media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
switch ( $op ) {
    case 'cat_help':
    case 'cat_about':
    case 'cat_list':
    case 'cat_edit':
    case 'cat_delete':
    case 'cat_save':
    case 'cat_index':
    case 'cat_updateall':
    case 'cat_deleteall':
    case 'cat_cloneall':
    case 'cat_clone':
        include_once 'category.php';
        break;
    case 'media_edit';
    case 'media_list';
    case 'media_delete';
    case 'media_save';
        include_once 'media.php';
        break;
    case 'uploader';
    case 'upload';
    case 'addfile';
    case 'batchaddfile';
        include_once 'upload.php';
        break;
    case 'index';
    default;
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, getActionMenu( $media_cid ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
}
zarilia_cp_footer();
exit();

/**
 * getActionMenu()
 *
 * @param mixed $id
 * @return
 */
function getActionMenu( $id = 0 ) {
    global $addonversion, $media_cat_handler;
	$menu_array = array( $addonversion['adminpath'] . '&amp;op=cat_edit' => _MA_AD_MEDIA_CREATE );
    $count = $media_cat_handler->getCount();
    if ( $count > 0 ) {
        $menu = array( $addonversion['adminpath'] . '&amp;op=uploader' => _MD_AM_ADD_MEDIA, $addonversion['adminpath'] . '&amp;op=media_list' => _MD_AM_LIST_MEDIA );
        $menu_array = array_merge( $menu, $menu_array );
        ksort( $menu_array );
    }
    return $menu_array;
}

function getCatFilterMenu() {
    global $addonversion, $media_cat_handler, $media_cid, $list_array, $nav;
    $aSearchBy = array( 'image_mimetype' => _MD_AM_SHOW_MIMETYPE, 'image_ext' => _MD_AM_SHOW_EXT );
    $media_cat_array = $media_cat_handler->getList();
    $form_action = $addonversion['adminpath'] . '&amp;op=media_list';
    $form = "
		 <div class='sidetitle'>" . _MD_AM_DISPLAY_MEDIACAT . "</div>
		 <div class='sidecontent'> " . zarilia_getSelection( $media_cat_array, $media_cid, "media_cid", 1, 1, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=media_list&amp;media_cid='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=media_list&amp;media_cid=" . $media_cid . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <form op='" . $form_action . "' method='post'>
		  <div class='sidetitle'>" . _MD_AM_SEARCH_MEDIA . "</div>
		  <div class='sidecontent'> " . zarilia_getSelection( $aSearchBy, $nav['search_by'], "search_by", 1, 0 , false, "", "", 0, false ) . "</div>
		  <div class='sidetitle'>" . _MD_AM_SEARCH_TEXT . "</div>
		  <div class='sidecontent'>
		  	<input type='text' name='search_text' id='search_text' value='' />
		  	<input type='submit' class='formbutton' name='mime_search' id='mime_search' value='" . _SUBMIT . "' />
		  </div>
		 </form>";
    return $form;
}

?>