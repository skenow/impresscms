<?php
// $Id: avatar.php,v 1.4 2007/05/05 11:12:11 catzwolf Exp $
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
 * Zarilia Avatar Class
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaAvatar extends ZariliaObject
{
    /**
     *
     * @var intval
     * @access protected
     */
    var $_userCount;

    /**
     * constructor
     */
    function ZariliaAvatar()
    {
        $this->ZariliaObject();
        $this->initVar( 'avatar_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'avatar_file', XOBJ_DTYPE_IMAGE, '', false, 30 );
        $this->initVar( 'avatar_name', XOBJ_DTYPE_TXTBOX, null, true, 100 );
        $this->initVar( 'avatar_mimetype', XOBJ_DTYPE_OTHER, null, false );
        $this->initVar( 'avatar_created', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'avatar_display', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'avatar_weight', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'avatar_type', XOBJ_DTYPE_OTHER, 0, false );
        $this->initVar( 'avatar_uid', XOBJ_DTYPE_OTHER, 0, false );
        $this->initVar( 'avatar_usercount', XOBJ_DTYPE_OTHER, 0, false );
    }

    function formEdit()
    {
        include_once ZAR_ROOT_PATH . '/kernel/kernel_forms/avatar.php';
    }

    function showAvatar()
    {
        $_file = $this->getVar( 'avatar_file', 's' );
        if ( is_readable( ZAR_UPLOAD_PATH . DIRECTORY_SEPARATOR . $_file ) )
        {
            return '<img src="' . ZAR_UPLOAD_URL . '/' . $_file . '" width="30" height="30" border="1" title="" alt="" />';
        }
        unset( $_file );
        return '------------';
    }

    function getLinkedUserName( $linked = 1 )
    {
        $ret = zarilia_getLinkedUnameFromId( $this->getVar( "avatar_uid" ), 0, $linked );
        return $ret;
    }
}

/**
 * ZariliaAvatarHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: avatar.php,v 1.4 2007/05/05 11:12:11 catzwolf Exp $
 * @access public
 */
class ZariliaAvatarHandler extends ZariliaPersistableObjectHandler
{
    /**
     * constructor
     */
    function ZariliaAvatarHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'avatar', 'zariliaavatar', 'avatar_id', 'avatar_name' );
    }

    /**
     * ZariliaAvatarHandler::addUser()
     *
     * @param  $avatar_id
     * @param  $user_id
     * @return
     */
    function addUser( $avatar_id, $user_id )
    {
        $avatar_id = intval( $avatar_id );
        $user_id = intval( $user_id );
        if ( $avatar_id < 1 || $user_id < 1 )
        {
            return false;
        }
        $sql = sprintf( "DELETE FROM %s WHERE user_id = %u", $this->db->prefix( 'avatar_user_link' ), $user_id );
        if ( !$result2 = &$this->db->Execute( $sql ) )
        {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
        }
        $sql2 = sprintf( "INSERT INTO %s (avatar_id, user_id) VALUES (%u, %u)", $this->db->prefix( 'avatar_user_link' ), $avatar_id, $user_id );
        if ( !$result2 = &$this->db->Execute( $sql2 ) )
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $this->db->errno() . " " . $this->db->error() );
            return false;
        }
        return true;
    }

    /**
     * ZariliaAvatarHandler::getUser()
     *
     * @param mixed $obj
     * @return
     */
    function getUser( &$obj )
    {
        $ret = array();
        if ( strtolower( get_class( $obj ) ) != strtolower( $this->obj_class ) )
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _ER_PAGE_NOT_OBJECT, __FILE__, __LINE__ );
            return false;
        }
        $sql = 'SELECT user_id FROM ' . $this->db->prefix( 'avatar_user_link' ) . ' WHERE avatar_id=' . $obj->getVar( 'avatar_id' );
        if ( !$result = $this->db->Execute( $sql ) )
        {
			$GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, 'Database error: '. $sql, __FILE__, __LINE__ );
            return false;
        }
        while ( $myrow = $result->FetchRow() )
        {
            $ret[] = &$myrow['user_id'];
        }
        return $ret;
    }

    /**
     * ZariliaAvatarHandler::getList()
     *
     * @param unknown $avatar_type
     * @param unknown $avatar_display
     * @return
     */
    function &getAList( $avatar_type = null, $avatar_display = null )
    {
        $this->keyName = 'avatar_file';
        $query = 'avatar_file, avatar_name';
        $criteria = new CriteriaCompo();

        if ( isset( $avatar_type ) )
        {
            $avatar_type = ( $avatar_type == 'C' ) ? 'C' : 'S';
            $criteria->add( new Criteria( 'avatar_type', $avatar_type ) );
        }
        if ( isset( $avatar_display ) )
        {
            $criteria->add( new Criteria( 'avatar_display', intval( $avatar_display ) ) );
        }
        $avatars = &$this->getList( $criteria, $query );
        $ret['blank.png'] = _NONE;
        foreach ( $avatars as $k => $v )
        {
            $ret[$k] = htmlspecialchars( $v, ENT_QUOTES );
        }
        return $ret;
    }

    /**
     * ZariliaAvatarHandler::getAvatarObj()
     *
     * @param array $nav
     * @param mixed $avatar_type
     * @param mixed $avatar_display
     * @return
     */
    function getAvatarObj( $nav = array(), $avatar_type = null, $avatar_display = null )
    {
        $criteria = new CriteriaCompo();
        if ( isset( $avatar_type ) )
        {
            $avatar_type = ( $avatar_type == 'C' ) ? 'C' : 'S';
            $criteria->add( new Criteria( 'avatar_type', $avatar_type ) );
        }
        if ( isset( $avatar_display ) )
        {
            $criteria->add( new Criteria( 'avatar_display', intval( $avatar_display ) ) );
        }
        $obj['count'] = $this->getCount( $criteria, false );
        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    /**
     * ZariliaAvatarHandler::setUpload()
     *
     * @param mixed $obj
     * @return
     */
    function setUpload( &$_obj )
    {
        global $_obj;
        $config_handler = &zarilia_gethandler( 'config' );
        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );

        if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) )
        {
            include_once ZAR_ROOT_PATH . '/class/uploader.php';
            $uploader = new ZariliaMediaUploader(
                ZAR_UPLOAD_PATH,
                array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' ),
                $zariliaConfigUser['avatar_maxsize'],
                $zariliaConfigUser['avatar_width'],
                $zariliaConfigUser['avatar_height']
                );
            $uploader->setPrefix( 'savt' );
            $ucount = count( $_POST['zarilia_upload_file'] );
            for ( $i = 0; $i < $ucount; $i++ )
            {
                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) )
                {
                    if ( !$uploader->upload() )
                    {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $uploader->getErrors(), __FILE__, __LINE__ );
                        return false;
                    }
                    else
                    {
                        if ( !$_obj->isNew() )
                        {
                            /*Unlink File*/
                            unlink( ZAR_UPLOAD_PATH . '/' . $_obj->getVar( 'avatar_file' ) );
                        }
                        $_obj->setVar( 'avatar_file', $uploader->getSavedFileName() );
                        $_obj->setVar( 'avatar_mimetype', $uploader->getMediaType() );
                    }
                }
                else
                {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILFETCHIMG, $i ), __FILE__, __LINE__ );
                    return false;
                }
            }
        }
        else
        {
            if ( $_REQUEST['avatar_image_dir'] )
            {
                $_obj->setVar( 'avatar_file', $_REQUEST['avatar_image_dir'] );
                $_obj->setVar( 'avatar_mimetype', $this->getmimetype( $_REQUEST['avatar_image_dir'], true ) );
            }
        }
        $_obj->setVar( 'avatar_name', $_REQUEST['avatar_name'] );
        $_obj->setVar( 'avatar_display', empty( $_REQUEST['avatar_display'] ) ? 0 : 1 );
        $_obj->setVar( 'avatar_weight', $_REQUEST['avatar_weight'] );
        $_obj->setVar( 'avatar_type', $_REQUEST['type'] );
        return true;
    }

    /**
     * ZariliaAvatarHandler::deleteAvatar()
     *
     * @param mixed $obj
     * @return
     */
    function deleteAvatar( &$obj )
    {
        if ( !$this->delete( $obj ) )
        {
            return false;
        }
        $file = $obj->getVar( 'avatar_file' );
        //  @unlink ( ZAR_UPLOAD_PATH . '/' . $file );
        if ( isset( $user_id ) && $obj->getVar( 'avatar_type' ) == 'C' )
        {
            $sql = sprintf( "UPDATE %s SET user_avatar='blank.gif' WHERE uid= %u", $this->db->prefix( 'users' ), $user_id );
        }
        else
        {
            $sql = sprintf( "UPDATE %s SET user_avatar='blank.gif' WHERE user_avatar= %s", $this->db->prefix( 'users' ), $file );
        }
        return true;
    }

    /**
     * ZariliaAvatarHandler::mimetype()
     *
     * @return
     */
    function mimetype()
    {
        return array( "gif" => "image/gif",
            "ief" => "image/ief",
            "jpeg" => "image/pjpeg",
            "jpeg" => "image/jpeg",
            "jpg" => "image/jpeg",
            "jpe" => "image/jpeg",
            "png" => "image/png",
            "unknown" => "application/octet-stream"
            );
    }

    /**
     * ZariliaAvatarHandler::getmimetype()
     *
     * @param mixed $ext
     * @return
     */
    function getmimetype( $ext, $return = false )
    {
        if ( $return == true )
        {
            $filename = explode( '.', $ext );
            $ext = $filename['1'];
        }
        $mimetypes = $this->mimetype();
        if ( isset( $mimetypes[$ext] ) )
        {
            return $mimetypes[$ext];
        }
        else
        {
            return false;
        }
    }

    /**
     * ZariliaAvatarHandler::processFile()
     *
     * @param mixed $sourcefile
     * @return
     */
    function processFile( $sourcefile )
    {
        $ret = array();
        $filename = explode( '.', $sourcefile );
        $ret['name'] = $filename['0'];
        $ret['ext'] = $filename['1'];
        $ret['filename'] = $sourcefile;
        $ret['filenamepath'] = ZAR_CUPLOAD_PATH . DIRECTORY_SEPARATOR . $sourcefile;
        $ret['newfilename'] = $destfile = uniqid( 'savt' ) . '.' . $ret['ext'];
        $ret['filemimetype'] = $this->getmimetype( $ret['ext'] );
        $ret['imagedetails'] = getimagesize( ZAR_CUPLOAD_PATH . DIRECTORY_SEPARATOR . $sourcefile );
        $ret['imagesize'] = filesize( ZAR_CUPLOAD_PATH . DIRECTORY_SEPARATOR . $sourcefile );
        return $ret;
    }
}

?>