<?php
// $Id: user_edituser.php,v 1.3 2007/05/05 11:11:34 catzwolf Exp $
// auth_zarilia.php - ZARILIA authentification class
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
 * ZariliaUserEdituser
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: user_edituser.php,v 1.3 2007/05/05 11:11:34 catzwolf Exp $
 * @access public
 */
class ZariliaUserEdituser extends ZariliaAuth
{
    var $_options = array();
    /**
     * Authentication Service constructor
     */
    function ZariliaUserEdituser ()
    {
    }

    /**
     * ZariliaUserEdituser::addOptions()
     *
     * @param mixed $key
     * @param mixed $value
     * @return
     */
    function addOptions( $key = null, $value = null )
    {
        if ( !is_array( $key ) )
        {
            $this->option[$key] = $value;
        }
        else
        {
            foreach( $key as $k => $v )
            {
                $this->option[$k] = $v;
            }
        }
    }

    function checkUser()
    {
        global $zariliaUser;
        $uid = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0, XOBJ_DTYPE_INT );
        if ( empty( $uid ) || $zariliaUser->getVar( 'uid' ) != $uid )
        {
            redirect_header( ZAR_URL, 3, _US_NOEDITRIGHT );
            exit();
        }
        else
        {
            return $uid;
        }
    }

    function addprofile()
    {
        $uid = $this->checkUser();
        $userprofile = is_array( $_REQUEST['userprofile'] ) ? $_REQUEST['userprofile'] : array();
        if ( count( $userprofile ) > 0 )
        {
            $userprofile_cid = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', 0, XOBJ_DTYPE_INT );
            $userprofile_handler = &zarilia_gethandler( 'userprofile' );
            $criteria = new CriteriaCompo();
            $criteria->add( new Criteria( 'userprofile_uid', $uid ) );
            $userprofile_handler->deleteAll( $criteria );
            foreach ( $userprofile as $k => $v )
            {
                $profile_obj = $userprofile_handler->create();
                $profile_obj->setVar( 'userprofile_uid', $uid );
                $profile_obj->setVar( 'userprofile_cid', $userprofile_cid );
                $profile_obj->setVar( 'userprofile_value', $v );
                $profile_obj->setVar( 'userprofile_pid', $k );
                if ( !$userprofile_handler->insert( $profile_obj ) )
                {
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
                }
            }
        }
        else
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
        }

        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            include ZAR_ROOT_PATH . '/header.php';
            $GLOBALS['zariliaLogger']->sysRender();
            include ZAR_ROOT_PATH . '/footer.php';
        }
        else
        {
            redirect_header( 'index.php?page_type=user&amp;act=logout', 1, _US_PROFUPDATED );
        }
    }

    function saveuser()
    {
        global $zariliaUser, $member_handler;

        $uid = $this->checkUser();
        if ( $zariliaConfigUser['allow_chgmail'] == 1 )
        {
            $email = zarilia_cleanRequestVars( $_REQUEST, 'email', '', XOBJ_DTYPE_EMAIL );
            if ( $email == '' || !checkEmail( $email ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _US_INVALIDMAIL );
            }
        }

        $edituser = $member_handler->getUser( $uid );
        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            $pass = zarilia_cleanRequestVars( $_REQUEST, 'pass', '', XOBJ_DTYPE_TXTBOX );
            $pass2 = zarilia_cleanRequestVars( $_REQUEST, 'pass2', '', XOBJ_DTYPE_TXTBOX );
            unset( $_REQUEST['pass'], $_REQUEST['pass2'] );

            $edituser->setVars( $_REQUEST );
            if ( isset( $_REQUEST['user_coppa_dob'] ) )
            {
                $edituser->setVar( 'user_coppa_dob', strtotime( $_REQUEST['user_coppa_dob'] ) );
                $edituser->setVar( 'user_coppa_agree', isset( $_REQUEST['user_coppa_agree'] ) ? 1 : 0 );
            }
            if ( !empty( $pass ) && !empty( $pass2 ) )
            {				
                $edituser->setVar( "pass", $GLOBALS['zariliaSecurity']->execEncryptionFunc('encrypt', $pass ) );
            }
            if ( !$member_handler->insertUser( $edituser ) )
            {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $edituser->getErrors() );
            }
        }

        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() )
        {
            if ( !empty( $_REQUEST['usecookie'] ) )
            {
                setcookie( $zariliaConfig['usercookie'], $zariliaUser->getVar( 'uname' ), time() + 31536000 );
            }
            else
            {
                setcookie( $zariliaConfig['usercookie'] );
            }
            redirect_header( 'index.php?page_type=edituser&uid='.$uid, 1, _US_PROFUPDATED );
        }
        else
        {
            include ZAR_ROOT_PATH . '/header.php';
            $GLOBALS['zariliaLogger']->sysRender();
            include ZAR_ROOT_PATH . '/footer.php';
            exit();
        }
    }

    function isdefault()
    {
        global $zariliaUser, $zariliaConfig;

        require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        require_once ZAR_ROOT_PATH . '/class/class.menubar.php';

        $profile_id = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', 0 );
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );

        $tabbar = new ZariliaTabMenu( $opt );
        $url = "index.php?page_type=edituser";
        $this_array = array( _US_PERSONAL => $url, _US_NOTIFICATIONS => $url );

        $profilecat_handler = &zarilia_gethandler( 'profilecategory' );
        $_array = $profilecat_handler->getList( 1 );
        if ( is_array( $_array ) )
        {
            foreach ( $_array as $k => $v )
            {
                $this_array[$v] = $url . "&amp;profile_id=" . intval( $k );
            }
        }

        $tabbar->addTabArray( $this_array );
        $menubar = $tabbar->renderStart( 1, 1 );
        $form = new ZariliaThemeForm( '', 'zariliaform', $url );
        $form->setExtra( 'enctype="multipart/form-data"' );
        $opt = ( $opt > 2 ) ? 2 : $opt;
        switch ( $opt )
        {
            case 0:
            default:
                $form->addElement( new ZariliaFormText( _US_NICKNAME, 'uname', 25, 25, $zariliaUser->getVar( 'uname', 'E' ) ), true );
                $form->addElement( new ZariliaFormText( _US_NAME, 'name', 50, 50, $zariliaUser->getVar( 'name', 'E' ) ), false );
                $email_tray = new ZariliaFormElementTray( _US_EMAIL, '<br />' );
                $email_text = new ZariliaFormText( '', 'email', 30, 60, $zariliaUser->getVar( 'email', 'E' ) );
                $email_tray->addElement( $email_text, true );
                $email_cbox_value = $zariliaUser->getVar( 'user_viewemail' ) ? 1 : 0;
                $email_cbox = new ZariliaFormCheckBox( '', 'user_viewemail', $email_cbox_value );
                $email_cbox->addOption( 1, _US_AOUTVTEAD );
                $email_tray->addElement( $email_cbox );
                $form->addElement( $email_tray, true );
                $form->addElement( new ZariliaFormSelectLang( _US_LANGUAGE, 'user_language', $zariliaUser->getVar( 'user_language', 'E' ), 1, false ) );
                $form->addElement( new ZariliaFormSelectTheme( _US_THEME, 'theme', $zariliaUser->getVar( 'theme', 'E' ), 1, false ) );
                $form->addElement( new ZariliaFormSelectTimezone( _US_TIMEZONE, 'timezone_offset', $zariliaUser->getVar( 'timezone_offset', 'E' ) ) );
                $form->addElement( new ZariliaFormSelectEditor( _US_EDITOR, 'editor', $zariliaUser->getVar( 'editor', 'e' ), false ) );

                $lvl_cattype = new ZariliaFormSelect( _US_LEVEL, 'user_usrlevel', $zariliaUser->getVar( 'user_usrlevel' ), '', '', 0 );
                $lvl_cattype->addOptionArray( ZariliaLists::userlevel( null ) );
                $form->addElement( $lvl_cattype );

                $med_cattype = new ZariliaFormSelect( _US_MEDPREF, 'user_usrmedpref', $zariliaUser->getVar( 'user_usrmedpref' ), '', '', 0 );
                $med_cattype->addOptionArray( ZariliaLists::usermedia( null ) );
                $form->addElement( $med_cattype );

                $age_tray = new ZariliaFormElementTray( _US_BIRTHDATE, '<br />' );
                $age_text = new ZariliaFormTextDateSelect( '', 'user_coppa_dob', 15, $zariliaUser->getVar( 'user_coppa_dob' ) );
                $age_tray->addElement( $age_text, true );
                $age_cbox_value = $zariliaUser->getVar( 'user_coppa_agree' ) ? 1 : 0;
                $age_cbox = new ZariliaFormCheckBox( '', 'user_coppa_agree', $age_cbox_value );
                $age_cbox->addOption( 1, _US_IAMOVER );
                $age_tray->addElement( $age_cbox );
                $form->addElement( $age_tray, true );

                $form->insertSplit( _US_LOGINDETAILS );
                $ulogin = new ZariliaFormText( _US_ULOGINNAME, 'ulogin', 30, 30, $zariliaUser->getVar( 'login' ) );
                $ulogin->setDescription( _US_ULOGINNAME_DSC );
                $form->addElement( $ulogin, true );

                $pass = new ZariliaFormPassword( _US_PASSWORD, 'pass', 10, 32, '', 1, 'zariliaform' ) ;
                $pass->setDescription( _US_PASSWORD_DSC );
                $form->addElement( $pass, true );
                // $form->addElement( new ZariliaFormGenPassword( _US_CREATEPASSWORD, 'password', 10, 32, '', '' ), false );
                $form->addElement( new ZariliaFormHidden( 'act', 'saveuser' ) );
                break;

            case 1:
                $form->addElement( new ZariliaFormRadioYN( _US_MAILOK, 'user_mailok', $zariliaUser->getVar( 'user_mailok', 'E' ) ), true );
                $umode_select = new ZariliaFormSelect( _US_CDISPLAYMODE, 'umode', $zariliaUser->getVar( 'umode' ) );
                $umode_select->addOptionArray(
                    array( 'nest' => _NESTED,
                        'flat' => _FLAT,
                        'thread' => _THREADED
                        )
                    );
                $form->addElement( $umode_select );
                $uorder_select = new ZariliaFormSelect( _US_CSORTORDER, 'uorder', $zariliaUser->getVar( 'uorder' ) );
                $uorder_select->addOptionArray(
                    array( '0' => _OLDESTFIRST,
                        '1' => _NEWESTFIRST
                        )
                    );
                $form->addElement( $uorder_select );
                require_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/notifications.php';
                require_once ZAR_ROOT_PATH . '/include/notification_constants.php';
                $notify_method_select = new ZariliaFormSelect( _NOT_NOTIFYMETHOD, 'notify_method', $zariliaUser->getVar( 'notify_method' ) );
                $notify_method_select->addOptionArray(
                    array(
                        ZAR_NOTIFICATION_METHOD_DISABLE => _NOT_METHOD_DISABLE,
                        ZAR_NOTIFICATION_METHOD_PM => _NOT_METHOD_PM,
                        ZAR_NOTIFICATION_METHOD_EMAIL => _NOT_METHOD_EMAIL
                        )
                    );
                $form->addElement( $notify_method_select );
                $notify_mode_select = new ZariliaFormSelect( _NOT_NOTIFYMODE, 'notify_mode', $zariliaUser->getVar( 'notify_mode' ) );
                $notify_mode_select->addOptionArray(
                    array(
                        ZAR_NOTIFICATION_MODE_SENDALWAYS => _NOT_MODE_SENDALWAYS,
                        ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE => _NOT_MODE_SENDONCE,
                        ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT => _NOT_MODE_SENDONCEPERLOGIN )
                    );
                $form->addElement( $notify_mode_select );
                $form->addElement( new ZariliaFormHidden( 'act', 'saveuser' ) );
                break;
            case 2:
                $zariliaUserprofile_handler = &zarilia_gethandler( 'userprofile' );
                $form = $zariliaUserprofile_handler->displayProfile( $form, $profile_id, $zariliaUser->getVar( 'uid' ), '', zarilia_getenv( 'PHP_SELF' ) );
                $form->addElement( new ZariliaFormHidden( 'act', 'addprofile' ) );
                $form->addElement( new ZariliaFormHidden( 'profile_id', $profile_id ) );
                break;
        } // switch
        if ( $opt > 0 )
        {
            $form->addElement( new ZariliaFormHidden( 'login', $zariliaUser->getVar( 'login' ) ) );
        }
        // $form->addElement( new ZariliaFormHidden( 'op', $profile_id ) );
        $form->addElement( new ZariliaFormHidden( 'profile_id', $profile_id ) );
        $form->addElement( new ZariliaFormHidden( 'uid', $zariliaUser->getVar( 'uid' ) ) );

        /*button_tray*/
        $form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );

        $content['form'] = $form;
        $content['file'] = 'profile';
        $this->addOptions( 'template_main', 'system_edituserform.html' );
        $this->addOptions(
            array( 'title' => _US_REGPROFILE,
                'subtitle' => _US_REGPROFILE_DSC,
                'content' => $content,
                'menubar' => $menubar,
                'page_type' => 'edituser'
                )
            );
        return $this->option;
        break;
    }
}

?>