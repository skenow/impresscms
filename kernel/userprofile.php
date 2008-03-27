<?php
// $Id: userprofile.php,v 1.3 2007/05/05 11:12:12 catzwolf Exp $
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
 * ZariliaUserProfile
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: userprofile.php,v 1.3 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaUserProfile extends ZariliaObject {
    function ZariliaUserProfile() {
        $this->zariliaObject();
        $this->initVar( 'userprofile_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'userprofile_uid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'userprofile_cid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'userprofile_name', XOBJ_DTYPE_TXTAREA );
        $this->initVar( 'userprofile_value', XOBJ_DTYPE_TXTAREA );
        $this->initVar( 'userprofile_pid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'userprofile_weight', XOBJ_DTYPE_INT, null, false );
    }
}

/**
 * ZariliaProfileItemHandler
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of Zarilia configuration class objects.
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: userprofile.php,v 1.3 2007/05/05 11:12:12 catzwolf Exp $
 * @access public
 */
class ZariliaUserProfileHandler extends ZariliaPersistableObjectHandler {
    /**
     * ZariliaUserProfileHandler::ZariliaUserProfileHandler()
     *
     * @param mixed $db
     */
    function ZariliaUserProfileHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'userprofile', 'zariliauserprofile', 'userprofile_id', 'userprofile_value' );
    }

    /**
     * ZariliaUserProfileHandler::displayProfile()
     *
     * @param mixed $form
     * @param integer $profile_id
     * @param integer $uid
     * @param integer $catid
     * @param string $url
     * @return
     */
    function displayProfile( $form, $profile_id = 1, $uid = 0, $catid = 0, $url = '' ) {
        global $zariliaUser;

		$profile_handler = &zarilia_gethandler( 'profile' );
        $criteria = new CriteriaCompo();
		$criteria->add( new Criteria( 'profile_catid', $profile_id ) );
        $criteria->add( new Criteria( 'profile_display', 1 ) );
        $criteria->setSort( 'profile_order' );
       	$config = &$profile_handler->getProfiles( $criteria );
        $profile_value = $this->getValues( 1, $uid );
		/*
		* this array is required
		*/
        $profilecat_handler = &zarilia_gethandler( 'Profilecategory' );
        $menu_items = $profilecat_handler->getList();
        $values = $this->getValues( null, $uid, 1 );
        for ( $i = 0; $i < count( $config ); $i++ ) {
            /*
			* Set title
			*/
            $profile_id = $config[$i]->getVar( 'profile_id' );
            $profile_required = ( $config[$i]->getVar( 'profile_required' ) == 1 ) ? true : false;
            $_profile_title = $config[$i]->getVar( 'profile_title' );
            $_profile_title .= '<br /><br /><span style="font-weight:normal;">' . $config[$i]->getVar( 'profile_desc' ) . '</span>';
            $_profiles = ( isset( $values[$profile_id] ) ) ? $values[$profile_id] : null;
            $_profile_value = $config[$i]->getProfValueForminput( $_profiles );
            $_profile_formtype = $config[$i]->getVar( 'profile_formtype' );
            /*
			*
			*/
            switch ( $_profile_formtype ) {
                case 'hidden':
                    $form->addElement( new ZariliaFormHidden( "userprofile[$profile_id]", $_profile_value ) );
                    break;

                case 'zariliatextarea':
                case 'textarea':
                case 'htmltextarea':
                    $options['name'] = "userprofile[$profile_id]";
                    if ( $_profile_value == 'array' ) {
                        // this is exceptional.. only when value type is array need a smarter way for this
                        $options['value'] = ( $_profile_value != '' ) ? htmlspecialchars( implode( '|', $_profile_value ), ENT_QUOTES ): $_profile_value;
                    } else {
                        $options['value'] = $_profile_value;
                    }
                    $form->addElement( new ZariliaFormEditor( $_profile_title, $zariliaUser->getVar( 'editor' ), $options, $nohtml = false, $onfailure = "textarea" ), $profile_required );
                    break;
                case 'select':
                    $ele = new ZariliaFormSelect( $_profile_title, "userprofile[$profile_id]", $_profile_value );
                    $options = &$profile_handler->getProfileOptions( new Criteria( 'profile_id', $config[$i]->getVar( 'profile_id' ) ) );
                    // $options = array( 0 => 'this option' );
                    $opcount = count( $options );
                    for ( $j = 0; $j < $opcount; $j++ ) {
                        $optval = defined( $options[$j]->getVar( 'confop_value' ) ) ? $options[$j]->getVar( 'confop_value' ) : $options[$j]->getVar( 'confop_value' );
                        $optkey = defined( $options[$j]->getVar( 'confop_name' ) ) ? $options[$j]->getVar( 'confop_name' ) : $options[$j]->getVar( 'confop_name' );
                        $ele->addOption( $optval, $optkey );
                    }
                    $ele->addOptionArray( $options );
                    $form->addElement( $ele, $profile_required );
                    break;
                case 'select_multi':
                    $ele = new ZariliaFormSelect( $_profile_title, "userprofile[$profile_id]", $_profile_value, 5, true );
                    $options = &$profile_handler->getProfileOptions( new Criteria( 'profile_id', $config[$i]->getVar( 'profile_id' ) ) );
                    $opcount = count( $options );
                    for ( $j = 0; $j < $opcount; $j++ ) {
                        $optval = defined( $options[$j]->getVar( 'confop_value' ) ) ? $options[$j]->getVar( 'confop_value' ) : $options[$j]->getVar( 'confop_value' );
                        $optkey = defined( $options[$j]->getVar( 'confop_name' ) ) ? $options[$j]->getVar( 'confop_name' ) : $options[$j]->getVar( 'confop_name' );
                        $ele->addOption( $optval, $optkey );
                    }
                    $form->addElement( $ele, $profile_required );
                    break;
                case 'language':
                    $ele = new ZariliaFormSelect( $_profile_title, "userprofile[$profile_id]", $_profile_value );
                    $list = ZariliaLists::getLangList( ZAR_ROOT_PATH . '/languague' );
                    $ele->addOptionArray( $list );
                    $form->addElement( $ele, $profile_required );
                    break;
                case 'yesno':
					$form->addElement( new ZariliaFormRadioYN( $_profile_title, "userprofile[$profile_id]", $_profile_value, _YES, _NO ), $profile_required );
                    break;
                case 'group':
                    $form->addElement( new ZariliaFormSelectGroup( $_profile_title, "userprofile[$profile_id]", true, $_profile_value, 1, false ), $profile_required );
                    break;
                case 'group_multi':
                    $form->addElement( new ZariliaFormSelectGroup( $_profile_title, "userprofile[$profile_id]", true, $_profile_value, 5, true ), $profile_required );
                    break;
                case 'user':
                    $form->addElement( new ZariliaFormSelectUser( $_profile_title, "userprofile[$profile_id]", false, $_profile_value, 1, false ), $profile_required );
                    break;
                case 'user_multi':
                    $form->addElement( new ZariliaFormSelectUser( $_profile_title, "userprofile[$profile_id]", false, $_profile_value, 5, true ), $profile_required );
                    break;
                case 'password':
                    $form->addElement( new ZariliaFormPassword( $_profile_title, "userprofile[$profile_id]", 50, 255, htmlspecialchars( $_profile_value ) ), $profile_required );
                    break;
                case 'label':
                    $form->insertBreak( htmlspecialchars( $_profile_value ) );
                    break;
                case 'insertbreak':
                    $form->insertSplit( htmlspecialchars( $_profile_value ) );
                    break;
                case 'textbox':
                default:
                    $form->addElement( new ZariliaFormText( $_profile_title, "userprofile[$profile_id]", 50, 255, $_profile_value ), $profile_required );
                    break;
            }
            unset( $ele );
        }
        return $form;
    }

    function displayUserProfile( $profile_id = 1, $uid = 0, $catid = 0 ) {
        if ( $catid == 0 ) {
            return null;
        }

        $profile_handler = &zarilia_gethandler( 'profile' );
        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'profile_catid', $profile_id ) );
        $criteria->add( new Criteria( 'profile_display', 1 ) );
        $criteria->setSort( 'profile_order' );
        $config = &$profile_handler->getProfiles( $criteria );
        /*
		* this array is required
		*/
        $_array = array();
        $values = $this->getValues( null, $uid, $catid );
        for ( $i = 0; $i < count( $config ); $i++ ) {
            /*
			* Set title
			*
			*/
            $profile_id = $config[$i]->getVar( 'profile_id' );
            $profile_required = ( $config[$i]->getVar( 'profile_required' ) == 1 ) ? true : false;
            $_profile_title = $config[$i]->getVar( 'profile_title' );
            $_profiles = ( isset( $values[$profile_id] ) ) ? $values[$profile_id] : '';
            $_profile_value = $config[$i]->getProfValueForminput( $_profiles );
            $_profile_formtype = $config[$i]->getVar( 'profile_formtype' );
            $_profile_valuetype = $config[$i]->getVar( 'profile_valuetype' );
            /*
			*
			*/
            switch ( $_profile_formtype ) {
                case 'htmltextarea':
                case 'textarea':
                case 'textbox':
                case 'language':
                    break;
                case 'yesno':
                    $_profile_value = ( $_profile_value ) ? _YES : _NO;
                    break;
            }
            $_array[] = array( 'title' => $_profile_title, 'value' => $_profile_value );
        }
        return $_array;
    }

    /**
     * ZariliaUserProfileHandler::getValues()
     *
     * @param mixed $value
     * @param integer $uid
     * @param mixed $catid
     * @return
     */
    function getValues( $value, $uid = 0, $catid = 0 ) {
        $this->keyName = 'userprofile_pid';
		$query = 'userprofile_pid, userprofile_value';
		$criteria = new CriteriaCompo();
        $criteria->add ( new Criteria( 'userprofile_uid', $uid ) );
        $criteria->add ( new Criteria( 'userprofile_cid', $catid ) );
        $ret = $this->getList( $criteria, $query );
        return $ret;
    }
}

?>