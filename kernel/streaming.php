<?php
// $Id: streaming.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
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
 * ZariliaStreaming
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: streaming.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaStreaming extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaStreaming()
    {
        $this->zariliaObject();
        $this->initVar( 'streaming_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'streaming_title', XOBJ_DTYPE_TXTBOX, null, false, 150 );
        $this->initVar( 'streaming_file', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'streaming_uid', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'streaming_description', XOBJ_DTYPE_TXTAREA, null, false, null, 1 );
        $this->initVar( 'streaming_image', XOBJ_DTYPE_IMAGE, 'blank.png', false, 150 );
        $this->initVar( 'streaming_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'streaming_display', XOBJ_DTYPE_OTHER, 1, false );
        $this->initVar( 'streaming_published', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'streaming_mimetype', XOBJ_DTYPE_TXTBOX, null, false, 100 );
        $this->initVar( 'streaming_alias', XOBJ_DTYPE_TXTBOX, null, false, 60 );
    }

    /**
     * ZariliaStreaming::formEdit()
     *
     * @return
     */
    function formEdit()
    {
        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/streaming.php';
    }
    // /**
    // * Display a human readable date form
    // * parm: intval: 	$time	- unix timestamp
    // */
    // function formatTimeStamp( $time = null, $format = 'D, M-d-Y', $var = '', $err = '---------------' ) {
    // $_time = ( $time == null ) ? $this->getVar( 'streaming_published' ) : $this->getVar( $time );
    // $ret = ( $_time ) ? formatTimestamp( $_time ) : $err;
    // return $ret;
    // }
    /**
     * ZariliaContent::getZariliaUser()
     *
     * @return
     */
    function getcpUser( $is_linked = true, $usereal = false, $uid = null )
    {
        $member_handler = &zarilia_gethandler( 'member' );
        $uid = ( $uid != null ) ? $uid : $this->getVar( 'streaming_uid' );
        $content_user = &$member_handler->getUser( $uid );
        if ( $this->getVar( 'streaming_alias' ) != '' ) {
            $ret['name'] = $this->getVar( 'streaming_alias' );
        } else
        if ( is_object( $content_user ) ) {
            if ( intval( $usereal ) ) {
                $ret['name'] = $content_user->getVar( 'name' );
            } else {
                $ret['name'] = $content_user->getVar( 'uname' );
            }
            if ( $is_linked ) {
                $ret['name'] = '<a href="' . ZAR_URL . '/index.php?page_type=userinfo&uid=' . $content_user->getVar( 'uid' ) . '">' . $ret['name'] . '</a>';
            }
            $ret['avatar'] = $content_user->getVar( 'user_avatar' ) ? $content_user->getVar( 'user_avatar' ) : 'nouserimage.jpg';
            $ret['online'] = $content_user->isOnline();
        } else {
            $ret['name'] = $GLOBALS['zariliaConfig']['anonymous'];
        }
        return $ret;
    }
}

/**
 * ZariliaStreamingHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: streaming.php,v 1.4 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaStreamingHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaStreamingHandler::ZariliaStreamingHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaStreamingHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'streaming', 'ZariliaStreaming', 'streaming_id', 'streaming_title', 'streaming_read' );
    }

    /**
     * ZariliaStreamingHandler::getbyType()
     *
     * @param mixed $streaming_type
     * @return
     */
    function getbyType( $streaming_type = null )
    {
        $criteria = new CriteriaCompo();
        if ( $streaming_type == null ) {
            return;
        }
        $criteria->add( new Criteria( 'streaming_type', $streaming_type ) );
        $criteria->setStart( 0 );
        $criteria->setLimit( 1 );
        $object = $this->getObjects( $criteria, false );
        return $object[0];
    }

    /**
     * ZariliaStreamingHandler::getStreamObj()
     *
     * @param array $nav
     * @param mixed $streaming_id
     * @return
     */
    function getStreamObj( $nav = null )
    {
        $criteria = new CriteriaCompo();
        if ( isset( $nav['streaming_display'] ) && $nav['streaming_display'] != 3 ) {
            $criteria->add( new Criteria( 'streaming_display', $nav['streaming_display'] ) );
        }

        $object['count'] = $this->getCount( $criteria, false );
        if ( isset( $nav ) ) {
            $criteria->setSort( $nav['sort'] );
            $criteria->setOrder( $nav['order'] );
            $criteria->setStart( $nav['start'] );
            $criteria->setLimit( $nav['limit'] );
        }
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }

    /**
     * ZariliaAvatarHandler::setUpload()
     *
     * @param mixed $obj
     * @return
     */
    function setUpload( &$obj )
    {
        global $zariliaConfigUser;

        $mimetype_handler = &zarilia_gethandler( 'mimetype' );
        $mime_obj = &$mimetype_handler->getMtypeArray();
        $zariliaConfigUser['streaming_maxsize'] = "1000000";
        if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) ) {
            include_once ZAR_ROOT_PATH . '/class/uploader.php';
            $uploader = new ZariliaMediaUploader(
                ZAR_UPLOAD_PATH . '/streams',
                $mime_obj,
                $zariliaConfigUser['streaming_maxsize'],
                $zariliaConfigUser['streaming_width'],
                $zariliaConfigUser['streaming_height']
                );

            $uploader->setPrefix( 'savt' );
            $ucount = count( $_POST['zarilia_upload_file'] );
            for ( $i = 0; $i < $ucount; $i++ ) {
                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) ) {
                    if ( !$uploader->upload() ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $uploader->getErrors() );
                    } else {
                        if ( !$obj->isNew() ) {
                            /*Unlink File*/
                            unlink( ZAR_UPLOAD_PATH . '/' . $obj->getVar( 'streaming_file' ) );
                        }
                        $obj->setVar( 'streaming_file', $uploader->getSavedFileName() );
                        $obj->setVar( 'streaming_mimetype', $uploader->getMediaType() );
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILFETCHIMG, $i ) );
                }
            }
        } else {
            if ( isset( $_REQUEST['streaming_file'] ) ) {
                $obj->setVar( 'streaming_file', $_REQUEST['streaming_file'] );
                $mimetype = $this->getmimetype( $_REQUEST['streaming_file'], true );
                $obj->setVar( 'streaming_mimetype', $mimetype['mimetype'] );
            }
        }
        $obj->setVar( 'streaming_title', $_REQUEST['streaming_title'] );
        $obj->setVar( 'streaming_description', $_REQUEST['streaming_description'] );
        $obj->setVar( 'streaming_image', $_REQUEST['streaming_image'] );
        $obj->setVar( 'streaming_weight', $_REQUEST['streaming_weight'] );
        $obj->setVar( 'streaming_display', empty( $_REQUEST['streaming_display'] ) ? 0 : 1 );
        $obj->setVar( 'streaming_uid', $_REQUEST['streaming_uid'] );
        $obj->setVar( 'streaming_alias', $_REQUEST['streaming_alias'] );
        if ( isset( $_REQUEST['streaming_published'] ) ) {
            $obj->setVar( 'streaming_published', strtotime( $_REQUEST['streaming_published'] ) );
        }
    }

    /**
     * ZariliaAvatarHandler::getmimetype()
     *
     * @param mixed $ext
     * @return
     */
    function getmimetype( $file, $return = false )
    {
        $mimetype_handler = &zarilia_gethandler( 'mimetype' );
        $mimetype = $mimetype_handler->ret_mime( $file );
        return $mimetype[0];
    }

    function streaming_header()
    {
    }
}

?>