<?php
/**
 *
 * @version $Id: upload.php,v 1.3 2007/04/21 09:42:31 catzwolf Exp $
 * @copyright 2007
 */

switch ( $op ) {
    case "uploader":
        $_SESSION['uploadedtimes'] = 0;
        if ( !$media_cat_handler->getCount() ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_MEDIA_NOCATEGORIES );
        }

        $cache_path = ZAR_UPLOAD_PATH . '/cache';
        if ( !zarilia_get_dir_status( $cache_path ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MA_AD_MEDIA_CACHEDIR, $cache_path ) );
        }

        /**
         * Show errors
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 3 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        require_once ZAR_ROOT_PATH . '/class/class.menubar.php';
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
        $tabbar = new ZariliaTabMenu( $opt );
        $url = $addonversion['adminpath'] . "&op=uploader";
        $tabbar->addTabArray( array( _MA_AD_MEDIA_SINGLEFILE => $url, _MA_AD_MEDIA_MULTIFILE => $url ) );

        zarilia_cp_header();
        $menu_handler->render( 3 );
        echo "
		 <div style='padding-bottom: 12px;'><strong>" . _MA_AD_MEDIA_UPLOADPATH . ":</strong> " . $cache_path . "</div>
		 <div style='padding-bottom: 12px;'>" . _MA_AD_MEDIA_HOWTOUSE . "</div><br />";
        $tabbar->renderStart();
        $form = new ZariliaThemeForm( _MA_AD_MEDIA_ADDFILE, 'media_upload_form', 'index.php' );
        $form->setExtra( 'enctype="multipart/form-data"' );
        switch ( $opt ) {
            case '0':
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NAME, 'media_nicename', 50, 255 ), false );
                $select = new ZariliaFormSelect( _MA_AD_MEDIA_CAT, 'media_cid', $media_cid );
                $select->addOptionArray( $media_cat_handler->getList() );
                $form->addElement( $select, true );

                $form->addElement( new ZariliaFormFile( _MA_AD_MEDIA_FILE, 'media_file', 5000000 ) );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_WEIGHT, 'media_weight', 3, 4, 0 ) );
                $form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_DISPLAY, 'media_display', 1, ' ' . _YES . '', ' ' . _NO . '' ) );
                $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );

                $form->insertSplit( _MA_AD_MEDIA_RESIZEOPTIONS );
                $form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_RESIZE, 'media_resize', 0, ' ' . _YES . '', ' ' . _NO . '' ) );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWPREFIX, 'media_ext', 10, 80, @$zariliaMediaConfig['media_prefix'] ), false );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWWIDTH, 'media_width', 10, 80, '300' ), false );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWHEIGHT, 'media_height', 10, 80, '250' ), false );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWQUALITY, 'media_quality', 3, 30, '100' ), false );
                $form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_KEEPASPECT, 'media_aspect', 0, ' ' . _YES . '', ' ' . _NO . '' ) );

                $form->addElement( new ZariliaFormHidden( 'fct', 'media' ) );
                $form->addElement( new ZariliaFormHidden( 'op', 'addfile' ) );
                $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
                break;

            case '1':
            default:
                $graph_array = &ZariliaLists::getImgListAsArray( $cache_path );
                if ( empty( $graph_array ) || !$graph_array ) {
                    $graph_array = array( _MA_AD_MEDIA_NAVAILTOUPLOAD => 0 );
                }
                $smallmedia_select = new ZariliaFormSelect( '', 'imagelisting', '', 10, true );
                $smallmedia_select->addOptionArray( $graph_array );
                $smallmedia_tray = new ZariliaFormElementTray( _MA_AD_MEDIA_SELECT, '&nbsp;' );
                $smallmedia_tray->addElement( $smallmedia_select );
                $form->addElement( $smallmedia_tray );

                $select = new ZariliaFormSelect( _MA_AD_MEDIA_CAT, 'media_cid' );
                $select->addOptionArray( $media_cat_handler->getList() );
                $form->addElement( $select, true );

                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_WEIGHT, 'media_weight', 3, 4, 0 ) );
                $form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_AUTOWEIGHT, 'media_autoweight', 0, _YES, _NO ) );
                $form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_DISPLAY, 'media_display', 1, _YES, _NO ) );

                $form->insertSplit( _MA_AD_MEDIA_RESIZEOPTIONS );
                $form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_RESIZE, 'media_resize', 0, ' ' . _YES . '', ' ' . _NO . '' ) );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWWIDTH, 'media_width', 10, 80, '300' ), false );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWHEIGHT, 'media_height', 10, 80, '250' ), false );
                $form->addElement( new ZariliaFormText( _MA_AD_MEDIA_NEWQUALITY, 'media_quality', 3, 30, '100' ), false );
                $form->addElement( new ZariliaFormRadioYN( _MA_AD_MEDIA_KEEPASPECT, 'media_aspect', 0, ' ' . _YES . '', ' ' . _NO . '' ) );

                $form->addElement( new ZariliaFormHidden( 'fct', 'media' ) );
                $form->addElement( new ZariliaFormHidden( 'op', 'batchaddfile' ) );
                $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
                break;
        } // switch
        $form->display();
        break;

    case "addfile":
        if ( $_SESSION['uploadedtimes'] > 0 ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Session has expired' );
        }

        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $media_cat_obj = $media_cat_handler->get( $media_cid );
        if ( !is_object( $media_cat_obj ) || !$media_cat_obj->getVar( 'media_cdirname' ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
        }

        $_upload_path = ZAR_ROOT_PATH . DIRECTORY_SEPARATOR . $media_cat_obj->getVar( 'media_cdirname' );
        if ( !is_readable( $_upload_path ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
        }

        /**
         * Show errors
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 3 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

		$media_handler->uploadFile( $_upload_path );
        // if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) ) {
        // $mimetype_handler = &zarilia_gethandler( 'mimetype' );
        // $mime_obj = &$mimetype_handler->getMtypeArray();
        // /**
        // * *hello
        // */
        // include_once ZAR_ROOT_PATH . '/class/class.uploader.php';
        // $uploader = new ZariliaMediaUploader( $_upload_path,
        // $mime_obj,
        // $media_cat_obj->getVar( 'media_cmaxsize' ),
        // $media_cat_obj->getVar( 'media_cmaxwidth' ),
        // $media_cat_obj->getVar( 'media_cmaxheight' )
        // );
        // $uploader->setPrefix( 'media' );
        // $uploader->setRandom( 1 );
        // /**
        // * *hello
        // */
        // for ( $i = 0; $i < count( $_POST['zarilia_upload_file'] ); $i++ ) {
        // if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) ) {
        // if ( !$uploader->upload() ) {
        // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MA_AD_MEDIA_FAILFETCHIMG, $_FILES[$_POST['zarilia_upload_file'][$i]]['name'], $uploader->getErrors() ) );
        // } else {
        // $media_nicename = zarilia_cleanRequestVars( $_REQUEST, 'media_nicename', '' );
        // if ( $media_nicename == '' ) {
        // $media_nicename = explode( '.', $uploader->getMediaName() );
        // $media_nicename = $media_nicename[0];
        // }
        // $media_display = zarilia_cleanRequestVars( $_REQUEST, 'media_display', 0 );
        // $media_weight = zarilia_cleanRequestVars( $_REQUEST, 'media_weight', 0 );
        // $media_dirname = zarilia_cleanRequestVars( $_REQUEST, 'media_dirname', 0 );
        // $media_name = $uploader->getSavedFileName();
        // $media_mimetype = $uploader->getMediaType();
        // /**
        // * Do resize if requested for images only
        // */
        // $media_resize = zarilia_cleanRequestVars( $_REQUEST, 'media_resize', 0 );
        // $allowed_mimetypes = array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png' );
        // if ( $media_resize && in_array( $media_mimetype, $allowed_mimetypes ) ) {
        // include_once ZAR_ROOT_PATH . '/class/class.thumbnail.php';
        // $_thumb_image = new ZariliaThumbs( $media_name, $media_cat_obj->getVar( 'media_cdirname' ) );
        // if ( is_object( $_thumb_image ) ) {
        // $media_width = zarilia_cleanRequestVars( $_REQUEST, 'media_width', 0 );
        // $media_height = zarilia_cleanRequestVars( $_REQUEST, 'media_height', 0 );
        // $media_quality = zarilia_cleanRequestVars( $_REQUEST, 'media_quality', 0 );
        // $media_aspect = zarilia_cleanRequestVars( $_REQUEST, 'media_aspect', 0 );
        // $media_ext = zarilia_cleanRequestVars( $_REQUEST, 'media_ext', 'media', XOBJ_DTYPE_TXTBOX );
        // /**
        // */
        // if ( $media_ext ) {
        // $_thumb_image->setImagePrefix( $media_ext );
        // }
        // $_thumb_image->setLibType( 1 );
        // $_image = $_thumb_image->do_thumb( $media_width, $media_height, $media_quality, $media_aspect, false, false );
        // if ( $_image == false ) {
        // $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_thumb_image->getErrors() );
        // } else {
        // $media_name = basename( $_image['imgTitle'] );
        // }
        // } else {
        // $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_thumb_image->getErrors() );
        // }
        // }
        // $obj = $media_handler->create();
        // $obj->setVar( 'media_name', $media_name );
        // $obj->setVar( 'media_nicename', $media_nicename );
        // $obj->setVar( 'media_mimetype', $media_mimetype );
        // $obj->setVar( 'media_ext', $uploader->getExt() );
        // $obj->setVar( 'media_created', time() );
        // $obj->setVar( 'media_display', $media_display );
        // $obj->setVar( 'media_weight', $media_weight );
        // $obj->setVar( 'media_cid', $media_cid );
        // $obj->setVar( 'media_uid', $zariliaUser->getVar( 'uid' ) );
        // $obj->setVar( 'media_filesize', $uploader->getMediaSize() );
        // if ( !$media_handler->insert( $obj ) ) {
        // $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $obj->getErrors() );
        // } else {
        // $_SESSION['uploadedtimes'] = 1;
        // }
        // }
        // } else {
        // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MA_AD_MEDIA_FAILFETCHIMG, $_FILES[$_POST['zarilia_upload_file'][$i]]['name'], $uploader->getErrors() ) );
        // }
        // }
        // } else {
        // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_MEDIA_NOFILESSELECTED );
        // }
        /**
         * display any errors found/* media_cid
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 3 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list&amp;media_cid=' . $media_cid, 2, _DBUPDATED );
        }
        break;

    case "batchaddfile":
        $err = false;
        // if ( in_array( ZAR_GROUP_ADMIN, $gperm_groupid ) ) {
        // $is_admin = true;
        // }
        if ( $_SESSION['uploadedtimes'] > 0 ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Session has expired' );
        }

        $media_cid = zarilia_cleanRequestVars( $_REQUEST, 'media_cid', 0 );
        $media_cat_obj = $media_cat_handler->get( $media_cid );
        if ( !is_object( $media_cat_obj ) || !$media_cat_obj->getVar( 'media_cdirname' ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
        }

        $_upload_path = ZAR_ROOT_PATH . '/' . $media_cat_obj->getVar( 'media_cdirname' );
        if ( !zarilia_get_dir_status( $_upload_path ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, '' );
        }

        $cache_path = ZAR_UPLOAD_PATH . '/' . 'cache';
        if ( !zarilia_get_dir_status( $cache_path ) ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MA_AD_MEDIA_CACHEDIR, $cache_path ) );
        }

        $media_display = zarilia_cleanRequestVars( $_REQUEST, 'media_display', 0 );
        $media_weight = zarilia_cleanRequestVars( $_REQUEST, 'media_weight', 0 );

        $mimetype_handler = &zarilia_gethandler( 'mimetype' );
        $mime_obj = &$mimetype_handler->getMtypeArray();

        $media_maxsize = $media_cat_obj->getVar( 'media_cmaxsize' );
        $media_height = $media_cat_obj->getVar( 'media_cmaxheight' );
        $media_width = $media_cat_obj->getVar( 'media_cmaxwidth' );
        $media_resize = zarilia_cleanRequestVars( $_REQUEST, 'media_resize', 0 );
        $media_display = zarilia_cleanRequestVars( $_REQUEST, 'media_display', 0 );
        $media_weight = zarilia_cleanRequestVars( $_REQUEST, 'media_weight', 0 );

        if ( isset( $_REQUEST['imagelisting'] ) && count( $_REQUEST['imagelisting'] ) ) {
            foreach( $_REQUEST['imagelisting'] as $file ) {
                $sourcefile = $cache_path . '/' . $file;

                /**
                 * File mimetype check
                 */
                if ( !file_exists( $sourcefile ) ) {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _MA_AD_MEDIA_PATHNOTEXIST, $file, $cache_path ) );
                    continue;
                }

                /**
                 * File size check
                 */
                if ( is_file( $sourcefile ) && !is_dir( $sourcefile ) ) {
                    $media_filesize = filesize( $sourcefile );
                    if ( $media_filesize > $media_maxsize ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _MA_AD_MEDIA_FILESIZEEXCEEDS, $file, $media_maxsize ) );
                        continue;
                    }
                }

                /**
                 * image File size check
                 */
                $mediaDimension = getimagesize( $sourcefile );
                if ( $mediaDimension ) {
                    if ( !is_array( $mediaDimension ) && $mediaDimension[0] > $media_height || $mediaDimension[1] > $media_height ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _MA_AD_MEDIA_FILEWIDTHERROR, $mediaDimension[1], $mediaDimension[0], $file, $media_height, $media_height ) );
                        continue;
                    }
                }

                /**
                 * File mimetype check
                 */
                $media_mimetype = $mimetype_handler->ret_mime( $sourcefile );
                if ( $media_mimetype['ret_mime'] == false || count( $mime_obj ) > 0 && !in_array( $media_mimetype['ret_mime'], $mime_obj ) ) {
                    $_error_message = $media_mimetype['ret_mime'] == false ? _MA_AD_MEDIA_UNKNOWNFILETYPE : _MA_AD_MEDIA_UNKNOWNMIME;
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( $_error_message, $file ) );
                    continue;
                }

                if ( $media_resize && in_array( $media_mimetype['ret_mime'], $mime_obj ) ) {
                    include_once ZAR_ROOT_PATH . '/class/class.thumbnail.php';
                    $_thumb_image = new ZariliaThumbs( $file, 'uploads/cache' );
                    if ( is_object( $_thumb_image ) ) {
                        $nmedia_width = zarilia_cleanRequestVars( $_REQUEST, 'media_width', 0 );
                        $nmedia_height = zarilia_cleanRequestVars( $_REQUEST, 'media_height', 0 );
                        $nmedia_quality = zarilia_cleanRequestVars( $_REQUEST, 'media_quality', 0 );
                        $nmedia_aspect = zarilia_cleanRequestVars( $_REQUEST, 'media_aspect', 0 );
                        $nmedia_ext = zarilia_cleanRequestVars( $_REQUEST, 'media_ext', 'media', XOBJ_DTYPE_TXTBOX );
                        /**
                         */
                        if ( $nmedia_ext ) {
                            $_thumb_image->setImagePrefix( $nmedia_ext );
                        }
                        $_thumb_image->setLibType( 1 );
                        $_image = $_thumb_image->do_thumb( $nmedia_width, $nmedia_height, $nmedia_quality, $nmedia_aspect, false, false );
                        if ( $_image == false ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_thumb_image->getErrors() );
                        } else {
                            $sourcefile = $cache_path . '/' . basename( $_image['imgTitle'] );
                        }
                    } else {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_thumb_image->getErrors() );
                    }
                }

                $file_ext = zarilia_getFileExtension( $sourcefile );
                /**
                 * copy files over with new name and delete the old one
                 */
                $media_hidden = 1; //zarilia_cleanRequestVars( $_REQUEST, 'image_hidden', 0 );
                if ( $media_hidden ) {
                    $media_name = uniqid( 'media' ) . '.' . strtolower( $file_ext['ext'] );
                } else {
                    $media_name = basename( $sourcefile );
                }

                $destinationfile = $_upload_path . "/" . $media_name;
                if ( rename( $sourcefile, $destinationfile ) ) {
                    clearstatcache();
                    $media_nicename = explode( '.', $file );
                    $media_nicename = $media_nicename[0];

                    $obj = $media_handler->create();
                    $obj->setVar( 'media_name', $media_name );
                    $obj->setVar( 'media_nicename', $media_nicename );
                    $obj->setVar( 'media_mimetype', $media_mimetype['ret_mime'] );
                    $obj->setVar( 'media_ext', $file_ext['ext'] );
                    $obj->setVar( 'media_created', time() );
                    $obj->setVar( 'media_display', $media_display );
                    $obj->setVar( 'media_weight', $media_weight );
                    $obj->setVar( 'media_cid', $media_cid );
                    $obj->setVar( 'media_uid', $zariliaUser->getVar( 'uid' ) );
                    $obj->setVar( 'media_filesize', $media_filesize );
                    if ( !$media_handler->insert( $obj ) ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $obj->getErrors() );
                    } else {
                        $_SESSION['uploadedtimes'] = 1;
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, sprintf( _MA_AD_MEDIA_ERRORCOPY, $sourcefile, $destinationfile ) );
                }
            }
        } else {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _MA_AD_MEDIA_NOFILESSELECTED );
        }
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 3 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        } else {
            redirect_header( $addonversion['adminpath'] . '&amp;op=cat_list', 1, _DBUPDATED );
        }
        break;
}

?>