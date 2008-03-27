<?php
// $Id: mimetype.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
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
 * Zarilia Mimetype Class
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaMimetype extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaMimetype( $id = null ) {
        $this->zariliaObject();
        $this->initVar( 'mime_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'mime_ext', XOBJ_DTYPE_TXTBOX, null, true, 10 );
        $this->initVar( 'mime_name', XOBJ_DTYPE_TXTBOX, null, true, 60 );
        $this->initVar( 'mime_types', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'mime_images', XOBJ_DTYPE_IMAGE, null, true, 120 );
        $this->initVar( 'mime_safe', XOBJ_DTYPE_INT, 0, true );
        $this->initVar( 'mime_category', XOBJ_DTYPE_TXTBOX, 'unknown', true, 10 );
        $this->initVar( 'mime_display', XOBJ_DTYPE_INT, 1, false );
    }

    function notLoaded() {
        return ( $this->getVar( 'mime_id' ) == -1 );
    }

    function open_url( $fileext ) {
        echo "<meta http-equiv=\"refresh\" content=\"0;url=http://filext.com/detaillist.php?extdetail=$fileext\">\r\n";
    }

    function formEdit() {
        require_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";

        global $mimetype_handler, $addonversion, $zariliaConfigUser;

        $form = new ZariliaThemeForm( $forminfo = ( $this->isNew() ) ? _MA_AD_MIME_CREATEF : _MA_AD_MIME_MODIFYF , "mimetype_form", $addonversion['adminpath'] );

        $form->addElement( new ZariliaFormText( _MA_AD_MIME_EXTF, 'mime_ext', 5, 60, $this->getVar( "mime_ext", "e" ) ), true );
        $form->addElement( new ZariliaFormText( _MA_AD_MIME_NAMEF, 'mime_name', 50, 60, $this->getVar( "mime_name", "e" ) ), true );

        $textarea = new ZariliaFormTextArea( _MA_AD_MIME_TYPEF, 'mime_types', $this->getVar( "mime_types", "e" ), 15, 70 );
        $textarea->setDescription( '<span style="font-size: x-small; font-weight: normal;">' . _MA_AD_MIME_USEFULTAGS . '</span></span>' );
        $form->addElement( $textarea, true );

        $mimetype_image_dir = new ZariliaFormSelectImg( _MA_AD_MIME_EIMAGE, 'mime_images', $this->getVar( 'mime_images' ), $id = 'zarilia_image', 0 );
        $mimetype_image_dir->setDescription( _MA_AD_MIME_EIMAGE_DSC );
        $mimetype_image_dir->setCategory( 'addons/system/images/mimetypes' );
        $form->addElement( $mimetype_image_dir );

        /*
        $mimetype_tray = new ZariliaFormElementTray( _MA_AD_EAVATAR_FILE, '&nbsp;' );
        $mimetype_tray->setDescription( sprintf( _MA_AD_EAVATAR_FILE_DSC, $zariliaConfigUser['avatar_maxsize'], $zariliaConfigUser['avatar_width'], $zariliaConfigUser['avatar_height'] ) );
        $mimetype_tray->addElement( new ZariliaFormFile( '', 'upload_file', $zariliaConfigUser['avatar_maxsize'] ) );
        $form->addElement( $mimetype_tray );
*/
        $mime_category = new ZariliaFormSelect( _MA_AD_MIME_ECATEGORY, 'mime_category', $this->getVar( 'mime_category' ) );
        $mime_category->setDescription( _MA_AD_MIME_ECATEGORY_DSC );
        $cat = $mimetype_handler->mimeCategory();
        unset( $cat['all'] );
        $mime_category->addOptionArray( $cat );
        $form->addElement( $mime_category );

        $mime_safe = new ZariliaFormRadioYN( _MA_AD_MIME_ESAFE, 'mime_safe', $this->getVar( 'mime_safe' ) , ' ' . _MA_AD_MIME_YSAFE . '', ' ' . _MA_AD_MIME_YUNSAFE . '' );
        $mime_safe->setDescription( _MA_AD_MIME_ESAFE_DSC );
        $form->addElement( $mime_safe );

        /*Set display name*/
        $mime_display = new ZariliaFormRadioYN( _MA_AD_MIME_EACTIVATE, 'mime_display', $this->getVar( 'mime_display' ) , ' ' . _YES . '', ' ' . _NO . '' );
        $mime_display->setDescription( _MA_AD_MIME_EACTIVATE_DSC );
        $form->addElement( $mime_display );

        $form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
        $form->addElement( new ZariliaFormHidden( 'mime_id', $this->getVar( 'mime_id' ) ) );
        $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
        $form->display();
        unset( $hidden );
    }

    function mimeCategory() {
        global $mimetype_handler;
        static $ret;

        $haystack = $mimetype_handler->mimeCategory();
        $needle = $this->getVar( 'mime_category' );
        if ( isset( $haystack[$needle] ) ) {
            return $haystack[$needle];
        } else {
            return $haystack['unknown'];
        }
    }
}

/**
 * mimetypeHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2005
 * @version $Id: mimetype.php,v 1.1 2007/03/16 02:39:11 catzwolf Exp $
 * @access public
 */
class ZariliaMimetypeHandler extends ZariliaPersistableObjectHandler {
    /**
     * constructor
     */
    function ZariliaMimetypeHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'mimetypes', 'ZariliaMimetype', 'mime_id', 'mime_types', 'mime_read' );
    }

    /**
     * mimetypeHandler::getInstance()
     *
     * @param  $db
     * @return
     */
    function &getInstance( &$db ) {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new mimetypeHandler( $db );
        }
        return $instance;
    }
    // get Mimetypes
    // function getMimetypes( $type = 1, $limit = 0, $start = 0, $sort = 'mime_id', $order = 'ASC', $id_as_key = false, $as_object = true ) {
    // if ( $type == 1 ) {
    // $criteria = new CriteriaCompo( new Criteria( 'mime_admin', 1 ) );
    // } else if ( $type == 2 ) {
    // $criteria = new CriteriaCompo( new Criteria( 'mime_user', 1 ) );
    // } else {
    // $criteria = new CriteriaCompo();
    // }
    // $criteria->setSort( $sort );
    // $criteria->setOrder( $order );
    // $criteria->setStart( $start );
    // $criteria->setLimit( $limit );
    // return $this->getObjects( $criteria, $id_as_key, $as_object );
    // }
    /**
     * ZariliaMimetypeHandler::getMimetypeObj()
     *
     * @param array $nav
     * @param mixed $mimetype_display
     * @return
     */
    function getMimetypeObj( $nav = array(), $mimetype_display = null ) {
        $criteria = new CriteriaCompo();
        if ( isset( $nav['search_text'] ) && !empty( $nav['search_text'] ) ) {
            // // $search_field = $nav['search_by'];
            // // $search_text = $nav['search_text'];
            $criteria->add( new Criteria( $nav['search_by'], "%" . $nav['search_text'] . "%", 'LIKE' ) );
        }
        if ( isset( $nav['mime_display'] ) && $nav['mime_display'] != 3 ) {
            $criteria->add( new Criteria( 'mime_display', $nav['mime_display'] ) );
        }
        if ( isset( $nav['mime_safe'] ) && $nav['mime_safe'] != 3 ) {
            $criteria->add( new Criteria( 'mime_safe', $nav['mime_safe'] ) );
        }
        // if ( isset( $nav['mime_category'] ) && $nav['mime_category'] != 'all' ) {
        // $criteria->add( new Criteria( 'mime_category', $nav['mime_category'] ) );
        // }
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    function getMtypeArray( $gperm_name = '', $modid = 1 ) {
        global $zariliaUser;

        static $permissions;
        $ret = $this->getList( null, '', null, false );

        $this_array = array();
        $new_array = array();
        foreach ( $ret as $k => $v ) {
            $new_array = explode( ' ', $v );
            $this_array = array_merge( $this_array, $new_array );
        }
        $ret = array_unique( $this_array );
        sort( $ret );
        return $ret;
    }

    function ret_mime( $filename ) {
        $ret = array();
        $zariliaDB = &ZariliaDatabaseFactory::getDatabaseConnection();
        $ext = pathinfo( $filename, PATHINFO_EXTENSION );
        $sql = "SELECT mime_types, mime_ext, mime_image FROM " . $zariliaDB->prefix( 'mimetypes' ) . " WHERE mime_ext='" . strtolower( $ext ) . "' AND mime_display=1";
        $result = $zariliaDB->Execute( $sql );
        list( $mime_types , $mime_ext, $mime_image ) = $zariliaDB->fetchrow( $result );
        $mimetypes = explode( ' ', trim( $mime_types ) );
        $ret['mimetype'] = $mimetypes[0];
        $ret['ext'] = $mime_ext;
        $ret['image'] = $mime_image;
        return $ret;
    }

    function mimeCategory( $do_select = false ) {
        $ret = array( 'all' => _MA_AD_MIME_ALLCAT,
            'unknown' => _MA_AD_MIME_CUNKNOWN,
            'archive' => _MA_AD_MIME_CARCHIVES,
            'audio' => _MA_AD_MIME_CAUDIO,
            'text' => _MA_AD_MIME_CTEXT,
            'document' => _MA_AD_MIME_CDOCUMENT,
            'help' => _MA_AD_MIME_CHELP,
            'source' => _MA_AD_MIME_CSOURCE,
            'video' => _MA_AD_MIME_CVIDEO,
            'html' => _MA_AD_MIME_CHTML,
            'graphic' => _MA_AD_MIME_CGRAPHICS,
            'midi' => _MA_AD_MIME_CMIDI,
            'binary' => _MA_AD_MIME_CBINARY
            );
        return $ret;
    }

    function mimetypeImage( $image ) {
        $zariliaDB = &ZariliaDatabaseFactory::getDatabaseConnection();

        $ret = array();
        $ext = pathinfo( $image, PATHINFO_EXTENSION );
        $sql = "SELECT mime_images FROM " . $zariliaDB->prefix( 'mimetypes' ) . " WHERE mime_ext LIKE '" . strtolower( $ext ) . "'";
        $result = $zariliaDB->Execute( $sql );
        list( $mime_images ) = $zariliaDB->fetchrow( $result );
        if ( !$mime_images ) {
            $mime_images = 'unknown.png';
        }
        return ZAR_URL . '/addons/system/images/mimetypes/' . $mime_images;
    }
}

?>