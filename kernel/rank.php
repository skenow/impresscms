<?php
// $Id: rank.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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
 * Zarilia Rank Class
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaRank extends ZariliaObject {
    /**
     * Constructor
     */
    function ZariliaRank() {
        $this->ZariliaObject();
        $this->initVar( 'rank_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'rank_title', XOBJ_DTYPE_TXTBOX, null, '' );
        $this->initVar( 'rank_min', XOBJ_DTYPE_INT, 0 );
        $this->initVar( 'rank_max', XOBJ_DTYPE_INT, 0 );
        $this->initVar( 'rank_special', XOBJ_DTYPE_INT, 0 );
        $this->initVar( 'rank_image', XOBJ_DTYPE_TXTBOX, 'blank.png' );
    }

    /**
     * ZariliaRank::formEdit()
     *
     * @param mixed $caption
     * @return
     */
    function formEdit( $caption ) {
        require_once ZAR_ROOT_PATH . '/kernel/kernel_forms/rank.php';
        return $form;
    }

    /**
     * ZariliaRank::getRankImage()
     *
     * @return
     */
    function getRankImage() {
        $file = '';
        if ( file_exists( ZAR_UPLOAD_PATH . '/' . $this->getVar( 'rank_image' ) ) ) {
            $file .= '<img src="' . ZAR_UPLOAD_URL . '/' . $this->getVar( 'rank_image' ) . '" title="" alt="" />';
            return $file;
        }
        return '------------';
    }

    /**
     * ZariliaRank::getSpecial()
     *
     * @return
     */
    function getSpecial() {
        return ( $this->getVar( 'rank_special' ) == 1 ) ? _YES : _NO;
    }
}

/**
 * ZariliaRankHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: rank.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaRankHandler extends ZariliaPersistableObjectHandler {
    /*
	*
	*/
    function ZariliaRankHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'ranks', 'ZariliaRank', 'rank_id', 'rank_title' );
    }

    /**
     * ZariliaRankHandler::getRank()
     *
     * @param integer $rank_id
     * @param mixed $posts
     * @return
     **/
    function getRank( $rank_id = 0, $posts = 0 ) {
        $criteria = new CriteriaCompo();
        $criteria->setOrder( 'rank_title' );
        if ( $rank_id != 0 ) {
            return $this->get( $rank_id );
        } else {
            $criteria->setLimit( 1 );
            $criteria->add( new Criteria( 'rank_min', $posts, '<=' ) );
            $criteria->add( new Criteria( 'rank_max', $posts, '>=' ) );
            $criteria->add( new Criteria( 'rank_special', 0 ) );
            $ret = $this->getObjects( $criteria, true );
            return $ret[1];
        }
    }

    /**
     * ZariliaRankHandler::getRankObj()
     *
     * @param array $nav
     * @return
     **/
    function getRankObj( $nav = array() ) {
        $criteria = new CriteriaCompo();
        if ( isset( $avatar_display ) ) {
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

    function setUpload( &$obj ) {
        $config_handler = &zarilia_gethandler( 'config' );
        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );

        if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) ) {
            include_once ZAR_ROOT_PATH . '/class/uploader.php';
            $uploader = new ZariliaMediaUploader(
                ZAR_UPLOAD_PATH,
                array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' ),
                $zariliaConfigUser['avatar_maxsize'],
                $zariliaConfigUser['avatar_width'],
                $zariliaConfigUser['avatar_height']
                );

            $uploader->setPrefix( 'rank' );
            $ucount = count( $_POST['zarilia_upload_file'] );
            for ( $i = 0; $i < $ucount; $i++ ) {
                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) ) {
                    if ( !$uploader->upload() ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $uploader->getErrors() );
                    } else {
                        $obj->setVar( 'rank_image', $uploader->getSavedFileName() );
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILFETCHIMG, $i ) );
                }
            }
        } else {
            if ( $_REQUEST['rank_image_dir'] ) {
                $obj->setVar( 'rank_image', $_REQUEST['rank_image_dir'] );
            }
        }
        $obj->setVar( 'rank_title', $_REQUEST['rank_title'] );
        $obj->setVar( 'rank_special', empty( $_REQUEST['rank_special'] ) ? 0 : 1 );
        $obj->setVar( 'rank_min', $_REQUEST['rank_min'] );
        $obj->setVar( 'rank_max', $_REQUEST['rank_max'] );
    }
}

?>