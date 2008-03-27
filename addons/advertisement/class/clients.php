<?php
// $Id: clients.php,v 1.1 2007/03/16 02:34:24 catzwolf Exp $
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
 * Banner Clients
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaClients extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaClients( $id = null ) {
        $this->zariliaObject();
        $this->initVar( "cid", XOBJ_DTYPE_INT, null, false );
        $this->initVar( "name", XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( "address", XOBJ_DTYPE_TXTBOX, null, false, 120 );
        $this->initVar( "city", XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( "state", XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( "zipcode", XOBJ_DTYPE_TXTBOX, null, false, 6 );
        $this->initVar( "country", XOBJ_DTYPE_TXTBOX, null, false, 50 );
        $this->initVar( "contact", XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( "email", XOBJ_DTYPE_TXTBOX, null, false, 60 );
        $this->initVar( "telephone", XOBJ_DTYPE_TXTBOX, null, false, 20 );
        $this->initVar( "userid", XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'extrainfo', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( "created", XOBJ_DTYPE_INT, null, false );
        $this->initVar( "isactive", XOBJ_DTYPE_INT, 1, false );
        $this->initVar( "editownsendemail", XOBJ_DTYPE_INT, null, false );
        $this->initVar( "report", XOBJ_DTYPE_INT, null, false );
        $this->initVar( "editownsettings", XOBJ_DTYPE_INT, null, false );
        $this->initVar( "deactivate", XOBJ_DTYPE_INT, 1, false );
        $this->initVar( "manageown", XOBJ_DTYPE_INT, null, false );
    }

    function clientForm( $caption, $opt ) {
        require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $form = new ZariliaThemeForm( _MA_AD_CREATECLIENT, 'clientadd', zarilia_getenv( 'PHP_SELF' ) );
        switch ( $opt ) {
            case 0;
                $form->addElement( new ZariliaFormSelectUser( _MA_AD_CLINUNAME, 'userid', false, $this->getVar( 'userid' ), 1, false ), true );
                $ele = new ZariliaFormText( _MA_AD_CLINAMET, 'name', 50, 60, $this->getVar( 'name', 'e' ) );
                $ele->setDescription( _MA_AD_CLINAMET_DSC );
                $form->addElement( $ele, true );

                $form->addElement( new ZariliaFormText( _MA_AD_CLINADDRESS, 'address', 50, 60, $this->getVar( 'address', 'e' ) ), true );
                $form->addElement( new ZariliaFormText( _MA_AD_CLINCITY, 'city', 30, 60, $this->getVar( 'city', 'e' ) ), true );
                $form->addElement( new ZariliaFormText( _MA_AD_CLINSTATE, 'state', 30, 50, $this->getVar( 'state', 'e' ) ), true );
                $country = ( $this->isNew() ) ? 'US' : $this->getVar( 'country' );
                $form->addElement( new ZariliaFormSelectCountry( _MA_AD_CLINCOUNTRY, 'country', $country, 1 ), true );

                $zip = new ZariliaFormText( _MA_AD_CLINZIPCODE, 'zipcode', 6, 6, $this->getVar( 'zipcode', 'e' ) );
                $zip->setDescription( _MA_AD_CLINZIPCODE_DSC );
                $form->addElement( $zip, true );
                $index_spotlight_radio = new ZariliaFormRadioYN( _MA_AD_ISACTIVE, 'isactive', $this->getVar( 'isactive' ) , ' ' . _MA_AD_ACTIVE . '', ' ' . _MA_AD_SUSPENDED . '' );
                $form->addElement( $index_spotlight_radio );

                $form->insertSplit( _MA_AD_CONTACTINFO );
                $form->addElement( new ZariliaFormText( _MA_AD_CONTNAMET, 'contact', 50, 200, $this->getVar( 'contact', 'e' ) ), true );
                $form->addElement( new ZariliaFormText( _MA_AD_CONTMAILT, 'email', 50, 200, $this->getVar( 'email', 'e' ) ), true );
                $form->addElement( new ZariliaFormText( _MA_AD_CONTTELEPHONE, 'telephone', 30, 30, $this->getVar( 'telephone', 'e' ) ), false );
                $form->addElement( new ZariliaFormTextArea( _MA_AD_EXTINFO, 'extrainfo', $this->getVar( 'extrainfo', 'e' ), 10, 60 ), false );

                $form->addElement( new ZariliaFormHidden( 'editownsendemail', $this->getVar( 'editownsendemail' ) ) );
                $form->addElement( new ZariliaFormHidden( 'report', $this->getVar( 'report' ) ) );
                $form->addElement( new ZariliaFormHidden( 'editownsettings', $this->getVar( 'editownsettings' ) ) );
                $form->addElement( new ZariliaFormHidden( 'deactivate', $this->getVar( 'deactivate' ) ) );
                $form->addElement( new ZariliaFormHidden( 'manageown', $this->getVar( 'manageown' ) ) );
                break;

            case 1;
                /**
                 * Document Text Formatting options
                 */
                $report_tray = new ZariliaFormElementTray( _MA_AD_REPORTS, '<br />' );
                $sendemail_checkbox = new ZariliaFormCheckBox( '', 'editownsendemail', $this->getVar( 'editownsendemail' ) );
                $sendemail_checkbox->addOption( 1, _MA_AD_DEACTIVATED );
                $report_tray->addElement( $sendemail_checkbox );
                $report_checkbox = new ZariliaFormCheckBox( '', 'report', $this->getVar( 'report' ) );
                $report_checkbox->addOption( 1, _MA_AD_EMAILREPORT );
                $report_tray->addElement( $report_checkbox );
                $form->addElement( $report_tray );
                /**
                 * Document Text Formatting options
                 */
                $options_tray = new ZariliaFormElementTray( _MA_AD_OPTIONS, '<br />' );
                $settings_checkbox = new ZariliaFormCheckBox( '', 'editownsettings', $this->getVar( 'editownsettings' ) );
                $settings_checkbox->addOption( 1, _MA_AD_MODIFYSETTINGS );
                $options_tray->addElement( $settings_checkbox );
                $deactivate_checkbox = new ZariliaFormCheckBox( '', 'deactivate', $this->getVar( 'deactivate' ) );
                $deactivate_checkbox->addOption( 1, _MA_AD_DEACTIVATE );
                $options_tray->addElement( $deactivate_checkbox );

				$manageown_checkbox = new ZariliaFormCheckBox( '', 'manageown', $this->getVar( 'manageown' ) );
                $manageown_checkbox->addOption( 1, _MA_AD_MANAGE );
                $options_tray->addElement( $manageown_checkbox );
                $form->addElement( $options_tray );
                break;
        }
        $create_tray = new ZariliaFormElementTray( '', '' );
        $create_tray->addElement( new ZariliaFormHidden( 'op', 'save' ) );
        $create_tray->addElement( new ZariliaFormHidden( 'cid', $this->getVar( 'cid' ) ) );
        if ( $opt == 1 ) {
            $create_tray->addElement( new ZariliaFormHidden( 'email', $this->getVar( 'email' ) ) );
        }
        $create_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $form->addElement( $create_tray );
        return $form;
    }

    /**
     * Display a human readable date form
     * parm: intval: 	$time	- unix timestamp
     */
    function formatTimeStamp( $time = '', $format = 'D, M-d-Y' ) {
        if ( $time == '' ) {
            $time = $this->getVar( 'created' );
        }
        $ret = formatTimestamp( $time, $format );
        return $ret;
    }
}

/**
 * ZariliaBannersClientsHandler
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: clients.php,v 1.1 2007/03/16 02:34:24 catzwolf Exp $
 * @access public
 */
class ZariliaClientsHandler extends ZariliaPersistableObjectHandler {
    /**
     * constructor
     */
    function ZariliaClientsHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'bannerclient', 'zariliaclients', 'cid' );
    }

    /**
     * ZariliaBannersClientsHandler::getInstance()
     *
     * @param  $db
     * @return
     */
    function &getInstance( &$db ) {
        static $instance;
        if ( !isset( $instance ) ) {
            $instance = new ZariliaBannersClientsHandler( $db );
        }
        return $instance;
    }

    /**
     * ZariliaBannersClientsHandler::getBannerCount()
     *
     * @param integer $limit
     * @param integer $start
     * @param string $sort
     * @param string $order
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
     * ZariliaBannersClientsHandler::getBannersClients()
     *
     * @param integer $limit
     * @param integer $start
     * @param string $sort
     * @param string $order
     * @return
     */
    function getClientsObj( $nav ) {
        $criteria = new CriteriaCompo();
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
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
    function getClients( $limit = 10, $start = 0, $sort = 'name', $order = 'ASC' ) {
        $ret = array();

        $db = &ZariliaDatabaseFactory::getDatabaseConnection();
        $sql = "SELECT cid, name FROM " . $db->prefix( "bannerclient" ) . " ORDER BY $sort $order";
        $result = $db->Execute( $sql, $limit, $start );
        while ( $myrow = $result->FetchRow() ) {
            $ret[$myrow['cid']] = stripslashes( $myrow['name'] );
        }
        return $ret;
    }

    function getUserInfo( $id, $as_object = true ) {
        $ret = false;
        $criteria = new Criteria( 'userid', intval( $id ) );
        $criteria->setLimit( 1 );
        $obj_array = $this->getObjects( $criteria, false, $as_object );
        if ( !is_array( $obj_array ) || count( $obj_array ) != 1 ) {
            $rez = false;
            return $rez;
        } else {
            $ret = &$obj_array[0];
        }
        return $ret;
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
     * ZariliaBannersClientsHandler::get_chmod_folder()
     *
     * @param string $chmod
     * @return
     */
    function get_chmod_folder( $chmod = '0666' ) {
        $mkdir = mkdir( $path_name, $chmod );
        return $mkdir;
    }

    function getClientInfo( $cid ) {
        $client_obj = $this->get( $cid );
        if ( !is_object( $client_obj ) ) {
            return '';
        }

        $ret = "
			<div style='text-align: left; padding-bottom: 12px;'><b>" . _AM_CLIENTINFO . "</b></div>
			 <table width='100%' cellpadding='2' cellspacing='1' class='outer'>
			  <tr style='text-align: left;'>
			   <td width='10%' rowspan='4'><div align='center'>" . zarilia_img_show( 'client', '', 'middle', 'images/icons' ) . "</div></td>
			   <td width='10%'><b>Name:</b> </td>
			   <td>" . $client_obj->getVar( 'name' ) . "</td>
			   <td width='10%'><b>Name:</b> </td>
			   <td>" . $client_obj->getVar( 'name' ) . "</td>
			  </tr>
			 <tr style='text-align: left;'>
			  <td><b>Contact:</b> </td>
			  <td>" . $client_obj->getVar( 'contact' ) . "</td>
			 </tr>
			 <tr style='text-align: left;'>
			  <td><b>Email:</b> </td>
			  <td>" . $client_obj->getVar( 'email' ) . "</td>
			 </tr>
			 <tr style='text-align: left;'>
			 <td><b>Extra Info:</b> </td>
			 <td>" . $client_obj->getVar( 'extrainfo' ) . "</td>
			 </tr>
			</table><br />";
        unset( $client_obj );
        return $ret;
    }
}

?>