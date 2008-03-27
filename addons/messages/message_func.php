<?php
include_once "header.php";
message_check_user();
/*
* Check to see if user is logged in, if not redirect to index
*/
#
#$allowed !== false;
#if ( is_object( $zariliaUser ) ) {
#    if ( array_intersect( $zariliaUser->getGroups(), $zariliaConfig['message_okgrp'] ) || ZAR_GROUP_ADMIN == $group ) {
#        $allowed = true;
#    } 
#} 
#$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'inbox' );
#if ( empty( $op ) || !in_array( $op, array( 'delsent', 'stoptrackin', 'trash', 'del', 'delall3', 'saved', 'testdata', 'read', 'unread', 'testdata2' ) ) ) {
#	$allowed = false;
#} 
#if ( $allowed !== true ) {
#    include ZAR_ROOT_PATH . "/header.php";
#    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOPERMISSION );
#	$GLOBALS['zariliaLogger']->sysRender();
#    include ZAR_ROOT_PATH . "/footer.php";
#    exit();
#}

$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'inbox' );
$uid = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0, XOBJ_DTYPE_INT );
switch ( strtolower( $op ) ) {
    case 'testdata':
        $pm_handler -> create_test_data();
        break;

    case 'testdata2':
        $msgsent_handler = &zarilia_gethandler( 'messagesent' );
        if ( !$msgsent_handler -> create_test_data() ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
			$GLOBALS['zariliaLogger']->sysRender();
            zarilia_mod_footer();
            break;
        } 
        break;

    case 'stoptrackin':
        $this_id = $pm_handler -> do_id( $_REQUEST['id'] );
        if ( count( $this_id ) == 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOTHINGSELECTED );
			$GLOBALS['zariliaLogger']->sysRender();
            zarilia_mod_footer();
            break;
        } else {
            foreach ( $this_id as $k => $v ) {
                $message_obj = $pm_handler -> get( $v );
                $message_obj -> setVar( 'track', 0 );
                if ( $pm_handler -> insert( $message_obj, true ) ) {
                    $return_message = _PM_SAVED;
                    unset( $message_obj );
                } else {
                    zarilia_mod_header();
            		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
					$GLOBALS['zariliaLogger']->sysRender();
                    zarilia_mod_footer();
                    break;
                } 
            } 
        } 
        redirect_header( "index.php", 1, $return_message );
        break;

    case 'sent':
        $msgsent_handler = &zarilia_gethandler( 'messagesent' );
        $this_id = $msgsent_handler -> do_id( $_REQUEST['id'] );
        if ( count( $this_id ) == 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOTHINGSELECTED );
			$GLOBALS['zariliaLogger']->sysRender();
            zarilia_mod_footer();
            break;
        } else {
            foreach ( $this_id as $k => $v ) {
                $message_obj = $msgsent_handler -> get( $v );
                $message_obj -> setVar( 'msg', 1 );
                $message_obj -> setVar( 'read_date', time() );
                if ( $msgsent_handler -> insert( $message_obj, true ) ) {
                    unset( $message_obj );
                } else {
                    zarilia_mod_header();
            		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
					$GLOBALS['zariliaLogger']->sysRender();
                    zarilia_mod_footer();
                    exit();
                } 
            } 
        } 
        redirect_header( "index.php", 1, _DBUPDATED );
        break;

    case 'read':
        $this_id = $pm_handler -> do_id( $_REQUEST['id'] );
        if ( count( $this_id ) == 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOTHINGSELECTED );
            zarilia_mod_footer();
            break;
        } else {
            foreach ( $this_id as $k => $v ) {
                $message_obj = $pm_handler -> get( $v );
                $message_obj -> setVar( 'msg', 1 );
                $message_obj -> setVar( 'read_date', time() );
                if ( $pm_handler -> insert( $message_obj, true ) ) {
                    unset( $message_obj );
                } else {
                    zarilia_mod_header();
            		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
					$GLOBALS['zariliaLogger']->sysRender();
                    zarilia_mod_footer();
                    exit();
                } 
            } 
        } 
        redirect_header( "index.php", 1, _PM_READ );
        break;

    case 'unread':
        $this_id = $pm_handler -> do_id( $_REQUEST['id'] );
        if ( count( $this_id ) == 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOTHINGSELECTED );
            zarilia_mod_footer();
            break;
        } else {
            foreach ( $this_id as $k => $v ) {
                $message_obj = $pm_handler -> get( $v );
                $message_obj -> setVar( 'msg', 0 );
                $message_obj -> setVar( 'read_date', 0 );
                if ( $pm_handler -> insert( $message_obj, true ) ) {
                    $return_message = _PM_UNREAD;
                    unset( $message_obj );
                } else {
                    zarilia_mod_header();
            		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
					$GLOBALS['zariliaLogger']->sysRender();
                    zarilia_mod_footer();
                    exit();
                } 
            } 
        } 
        redirect_header( "index.php", 1, $return_message );
        break;

    case 'trash':
        $this_id = $pm_handler -> do_id( $_REQUEST['id'] );
        if ( count( $this_id ) == 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOTHINGSELECTED );
            zarilia_mod_footer();
            break;
        } else {
            foreach ( $this_id as $k => $v ) {
                $message_obj = $pm_handler -> get( $v );
                $message_obj -> setVar( 'is_trash', 1 );
                $message_obj -> setVar( 'is_saved', 0 );
                if ( $pm_handler -> insert( $message_obj, true ) ) {
                    $return_message = _PM_TRASHED;
                    unset( $message_obj );
                } else {
                    zarilia_mod_header();
            		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
					$GLOBALS['zariliaLogger']->sysRender();
                    zarilia_mod_footer();
                    break;
                } 
            } 
        } 
        redirect_header( "index.php", 1, $return_message );
        break;

    case 'saved':
        $this_id = $pm_handler -> do_id( $_REQUEST['id'] );
        if ( count( $this_id ) == 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOTHINGSELECTED );
            zarilia_mod_footer();
            break;
        } else {
            foreach ( $this_id as $k => $v ) {
                $message_obj = $pm_handler -> get( $v );
                $message_obj -> setVar( 'is_trash', 0 );
                $message_obj -> setVar( 'is_saved', 1 );
                if ( $pm_handler -> insert( $message_obj, true ) ) {
                    unset( $message_obj );
                } else {
                    zarilia_mod_header();
            		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
					$GLOBALS['zariliaLogger']->sysRender();
                    zarilia_mod_footer();
                    break;
                } 
            } 
        } 
        redirect_header( "index.php", 1, _PM_SAVED );
        break;

    case 'del':
    case 'delsent':
        $this_id = $pm_handler -> do_id( $_REQUEST['id'] );
        if ( count( $this_id ) == 0 ) {
            zarilia_mod_header();
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _PM_NOTHINGSELECTED );
            zarilia_mod_footer();
            exit();
        } else {
           	$_handler = ( $op == 'del' ) ? 'pm_handler': 'msgsent_handler';		
			foreach ( $this_id as $k => $v ) {
                $message_obj = $$_handler -> get( $v );
                if ( $$_handler-> delete( $message_obj ) ) {
                    unset( $message_obj );
                } else {
                    zarilia_mod_header();
            		$GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $msgsent_handler -> getErrors() );
					$GLOBALS['zariliaLogger']->sysRender();
                    zarilia_mod_footer();
                    exit();
                } 
            } 
        }
        redirect_header( "index.php", 1, _PM_DELETED );
        break;

    case 'delall':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        if ( $ok == 1 ) {
            $uid = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0, XOBJ_DTYPE_INT );
            $criteria = new CriteriaCompo( new Criteria( 'to_userid', $uid ) );
            $pm_handler -> deleteAll( $criteria );
            redirect_header( "index.php", 1, _DBUPDATED );
            break;
        } else {
            zarilia_mod_header();
            echo"<div style='text-align: left; padding-bottom: 12px;'>" . _AM_EDITBNR . "</div>";
            zarilia_confirm( array( 'op' => 'delall', 'uid' => $zariliaUser -> getVar( 'uid' ), 'ok' => 1 ), 'message_func.php', _AM_SUREDELE );
            zarilia_mod_footer();
        } 
        break;
} // switch

?>