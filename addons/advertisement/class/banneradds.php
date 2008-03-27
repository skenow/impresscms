<?php
// $Id: banneradds.php,v 1.3 2007/04/21 09:40:19 catzwolf Exp $
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
 *
 * @author John Neill AKA Catzwolf <catzwolf@zarilia.com>
 * @copyright copyright (c) 2006 Zarilia
 */

/**
 * Zarilia Banners
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaBannersAdds extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaBannersAdds( $id = null ) {
        $this->zariliaObject();
        $this->initVar( 'add_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'add_type', XOBJ_DTYPE_TXTBOX, null, false, 20 );
        $this->initVar( 'add_sizew', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'add_sizeh', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'add_weekly', XOBJ_DTYPE_OTHER, '0.00', false );
        $this->initVar( 'add_monthly', XOBJ_DTYPE_OTHER, '0.00', false );
        $this->initVar( 'add_yearly', XOBJ_DTYPE_OTHER, '0.00', false );
        $this->initVar( 'add_image', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'add_active', XOBJ_DTYPE_INT, 1, false );
    }

    function banneraddsForm( $button ) {
        require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $button = ( !$this->isNew() ) ? _MA_AD_MODIFY : _MA_AD_CREATE;
        /*
		*
		*/
        $form = new ZariliaThemeForm( _MA_AD_CREATECLIENT, 'clientadd', zarilia_getenv( 'PHP_SELF' ) );
        $form->addElement( new ZariliaFormText( _MA_AD_EADD_TYPE, 'add_type', 30, 60, $this->getVar( 'add_type', 'e' ) ), true );
        $form->addElement( new ZariliaFormText( _MA_AD_EADD_SIZEW, 'add_sizew', 30, 60, $this->getVar( 'add_sizew', 'e' ) ), true );
        $form->addElement( new ZariliaFormText( _MA_AD_EADD_SIZEH, 'add_sizeh', 30, 60, $this->getVar( 'add_sizeh', 'e' ) ), true );
        $form->addElement( new ZariliaFormText( _MA_AD_EADD_WEEKLY, 'add_weekly', 30, 60, $this->getVar( 'add_weekly', 'e' ) ), true );
        $form->addElement( new ZariliaFormText( _MA_AD_EADD_MONTHLY, 'add_monthly', 30, 60, $this->getVar( 'add_monthly', 'e' ) ), true );
        $form->addElement( new ZariliaFormText( _MA_AD_EADD_YEARLY, 'add_yearly', 30, 60, $this->getVar( 'add_yearly', 'e' ) ), true );
        $index_spotlight_radio = new ZariliaFormRadioYN( _MA_AD_EADD_ACTIVE, 'add_active', $this->getVar( 'add_active' ) , ' ' . _MA_AD_ACTIVE . '', ' ' . _MA_AD_SUSPENDED . '' );
        $form->addElement( $index_spotlight_radio );
        /*
		* Form Buttons
		*/
        $create_tray = new ZariliaFormElementTray( '', '' );
        $create_tray->addElement( new ZariliaFormHidden( 'op', 'save' ) );
        $create_tray->addElement( new ZariliaFormHidden( 'add_id', $this->getVar( 'add_id' ) ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'submit', $button, 'submit' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $form->addElement( $create_tray );
        return $form;
    }
}

/**
 * ZariliaBannersHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: banneradds.php,v 1.3 2007/04/21 09:40:19 catzwolf Exp $
 * @access public
 */
class ZariliaBannerAddsHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaBannersHandler::ZariliaBannersAddsHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaBannerAddsHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'badds', 'ZariliaBannersAdds', 'add_id', 'add_type' );
    }

    function getBannerAddObj( $nav, $is_active = false ) {
        $criteria = new CriteriaCompo();
        if ( $is_active == true ) {
            $criteria->add ( new Criteria( 'add_active', 1, '=' ) );
        }
        $obj['count'] = $this->getCount( $criteria );
        // $criteria->setSort( $nav['sort'] );
        // $criteria->setOrder( $nav['order'] );
        // $criteria->setStart( $nav['start'] );
        // $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria );
        return $obj;
    }

    /**
     * ZariliaBannersClientsHandler::getClients()
     *
     * @param integer $limit
     * @param integer $start
     * @param string $sort
     * @param string $order
     * @return
     */
    function getBanneradds( $limit = 10, $start = 0, $sort = 'name', $order = 'ASC' ) {
        $ret = array();
        $db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT add_id, add_type, add_sizew, add_sizeh FROM " . $db->prefix( "badds" );
        $result = $db->Execute( $sql );
        while ( $myrow = $result->FetchRow() ) {
            $ret[$myrow['add_id']] = stripslashes( $myrow['add_type'] . " {$myrow['add_sizew']} x {$myrow['add_sizeh']}" );
        }
        return $ret;
    }

    function getUploads( &$obj, &$bannerAdd ) {
        global $_FILES, $_POST;

        $db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $imageurl = '';
        if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) ) {
            $result = $db->Execute( "SELECT name FROM " . $db->prefix( "bannerclient" ) . " WHERE cid=" . $obj->getVar( 'cid' ) );
            list( $name ) = $db->fetchRow( $result );

            $new_name = preg_replace( '!\s+!', '_', strtolower( $name ) );
            $banner_dir = ZAR_UPLOAD_PATH . "/banners/$new_name";
            zarilia_admin_mkdir( $banner_dir, 0777 );

            include_once ZAR_ROOT_PATH . '/class/uploader.php';
            $uploader = new ZariliaMediaUploader(
                ZAR_UPLOAD_PATH,
                array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' ),
                $zariliaConfigUser['avatar_maxsize'],
                $zariliaConfigUser['avatar_width'],
                $zariliaConfigUser['avatar_height']
                );

            $ucount = count( $_POST['zarilia_upload_file'] );
            for ( $i = 0; $i < $ucount; $i++ ) {
                if ( file_exists( $banner_dir . "/" . $_POST['zarilia_upload_file'][$i] ) ) {
                    redirect_header( $_PHP_SELF, 2, _MA_AD_IMAGEEXIST );
                }

                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) ) {
                    if ( !$uploader->upload() ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $uploader->getErrors() );
                    } else {
                        if ( !$obj->isNew() ) {
                            /*Unlink File*/

                            unlink( ZAR_UPLOAD_PATH . '/' . $obj->getVar( 'avatar_file' ) );
                        }
                        $obj->setVar( 'avatar_file', $uploader->getSavedFileName() );
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILFETCHIMG, $i ) );
                }
            }
        }
#        } else {
#            if ( $_REQUEST['avatar_image_dir'] ) {
#                $obj->setVar( 'avatar_file', $_REQUEST['avatar_image_dir'] );
#            }
#        }
        return true;
		exit();

        if ( $_FILES['userfile']['name'] != "" ) {
            $result = $db->Execute( "SELECT name FROM " . $db->prefix( "bannerclient" ) . " WHERE cid=" . $obj->getVar( 'cid' ) );
            list( $name ) = $db->fetchRow( $result );

            $new_name = preg_replace( '!\s+!', '_', strtolower( $name ) );
            $banner_dir = ZAR_ROOT_PATH . "/images/banners/$new_name";
            zarilia_admin_mkdir( $banner_dir, 0777 );
            if ( file_exists( $banner_dir . "/" . $_FILES['userfile']['name'] ) ) {
                redirect_header( $_PHP_SELF, 2, _MA_AD_IMAGEEXIST );
            }

            $allowed_mimetypes = array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png' );
            include_once ZAR_ROOT_PATH . '/class/uploader.php';
            $uploader = new ZariliaMediaUploader( $banner_dir, $allowed_mimetypes, 250000, $bannerAdd->getVar( 'add_sizew' ), $bannerAdd->getVar( 'add_sizeh' ) );
            if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][0] ) ) {
                if ( !$uploader->upload() ) {
                    return $uploader->getErrors();
                } else {
                    $imageurl = ZAR_URL . "/images/banners/$new_name/" . $uploader->getMediaName();
                }
            } else {
                return $uploader->getErrors();
            }
        }
        return $imageurl;
    }
}

?>