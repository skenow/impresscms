<?php
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
$zariliaOption['pagetype'] = 'media';

include './mainfile.php';
$target = zarilia_cleanRequestVars( $_REQUEST, 'target', '', XOBJ_DTYPE_TXTBOX );
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'list', XOBJ_DTYPE_TXTBOX );
if ( !isset( $target ) ) {
    exit();
}

switch ( $op ) {
    case 'upload':
        $media_handler = &zarilia_gethandler( 'media' );
        $media_cat_handler = &zarilia_gethandler( 'mediacategory' );

        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0, XOBJ_DTYPE_TXTBOX );
        $_media_cat_obj = $media_cat_handler->get( $media_cid );
        if ( $_media_cat_obj ) {
            $mediaperm_handler = &zarilia_gethandler( 'groupperm' );
            $groups = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
            if ( !$mediaperm_handler->checkRight( 'mediacategory_write', $media_cid, $zariliaUser->getGroups() ) ) {
                $error = true;
            }
        } else {
            zarilia_header( false );
            echo '</head><body>';
            echo '<div style="text-align:center;"><input value="' . _BACK . '" type="button" onclick="javascript:history.go(-1);" /></div>';
            zarilia_footer();
            // error here
            exit();
        }
        /**
         * start of form
         */
        require_once ZAR_ROOT_PATH . '/class/template.php';
        $zariliaTpl = new ZariliaTpl();
        $zariliaTpl->addLink( ZAR_CSS_URL );
        $zariliaTpl->assign( 'zarilia_sitename', $zariliaConfig['sitename'] );
        $zariliaTpl->assign( 'maxsize', $_media_cat_obj->getVar( 'media_cmaxsize', 's' ) );
        $zariliaTpl->assign( 'maxwidth', $_media_cat_obj->getVar( 'media_cmaxwidth', 's' ) );
        $zariliaTpl->assign( 'maxheight', $_media_cat_obj->getVar( 'media_cmaxwidth', 's' ) );

        $_media_obj = $media_handler->create();
        $form = $_media_obj->formManagerEdit( $zariliaTpl );
        $zariliaTpl->display( 'db:system_mediaupload.html' );
        break;

    case 'save':
        $media_handler = &zarilia_gethandler( 'media' );
        $media_cat_handler = &zarilia_gethandler( 'mediacategory' );
        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $target = zarilia_cleanRequestVars( $_REQUEST, 'target', 0 );
        $media_cat_obj = $media_cat_handler->get( $media_cid );
        if ( !is_object( $media_cat_obj ) || !$media_cat_obj->getVar( 'media_cdirname' ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
        } else {
            $mediaperm_handler = &zarilia_gethandler( 'groupperm' );
            $groups = ( is_object( $zariliaUser ) ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
            if ( !$mediaperm_handler->checkRight( 'mediacategory_write', $media_cid, $zariliaUser->getGroups() ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
            } else {
                $_upload_path = ZAR_ROOT_PATH . DIRECTORY_SEPARATOR . $media_cat_obj->getVar( 'media_cdirname' );
                if ( !is_readable( $_upload_path ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
                }
            }
        }

        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_header( false );
            echo '</head><body>';
            $GLOBALS['zariliaLogger']->sysRender();
            echo '<div style="text-align:center;"><input value="' . _BACK . '" type="button" onclick="javascript:history.go(-1);" /></div>';
            zarilia_footer();
            exit();
        }

        $media_handler->uploadFile( $_upload_path );
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_header( false );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_footer();
            exit();
        } else {
            header( 'location: mediamanager.php?op=saved&amp;media_cid=' . $media_cid . '&target=' . $target );
        }
        break;

    case 'saved':
        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        zarilia_header( false );
        echo '</head><body>
		<h4 style="text-align:center;">' . _MA_AD_MEDIA_UPDATED . '</h4>
		<div style="text-align:center;"><a href="mediamanager.php?op=list&amp;media_cid=' . $media_cid . '&target=' . $target . '">' . _MA_AD_MEDIA_RETURN . '</a></div>';
        zarilia_footer();
        break;

    case 'list':
    default:
        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );

        require_once ZAR_ROOT_PATH . '/class/template.php';
        $media_handler = &zarilia_gethandler( 'media' );
        $media_cat_handler = &zarilia_gethandler( 'mediacategory' );

        $_media_cat_obj = $media_cat_handler->get( $media_cid );
        if ( $_media_cat_obj ) {
            $_media_cdirname = $_media_cat_obj->getVar( 'media_cdirname' );
            $_media_path = ZAR_ROOT_PATH . '/' . $_media_cdirname;
            $_media_url = ZAR_URL . '/' . $_media_cdirname;
        }

        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'media_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        $media_cats = $media_cat_handler->getList();
        $_selection_box = zarilia_getSelection( $media_cats, $media_cid, "media_cid", 1, true, false, 'Select Category', "onchange=\"location='" . ZAR_URL . "/mediamanager.php?target=" . $target . "&amp;media_cid='+this.options[this.selectedIndex].value\"" , 0, false, '120' );
        $zariliaTpl = new ZariliaTpl();
        $zariliaTpl->addLink( ZAR_CSS_URL );
        $zariliaTpl->assign( 'selection_box', $_selection_box );
        $zariliaTpl->assign( 'show_cat', count( $media_cats ) > 0 ? true : false );

        $media_obj = $media_handler->getMediaObj( $nav, $media_cid );
        if ( $media_obj['count'] > 0 ) {
            foreach ( $media_obj['list'] as $obj ) {
                $_media_id = $obj->getVar( 'media_id' );
                $_media_name = $obj->getVar( 'media_name' );
                $_media_caption = $obj->getVar( "media_caption" );
                $_media_mimetype = $obj->getVar( "media_mimetype" );
                $_media_created = $obj->formatTimeStamp( "media_created" );
                $_media_filesize = $obj->getVar( "media_filesize" );
                $_media_nicename = $obj->getVar( "media_nicename" ) . "." . $obj->getVar( "media_ext" );
                $_media_width = 0;
                $_media_height = 0;

                $_media_fullname = $_media_path . '/' . $_media_name;
                $_media_details = @getimagesize ( $_media_fullname );

                if ( !is_readable( $_media_fullname ) ) {
                    $_media_name = 'noimage.jpg';
                    $_media_cdirname = 'uploads';
                }

                $lcode = '[img capt=$_media_caption]' . $_media_url . '/' . $_media_name . '[/img]';
                $code = '[imgc capt=$_media_caption]' . $_media_url . '/' . $_media_name . '[/imgc]';
                $rcode = '[imgr capt=$_media_caption]' . $_media_url . '/' . $_media_name . '[/imgr]';

                if ( in_array( $_media_mimetype, array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png', 'image/bmp' ) ) ) {
                    $_media = 0;
                    $_image_path = $_media_url . '/' . $_media_name;
                    if ( $_media_details[0] >= 138 ) {
                        $_media_width = 138;
                        $_media_height = 100;
                        require_once ZAR_ROOT_PATH . '/class/class.thumbnail.php';
                        $_thumb_image = new ZariliaThumbs( $_media_name, $_media_cdirname );
                        if ( is_object( $_thumb_image ) ) {
                            $_thumb_image->setUseThumbs( 1 );
                            $_thumb_image->setLibType( 1 );
                            $_image_array = $_thumb_image->do_thumb( $_media_width, $_media_height, 100, true, false, true );
                            if ( $_image_array ) {
                                $_image_path = $_image_array['imgTitle'];
                            }
                        }
                    } else {
                        $_media_width = ( isset( $_media_details[0] ) ) ? $_media_details[0] : '138';
                        $_media_height = ( isset( $_media_details[1] ) ) ? $_media_details[1] : '100';
                    }
                } else {
                    $mimetype_handler = &zarilia_gethandler( 'mimetype' );
                    $_image_path = $mimetype_handler->mimetypeImage( $_media_name );
                    $code = '[media]' . $_media_url . '/' . $_media_name . '[/media]';
                    $_media = 1;
                    $_media_width = 45;
                    $_media_height = 45;
                }

                $_image = '<img src="' . $_image_path . '" width="' . $_media_width . '" height="' . $_media_height . '" alt="" />';
                $_media_url_direct = ZAR_URL . '/index.php?page_type=media&amp;media_id=' . $_media_id;
                $display_image = '<a href="' . $_media_url_direct . '" target="blank">' . $_image . '</a>';

                $zariliaTpl->append( 'images',
                    array( 'id' => $_media_id,
                        'nicename' => $_media_nicename,
                        'realname' => $_media_name,
                        'mimetype' => $_media_mimetype,
                        'size' => $_media_filesize,
                        'width' => $_media_width,
                        'height' => $_media_height,
                        'display_image' => $display_image,
                        'src' => $display_image,
                        'lxcode' => $lcode,
                        'xcode' => $code,
                        'rxcode' => $rcode,
                        'media' => $_media
                        )
                    );
            }
        }
        $media_pagenav = zarilia_pagnav( $media_obj['count'], $nav['limit'], $nav['start'], 'start', 1, 'mediamanager.php?target=' . $target . '&amp;media_cid=' . $media_cid, true );
        $zariliaTpl->assign( 'media_cid', $media_cid );
        $zariliaTpl->assign( 'target', $target );
        $zariliaTpl->assign( 'media_total', $media_obj['count'] );
        $zariliaTpl->assign( 'pagenav', $media_pagenav );
        $zariliaTpl->display( 'db:system_mediamanager.html' );
        break;
} // switch

?>