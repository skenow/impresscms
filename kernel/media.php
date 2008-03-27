<?php
// $Id: media.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaMedia
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: media.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaMedia extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaMedia() {
        $this->zariliaObject();
        $this->initVar( 'media_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'media_name', XOBJ_DTYPE_OTHER, null, false, 30 );
        $this->initVar( 'media_nicename', XOBJ_DTYPE_TXTBOX, null, false, 100 );
        $this->initVar( 'media_ext', XOBJ_DTYPE_TXTBOX, null, false, 4 );
        $this->initVar( 'media_mimetype', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'media_caption', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'media_created', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'media_display', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'media_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'media_cid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'media_dirname', XOBJ_DTYPE_TXTBOX, null, false, 100 );
        $this->initVar( 'media_uid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'media_filesize', XOBJ_DTYPE_INT, 0, false );
    }

    /**
     * ZariliaMedia::formEdit()
     *
     * @return
     */
    function formEdit() {
        include ZAR_ROOT_PATH . '/kernel/kernel_forms/media.php';
    }

    function formManagerEdit( $zariliaTpl = null ) {
        include ZAR_ROOT_PATH . '/kernel/kernel_forms/mediamanager.php';
    }

    function getMediaCategory( $media_cid = 0 ) {
        $media_cid = intval( $media_cid );
        $value = ( $media_cid > 0 ) ? $media_cid : $this->getVar( 'media_cid' );
        if ( $value ) {
            $media_cat_handler = &zarilia_gethandler( 'mediacategory' );
            $_temp_cat = $media_cat_handler->get( $value );
            if ( $_temp_cat ) {
                return $_temp_cat->getVar( 'media_cdirname' );
            }
        }
        return false;
    }
}

/**
 * ZariliaMediaHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: media.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaMediaHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaMediaHandler::ZariliaMediaHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaMediaHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'media', 'ZariliaMedia', 'media_id', 'media_title', 'media_read' );
    }

    /**
     * ZariliaMediaHandler::getImageObj()
     *
     * @param array $nav
     * @param mixed $media_cid
     * @return
     */
    /**
     * ZariliaMediaHandler::getMediaObj()
     *
     * @param array $nav
     * @param mixed $media_cid
     * @return
     */
    function getMediaObj( $nav = array(), $media_cid ) {
        $criteria = new CriteriaCompo();
        if ( !empty( $nav['search_text'] ) && ( $nav['search_by'] && $nav['search_text'] ) ) {
            echo $nav['search_text'];
            $criteria->add( new Criteria( $nav['search_by'], "%{$nav['search_text']}%", 'LIKE' ) );
        } else {
            if ( intval( $media_cid ) ) {
                $criteria->add( new Criteria( 'media_cid', $media_cid ) );
            }
        }
        $object['count'] = $this->getCount( $criteria );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setLimit( $nav['limit'] );
        $criteria->setStart( $nav['start'] );
        $object['list'] = $this->getObjects( $criteria );
        return $object;
    }

    function setUpload( &$obj ) {
        $obj->setVar( 'media_nicename', $_REQUEST['media_nicename'] );
        $obj->setVar( 'media_ext', $_REQUEST['media_ext'] );
        $obj->setVar( 'media_mimetype', $_REQUEST['media_mimetype'] );
        $obj->setVar( 'media_caption', $_REQUEST['media_caption'] );
        $obj->setVar( 'media_created', $_REQUEST['media_created'] );
        $obj->setVar( 'media_display', empty( $_REQUEST['media_display'] ) ? 0 : 1 );
        $obj->setVar( 'media_weight', $_REQUEST['media_weight'] );
        $obj->setVar( 'media_dirname', $_REQUEST['media_dirname'] );
        $obj->setVar( 'media_cid', $_REQUEST['media_cid'] );
        $obj->setVar( 'media_uid', $_REQUEST['media_uid'] );
        $obj->setVar( 'media_filesize', $_REQUEST['media_filesize'] );
    }

    function uploadFile( $_upload_path ) {
        global $media_cat_obj, $zariliaUser;

        if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) ) {
            $mimetype_handler = &zarilia_gethandler( 'mimetype' );
            $mime_obj = &$mimetype_handler->getMtypeArray();

            require ZAR_ROOT_PATH . '/class/class.uploader.php';
            $uploader = new ZariliaMediaUploader( $_upload_path,
                $mime_obj,
                $media_cat_obj->getVar( 'media_cmaxsize' ),
                $media_cat_obj->getVar( 'media_cmaxwidth' ),
                $media_cat_obj->getVar( 'media_cmaxheight' )
                );
            $uploader->setPrefix( 'media' );
            $uploader->setRandom( 1 );
            for ( $i = 0; $i < count( $_POST['zarilia_upload_file'] ); $i++ ) {
                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) ) {
                    if ( !$uploader->upload() ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MA_AD_MEDIA_FAILFETCHIMG, $_FILES[$_POST['zarilia_upload_file'][$i]]['name'], $uploader->getErrors() ) );
                    } else {
                        $media_nicename = zarilia_cleanRequestVars( $_REQUEST, 'media_nicename', '' );
                        if ( $media_nicename == '' ) {
                            $media_nicename = explode( '.', $uploader->getMediaName() );
                            $media_nicename = $media_nicename[0];
                        }
                        $media_display = zarilia_cleanRequestVars( $_REQUEST, 'media_display', 0 );
                        $media_weight = zarilia_cleanRequestVars( $_REQUEST, 'media_weight', 0 );
                        $media_dirname = zarilia_cleanRequestVars( $_REQUEST, 'media_dirname', 0 );
                        $media_name = $uploader->getSavedFileName();
                        $media_mimetype = $uploader->getMediaType();
                        /**
                         * Do resize if requested for images only
                         */
                        $media_resize = zarilia_cleanRequestVars( $_REQUEST, 'media_resize', 0 );
                        $allowed_mimetypes = array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png' );
                        if ( $media_resize && in_array( $media_mimetype, $allowed_mimetypes ) ) {
                            include_once ZAR_ROOT_PATH . '/class/class.thumbnail.php';
                            $_thumb_image = new ZariliaThumbs( $media_name, $media_cat_obj->getVar( 'media_cdirname' ) );
                            if ( is_object( $_thumb_image ) ) {
                                $media_width = zarilia_cleanRequestVars( $_REQUEST, 'media_width', 0 );
                                $media_height = zarilia_cleanRequestVars( $_REQUEST, 'media_height', 0 );
                                $media_quality = zarilia_cleanRequestVars( $_REQUEST, 'media_quality', 0 );
                                $media_aspect = zarilia_cleanRequestVars( $_REQUEST, 'media_aspect', 0 );
                                $media_ext = zarilia_cleanRequestVars( $_REQUEST, 'media_ext', 'media', XOBJ_DTYPE_TXTBOX );
                                /**
                                 */
                                if ( $media_ext ) {
                                    $_thumb_image->setImagePrefix( $media_ext );
                                }
                                $_thumb_image->setLibType( 1 );
                                $_image = $_thumb_image->do_thumb( $media_width, $media_height, $media_quality, $media_aspect, false, false );
                                if ( $_image == false ) {
                                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_thumb_image->getErrors() );
                                } else {
                                    $media_name = basename( $_image['imgTitle'] );
                                }
                            } else {
                                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_thumb_image->getErrors() );
                            }
                        }
                        $obj = $this->create();
                        $obj->setVar( 'media_name', $media_name );
                        $obj->setVar( 'media_nicename', $media_nicename );
                        $obj->setVar( 'media_mimetype', $media_mimetype );
                        $obj->setVar( 'media_ext', $uploader->getExt() );
                        $obj->setVar( 'media_created', time() );
                        $obj->setVar( 'media_display', $media_display );
                        $obj->setVar( 'media_weight', $media_weight );
                        $obj->setVar( 'media_cid', $media_cat_obj->getVar( 'media_cid' ) );
                        $obj->setVar( 'media_uid', $zariliaUser->getVar( 'uid' ) );
                        $obj->setVar( 'media_filesize', $uploader->getMediaSize() );
                        $obj->setVar( 'media_dirname', $media_cat_obj->getVar( 'media_cdirname' ) );
                        if ( !$this->insert( $obj ) ) {
                            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $obj->getErrors() );
                        } else {
                            $_SESSION['uploadedtimes'] = 1;
                        }
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _MA_AD_MEDIA_FAILFETCHIMG, $_FILES[$_POST['zarilia_upload_file'][$i]]['name'], $uploader->getErrors() ) );
                }
            }
        } else {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_MEDIA_NOFILESSELECTED );
        }
    }

    function getMediaPlayer( $media, $path = '', $width = '138', $height = '100' ) {
        $ext = pathinfo( $media, PATHINFO_EXTENSION );
        $file_name = ZAR_URL . "/" . $path . "/" . $media;
        switch ( $ext ) {
            case 'wmv':
            case 'wma':
                $ret = "
				      <OBJECT id='mediaPlayer' width='" . $width . "' height='" . $height . "'
				      classid='CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95'
				      codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701'
				      standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'>
				      <param name='fileName' value='" . $file_name . "'>
				      <param name='animationatStart' value='true'>
				      <param name='transparentatStart' value='true'>
				      <param name='autoStart' value='true'>
				      <param name='showControls' value='true'>
				      <param name='loop' value='true'>
				      <EMBED type='application/x-mplayer2'
				        pluginspage='http://microsoft.com/windows/mediaplayer/en/download/'
				        id='mediaPlayer' name='mediaPlayer' displaysize='4' autosize='-1'
				        bgcolor='darkblue' showcontrols='true' showtracker='-1'
				        showdisplay='0' showstatusbar='-1' videoborder3d='-1' width='" . $width . "' height='" . $height . "'
				        src='" . $file_name . "' autostart='false' designtimesp='5311' loop='true'>
				      </EMBED></div><div style=\"text-align:center;\">
				      </OBJECT>
				      <!-- ...end embedded WindowsMedia file -->";
                break;

            case 'mp3':
                $ret = "
				        <!-- begin video window... -->
				        <OBJECT classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width='" . $width . "' height='" . $height . "' codebase='http://www.apple.com/qtactivex/qtplugin.cab'>
				        <param name='src' value='" . $file_name . "'>
				        <param name='autoplay' value='false'>
				        <param name='controller' value='true'>
				        <param name='loop' value='true'>
				        <EMBED src='" . $file_name . "' width='" . $width . "' height='" . $height . "' autoplay='false' controller='true' loop='true' pluginspage='http://www.apple.com/quicktime/download/'></EMBED>
				        </OBJECT>
				        <!-- ...end embedded QuickTime file -->";
                break;
            // case 'mp3':
            // $ret = "
            // <!-- begin video window... -->
            // <tr><td>
            // <OBJECT classid='clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B' width='320' height='255' codebase='http://www.apple.com/qtactivex/qtplugin.cab'>
            // <param name='src' value='" . $file . "'>
            // <param name='autoplay' value='true'>
            // <param name='controller' value='true'>
            // <param name='loop' value='true'>
            // <EMBED src='" . $file . "' width='320' height='255' autoplay='true'
            // controller='true' loop='true' pluginspage='http://www.apple.com/quicktime/download/'>
            // </EMBED>
            // </OBJECT>
            // <!-- ...end embedded QuickTime file -->";
            // break;
            case 'flv':
            case 'swf':
                $ret = "<script type=\"text/javascript\" src=\"" . ZAR_URL . "/class/streaming/ufo.js\"></script>\n";
                $ret .= "<p id=\"ZariliaPlayer\"><a href=\"http://www.macromedia.com/go/getflashplayer\">Get the Flash Player</a> to see this player.</p>";
                $ret .= '<script type="text/javascript">
							var FO = {	movie:"' . ZAR_URL . '/class/streaming/mediaplayer.swf",width:"' . $width . '",height:"140",majorversion:"7",build:"0",bgcolor:"#FFFFFF",
										flashvars:"file=' . $file_name . '&showeq=true&showdigits=true&autostart=false&logo=' . ZAR_URL . '/class/streaming/logo.png" };
							UFO.create(	FO, "ZariliaPlayer");
						</script>';

                break;
            default:
                $mimetype_handler = &zarilia_gethandler( 'mimetype' );
                $ret = $mimetype_handler->mimetypeImage( $_media_name );
                $_media_width = 48;
                $_media_height = 48;
                break;
        } // switch
        return $ret;
    }
}

?>