<?php
// $Id: index.php,v 1.4 2007/05/05 11:10:42 catzwolf Exp $
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
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) )
{
    exit( 'Access Denied' );
}

require_once "admin_menu.php";
require_once ZAR_ROOT_PATH . '/class/class.permissions.php';

$profilecat_handler = &zarilia_gethandler( 'profilecategory' );
$profile_handler = &zarilia_gethandler( 'profile' );
/**
 */
switch ( strtolower( $op ) )
{
    case 'maintenace':
        $act = zarilia_cleanRequestVars( $_REQUEST, 'act', '', XOBJ_DTYPE_TXTBOX );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        switch ( $ok )
        {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'maintenace',
                        'act' => $act,
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AD_DOTABLE, $act )
                    );
                break;
            case 1:
                $act = zarilia_cleanRequestVars( $_REQUEST, 'act', '', XOBJ_DTYPE_TXTBOX );
                if ( false == $content_handler->doDatabase( $act ) )
                {
                    zarilia_cp_header();
                    $menu_handler->render( 0 );
                    $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                }
                redirect_header( $addonversion['adminpath'], 1, sprintf( _MD_AD_DOTABLEFINSHED, $act ) );
                break;
        } // switch
        break;
    /**
     */
    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php" ) )
        {
            @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        }
        break;
    /**
     */
    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 5 );

        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    /*
	* Category settings
	*/
    case 'edit_category':
    case 'create_category':
        $profilecat_id = zarilia_cleanRequestVars( $_REQUEST, 'profilecat_id', 0 );
        $_profilecat_obj = ( $profilecat_id > 0 ) ? $profilecat_handler->get( $profilecat_id ) : $profilecat_handler->create();
        if ( !$_profilecat_obj )
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _AM_US_SECTIONNOTFOUND );
            zarilia_cp_header();
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit_profile" => _MA_AD_BUTTON_NEW, $addonversion['adminpath'] . "&amp;op=edit_category" => _MA_AD_BUTTON_NEW_CAT ) );
        $menu_handler->render( 2 );
        $form = $_profilecat_obj->formEdit();
        $form->display();
        break;
    /**
     */
    case 'save_category':
        $profilecat_id = zarilia_cleanRequestVars( $_REQUEST, 'profilecat_id', 0 );
        $_profilecat_obj = ( $profilecat_id > 0 ) ? $profilecat_handler->get( $profilecat_id ) : $profilecat_handler->create();
        if ( !is_object( $_profilecat_obj ) )
        {
            $menu_handler->render( 2 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $profilecat_handler->getErrors() );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /**
         */
        $_profilecat_obj->setVars( $_REQUEST );
        if ( $profilecat_handler->insert( $_profilecat_obj, false ) )
        {
            $profilecat_id = $_profilecat_obj->getVar( 'profilecat_id' );
            $mod_id = $zariliaAddon->getVar( 'mid' );
            /*save psermissions*/
            $read_array = zarilia_cleanRequestVars( $_REQUEST, 'readgroup', array(), XOBJ_DTYPE_ARRAY );
            $readgroup = new cpPermission( '', 'profilecat_read', '', $mod_id );
            $readgroup->cpPermission_save( $read_array, $profilecat_id );
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_category', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 2 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case "delete_category":
        /**
         * create new mimetype object
         */
        $profilecat_id = zarilia_cleanRequestVars( $_REQUEST, 'profilecat_id', 0 );
        $_profilecat_obj = $profilecat_handler->get( $profilecat_id );
        if ( !is_object( $_profilecat_obj ) )
        {
            zarilia_cp_header();
            $menu_handler->render( 2 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, $profilecat_handler->getErrors() );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        if ( zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 ) )
        {
            $criteria = new CriteriaCompo();
            $criteria->add( new Criteria( 'profile_modid', 0 ) );
            $criteria->add( new Criteria( 'profile_catid', $profilecat_id ) );
            $profile_handler->deleteAll( $criteria );
            if ( $profilecat_handler->delete( $_profilecat_obj, false ) )
            {
                $readgroup = new cpPermission( '', 'profilecat_read', '', $zariliaAddon->getVar( 'mid' ) );
                $readgroup->cpPermission_delete( $profilecat_id );
            }
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_category', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 2 );
            zarilia_confirm(
                array( 'op' => 'delete_category',
                    'profilecat_id' => $profilecat_id,
                    'ok' => 1,
                    'cat_name' => $_profilecat_obj->getVar( 'profilecat_name' ) ),
                $addonversion['adminpath'],
                _MA_AD_EPROFILECAT_DELETE . "<br /><br>" . $_profilecat_obj->getVar( 'profilecat_name' )
                );
        }
        break;

    case 'deleteall_category':
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $profilecat_id )
        {
            $_profilecat_obj = $profilecat_handler->get( $profilecat_id );
            if ( !$profilecat_handler->delete( $_profilecat_obj, false ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_profilecat_obj->getVar( 'profilecat_name' ) ) );
            }
            else
            {
                $criteria = new CriteriaCompo();
                $criteria->add( new Criteria( 'profile_modid', 0 ) );
                $criteria->add( new Criteria( 'profile_catid', $profilecat_id ) );
                $profile_handler->deleteAll( $criteria );
                /*
				* Delete permissions
				*/
                $readgroup = new cpPermission( '', 'profilecat_read', '', $zariliaAddon->getVar( 'mid' ) );
                $readgroup->cpPermission_delete( $profilecat_id );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_category', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'updateall_category':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $profilecat_name = zarilia_cleanRequestVars( $_REQUEST, 'profilecat_name', array() );
        $profilecat_order = zarilia_cleanRequestVars( $_REQUEST, 'profilecat_order', array() );
        foreach ( $value_id as $id => $profilecat_id )
        {
            $_profilecat_obj = $profilecat_handler->get( $profilecat_id );
            if ( isset( $profilecat_name[$id] ) )
            {
                $_profilecat_obj->setVar( 'profilecat_name', $profilecat_name[$id] );
            }
            if ( isset( $profilecat_order[$id] ) )
            {
                $_profilecat_obj->setVar( 'profilecat_order', $profilecat_order[$id] );
            }
            /**
             */
            if ( !$profilecat_handler->insert( $_profilecat_obj, false ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_profilecat_obj->getVar( 'profilecat_name' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_category', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'cloneall_category':
        unset( $_REQUEST['value_id'] );
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        /**
         */
        foreach ( array_keys( $checkbox ) as $profilecat_id )
        {
            $readgroupold = new cpPermission( '', 'profilecat_read', '', $zariliaAddon->getVar( 'mid' ) );
            $read_array = $readgroupold->cpPermission_get( $profilecat_id );
            /**
             */
            $_profilecat_obj = &$profilecat_handler->get( $profilecat_id );
            $_profilecat_obj->setVar( 'profilecat_id', '' );
            $_profilecat_obj->setVar( 'profilecat_name', $_profilecat_obj->getVar( 'profilecat_name' ) . '_cloned' );
            $_profilecat_obj->setNew();
            if ( !$profilecat_handler->insert( $_profilecat_obj, false ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_profilecat_obj->getVar( 'profilecat_name' ) ) );
            }
            else
            {
                if ( !empty( $read_array ) )
                {
                    $readgroup = new cpPermission( '', 'profilecat_read', '', $zariliaAddon->getVar( 'mid' ) );
                    $readgroup->cpPermission_save( $read_array, $_profilecat_obj->getVar( 'profilecat_id' ) );
                }
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_category', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'list_category':
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        /*
        * required for Navigation
        */
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'profilecat_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );

        $form = "<div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div><div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list_category&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit_profile" => _MA_AD_BUTTON_NEW, $addonversion['adminpath'] . "&amp;op=edit_category" => _MA_AD_BUTTON_NEW_CAT ), _MD_AD_FILTER_BOX, $form );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'profilecat_id', '2%', 'center', true );
        $tlist->AddHeader( 'profilecat_name', '15%', 'left', true );
        $tlist->AddHeader( 'profilecat_order', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '5%', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'profiles' );
        $_array = array( 'updateall_category' => 'Update Selected', 'deleteall_category' => 'Delete Selected', 'cloneall_category' => 'Clone Selected' );
        $tlist->addFooter( $profilecat_handler->setSubmit( 'fct', $fct, $_array ) );

        $button = array( 'edit_category' => 'edit', 'delete_category' => 'delete', 'clone_category' => 'clone' );
        $profile_cat_obj = &$profilecat_handler->getProfileObj( $nav );
        $i = 0;
        foreach ( $profile_cat_obj['list'] as $obj )
        {
            $profilecat_id = $obj->getVar( 'profilecat_id' );
            // This line is required to make the boxes work correctly//
            $tlist->addHidden( $profilecat_id, 'value_id' );
            $tlist->add(
                array( $profilecat_id,
                    $obj->getTextbox( 'profilecat_id', 'profilecat_name', '50' ),
                    $obj->getTextbox( 'profilecat_id', 'profilecat_order', '5' ),
                    $obj->getCheckbox( 'profilecat_id' ),
                    zarilia_cp_icons( $button, 'profilecat_id', $profilecat_id )
                    ) );
            $i++;
        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $profile_cat_obj['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;limit=' . $nav['limit'] );
        break;

    case 'edit_profile':
        include_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";

        $profile_id = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', 0 );
        $profile = ( $profile_id == 0 ) ? $profile_handler->createProfile() : $profile_handler->getProfile( $profile_id );

        zarilia_cp_header();
        $menu_handler->render( 4 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit_profile" => _MA_AD_BUTTON_NEW, $addonversion['adminpath'] . "&amp;op=edit_category" => _MA_AD_BUTTON_NEW_CAT ) );

        $forminfo = ( $profile_id == 0 ) ? _MA_AD_EPROFILE_CREATE : _MA_AD_EPROFILE_MODIFY;
        $sform = new ZariliaThemeForm( $forminfo , "op", $addonversion['adminpath'] );
        $confcat_handler = &zarilia_gethandler( 'profilecategory' );
        $_array = $confcat_handler->getList();

        $conf_cattype = new ZariliaFormSelect( _MA_AD_EPROFILE_FORMTYPE, 'profile_catid', $profile->getVar( 'profile_catid' ), '', '', 0 );
        $conf_cattype->addOptionArray( $_array );
        $sform->addElement( $conf_cattype );

        $profile_title = new ZariliaFormText( _MA_AD_EPROFILE_TITLE, 'profile_title', 25, 60, $profile->getVar( 'profile_title', 'e' ), 'e' );
        $profile_title->setRequired( true );
        $sform->addElement( $profile_title, true );

        /**
         */
        $profile_desc = new ZariliaFormTextArea( _MA_AD_EPROFILE_DESC, 'profile_desc', $profile->getVar( 'profile_desc', 'e' ), 4, 60, 0, 0 );
        $profile_desc->setRequired( true );
        $sform->addElement( $profile_desc, true );
        /**
         */
        $profile_name = new ZariliaFormText( _MA_AD_EPROFILE_NAME, 'profile_name', 25, 60, $profile->getVar( 'profile_name', 'e' ), 'e' );
        $profile_name->setRequired( true );
        $sform->addElement( $profile_name, true );
        /**
         */
        $profile_value = new ZariliaFormTextArea( _MA_AD_EPROFILE_VALUE, 'profile_value', $profile->getVar( 'profile_value', 'e' ), 7, 60, 0, 0 );
        $profile_value->setRequired( false );
        $sform->addElement( $profile_value, false );
        /**
         */
        $profile_formtype_array = array( 'htmltextarea', 'textarea', 'yesno', 'theme', 'timezone', 'language', 'startpage', 'textbox' );
        $profile_formtype = new ZariliaFormSelect( _MA_AD_EPROFILE_FORMTYPE, 'profile_formtype', $profile->getVar( 'profile_formtype', 'e' ), '', '', 0 );
        $profile_formtype->addOptionArray( $profile_formtype_array, false );
        $sform->addElement( $profile_formtype );

        $profile_valuetype_array = array( 'textbox', 'int', 'other', 'text' );
        $profile_valuetype = new ZariliaFormSelect( _MA_AD_EPROFILE_VALUETYPE, 'profile_valuetype', $profile->getVar( 'profile_valuetype' ), '', '', 0 );
        $profile_valuetype->addOptionArray( $profile_valuetype_array, false );
        $sform->addElement( $profile_valuetype );
        /**
         */
        $profile_order = new ZariliaFormText( _MA_AD_EPROFILE_ORDER, 'profile_order', 10, 10, $profile->getVar( 'profile_order' ), 'e' );
        $profile_order->setRequired( true );
        $sform->addElement( $profile_order, true );

        $_profile_display = ( $profile->getVar( 'profile_display' ) ) ? 1 : 0;
        $profile_display = new ZariliaFormRadioYN( _MA_AD_EPROFILE_DISPLAY, 'profile_display', $_profile_display, ' ' . _YES . '', ' ' . _NO . '' );
        $sform->addElement( $profile_display );

        $_profile_required = ( $profile->getVar( 'profile_required' ) ) ? 1 : 0;
        $profile_required = new ZariliaFormRadioYN( _MA_AD_EPROFILE_REQUIRE, 'profile_required', $_profile_required, ' ' . _YES . '', ' ' . _NO . '' );
        $sform->addElement( $profile_required );

        /**
         */
        $sform->addElement( new ZariliaFormHidden( 'op', 'save_profile' ) );
        $sform->addElement( new ZariliaFormHidden( 'profile_id', $profile_id ) );
        $sform->addElement( new ZariliaFormHidden( 'profile_sectid', 1 ) );
        $sform->addElement( new ZariliaFormHidden( 'profile_modid', 1 ) );

        $button_tray = new ZariliaFormElementTray( '', '' );
        $button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $button_tray->addElement( new ZariliaFormButton( '', 'save', _SUBMIT, 'submit' ) );
        $sform->addElement( $button_tray, false );
        $sform->display();
        unset( $hidden );

        if ( in_array( $profile->getVar( 'profile_formtype' ), array( 'select' ) ) )
        {
            $_row = array();
            $sform = new ZariliaThemeForm( $forminfo , "op", $addonversion['adminpath'] );
            $options = &$profile_handler->getProfileOptions( new Criteria( 'profile_id', $profile_id ) );

            $publishdate_tray = new ZariliaFormElementTray( _AM_PUBLISHDATE, '' );
            for ( $j = 0; $j < count( $options ); $j++ )
            {
                $publishdate_tray->addElement( new ZariliaFormText( $options[$j]->getVar( 'profileop_name' ), 'profile[]', 25, 60, $options[$j]->getVar( 'profileop_value' ), 'e' ) );
            }
            $sform->insertBreak( "", "even" );;
            $sform->addElement( $publishdate_tray );
            $create_tray = new ZariliaFormElementTray( '', '' );
            $create_tray->addElement( new ZariliaFormHidden( 'op', 'saveprofile' ) );
            $butt_save = new ZariliaFormButton( '', '', _AM_SAVECHANGE, 'submit' );
            $butt_save->setExtra( 'onclick="this.form.elements.op.value=\'save\'"' );
            $create_tray->addElement( $butt_save );
            $butt_delete = new ZariliaFormButton( '', '', _AM_DEL, 'submit' );
            $butt_delete->setExtra( 'onclick="this.form.elements.op.value=\'delete\'"' );
            $create_tray->addElement( $butt_delete );
            $sform->addElement( $create_tray );
            $sform->display();
        }
        break;

    case 'delete_profile':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $profile_id = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', 0 );
        $profile_obj = $profile_handler->getProfile( $profile_id );
        $_is_profile = ( is_object( $profile_obj ) ) ? true : false;
        if ( $ok == 1 )
        {
            if ( $_is_profile == true )
            {
                if ( !$profile_handler->deleteProfile( $profile_obj ) )
                {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, _AM_US_NOTDELUSER );
                }
            }
            else
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
            }

            if ( $GLOBALS['zariliaLogger']->getSysErrorCount() )
            {
                zarilia_cp_header();
                $menu_handler->render( 2 );
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_cp_footer();
                exit();
            }
            else
            {
                redirect_header( $addonversion['adminpath'] . '&amp;op=list_profile', 1, _DBUPDATED );
            }
            break;
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 2 );
            if ( $_is_profile == true )
            {
                zarilia_confirm( array( 'fct' => $fct, 'op' => 'delete_profile', 'profile_id' => $profile_id, 'ok' => 1 ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $profile_obj->getVar( 'profile_title' ) ) );
            }
            else
            {
                ZariliaErrorHandler_HtmlError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
            }
        }
        break;

    case 'clone_profile':
        $profile_id = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', 0 );
        $_profile_obj = $profile_handler->getProfile( $profile_id );
        $_profile_obj->setVar( 'profile_id', '' );
        $_profile_obj->setVar( 'profile_name', $_profile_obj->getVar( 'profile_name' ) . '_cloned' );
        $_profile_obj->setNew();
        if ( !$profile_handler->insertProfile( $_profile_obj, false ) )
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $_profile_obj->getVar( 'profile_title' ) ) );
        }
        /**
         */
        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        else
        {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_profiles', 1, ( $_profile_obj->isNew() ) ? _DBCREATED : _DBUPDATED );
        }
        break;

    case 'save_profile':
        $profile_id = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', 0 );
        $profile_obj = ( $profile_id != 0 ) ? $profile_handler->getProfile( $profile_id ) : $profile_handler->createProfile();
        $profile_obj->setVars( $_REQUEST );
        // $profile_obj->setVar( 'profile_sectid ', 1 );
        // $profile_obj->setVar( 'profile_modid ', 1 );
        if ( $profile_handler->insertProfile( $profile_obj, false ) )
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_profiles', 1, _DBUPDATED );
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 2 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'updateall':
        $value_id = zarilia_cleanRequestVars( $_REQUEST, 'value_id', array() );
        $profile_name = zarilia_cleanRequestVars( $_REQUEST, 'profile_name', array() );
        $profile_order = zarilia_cleanRequestVars( $_REQUEST, 'profile_order', array() );
        $profile_display = zarilia_cleanRequestVars( $_REQUEST, 'profile_display', array() );
        foreach ( $value_id as $id => $profile_id )
        {
            $profile_obj = $profile_handler->getProfile( $profile_id );
            if ( isset( $profile_name[$id] ) )
            {
                $profile_obj->setVar( 'profile_name', $profile_name[$id] );
            }
            if ( isset( $profile_order[$id] ) )
            {
                $profile_obj->setVar( 'profile_order', $profile_order[$id] );
            }
            if ( isset( $profile_display[$id] ) )
            {
                $profile_obj->setVar( 'profile_display', $profile_display[$id] );
            }
            /**
             */
            if ( !$profile_handler->insertProfile( $profile_obj, false ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $profile_obj->getVar( 'profile_name' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_profiles', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'cloneall':
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $profile_id )
        {
            $profile_obj = $profile_handler->getProfile( $profile_id );
            $profile_obj->setVar( 'profile_id', '' );
            $profile_obj->setVar( 'profile_name', $profile_obj->getVar( 'profile_name' ) . '_cloned' );
            $profile_obj->setNew();
            if ( !$profile_handler->insertProfile( $profile_obj, false ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $profile_obj->getVar( 'profile_name' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_profiles', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'deleteall':
        $checkbox = zarilia_cleanRequestVars( $_REQUEST, 'checkbox', array() );
        foreach ( array_keys( $checkbox ) as $profile_id )
        {
            $profile_obj = $profile_handler->getProfile( $profile_id );
            if ( !$profile_handler->deleteProfile( $profile_obj, false ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, sprintf( _FAILSAVEIMG, $profile_obj->getVar( 'profile_name' ) ) );
            }
        }
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            redirect_header( $addonversion['adminpath'] . '&amp;op=list_profiles', 1, _DBUPDATED );
        }
        else
        {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
        }
        break;

    case 'list_profiles':
    default:
        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $profilecat_id = zarilia_cleanRequestVars( $_REQUEST, 'profilecat_id', 0 );
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'profile_id' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 10 );
        $form = "<div class='sidetitle'>" . _MD_AD_DISPLAYAMOUNT_BOX . "</div><div class='sidecontent'> # " . zarilia_getSelection( $list_array, $nav['limit'], "limit", 1, 0, false, false, "onchange=\"location='" . ZAR_URL . "/addons/system/" . $addonversion['adminpath'] . "&amp;op=list_profiles&amp;profilecat_id=" . $profilecat_id . "&amp;limit='+this.options[this.selectedIndex].value\"" , 0, false ) . "</div>";

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'profile_id', '5', 'center', false );
        $tlist->AddHeader( 'profile_name', '15%', 'left', true );
        $tlist->AddHeader( 'profile_desc', '', 'left', true );
        $tlist->AddHeader( 'profile_order', '', 'center', true );
        $tlist->AddHeader( 'profile_display', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'], 'profiles' );
        $tlist->addFooter( $profile_handler->setSubmit() );
        $tlist->setPath( 'op=' . $op );

        $button = array( 'edit_profile' => 'edit', 'delete_profile' => 'delete', 'clone_profile' => 'clone' );
        static $menu_items;
        if ( !$menu_items )
        {
        	$profilecat_handler = &zarilia_gethandler( 'Profilecategory' );
        	$menu_items = &$profilecat_handler->getList();
			if (!is_array($menu_items)) $menu_items = array();
        }
        if ( !$profilecat_id ) {
            $menu_Array = array_values( $menu_items );
            $profilecat_id = @$menu_Array[0];
            unset( $menu_Array );
        }
        $profile = &$profile_handler->getProfileObj( $nav, $profile_modid = 1, $profilecat_id );

        zarilia_cp_header();
        $menu_handler->render( 3 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit_profile" => _MA_AD_BUTTON_NEW, $addonversion['adminpath'] . "&amp;op=edit_category" => _MA_AD_BUTTON_NEW_CAT ), _MD_AD_FILTER_BOX, $form );
        $extra = "onchange=\"location='" . $addonversion['adminpath'] . "&amp;op=" . $op . "&amp;limit=" . $nav['limit'] . "&amp;profilecat_id='+this.options[this.selectedIndex].value\"";
        zarilia_getSelection( $menu_items, $profilecat_id, 'profile', 1, 0 , false, "", $extra, 0 );
        /**
         */
        if (is_array($profile['list'])) 
			foreach ( $profile['list'] as $obj ) {
	            $profile_id = $obj->getVar( 'profile_id' );
		        $tlist->addHidden( $profile_id, 'value_id' );
			    $tlist->add(
				    array( $profile_id,
					    $obj->getTextbox( 'profile_id', 'profile_name', '30' ),
	                    $obj->getVar( 'profile_desc' ),
		                $obj->getTextbox( 'profile_id', 'profile_order', '5' ),
			            $obj->getYesNobox( 'profile_id', 'profile_display' ),
				        $obj->getCheckbox( 'profile_id' ),
					    zarilia_cp_icons( $button, 'profile_id', $profile_id )
						) );
	        }
        $tlist->render();
        zarilia_cp_legend( $button );
        zarilia_pagnav( $profile['count'], $nav['limit'], $nav['start'], 'start', 1, $addonversion['adminpath'] . '&amp;op=' . $op . '&amp;profilecat_id=' . $profilecat_id . '&amp;limit=' . $nav['limit'] );

        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_Eprofile_CREATE ),
            _MD_AD_MAINTENANCE_BOX, zariliaMainAction()
            );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>