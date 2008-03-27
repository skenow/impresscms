<?php
/**
 * 
 * @version $Id: message_buddy.php,v 1.3 2007/04/21 09:41:14 catzwolf Exp $
 * @copyright 2006
 */
include_once "header.php";
$_PHP_SELF = "message_buddy.php";
/*
* 
*/
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'list_buddy' );

switch ( $op ) {
    /*
	* 
	*/
    case 'buddy_save':
        if ( !isset( $_SERVER['HTTP_REFERER'] ) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
            die( 'You are not allowed to do this method' );
        } 

        $buddy_id = zarilia_cleanRequestVars( $_REQUEST, 'buddy_id', 0 );
        $buddy_uid = zarilia_cleanRequestVars( $_REQUEST, 'buddy_uid', 0 );
        $buddy_name = zarilia_cleanRequestVars( $_REQUEST, 'buddy_name', '', XOBJ_DTYPE_TXTBOX );

        if ( $buddy_uid ) {
            $member_handler = &zarilia_gethandler( 'member' );
            $user = &$member_handler->getUser( $buddy_uid );
        } else if ( $buddy_name ) {
            $user = &$member_handler->getUserByName( $buddy_name );
        } else {
            $user = '';
        } 

        if ( !is_object( $user ) || !$user->isActive() ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_BUDDY_MEM_NOTEXIST );
        } 

        $buddy_obj = ( $buddy_id == 0 ) ? $buddy_handler->create() : $buddy_handler->get( $buddy_id );
        if ( $buddy_obj -> isNew() ) {
            $buddy_count = &$buddy_handler->getbuddycount( $user->getVar( 'uname' ) );
            if ( intval( $buddy_count ) > 0 ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_BUDDY_ALREADY );
            } 
        } 
        // }
        if ( is_object( $user ) && ( $user->getVar( 'uid' ) == $zariliaUser->getVar( 'uid' ) ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_BUDDY_OWNLIST );
        } 

        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_mod_header();
            $buddy_handler->buddy_header( '' );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_mod_footer();
            break;
        } 

        /*
		* save buddy here
		*/
        $buddy_obj->setVar( 'buddy_owner', $zariliaUser->getVar( 'uid' ) );
        $buddy_obj->setVar( 'buddy_name', $user->getVar( 'uname' ) );
        $buddy_obj->setVar( 'buddy_fname', $_REQUEST['buddy_fname'] );
        $buddy_obj->setVar( 'buddy_uid', $user->getVar( 'uid' ) );
        $buddy_obj->setVar( 'buddy_owner', $zariliaUser->getVar( 'uid' ) );
        $buddy_obj->setVar( 'buddy_allow', $_REQUEST['buddy_allow'] );
        $buddy_obj->setVar( 'buddy_desc', $_REQUEST['buddy_desc'] );
        $buddy_obj->setVar( 'buddy_date', time() );
        unset( $user );

        if ( $buddy_handler->insert( $buddy_obj, false ) ) {
            $redirect_mess = ( $buddy_obj->isNew() ) ? _PM_BUDDY_CREATED : _PM_BUDDY_MODIFIED;
        } 
        unset( $user, $buddy );

        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_mod_header();
            $buddy_handler->buddy_header( '' );
            ZariliaErrorHandler_HtmlError( 'Input Error' );
            zarilia_mod_footer();
        } else {
            redirect_header( $_PHP_SELF, 1, $redirect_mess );
        } 
        break;

    /*
	* 
	*/
    case 'delete_buddy':
        if ( !isset( $_SERVER['HTTP_REFERER'] ) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
            die( 'You are not allowed to do this method' );
        } 

        $buddy_id = zarilia_cleanRequestVars( $_REQUEST, 'buddy_id', 0 );
        $buddy_obj = $buddy_handler->get( $buddy_id );
        if ( !is_object( $buddy_obj ) || intval( $buddy_id ) <= 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_mod_footer();
            exit();
        } 

        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        if ( $ok ) {
            if ( !$buddy_handler->delete( $buddy_obj ) ) {
                zarilia_mod_header();
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_mod_footer();
                break;
            } else {
                redirect_header( $_PHP_SELF, 1, _DBUPDATED );
                break;
            } 
        } else {
            zarilia_mod_header();
            $buddy_handler->buddy_header( '' );
            zarilia_confirm( 
                array( 'op' => 'delete_buddy',
                    'buddy_id' => $buddy_obj->getVar( 'buddy_id' ),
                    'ok' => 1 
                    ), $_PHP_SELF, sprintf( _PM_BUDDY_DELETE_NOTICE, $buddy_obj->getVar( 'buddy_name' ) ) 
                );
            zarilia_mod_footer();
        } 
        break;

    /*
	* 
	*/
    case 'edit_buddy':
        $buddy_id = zarilia_cleanRequestVars( $_REQUEST, 'buddy_id', 0 );
        $buddy_obj = ( $buddy_id > 0 ) ? $buddy_handler->get( $buddy_id ) : $buddy_handler->create();

        zarilia_mod_header();
        $buddy_handler->buddy_header( $op );
        zarilia_show_buttons( "right", "files_button", "formbutton", array( "message_buddy.php" => _PM_BUDDY_LIST ) );

        if ( !is_object( $buddy_obj ) ) {
            // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $buddy_handler->getErrors() );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            $caption = ( !$buddy_obj->isNew() ) ? $caption = sprintf( _PM_BUDDY_MODIFY, $buddy_obj->getVar( 'buddy_name' ) ) : _PM_BUDDY_CREATE;
            $form = $buddy_obj->buddyForm( $caption );
            $form->display();
        } 
        zarilia_mod_footer();
        break;
    /*
	* 
	*/
    case 'list_buddy':
    default:
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'id', XOBJ_DTYPE_TXTBOX );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 20 );

        $uid = $zariliaUser->getVar( 'uid' );
        $buddy_arr = $buddy_handler->getBuddy( $uid, $nav );
        $buddy_count = $buddy_handler->getCount( $buddy_arr, $uid );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'buddy_id', '12%', 'center', true );
        $tlist->AddHeader( 'buddy_name', '200px', 'left', true );
        $tlist->AddHeader( 'buddy_allow', '17%', 'center', true );
        $tlist->AddHeader( 'buddy_date', '', 'center', true );
        $tlist->AddHeader( 'ACTION', '10%', 'center' );
        $tlist->setPrefix( '_PM_' );
        /*
		* 
		*/
        foreach ( $buddy_arr as $obj ) {
            $allpages['edit'] = '<a href="' . $_PHP_SELF . '?op=edit_buddy&amp;buddy_id=' . $obj->getVar( 'buddy_id' ) . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
            $allpages['edit'] .= '<a href="' . $_PHP_SELF . '?op=delete_buddy&amp;buddy_id=' . $obj->getVar( 'buddy_id' ) . '">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';

            $buddy_allow = $obj->getVar( 'buddy_allow' ) ? _ALLOW : _BLOCKED;
            $tlist->add( 
                array( $obj->getVar( 'buddy_id' ),
                    $obj->getVar( 'buddy_name' ),
                    $buddy_allow,
                    $obj->formatTimeStamp(),
                    $allpages['edit'] 
                    ) );
        } 
        /*
		* Display Output
		*/
        zarilia_mod_header();
        $buddy_handler->buddy_header( 'default' );
        zarilia_show_buttons( "right", "files_button", "formbutton", array( "message_buddy.php?op=edit_buddy" => _PM_BUDDY_ADD ) );
        $tlist->render();
        zarilia_pagnav( $buddy_count, $nav['limit'], $nav['start'], $form = 'start' );
        zarilia_mod_footer();
        break;
} // switch

?>