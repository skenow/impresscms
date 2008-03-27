<?php
// $Id: smilie.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaSmilie
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: smilie.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaSmilie extends ZariliaObject {
    /**
     * ZariliaSmilie::ZariliaSmilie()
     */
    function ZariliaSmilie()
    {
        $this->ZariliaObject();
        $this->initVar( 'id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'code', XOBJ_DTYPE_TXTBOX, null, true, 50 );
        $this->initVar( 'smile_url', XOBJ_DTYPE_TXTBOX, null, true, 100 );
        $this->initVar( 'emotion', XOBJ_DTYPE_TXTBOX, null, true, 75 );
        $this->initVar( 'display', XOBJ_DTYPE_INT, 1, false );
    }

    /**
     * ZariliaSmilie::formEdit()
     *
     * @param mixed $caption
     * @return
     */
    function formEdit( $caption )
    {
		require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/smilie.php';
		return $form;
    }

    /**
     * ZariliaSmilie::getSmilie()
     *
     * @return
     **/
    function getSmilie()
    {
        $file = '';
        if ( file_exists( ZAR_UPLOAD_PATH . '/' . $this->getVar( 'smile_url' ) ) ) {
            $file .= '<img src="' . ZAR_UPLOAD_URL . '/' . $this->getVar( 'smile_url' ) . '" title="" alt="" />';
            return $file;
        }
        return '------------';
    }
}

/**
 * ZariliaSmilieHandler
 *
 * @package Zarilia
 * @author John Neill
 * @copyright Copyright (c) 2006
 * @version $Id: smilie.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaSmilieHandler extends ZariliaPersistableObjectHandler {
    /**
     * constructor
     */
    function ZariliaSmilieHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'smiles', 'ZariliaSmilie', 'id', 'emotion' );
    }

    /**
     * ZariliaSmilieHandler::getInstance()
     *
     * @param  $db
     * @return
     */
    function &getInstance( &$db )
    {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaSmilieHandler( $db );
        }
        return $instance;
    }

    /**
     * ZariliaSmilieHandler::getList()
     *
     * @param unknown $avatar_type
     * @param unknown $avatar_display
     * @return
     */
    function getList()
    {
        $criteria = new CriteriaCompo();
        $object = &$this->getObjects( $criteria, true );
        $ret = array( 'blank.gif' => _NONE );
        foreach ( array_keys( $object ) as $i ) {
            $ret[$object[$i]->getVar( 'code' )] = $object[$i]->getVar( 'emotion' );
        }
        return $ret;
    }

    /**
     * ZariliaSmilieHandler::getSmiliesObj()
     *
     * @param array $nav
     * @param mixed $avatar_type
     * @param mixed $avatar_display
     * @return
     */
    function getSmiliesObj( $nav = array(), $_hidden = 3 )
    {
        $criteria = new CriteriaCompo();
        if ( $_hidden != 3 ) {
            $criteria->add( new Criteria( 'display', intval( $_hidden ) ) );
        }
        $object['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $object['list'] = $this->getObjects( $criteria, false );
        return $object;
    }

    /**
     * ZariliaSmilieHandler::setUpload()
     *
     * @param mixed $obj
     * @return
     */
    function setUpload( &$obj )
    {
		global $addonversion, $zariliaConfigUser;

        if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) ) {
            include_once ZAR_ROOT_PATH . '/class/uploader.php';
            $uploader = new ZariliaMediaUploader(
                ZAR_UPLOAD_PATH,
                array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' ),
                $zariliaConfigUser['avatar_maxsize'],
                $zariliaConfigUser['avatar_width'],
                $zariliaConfigUser['avatar_height']
                );

            $uploader->setPrefix( 'smil3' );
            $ucount = count( $_POST['zarilia_upload_file'] );
            for ( $i = 0; $i < $ucount; $i++ ) {
                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) ) {
                    if ( !$uploader->upload() ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $uploader->getErrors() );
                    } else {
                        if ( !$obj->isNew() ) {
                            /*Unlink File*/
                            unlink( ZAR_UPLOAD_PATH . '/' . $obj->getVar( 'smile_url' ) );
                        }
                        $obj->setVar( 'smile_url', $uploader->getSavedFileName() );
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILFETCHIMG, $i ) );
                }
            }
        } else {
            $obj->setVar( 'smile_url', $_REQUEST['smile_url'] );
        }
        $obj->setVar( 'code', $_REQUEST['code'] );
        $obj->setVar( 'display', empty( $_REQUEST['display'] ) ? 0 : 1 );
        $obj->setVar( 'emotion', $_REQUEST['emotion'] );
    }
}

?>