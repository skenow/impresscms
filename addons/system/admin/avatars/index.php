<?php
// $Id: index.php,v 1.4 2007/05/05 11:10:07 catzwolf Exp $
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( 'Access Denied' );
}
require_once 'admin_menu.php';

$_callback = &zarilia_gethandler( 'avatar' );
$do_callback = &ZariliaCallback::getSingleton();
$do_callback->setCallback( $_callback );
switch ( $op ) {
    case 'maintenace':
    case 'help':
    case 'about':
    case 'edit':
    case 'delete':
    case 'cloned':
        $do_callback->setmenu( 2 );
        call_user_func( array( $do_callback, $op ) );
        break;
    /**
     */
    case 'updateall':
        $do_callback->setmenu( 2 );
        $do_callback->updateAll( array( 'avatar_name', 'avatar_weight', 'avatar_display' ) );
        break;
    /**
     */
    case 'cloneall':
    case 'deleteall':
        $do_callback->setmenu( 2 );
        $do_callback->cdall( $op );
        break;
    /**
     */
    case 'save':
        $_function = ( $do_callback->getId( false ) > 0 ) ? 'get': 'create';
        $_obj = call_user_func( array( $_callback, $_function ), $do_callback->getId( true ) );
        if ( !$_obj ) {
        }

        /**
         */
        $isUpload = $_callback->setUpload( $_obj ) ;
        if ( $isUpload && call_user_func( array( $_callback, 'insert' ), $_obj, true ) ) {
            redirect_header( $_SERVER['HTTP_REFERER'], 1, ( $_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        } else {
            zarilia_cp_header();
            $GLOBALS['menu_handler']->render( 2 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;
    /**
     */
    case 'batch':
        zarilia_cp_header();
        $menu_handler->render( 3 );
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_batch.php" ) ) {
            include_once ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_batch.php";
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;
    /**
     * This really needs to be done again the hardcoded langiage is not acceptable FIXME!!!
     */
    case 'batchsave':
        $config_handler = &zarilia_gethandler( 'config' );
        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
        $err = array();
        $_obj_image = zarilia_cleanRequestVars( $_REQUEST, 'avatar_images', array() ); //This is an array not a string
        $_obj_weight = zarilia_cleanRequestVars( $_REQUEST, 'avatar_weight', 0 );
        $_obj_autoweight = zarilia_cleanRequestVars( $_REQUEST, 'avatar_autoweight', 0 );
        $_obj_display = zarilia_cleanRequestVars( $_REQUEST, 'avatar_display', 0 );
        $_obj_remove = zarilia_cleanRequestVars( $_REQUEST, 'avatar_remove', 0 );
        /**
         */
        zarilia_cp_header();
        $menu_handler->render( 3 );
        if ( !count( $_obj_image ) ) {
            $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, "No files selected for upload, please go back and try again!" );
            zarilia_cp_footer();
            exit();
        }
        foreach( $_obj_image as $image ) {
            /*Prepare image for save*/
            $image_details = $_callback->processFile( $image );
            /*Do file check and then save to database and upload to new directory*/
            /**
             */
            if ( $_obj_remove == 1 ) {
                !unlink( $image_details['filenamepath'] );
                echo "<div><b>Notice:</b> File {$image_details['filenamepath']} has been deleted successfully.</div>";
                continue;
            }

            if ( !is_dir( ZAR_UPLOAD_PATH ) || !is_writable( ZAR_UPLOAD_PATH ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG_UPLOADPATH, ZAR_UPLOAD_PATH ) );
                continue;
            }

            /*Check file is actually there before transfer*/
            if ( !is_file( $image_details['filenamepath'] ) || !is_writable( $image_details['filenamepath'] ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG_UPLOADIMAGE, $image_details['filename'] ) );
                continue;
            }

            /*Check file size is over allowed size*/
            if ( $image_details['imagesize'] > $zariliaConfigUser['avatar_maxsize'] ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG_UPLOADSIZE, $image_details['filename'], $image_details['imagesize'], $zariliaConfigUser['avatar_maxsize'] ) );
                continue;
            }

            /*Check file size is over allowed size*/
            if ( !array( $image_details['imagedetails'] ) || ( $image_details['imagedetails'][0] > $zariliaConfigUser['avatar_width'] || $image_details['imagedetails'][1] > $zariliaConfigUser['avatar_height'] ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG_UPLOADDEM, $image_details['filename'], $image_details['imagedetails'][0], $zariliaConfigUser['avatar_width'], $image_details['imagedetails'][1], $zariliaConfigUser['avatar_height'] ) );
                continue;
            }

            /*Check file size is over allowed size*/
            $allowedmimetypes = array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' );
            if ( $image_details['filemimetype'] == false || ( count( $allowedmimetypes ) > 0 && !in_array( $image_details['filemimetype'], $allowedmimetypes ) ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG_UPLOADMIME, $image_details['filename'], $image_details['filemimetype'] ) );
                continue;
            }

            /*rename requires php 4.3.3*/
            if ( !rename( ZAR_CUPLOAD_PATH . DIRECTORY_SEPARATOR . $image, ZAR_UPLOAD_PATH . DIRECTORY_SEPARATOR . $image_details['newfilename'] ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG_UPLOADCOPY, $image_details['filename'] ) );
                continue;
            } else {
                touch( ZAR_UPLOAD_PATH . DIRECTORY_SEPARATOR . $image_details['newfilename'] );
                /* Create Avatar and insert into database */
                $_obj = &$_callback->create();
                $_obj->setVar( 'avatar_file', $image_details['newfilename'] );
                $_obj->setVar( 'avatar_name', $image_details['name'] );
                $_obj->setVar( 'avatar_mimetype', $image_details['filemimetype'] );
                $_obj->setVar( 'avatar_display', empty( $_obj_display ) ? 0 : 1 );
                if ( $_obj_autoweight == 1 ) {
                    $_obj->setVar( 'avatar_weight', $i + 1 );
                } else {
                    $_obj->setVar( 'avatar_weight', $_obj_weight );
                }
                $_obj->setVar( 'avatar_type', 'S' );
                $_obj->setVar( 'avatar_created', time() );
                if ( !call_user_func( array( $_callback, 'insert' ), $_obj, true ) ) {
                    $GLOBALS['zariliaLogger']->setSysError();
                    continue;
                } else {
                }
            }
        }
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        break;
    case 'list':
        require ZAR_ROOT_PATH . '/class/class.tlist.php';
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'avatar_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $type = zarilia_cleanRequestVars( $_REQUEST, 'type', 'S' );
        $type_array = array( 'C' => _MD_AD_CSTAVATARS, 'S' => _MD_AD_SYSAVATARS );
        $form = "
		 <div class='sidetitle'>" . _MD_AD_DISPLAY_BOX . "</div>
		 <div class='sidecontent'>" . zarilia_getSelection( $type_array, $type, "type", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;limit=" . $nav['limit'] . "&amp;type='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>
		 <div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div>
		 <div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list&amp;type=" . $type . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";
        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_EAVATAR_CREATE, $addonversion['adminpath'] . "&amp;op=batch" => _MA_AD_EAVATAR_BATCH ),
            _MD_AD_FILTER_BOX, $form
            );
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'avatar_id', '5', 'center', false );
        $tlist->AddHeader( 'avatar_name', '', 'left', true );
        $tlist->AddHeader( 'avatar_filename', '10%', 'center', true );
        $tlist->AddHeader( 'avatar_mimetype', '', 'center', true );
        $date = ( $type == 'C' ) ? 'avatar_uploaded' : 'avatar_created';
        $tlist->AddHeader( $date, '', 'center', true );
        $tlist->AddHeader( ( $type == 'C' ) ? 'avatar_uid' : 'avatar_up', '', 'center', true );
        $tlist->AddHeader( 'avatar_weight', '', 'center', true );
        $tlist->AddHeader( 'avatar_display', '', 'center', 1 );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'action', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'avatars' );
        $tlist->addFooter( call_user_func( array( $_callback, 'setSubmit' ), $fct ) );
        $tlist->setPath( 'op=' . $op );
        $button = array( 'edit', 'delete', 'cloned' );
        $_obj = &call_user_func( array( $_callback, 'getAvatarObj' ), $nav, $type );
        foreach ( $_obj['list'] as $obj ) {
            $_id = $obj->getVar( 'avatar_id' );
            $tlist->addHidden( $_id, 'value_id' );
            $tlist->add(
                array( $_id,
                    $obj->getTextbox( 'avatar_id', 'avatar_name', '35' ),
                    $obj->ShowAvatar(),
                    $obj->getVar( 'avatar_mimetype' ),
                    $obj->getVar( 'avatar_created' ),
                    $obj->getLinkedUserName(),
                    $obj->getTextbox( 'avatar_id', 'avatar_weight', '5' ),
                    $obj->getYesNobox( 'avatar_id', 'avatar_display' ),
                    $obj->getCheckbox( 'avatar_id' ),
                    zarilia_cp_icons( $button, 'avatar_id', $_id )
                    )
                );
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&type=' . $type . '&amp;limit=' . $nav['limit'] );
        break;
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_EAVATAR_CREATE, $addonversion['adminpath'] . "&amp;op=batch" => _MA_AD_EAVATAR_BATCH ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        $savatar_count = $_callback->getCount( new Criteria( 'avatar_type', 'S' ) );
        $cavatar_count = $_callback->getCount( new Criteria( 'avatar_type', 'C' ) );
        echo '<ul><li>' . _MD_AD_SYSAVATARS . ' (' . sprintf( _NUMIMAGES, '<b>' . $savatar_count . '</b>' ) . ') [<a href="' . $addonversion['adminpath'] . '&amp;op=list&amp;type=S">' . _LIST . '</a>]</li><li>' . _MD_AD_CSTAVATARS . ' (' . sprintf( _NUMIMAGES, '<b>' . $cavatar_count . '</b>' ) . ') [<a href="' . $addonversion['adminpath'] . '&amp;op=list&amp;type=C">' . _LIST . '</a>]</li></ul>';
        break;
} // switch
zarilia_cp_footer();

?>