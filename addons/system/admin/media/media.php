<?php
// $Id: media.php,v 1.3 2007/04/21 09:42:31 catzwolf Exp $
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
switch ( strtolower( $op ) ) {
    case "media_edit":
        $media_id = zarilia_cleanRequestVars( $_REQUEST, 'media_id', 0 );
        $_media_obj = ( $media_id > 0 ) ? $media_handler->get( $media_id ) : $media_handler->create();
        if ( !$_media_obj ) {
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, getActionMenu( $media_id ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $form = $_media_obj->formEdit();
        break;

    case 'media_save':
        $media_id = zarilia_cleanRequestVars( $_REQUEST, 'media_id', 0 );
        $_media_obj = ( $media_id > 0 ) ? $media_handler->get( $media_id ) : $media_handler->create();
        /**
         */
        $media_handler->setUpload( $_media_obj );
        if ( !$_media_obj->isNew() ) {
            $_media_cat_obj = $media_cat_handler->get( intval( $_REQUEST['media_dirname'] ) );
            $_media_obj->setVar( 'media_dirname', $media_cat_obj->getVar( 'media_cdirname' ) );
        }
        if ( !$media_handler->insert( $_media_obj, false ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_media_obj->getVar( 'media_nicename' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            $GLOBALS['zariliaLogger']->sysRender();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list', 1, ( $_media_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'media_resize':
        $media_id = zarilia_cleanRequestVars( $_REQUEST, 'media_id', 0 );
        $_media_obj = $media_handler->get( $media_id );
        if ( !$_media_obj ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _AM_US_SECTIONNOTFOUND );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        /**
         */
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, getActionMenu( $media_id ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $form = $_media_obj->formResizeEdit();
        break;

    case 'media_doresize':
        break;

    case 'media_delete':
        $media_id = zarilia_cleanRequestVars( $_REQUEST, 'media_id', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $_media_obj = $media_handler->get( $media_id );
        if ( !is_object( $_media_obj ) ) {
            zarilia_cp_header();
            $menu_handler->render( 0 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 0 );
                zarilia_confirm(
                    array( 'op' => 'media_delete',
                        'media_id' => $_media_obj->getVar( 'media_id' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $_media_obj->getVar( 'media_nicename' ) . "." . $_media_obj->getVar( 'media_ext' ) )
                    );
                break;
            case 1:
                if ( !$media_handler->delete( $_media_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 0 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    $media_cat_obj = &$media_cat_handler->get( $_media_obj->getVar( 'media_cid' ) );
                    $_tempfile = ZAR_ROOT_PATH . '/' . $media_cat_obj->getVar( 'imgcat_dirname' ) . '/' . $_media_obj->getVar( 'media_name' );
                    if ( file_exists( $_tempfile ) ) {
                        @chmod( $_tempfile, 0777 );
                        @unlink( $_tempfile );
                    }
                    redirect_header( $addonversion['adminpath'] . '&amp;op=media_list', 1, _DBUPDATED );
                }
                break;
        } // switch
        break;

    /**
     */
    case "media_list":
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'media_id', XOBJ_DTYPE_TXTBOX );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $nav['search_by'] = zarilia_cleanRequestVars( $_REQUEST, 'search_by', '', XOBJ_DTYPE_TXTBOX );
        $nav['search_text'] = zarilia_cleanRequestVars( $_REQUEST, 'search_text', '', XOBJ_DTYPE_TXTBOX );

        if ( $media_cid > 0 ) {
            $_media_cat_obj = &$media_cat_handler->get( $media_cid );
            if ( !$_media_cat_obj ) {
                exit();
            }
            $_media_cdirname = $_media_cat_obj->getVar( 'media_cdirname' );
            $_media_path = ZAR_ROOT_PATH . '/' . $_media_cdirname;
            $_media_url = ZAR_URL . '/' . $_media_cdirname;
        } else {
            $_media_cat_obj = &$media_cat_handler->getList();
        }
        /*<!-- Start category loop --> */
        $media_obj = &$media_handler->getMediaObj( $nav, $media_cid );
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, getActionMenu( $media_cid ), _MD_AD_FILTER_BOX, getCatFilterMenu() );
        if ( $media_obj['count'] > 0 ) {
            $i = 0;
            $ret = '<table width="100%" cellspacing="2" cellpadding="1" align="center" ><tr>';
            foreach ( $media_obj['list'] as $obj ) {
                $_media_id = $obj->getVar( 'media_id' );
                $_media_name = $obj->getVar( 'media_name' );
                $_media_caption = $obj->getVar( 'media_caption' );
                $_media_mimetype = $obj->getVar( 'media_mimetype' );
                $_media_created = $obj->getVar( 'media_created' );
                $_media_filesize = $obj->getVar( 'media_filesize' );
                $_media_width = $_media_height = 0;
                $_media_dirname = $obj->getVar( 'media_dirname' );
                $_media_fullname = ZAR_ROOT_PATH . '/' . $_media_dirname . '/' . $_media_name;
                $_media_details = @getimagesize ( $_media_fullname );
                if ( !is_readable( $_media_fullname ) ) {
                    $_media_name = 'noimage.jpg';
                    $_media_cdirname = 'uploads';
                }
                if ( in_array( $_media_mimetype, array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/bmp' ) ) ) {
                    $_image_path = ZAR_URL . '/' . $obj->getVar( 'media_dirname' ) . '/' . $_media_name;
                    echo $_image_path;
                    if ( $_media_details[0] >= 138 ) {
                        $_media_width = 138;
                        $_media_height = 100;
                        require_once ZAR_ROOT_PATH . '/class/class.thumbnail.php';
                        $_thumb_image = new ZariliaThumbs( $_media_name, $_media_cdirname );
                        if ( is_object( $_thumb_image ) ) {
                            $_thumb_image->setUseThumbs( 1 );
                            $_thumb_image->setLibType( 1 );
                            $_image_array = $_thumb_image->do_thumb( $_media_width, $_media_height, 100, true, false, true );
                                print_r_html( $_image_array );
                            if ( $_image_array ) {
                                print_r_html( $_image_array );
                                $_image_path = $_image_array['imgTitle'];
                            }
                        }
                    } else {
                        $_media_width = ( isset( $_media_details[0] ) ) ? $_media_details[0] : '138';
                        $_media_height = ( isset( $_media_details[1] ) ) ? $_media_details[1] : '100';
                    }
                } else {
                    // $_image = $media_handler->getMediaPlayer( $_media_name, $_media_cdirname );
                    $mimetype_handler = &zarilia_gethandler( 'mimetype' );
                    $_image_path = $mimetype_handler->mimetypeImage( $_media_name );
                    $_media_width = 48;
                    $_media_height = 48;
                }
                $_image = '<img src="' . $_image_path . '" width="' . $_media_width . '" height="' . $_media_height . '" alt="" />';
                $_media_url_direct = ZAR_URL . '/index.php?page_type=media&amp;media_id=' . $_media_id;
                $display_image = '<a href="' . $_media_url_direct . '" target="blank">' . $_image . '</a>';
                /**
                 */
                $content_array = array( 'Real Name' => $obj->getVar( 'media_nicename' ), 'File Size' => $_media_filesize, 'File Type' => $_media_mimetype, 'Image Width' => $_media_details[0], 'Image height' => $_media_details[1], 'Last Changed' => $_media_created );
                $content = "<table width='100%' cellpadding='0' cellspacing='1'>";
                foreach( $content_array as $k => $v ) {
                    if ( $k == 'Image Width' || $k == 'Image height' ) {
                        if ( isset( $v ) ) {
                            $content .= "<tr><td class='head'>$k:</td><td class='even'>" . $v . "</td></tr>";
                        }
                    } else {
                        $content .= "<tr><td class='head'>$k:</td><td class='even'>" . $v . "</td></tr>";
                    }
                }
                $content .= "</table>";
                $ret .= '
				  <td align="center" valign="top">
					<table border="0" cellspacing="1" cellpadding="2" class="outer">
					  <tr style="text-align: left;">
					   <td class="head">' . $obj->getVar( 'media_nicename' ) . '</td>
					  </tr>
					  <tr>
					   <td style="text-align: center;" class="outer" width="200" height="150">
					    <span title="header=[' . $_media_name . '] body=[' . @$content . ']">
					     <div>' . $display_image . '<br />' . $_media_caption . '</div>
					    </span>
					   </td>
					  </tr>
					  <tr  style="text-align: center;">
					   <td class="head">
					      <a href="index.php?fct=media&amp;op=media_edit&amp;media_id=' . $_media_id . '">' . zarilia_img_show( "edit", _EDIT ) . '</a><span>
					      <a href="index.php?fct=media&amp;op=media_delete&amp;media_id=' . $_media_id . '">' . zarilia_img_show( "delete", _DELETE ) . '</a><span>
					      <a href="' . ZAR_URL . '/index.php?page_type=media&amp;media_id=' . $_media_id . '&amp;media_direct=1">' . zarilia_img_show( "download", _DOWNLOAD ) . '</a><span>
						</td>
					  </tr>
					</table>
			       </td>';
                if ( ( $i % 4 ) >= 3 ) {
                    $ret .= "</tr><tr>";
                }
                $i++;
            }
            $ret .= '</tr></table><br />';
            echo $ret;
            zarilia_pagnav( $media_obj['count'], $nav['limit'], $nav['start'], 'start', 1, 'index.php?fct=media&op=media_list&media_cid=' . $media_cid );
        } else {
            echo "<h4>" . _MA_AD_MEDIA_NOATTACHMENTS . "</h4>";
        }
        break;
}

?>