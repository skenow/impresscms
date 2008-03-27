<?php
// $Id: index.php,v 1.3 2007/04/21 09:42:08 catzwolf Exp $
// ------------------------------------------------------------------------ //
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
// -------------------------------------------------------------------------//$contestOption_handler = &zarilia_gethandler( 'contestoption' );
$contest_handler = &zarilia_gethandler( 'contest' );

$nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
$nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', '', XOBJ_DTYPE_TXTBOX );
$nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC', XOBJ_DTYPE_TXTBOX );

switch ( strtolower( $op ) ) {
    case 'resend_act_mail':
        $cont_id = zarilia_cleanRequestVars( $_REQUEST, 'cont_id', 0 );
        $contest_obj = &$contest_handler->get( $cont_id );
        if ( $contest_handler->sendActEmail( $contest_obj ) ) {
            redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, sprintf( AM_CONTEST_SENDINGEMAIL, $contest_obj->getVar( 'cont_name' ), $contest_obj->getVar( 'cont_email' ) ) );
        } else {
            zarilia_cp_header();
            zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
            zarilia_error( sprintf( _MD_AM_CONTEST_FAILSENDINGEMAIL, $contest_obj->getVar( 'cont_name' ), $contest_obj->getVar( 'cont_email' ) ) );
            zarilia_cp_footer();
        }
        break;

    case 'view_contestant':
        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

        $member_handler = &zarilia_gethandler( 'member' );
        $country_arr = array( 0 => 'Canada', 1 => 'USA' );
        $sex_arr = array( 0 => 'Female', 1 => 'Male' );
        $shirt_arr = array( 0 => 'Small', 1 => 'medium', 2 => 'Large', 3 => 'XLarge', 4 => 'Bury Me' );
        $win_arr = array( 0 => 'None', 1 => 'First', 2 => 'Second', 3 => 'Third' );

        $contest_handler = &zarilia_gethandler( 'contest' );
        $cont_id = zarilia_cleanRequestVars( $_REQUEST, 'cont_id', 0 );
        $contest_obj = &$contest_handler->get( $cont_id );

        $id = $contest_obj->getVar( 'cont_uid' );
        $user = $member_handler->getUser( $id );
        $userdetails = $user ? $member_handler->getUser( $id ) : $contest_obj->getVar( 'cont_uid' );

        zarilia_cp_header();
        zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
        zarilia_show_buttons( 'right', 'files_button', 'mainbutton',
            array( $_PHP_SELF . "?fct=" . $fct . "&op=resend_act_mail&cont_id=" . $cont_id => 'Resend Email',
                )
            );

        $form = new ZariliaThemeForm( _MD_AM_CONTEST_VIEWUSER , 'contest_form', 'admin.php' );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_UID, $userdetails ) );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_IP, whois( $contest_obj->getVar( "cont_ipaddress" ) ) ) );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_NAME, $contest_obj->getVar( 'cont_name' ) ) );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_EMAIL, $contest_obj->getVar( 'cont_email', 'c' ) ) );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_ENTERED, $contest_obj->getTimeStamp() ) );

        $address = $contest_obj->getVar( 'cont_mailaddress1' ) . "<br />";
        if ( $contest_obj->getVar( 'cont_mailaddress2' ) ) {
            $address .= $contest_obj->getVar( 'cont_mailaddress2' ) . "<br />";
        }
        $address .= $contest_obj->getVar( 'cont_city' ) . "<br />";
        $address .= $contest_obj->getVar( 'cont_state' ) . "<br />";
        $address .= $country_arr[$contest_obj->getVar( 'cont_country' )] . "<br />";
        $address .= $contest_obj->getVar( 'cont_zipcode' ) . "<br />" . $contest_obj->zipcode();

        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_MAILINGADD, $address ) );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_SEX, $sex_arr[$contest_obj->getVar( 'cont_sex' )] ) );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_SHIRT, $shirt_arr[$contest_obj->getVar( 'cont_shirt' )] ) );
        $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_ACTKEY, $contest_obj->getVar( 'cont_key' ) ) );
        $create_tray = new ZariliaFormElementTray( '', '' );
        $create_tray->addElement( new ZariliaFormButton( '', 'cancel', _FINISHED, 'button', 'onClick="history.go(-1);return true;"' ) );
        $form->addElement( $create_tray );
        $form->display();
        zarilia_cp_footer();
        break;

    case 'edit_contestant':
        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

        $member_handler = &zarilia_gethandler( 'member' );
        $country_arr = array( 0 => 'Canada', 1 => 'USA' );
        $sex_arr = array( 0 => 'Female', 1 => 'Male' );
        $shirt_arr = array( 0 => 'Small', 1 => 'medium', 2 => 'Large', 3 => 'XLarge', 4 => 'Bury Me' );
        $win_arr = array( 0 => 'None', 1 => 'First', 2 => 'Second', 3 => 'Third' );

        $contest_handler = &zarilia_gethandler( 'contest' );
        $cont_id = zarilia_cleanRequestVars( $_REQUEST, 'cont_id', 0 );
        $contest_obj = ( $cont_id ) ? $contest_handler->get( $cont_id ) : $contest_handler->create();

        zarilia_cp_header();
        zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );

        if ( $cont_id ) {
            zarilia_show_buttons( 'right', 'files_button', 'mainbutton',
                array( $_PHP_SELF . "?fct=" . $fct . "&op=resend_act_mail&cont_id=" . $cont_id => 'Resend Email',
                    )
                );
        }

        $_entry_heading = ( $cont_id == 0 ) ? _MD_AM_CONTEST_EDITUSER : _MD_AM_CONTEST_MODIFYUSER;

        $form = new ZariliaThemeForm( $_entry_heading , 'contest_form', 'admin.php' );
        if ( $cont_id != 0 ) {
            $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_UID, $contest_obj->getVar( 'cont_uid' ) ) );
            $form->addElement( new ZariliaFormLabel( _MD_AM_CONTEST_V_IP, whois( $contest_obj->getVar( "cont_ipaddress" ) ) ) );
        }
        $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_V_NAME, 'cont_name', 50, 60, $contest_obj->getVar( 'cont_name' ) ), true );
        $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_V_EMAIL, 'cont_email', 50, 120, $contest_obj->getVar( 'cont_email', 'e' ) ), true );
        $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_V_MAILINGADD, 'cont_mailaddress1', 50, 60, $contest_obj->getVar( 'cont_mailaddress1', 'e' ) ), true );
        $form->addElement( new ZariliaFormText( '', 'cont_mailaddress2', 50, 60, $contest_obj->getVar( 'cont_mailaddress2', 'e' ) ), false );
        $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_V_CITY, 'cont_city', 50, 60, $contest_obj->getVar( 'cont_city', 'e' ) ), false );
        $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_V_STATE, 'cont_state', 50, 60, $contest_obj->getVar( 'cont_state', 'e' ) ), false );

        $select = new ZariliaFormSelect( _MD_AM_CONTEST_V_COUNTRY, 'cont_country', $contest_obj->getVar( 'cont_country' ) );
        $select->addOptionArray( $country_arr );
        $form->addElement( $select, true );
        unset( $select );
        $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_V_ZIPCODE, 'cont_zipcode', 50, 60, $contest_obj->getVar( 'cont_zipcode', 'e' ) ), false );

        $select = new ZariliaFormSelect( _MD_AM_CONTEST_V_SEX, 'cont_sex', $contest_obj->getVar( 'cont_sex' ) );
        $select->addOptionArray( $sex_arr );
        $form->addElement( $select, true );
        unset( $select );

        $select = new ZariliaFormSelect( _MD_AM_CONTEST_V_SHIRT, 'cont_shirt', $contest_obj->getVar( 'cont_shirt' ) );
        $select->addOptionArray( $shirt_arr );
        $form->addElement( $select, true );
        unset( $select );

        $select = new ZariliaFormSelect( _MD_AM_CONTEST_V_WINPOSITION, 'cont_win_position', $contest_obj->getVar( 'cont_win_position' ) );
        $select->addOptionArray( $win_arr );
        $form->addElement( $select, true );
        unset( $select );

        $cont_key = ( $cont_id == 0 ) ? substr( uniqid( mt_rand(), 1 ), 0, 5 ) : $contest_obj->getVar( 'cont_key' );
        $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_V_ACTKEY, "cont_key", 5, 5, $cont_key ), true );

        $create_tray = new ZariliaFormElementTray( '', '' );
        $create_tray->addElement( new ZariliaFormHidden( 'op', 'save_contestant' ) );
        $create_tray->addElement( new ZariliaFormHidden( 'fct', 'contest' ) );
        $create_tray->addElement( new ZariliaFormHidden( 'cont_id', $cont_id ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $form->addElement( $create_tray );
        $form->display();
        zarilia_cp_footer();
        break;

    case 'save_contestant':
        $contest_handler = &zarilia_gethandler( 'contest' );
        $cont_id = zarilia_cleanRequestVars( $_REQUEST, 'cont_id', 0 );
        $contest_obj = ( $cont_id ) ? $contest_handler->get( $cont_id ) : $contest_handler->create();

        $contest_obj->setVars( $_REQUEST );
        if ( $contest_handler->insert( $contest_obj, false ) ) {
            if ( $contest_handler->sendActEmail( $contest_obj, $cont_id ) ) {
                // redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, sprintf( AM_CONTEST_SENDINGEMAIL, $contest_obj -> getVar( 'cont_name' ), $contest_obj -> getVar( 'cont_email' ) ) );
            } else {
                zarilia_cp_header();
                zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
                zarilia_error( sprintf( _MD_AM_CONTEST_FAILSENDINGEMAIL, $contest_obj->getVar( 'cont_name' ), $contest_obj->getVar( 'cont_email' ) ) );
                zarilia_cp_footer();
                exit();
            }
            $redirect_mess = ( $contest_obj->isNew() ) ? _MD_AM_CLIENT_CREATED : _DBUPDATED;
        } else {
            zarilia_cp_header();
            zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, $redirect_mess );
        break;

    /**
     * Delete Client information
     */
    case "delete_contestant":

        $cont_id = zarilia_cleanRequestVars( $_REQUEST, 'cont_id', 0 );
        $_contest_obj = $contest_handler->get( $cont_id );
        if ( !is_object( $_contest_obj ) || intval( $cont_id ) <= 0 ) {
            redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, _MD_AM_CONTEST_CANNOTDELCONTEST );
        }

        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        if ( $ok ) {
            if ( !$contest_handler->delete( $_contest_obj ) ) {
                zarilia_cp_header();
                zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
                zarilia_error( sprintf( _MD_FAILDEL, $_contest_obj->getVar( 'cont_name' ) ) );
            } else {
                /**
                 * Should we allow to delete previous winners etc here?
                 */
                redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, _DBUPDATED );
            }
        } else {
            zarilia_cp_header();
            zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
            zarilia_confirm(
                array( 'fct' => 'contest',
                    'op' => 'delete_contestant',
                    'cont_id' => $_contest_obj->getVar( 'cont_id' ),
                    'ok' => 1
                    ), 'admin.php', sprintf( _MD_AM_CONTEST_ARUSURE, $_contest_obj->getVar( 'cont_name' ) )
                );
        }
        zarilia_cp_footer();
        break;

    case 'display_entries':
        $contest_handler = &zarilia_gethandler( 'contest' );
        $contest_id = zarilia_cleanRequestVars( $_REQUEST, 'contest_id', 0 );

        $start = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $sort = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'cont_name' );
        $order = zarilia_cleanRequestVars( $_REQUEST, 'order', 'ASC' );

        $contest_obj = &$contest_handler->getContestants( $contest_id, 30, $start, $sort, $order, true );
        $contest_count = $contest_handler->getCount( null, $contest_id );
        zarilia_cp_header();
        zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
        zarilia_show_buttons( 'right', 'files_button', 'mainbutton',
            array( $_PHP_SELF . "?fct=" . $fct . "&op=edit_contestant&cont_id=0" => 'Create Contestant',
                )
            );

        echo "
		<h4>" . _MD_AM_CONTEST_HEADING . "</h4>
		<table width='100%' cellpadding='2' cellspacing='1' class='outer'>\n
		 <tr style='text-align: center;'>\n
		  <th>&nbsp;</td>\n
		  <th style='text-align: left;' width='30%'>" . _MD_AM_CONTEST_ENT_NAME . "</td>\n
		  <th>" . _MD_AM_CONTEST_ENT_IP . "</td>\n
		  <th>" . _MD_AM_CONTEST_ENT_EMAIL . "</td>\n
		  <th>" . _MD_AM_CONTEST_ENT_ENTERED . "</td>\n
		  <th>" . _ACTION . "</td>\n
		 </tr>\n";

        if ( !is_array( $contest_obj ) ) {
            echo "
			<tr style='text-align: center;'>\n
             <td colspan='6' class='head'>" . _NOTHINGFOUND . "</td>\n
            </tr>\n
			";
        } else {
            foreach ( array_keys( $contest_obj ) as $j ) {
                $ip_address = whois( $contest_obj[$j]->getVar( "cont_ipaddress" ) );
                echo "
				 <tr style='text-align: center;'>
                  <td class='head'>" . $contest_obj[$j]->getVar( "cont_id" ) . "</td>
                  <td class='even' style='text-align: left;'>" . $contest_obj[$j]->getVar( 'cont_name' ) . "</td>
                  <td class='even'>" . $ip_address . "</td>
				  <td class='even'>" . $contest_obj[$j]->getVar( 'cont_email', 'c' ) . "</td>
				  <td class='even'>" . $contest_obj[$j]->getTimeStamp( null, 'cont_date' ) . "</td>
                  <td class='even'>
				   <a href='" . $_PHP_SELF . "?fct=" . $fct . "&amp;cont_id=" . $contest_obj[$j]->getVar( "cont_id" ) . "&amp;op=edit_contestant'>" . zarilia_img_show( 'edit', _EDIT, 'middle' ) . "</a>
				   <a href='" . $_PHP_SELF . "?fct=" . $fct . "&amp;cont_id=" . $contest_obj[$j]->getVar( "cont_id" ) . "&amp;op=delete_contestant'>" . zarilia_img_show( 'delete', _DELETE, 'middle' ) . "</a>
				   <a href='" . $_PHP_SELF . "?fct=" . $fct . "&amp;cont_id=" . $contest_obj[$j]->getVar( "cont_id" ) . "&amp;op=view_contestant'>" . zarilia_img_show( 'view', _VIEW, 'middle' ) . "</a>
                  </td>
                 </tr>\n";
            }
        }
        echo "</table>";
        zarilia_pagnav( $contest_count, 30, $start, 'start', 1, 'fct=contest' );
        zarilia_cp_footer();
        break;

    /**
     */
    case 'contest_save':
		$contest_id = zarilia_cleanRequestVars( $_REQUEST, 'contest_id', 0 );
        $_contest_obj = ( $contest_id > 0 ) ? $contestOption_handler->get( $contest_id ) : $contestOption_handler->create();

        if ( !isset( $_REQUEST['contest_main'] ) ) {
            $_REQUEST['contest_main'] = 0;
        }

        if ( !isset( $_REQUEST['contest_members_only'] ) ) {
            $_REQUEST['contest_members_only'] = 0;
        }

        if ( !isset( $_REQUEST['contest_only_once'] ) ) {
            $_REQUEST['contest_only_once'] = 0;
        }

        if ( !isset( $_REQUEST['contest_active'] ) ) {
            $_REQUEST['contest_active'] = 0;
        }

        if ( !isset( $_REQUEST['contest_auto'] ) ) {
            $_REQUEST['contest_auto'] = 0;
        }

        if ( !isset( $_REQUEST['contest_emailadmin'] ) ) {
            $_REQUEST['contest_emailadmin'] = 0;
        }
		$_contest_obj->setVars( $_REQUEST );

        $contest_publishdate = ( !empty( $_REQUEST['contest_publishdate']['date'] ) ) ? strtotime( $_REQUEST['contest_publishdate']['date'] ) + $_REQUEST['contest_publishdate']['time'] : time();
        $_contest_obj->setVar( "contest_publishdate", $contest_publishdate );

        $contest_expiredate = ( !empty( $_REQUEST['contest_expiredate']['date'] ) ) ? strtotime( $_REQUEST['contest_expiredate']['date'] ) + $_REQUEST['contest_expiredate']['time'] : 0;
        $_contest_obj->setVar( "contest_expiredate", $contest_expiredate );

        if ( isset( $_REQUEST['contest_main'] ) ) {
            $contestOption_handler->updateAll( 'contest_main', 0, null );
        }

        if ( $contestOption_handler->insert( $_contest_obj, false ) ) {
            $redirect_mess = ( $_contest_obj->isNew() ) ? _MD_AM_CLIENT_CREATED : _DBUPDATED;
        } else {
            zarilia_cp_header();
            zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            break;
        }
        redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, $redirect_mess );
        break;

    /**
     * Delete Client information
     */
    case "contest_delete":
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $contest_id = zarilia_cleanRequestVars( $_REQUEST, 'contest_id', 0 );
        $_contest_obj = $contestOption_handler->get( $contest_id );
        if ( !is_object( $_contest_obj ) || intval( $contest_id ) <= 0 ) {
            redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, _MD_AM_CONTEST_CANNOTDELCONTEST );
        }

        if ( $ok ) {
            if ( !$contestOption_handler->delete( $_contest_obj ) ) {
                zarilia_cp_header();
                zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
                $GLOBALS['zariliaLogger']->sysRender();
            } else {
                /**
                 * Should we allow to delete previous winners etc here?
                 */
                redirect_header( $_PHP_SELF . "?fct=" . $fct, 1, _DBUPDATED );
            }
        } else {
            zarilia_cp_header();
            zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
            zarilia_confirm(
                array( 'fct' => 'contest',
                    'op' => 'contest_delete',
                    'contest_id' => $_contest_obj->getVar( 'contest_id' ),
                    'ok' => 1
                    ), 'admin.php', sprintf( _MD_AM_CONTEST_ARUSURE, $_contest_obj->getVar( 'contest_name' ) )
                );
        }
        break;

    case 'contest_edit':
        require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        require_once ZAR_ROOT_PATH . '/class/zariliamenubar.php';

		$contest_id = zarilia_cleanRequestVars( $_REQUEST, 'contest_id', 0 );

		$opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );

        $_contest = ( $contest_id > 0 ) ? $contestOption_handler->get( $contest_id ) : $contestOption_handler->create();
        $tabbar = new ZariliaTabMenu( $opt );
        $tabbar->addTabArray( array( 'Details' => zarilia_getenv( 'PHP_SELF' ) . '?fct=contest&op=contest_edit&contest_id=' . $contest_id,
                'Options' => zarilia_getenv( 'PHP_SELF' ) . '?fct=contest&op=contest_edit&contest_id=' . $contest_id
                ) );

        zarilia_cp_header();
        zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
        $tabbar->renderStart();

        $_form_heading = ( $contest_id == 0 ) ? _MD_AM_CONTEST_CREATE : _MD_AM_CONTEST_MODIFY;
        $form = new ZariliaThemeForm( $_form_heading, 'mygallery', 'admin.php' );
        $form->setExtra( 'enctype="multipart/form-data"' );

		switch ( $opt ) {
            case 0:
                $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_NAME, 'contest_name', 30, 60, $_contest->getVar( 'contest_name', 'e' ) ), true );
                $form->addElement( new ZariliaFormText( _MD_AM_CONTEST_EMAIL, "contest_email", 60, 120, $_contest->getVar( 'contest_email', 'e' ) ) , true );
				$imgcat_id = zarilia_cleanRequestVars( $_REQUEST, 'imgcat_id', 0 );
				$image = new ZariliaFormSelectImg(_MD_AM_CONTEST_MAINIMAGE, 'contest_main_image', $_contest->getVar( 'contest_main_image' ), 'images', $_contest->getVar( 'contest_cat_id' ), 'uploads' );
        		//$image ->setExtra( "onchange=\"location='" . ZAR_URL . "/addons/system/admin.php?fct=contest&op=contest_edit&contest_id=".$contest_id."&opt=0&cid=" . 1 . "&imgcat_id2=".$imgcat_id2."&imgcat_id='+this.options[this.selectedIndex].value\"" );
                $form->addElement( $image );

                //$form->addElement( new ZariliaFormText( _MD_AM_CONTEST_MAINIMAGE, "contest_main_image", 60 , 255 , $_contest->getVar( 'contest_main_image', 'e' ) ) , true );
                //$form->addElement( new ZariliaFormText( _MD_AM_CONTEST_MAINIMAGEURL, "contest_main_image_url", 60 , 255 , $_contest->getVar( 'contest_main_image_url', 'e' ) ) , true );
				$imgcat_id2 = zarilia_cleanRequestVars( $_REQUEST, 'imgcat_id2', 0 );
				$image2 = new ZariliaFormSelectImg(_MD_AM_CONTEST_BOXIMAGE, 'contest_box_image', $_contest->getVar( 'contest_box_image' ), 'images1', $_contest->getVar( 'contest_cat_id' ) );
        		//$image2 ->setExtra( "onchange=\"location='" . ZAR_URL . "/addons/system/admin.php?fct=contest&op=contest_edit&contest_id=".$contest_id."&opt=0&cid=" . 1 . "&imgcat_id=".$imgcat_id."&imgcat_id2='+this.options[this.selectedIndex].value\"" );
                $form->addElement( $image2 );

				//$form->addElement( new ZariliaFormText( _MD_AM_CONTEST_BOXIMAGE, "contest_box_image", 60 , 255, $_contest->getVar( 'contest_box_image', 'e' ) ) , true );
                $options['name'] = 'contest_text';
                $options['value'] = $_contest->getVar( 'contest_text', 'e' );
                $ele = new ZariliaFormEditor( _MD_AM_CONTEST_TEXT, $zariliaUser->getVar( "editor" ), $options, $nohtml = false, $onfailure = "textarea" );
				$ele -> setNocolspan( 1 );
				$form->addElement($ele);
                //$form->addElement( new ZariliaFormTextArea( _MD_AM_CONTEST_TEXT, 'contest_text', $_contest->getVar( 'contest_text', 'e' ), 10, 60 ), false );
                $form->addElement( new ZariliaFormTextArea( _MD_AM_CONTEST_RULES, 'contest_rules', $_contest->getVar( 'contest_rules', 'e' ), 10, 60 ), false );
                $form->addElement( new ZariliaFormTextArea( _MD_AM_CONTEST_PRIZES, 'contest_prize', $_contest->getVar( 'contest_prize', 'e' ), 5, 60 ), false );
                $form->addElement( new ZariliaFormTextArea( _MD_AM_CONTEST_WTEXT, 'contest_wintext', $_contest->getVar( 'contest_wintext', 'e' ), 10, 60 ), false );
                break;

            case 1:
                $option_array = array( 0 => 1, 1 => 2, 2 => 3 );
                $select = new ZariliaFormSelect( _MD_AM_CONTEST_WINNERS, 'contest_wamount', $_contest->getVar( 'contest_wamount', 'e' ) );
                $select->addOption( 0, '-------------------' );
                $select->addOptionArray( $option_array );
                $form->addElement( $select, true );
                /**
                 * Published Date: Set or remove the publish date for document
                 */
                $publishdate_tray = new ZariliaFormElementTray( _MD_AM_CONTEST_PUBLISHDATE, '' );
                $publishdate_tray->addElement( new ZariliaFormDateTime( '', 'contest_publishdate', 15, $_contest->getVar( 'contest_publishdate' ), true ) );
                $form->addElement( $publishdate_tray );
                /**
                 * Expired Date:  Set or remove the expire date for document
                 */
                $expiredate_tray = new ZariliaFormElementTray( _MD_AM_CONTEST_EXPIREDATE, '<br />' );
                $expiredate_tray->addElement( new ZariliaFormDateTime( '', 'contest_expiredate', 15, $_contest->getVar( 'contest_expiredate' ), true ) );
                $form->addElement( $expiredate_tray );

                include_once ZAR_ROOT_PATH . '/class/class_permissions.php';
                $perms = new cpPermission( 'contest_read_perm', 'contest', 'Contest Permissions', $zariliaAddon->getVar( 'mid' ) );
                $groups = $perms->cpAdminPermission_get( $_contest->getVar( 'contest_id' ) );
                $form->addElement( new ZariliaFormSelectGroup( _MD_AM_CONTEST_MEMBERS, 'groups', false, $groups, 5, true ), false );
                //$form->addElement( new ZariliaFormText( _MD_AM_CONTEST_NUMDAYS, 'contest_numdays', 5, 5, $_contest->getVar( 'contest_numdays' ) ), true );
                $option_tray = new ZariliaFormElementTray( _MD_AM_CONTEST_OPTIONS, '<br />' );

                $main_contest_checkbox = new ZariliaFormCheckBox( '', 'contest_main', $_contest->getVar( 'contest_main' ) );
                $main_contest_checkbox->addOption( 1, _MD_AM_CONTEST_MAIN );
                $option_tray->addElement( $main_contest_checkbox );

                $contest_members_only_checkbox = new ZariliaFormCheckBox( '', 'contest_members_only', $_contest->getVar( 'contest_members_only' ) );
                $contest_members_only_checkbox->addOption( 1, _MD_AM_CONTEST_MEMBERS );
                $option_tray->addElement( $contest_members_only_checkbox );

                $once_checkbox = new ZariliaFormCheckBox( '', 'contest_only_once', $_contest->getVar( 'contest_only_once' ) );
                $once_checkbox->addOption( 1, _MD_AM_CONTEST_ONCE_ONLY );
                $option_tray->addElement( $once_checkbox );

                $sendemail_checkbox = new ZariliaFormCheckBox( '', 'contest_active', $_contest->getVar( 'contest_active' ) );
                $sendemail_checkbox->addOption( 1, _MD_AM_CONTEST_ISACTIVE );
                $option_tray->addElement( $sendemail_checkbox );

                $auto_checkbox = new ZariliaFormCheckBox( '', 'contest_auto', $_contest->getVar( 'contest_auto' ) );
                $auto_checkbox->addOption( 1, _MD_AM_CONTEST_AUTO_WINNERS );
                $option_tray->addElement( $auto_checkbox );

                $emailadmin_checkbox = new ZariliaFormCheckBox( '', 'contest_emailadmin', $_contest->getVar( 'contest_emailadmin' ) );
                $emailadmin_checkbox->addOption( 1, _MD_AM_CONTEST_EMAILADMIN );
                $option_tray->addElement( $emailadmin_checkbox );
                $form->addElement( $option_tray );
                break;
        }
        $create_tray = new ZariliaFormElementTray( '', '' );
        $create_tray->addElement( new ZariliaFormHidden( 'op', 'contest_save' ) );
        $create_tray->addElement( new ZariliaFormHidden( 'fct', 'contest' ) );
        $create_tray->addElement( new ZariliaFormHidden( 'contest_id', $contest_id ) );
        $create_tray->addElement( new ZariliaFormHidden( 'contest_expire', $_contest->getVar( 'contest_expiredate' ) ) );
        $create_tray->addElement( new ZariliaFormHidden( 'contest_publish', $_contest->getVar( 'contest_publishdate' ) ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
        $create_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
        $form->addElement( $create_tray );
        $form->display();
        break;

    /**
     */
    case 'main':
    default:
        $heading_arr = array( 'contest_id' => 1, 'contest_name' => 1, 'contest_publishdate' => 1, 'contest_expiredate' => 1, 'contest_main' => 1, 'contest_active' => 1, 'op' => 0 );
        $heading_arr_count = sizeof( $heading_arr );
        $contestO_array = $contestOption_handler->getContests( 30, $nav['start'], $nav['sort'], $nav['order'], false );
        $contestO_array_count = $contestOption_handler->getCount();

        zarilia_cp_header();
        zarilia_admin_menu( $tblColors, '', _MD_AM_CONTEST, $op );
        zarilia_show_buttons( 'right', 'files_button', 'mainbutton', array( $_PHP_SELF . "?fct=" . $fct . "&op=contest_edit" => _MD_AM_CONTEST_NEW, ) );
        //zarilia_headings( $heading_arr , true, '_MD_AM_', $fct );

        if ( !is_array( $contestO_array ) || sizeof( $contestO_array ) == 0 ) {
            zarilia_noSelection( $heading_arr_count );
        } else {
            for ( $i = 0; $i < sizeof( $contestO_array ); $i++ ) {
                $_id = $contestO_array[$i]->getVar( "contest_id" );
                $_isactive_image = $contestO_array[$i]->getVar( "contest_active" ) ? 'online' : 'offline';
                $_ismain_text = $contestO_array[$i]->getVar( "contest_main" ) ? 'on' : 'off';

                $allpages = "<a href='" . $_PHP_SELF . "?fct=" . $fct . "&amp;contest_id=" . $_id . "&amp;op=contest_edit'>" . zarilia_img_show( 'edit', _EDIT, 'middle' ) . "</a> ";
                $allpages .= "<a href='" . $_PHP_SELF . "?fct=" . $fct . "&amp;contest_id=" . $_id . "&amp;op=contest_delete'>" . zarilia_img_show( 'delete', _DELETE, 'middle' ) . "</a> ";
                $allpages .= "<a href='" . $_PHP_SELF . "?fct=" . $fct . "&amp;contest_id=" . $_id . "&amp;op=display_entries'>" . zarilia_img_show( 'view', _VIEW, 'middle' ) . "</a>";

                echo "
				 <tr style='text-align: center;'>\n
                  <td class='head'>" . $_id . "</td>\n
                  <td class='even' style='text-align: left;'>" . $contestO_array[$i]->getVar( "contest_name" ) . "</td>\n
                  <td class='even'>" . $contestO_array[$i]->getTimeStamp( null, 'contest_publishdate' ) . "</td>\n
				  <td class='even'>" . $contestO_array[$i]->getTimeStamp( null, 'contest_expiredate' ) . "</td>\n
				  <td class='even'>" . $_ismain_text . "</td>\n
				  <td class='even'>" . zarilia_img_show( $_isactive_image, '', 'middle' ) . "</td>\n
                  <td class='even'>" . $allpages . "</td>\n
                 </tr>\n";
                unset( $_id, $_isactive_image, $_ismain_text, $allpages );
            }
        }
        zarilia_listing_footer( $heading_arr_count );
        zarilia_pagnav( $contestO_array_count, 30, $nav['start'], 'start', 1, 'fct=contest' );
        break;
} // switch
zarilia_cp_footer();

?>