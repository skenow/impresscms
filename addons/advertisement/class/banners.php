<?php
// $Id: banners.php,v 1.3 2007/04/21 09:40:23 catzwolf Exp $
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
class ZariliaBanners extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaBanners( $id = null ) {
        $this->zariliaObject();
        $this->initVar( 'bid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'bannername', XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( 'bannertype', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'bannerpayments', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'cid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'imptotal', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'impmade', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'clicks', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'imageurl', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'clickurl', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'date', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'htmlcode', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'expiredate', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'publishdate', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'active', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'discount', XOBJ_DTYPE_INT, 0, true );
        $this->initVar( 'mediatype', XOBJ_DTYPE_TXTBOX, null, false, 30 );
    }

    function bannerForm( $caption, $cid = 0, $opt ) {
        global $zariliaDB, $client_handler, $banneradds_handler;

        require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $form = new ZariliaThemeForm( $caption, 'clientadd', zarilia_getenv( 'PHP_SELF' ) );
        $form->setExtra( 'enctype="multipart/form-data"' );
        switch ( $opt ) {
            case 0;
                $form->addElement( new ZariliaFormLabel( _MA_AD_EBANNERID, $this->getVar( 'bid' ) ) );
                $form->addElement( new ZariliaFormText( _MA_AD_EBANNERNAME, 'bannername', 50, 200, $this->getVar( 'bannername', 'e' ) ), true );
                $cid = ( $this->getVar( 'cid' ) ) ? $this->getVar( 'cid' ) : $cid;
                $userArray = $client_handler->getClients();
                $select = new ZariliaFormSelect( _MA_AD_ECLINAMET, 'cid', $cid );
                $select->addOption( '', 'Please Select Client' );
                $select->addOptionArray( $userArray );
                $form->addElement( $select, true );
                // if ( checkURL( $this->getVar( 'imageurl' ) ) == true ) {
                // $form->addElement( new ZariliaFormText( _MA_AD_EIMGURLT, 'imageurl', 50, 200, $this->getVar( 'imageurl', 'e' ) ), false );
                // $imageselect = new ZariliaFormSelectImg( _MA_AD_EAVATAR_SELECTIMAGE, 'imageselect', '', $id = 'zarilia_image', 0 );
                // } else {
                // $form->addElement( new ZariliaFormText( _MA_AD_EIMGURLT, 'imageurl', 50, 200, '' ), false );
                if ( isset( $userArray[$cid] ) ) {
                    $imageselect = new ZariliaFormSelectImg( _MA_AD_EIMGURLT, 'imageselect', basename( $this->getVar( 'imageurl' ) ), $id = 'zarilia_image', 0 );
                    // $imageselect->setDescription( _MA_AD_EAVATAR_SELECTIMAGE_DSC );
                    $basedir = createdir( $userArray[$cid] );
                    $imageselect->setCategory( 'uploads/' . $basedir );
                    $form->addElement( $imageselect );
                }

                $isnew = ( $this->isNew() ) ? true : false;
                $form->addElement( new ZariliaFormFile( _MA_AD_EIMGUPLOAD, 'userfile', '' ), false );
                $clickurl = ( $this->getVar( 'clickurl' ) ) ? $this->getVar( 'clickurl', 'e' ) : 'http://';
                $form->addElement( new ZariliaFormText( _MA_AD_ECLICKURLT, 'clickurl', 50, 200, $clickurl ), true );
                $html_banner = new ZariliaFormTextArea( _MA_AD_ECODEHTML, 'htmlcode', $this->getVar( 'htmlcode', 'e' ), 10, 60 );
                $html_banner->setDescription( _MA_AD_ECODEHTML_DSC );
                $form->addElement( $html_banner, false );

                $form->insertSplit( _MA_AD_CONTACTINFO );

                $bannersArray = $banneradds_handler->getBanneradds();
                $bselect = new ZariliaFormSelect( _MA_AD_EBANNERTYPE, 'bannertype', $this->getVar( 'bannertype' ) );
                $bselect->addOptionArray( $bannersArray );
                $form->addElement( $bselect, true );

                $cselect = new ZariliaFormSelect( _MA_AD_EBANNERTYPE, 'bannerpayments', $this->getVar( 'bannerpayments' ) );
                $cselect->addOption( 0, 'Impressions Only' );
                $cselect->addOption( 1, _MA_AD_WEEKLY );
                $cselect->addOption( 2, _MA_AD_MONTHLY );
                $cselect->addOption( 3, _MA_AD_YEARLY );
                $form->addElement( $cselect, true );
                $form->addElement( new ZariliaFormText( _MA_AD_EDISCOUNT, 'discount', 10, 50, $this->getVar( 'discount' ) ), true );
                $time = ( $this->isNew() ) ? time() : $this->getVar( 'publishdate' );
                $form->addElement( new ZariliaFormDateTime( _MA_AD_ESETPUBLISHDATE, 'publishdate', 15, $time, false ) );
                $form->addElement( new ZariliaFormDateTime( _MA_AD_ESETEXPIREDATE, 'expiredate', 15, $this->getVar( 'expiredate' ), false ) );

                $active = ( $this->isNew() ) ? 1 : $this->getVar( 'active' );
                $index_usehtml_radio = new ZariliaFormRadioYN( _MA_AD_EISACTIVE, 'active', $active, ' ' . _YES . '', ' ' . _NO . '' );
                $form->addElement( $index_usehtml_radio );

                $options_tray = new ZariliaFormElementTray( _MA_AD_EADDIMPT, '<br />' );
                $impadded_textbox = new ZariliaFormText( '', 'impadded', 12, 11 );
                $options_tray->addElement( $impadded_textbox );

                $impshown_textbox = new ZariliaFormLabel( '<br />', _MA_AD_EPURCHT . " <b>" . $this->getLeft() . "</b><br />" . _MA_AD_EMADET . "<b>" . $this->getVar( 'impmade' ) . "</b><br />" );
                $options_tray->addElement( $impshown_textbox );
                $form->addElement( $options_tray );
                break;

            case 1:
                $_code = "<div style='padding: 5px; text-align: center;'>";
                if ( $this->getVar( 'htmlcode' ) ) {
                    $_code .= $this->getVar( 'htmlcode' );
                } else {
                    $imageurl = $this->getVar( 'imageurl' );
                    if ( strtolower( substr( $imageurl, strrpos( $imageurl, "." ) ) ) == ".swf" ) {
                        $_code .= "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/ swflash.cab#version=6,0,40,0\"; width=\"468\" height=\"60\">";
                        $_code .= "<param name=movie value=\"$imageurl\">";
                        $_code .= "<param name=quality value=high>";
                        $_code .= "<embed src=\"$imageurl\" quality=high pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\"; type=\"application/x-shockwave-flash\" width=\"468\" height=\"60\">";
                        $_code .= "</embed>";
                        $_code .= "</object>";
                    } else if ( $imageurl ) {
                        $_code .= "<img src='$imageurl' alt='' border='1'/>";
                    } else {
                        $_code .= '';
                    }
                }
                $_code .= "</div>";

                $html_banner = new ZariliaFormTextArea( _MA_AD_EGETBANNERCODE, 'htmlcode', htmlspecialchars( $_code ), 10, 60 );
                $html_banner->setDescription( _MA_AD_EGETBANNERCODE_DSC );
                $form->addElement( $html_banner, false );
                break;
        }

        if ( $opt != 1 ) {
            $form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
            $form->addElement( new ZariliaFormHidden( 'bid', $this->getVar( 'bid' ) ) );
            $form->addElement( new ZariliaFormHidden( 'opt', $opt ) );
            $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
        }
        return $form;
    }

    /**
     * Display a human readable date form
     * parm: intval: 	$time	- unix timestamp
     */
    function formatTimeStamp( $time = '', $format = '' ) {
        if ( $time == '' ) {
            $time = $this->getVar( 'created' );
        }
        if ( !$this->getVar( $time ) ) {
            return '----------';
        }
        return formatTimestamp( $this->getVar( $time ), $format );
    }

    function getPercent() {
        $percent = ( $this->getVar( 'impmade' ) == 0 ) ? 0 : substr( 100 * $this->getVar( 'clicks' ) / $this->getVar( 'impmade' ), 0, 5 );
        return $percent . "%";
    }

    function getLeft() {
        $left = ( $this->getVar( 'imptotal' ) == 0 ) ? _MA_AD_UNLIMITED : ( $this->getVar( 'imptotal' ) - $this->getVar( 'impmade' ) );
        return $left;
    }

    function getClientName() {
        global $client_handler;
        $client_obj = $client_handler->get( $this->getVar( 'cid' ) );
        if ( $client_obj ) {
            return $client_obj->getVar( 'name' );
        } else {
            return '';
        }
    }

    function showImage() {
        if ( $this->isNew() ) {
            return '';
        }

        $_code = "
		 <fieldset>
		 <legend><b>Example</b></legend>
		 <div style='padding: 5px; text-align: center;'>";
        if ( $this->getVar( 'htmlcode' ) ) {
            $_code .= $this->getVar( 'htmlcode' );
        } else {
            $imageurl = $this->getVar( 'imageurl' );
            if ( strtolower( substr( $imageurl, strrpos( $imageurl, "." ) ) ) == ".swf" ) {
                $_code .= "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/ swflash.cab#version=6,0,40,0\"; width=\"468\" height=\"60\">";
                $_code .= "<param name=movie value=\"$imageurl\">";
                $_code .= "<param name=quality value=high>";
                $_code .= "<embed src=\"$imageurl\" quality=high pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\"; type=\"application/x-shockwave-flash\" width=\"468\" height=\"60\">";
                $_code .= "</embed>";
                $_code .= "</object>";
            } else if ( $imageurl ) {
                $_code .= "<img src='" . $imageurl . "' title='" . $this->getVar( 'bannername' ) . "' alt='" . $this->getVar( 'bannername' ) . "' border='1' />";
            } else {
                $_code .= 'No image stored for this banner';
            }
        }
        $_code .= "</div></fieldset><br />";
        return $_code;
    }

    function setDate( $format = 'publishdate' ) {
        global $_REQUEST;
        $_date = zarilia_cleanRequestVars( $_REQUEST, $format, '' );
        if ( isset( $_date['date'] ) && !empty( $_date['date'] ) ) {
            $_date = strtotime( $_date['date'] ) + $_date['time'];
        } else {
            $_date = '';
        }
        $this->setVar( $format, $_date );
    }
}

/**
 * ZariliaBannersHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: banners.php,v 1.3 2007/04/21 09:40:23 catzwolf Exp $
 * @access public
 */
class ZariliaBannersHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaBannersHandler::ZariliaBannersHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaBannersHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'banner', 'ZariliaBanners', 'bid', 'bannername' );
    }

    /**
     * categoryHandler::getInstance()
     *
     * @param  $db
     * @return
     */
    function &getInstance( &$db ) {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaBannersHandler( $db );
        }
        return $instance;
    }

    /**
     * ZariliaBannersHandler::getBannerCount()
     *
     * @param integer $cid
     * @return
     */
    function &getBannerCount( $cid = 0 ) {
        $criteria = new CriteriaCompo();
        if ( $cid != 0 ) {
            $criteria->add( new Criteria( 'cid', $cid ) );
        }
        $count = $this->getCount( $criteria, false );
        return $count;
    }

    /**
     * ZariliaBannersClientsHandler::get_create_folder()
     *
     * @param string $path_name
     * @param string $chmod
     * @return
     */
    function get_create_folder( $path_name = '', $chmod = '0666' ) {
        if ( is_dir( $path_name ) && !empty( $path_name ) ) {
            return true;
        }
        if ( file_exists( $path_name ) && !is_dir( $path_name ) ) {
            return false;
        }
        $mkdir = mkdir( $path_name, $chmod );
        return $mkdir;
    }

    /**
     * ZariliaBannersHandler::getBannersObj()
     *
     * @param array $nav
     * @param mixed $criteria
     * @param mixed $cid
     * @return
     */
    function getBannersObj( $nav = array(), $criteria = null, $opt = null, $cid = 0 ) {
        $criteria = new CriteriaCompo();
        switch ( $opt ) {
            case '0':
                $criteria->add ( new Criteria( 'publishdate', 0, '>' ) );
                $criteria->add ( new Criteria( 'publishdate', time(), '<=' ), 'AND' );
                $criteria->add ( new Criteria( 'active', 1, '=' ), 'AND' );
                $criteria->add ( new Criteria( 'expiredate', 0, '=' ) );
                $criteria->add ( new Criteria( 'expiredate', time(), '>' ), 'OR' );
                break;
            case '1':
                $criteria->add ( new Criteria( 'expiredate', 0, '>' ) );
                $criteria->add ( new Criteria( 'expiredate', time(), '<' ), 'OR' );
                $criteria->add ( new Criteria( 'active', 0, '=' ), 'AND' );
                $criteria->add ( new Criteria( 'publishdate', 0, '>' ), 'AND' );
                //$criteria->add ( new Criteria( 'imptotal', 'impmade', '=' ), 'OR' );
                //$criteria->add ( new Criteria( 'imptotal', 0, '>' ), 'AND' );
				break;
            case '2':
                $criteria->add ( new Criteria( 'publishdate', time(), '>' ) );
                break;
            case 3:
                $criteria->add ( new Criteria( 'active', '0', '=' ) );
                break;
            default:
                break;
        } // switch
        if ( $cid != 0 ) {
            $criteria->add( new Criteria( 'cid', $cid ) );
        }
        $obj['count'] = $this->getCount( $criteria, true );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, true );
        return $obj;
    }

    function setUpload( &$obj ) {
        global $zariliaConfigUser, $banneradds_handler;

        $db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $result = $db->Execute( "SELECT name FROM " . $db->prefix( "bannerclient" ) . " WHERE cid=" . $obj->getVar( 'cid' ) );
        list( $name ) = $db->fetchRow( $result );

        $bannerdir = createdir( $name );
        if ( !empty( $_FILES[$_POST['zarilia_upload_file'][0]]['name'] ) || !$obj ) {
            require ZAR_ROOT_PATH . '/class/uploader.php';

            $bannerAdd = $banneradds_handler->get( $obj->getVar( 'bannertype' ) );
            if ( $bannerAdd ) {
                $width = $bannerAdd->getVar( 'add_sizew' );
                $height = $bannerAdd->getVar( 'add_sizeh' );
            } else {
                $width = 486;
                $height = 60;
            }
            $do_check = true;
            $image_array = array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' );
            $uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH . DIRECTORY_SEPARATOR . $bannerdir, $image_array, 250000, $width, $height );
            $uploader->setPrefix( 'ban' );
            $ucount = count( $_POST['zarilia_upload_file'] );
            for ( $i = 0; $i < $ucount; $i++ ) {
                if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][$i] ) ) {
                    if ( !$uploader->upload() ) {
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $uploader->getErrors() );
                    } else {
                        $image = $uploader->getSavedFileName();
                    }
                } else {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILFETCHIMG, $i ) );
                }
            }
        } else if ( !empty( $_REQUEST['imageselect'] ) && $_REQUEST['imageselect'] != "||" ) {
            $do_check = true;
            $image = explode( '|', $_REQUEST['imageselect'] );
            $image = $bannerdir . '/' . $image[0];
        } else if ( !empty( $_REQUEST['imageurl'] ) ) {
            $do_check = true;
            $image = $bannerdir . '/' . $_REQUEST['imageurl'];
        } else {
            $do_check = false;
            if ( !isset( $_REQUEST['imageurl'] ) || empty( $_REQUEST['imageurl'] ) ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Please enter the HTML code you would like to use for this banner Or Select a banner image from the selection menu.' );
            }
        }
        $obj->setVar( 'imageurl', ZAR_UPLOAD_URL . '/' . $image );
        /**
         * Do check only if there is a file involved else ignore check
         */
        if ( $do_check == true ) {
            $imagedetails = @getimagesize( ZAR_UPLOAD_PATH . '/' . $image );
            $result = $db->Execute( "SELECT add_sizew, add_sizeh FROM " . $db->prefix( "badds" ) . " WHERE add_id = " . $obj->getVar( 'bannertype' ) );
            list( $add_sizew, $add_sizeh ) = $db->fetchRow( $result );
            if ( $add_sizew != $imagedetails[0] && $add_sizeh != $imagedetails[1] ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, 'Image details do not match selected banner advertisement width and height. Image details must match exactly!' );
                if ( $obj->isNew() ) {
                    unlink( ZAR_UPLOAD_PATH . '/' . $image );
                }
            }
        }
    }

    function expireimpressions() {
        $db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $sql = "UPDATE " . $db->prefix( "banner" ) . " SET expiredate = " . time() . " WHERE (imptotal > 0 AND impmade > 0) AND impmade >= imptotal";
        if ( $result = $db->Execute( $sql ) ) {
            return true;
        } else {
            return false;
        }
    }
}

?>