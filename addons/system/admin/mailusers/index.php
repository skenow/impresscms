<?php
// $Id: index.php,v 1.2 2007/04/21 09:42:29 catzwolf Exp $
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

if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar('mid') ) ) {
    exit( "Access Denied" );
}
require_once "admin_menu.php";
require_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";

switch ( $op ) {
    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php" ) ) {
            @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        }
        break;

    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 2 );

        require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'send';
        $added = array();
        $added_id = array();
        $criteria = array();
        zarilia_cp_header();
        $menu_handler->render( 0 );

        if ( !empty( $_POST['mail_inactive'] ) ) {
            $criteria[] = "level = 0";
        } else {
            if ( !empty( $_POST['mail_mailok'] ) ) {
                $criteria[] = 'user_mailok = 1';
            } else {
                if ( !empty( $_POST['mail_to_group'] ) ) {
                    $member_handler = &zarilia_gethandler( 'member' );
                    $user_list = array();
                    foreach ( $_POST['mail_to_group'] as $groupid ) {
                        $members = &$member_handler->getUsersByGroup( $groupid, false );
                        // Mith: Changed this to not fetch user objects with getUsersByGroup
                        // as it is resource-intensive and all we want is the userIDs
                        $user_list = array_merge( $members, $user_list );
                        // RMV: changed this because makes more sense to me
                        // if options all grouped by 'AND', not 'OR'
                        /*
						foreach ($members as $member) {
							if (!in_array($member->getVar('uid'), $user_list)) {
								$user_list[] = $member->getVar('uid');
							}
						}
						*/
                        // if (!in_array($member->getVar('uid'), $added_id) ) {
                        // $added_id[] = $member->getVar('uid');
                        // $added[] =& $member;
                        // unset($member);
                        // }
                        // }
                    }
                    if ( !empty( $user_list ) ) {
                        $criteria[] = 'uid IN (' . join( ',', $user_list ) . ')';
                    }
                }
            }
            if ( !empty( $_POST['mail_lastlog_min'] ) ) {
                $f_mail_lastlog_min = trim( $_POST['mail_lastlog_min'] );
                $time = mktime( 0, 0, 0, substr( $f_mail_lastlog_min, 5, 2 ), substr( $f_mail_lastlog_min, 8, 2 ), substr( $f_mail_lastlog_min, 0, 4 ) );
                if ( $time > 0 ) {
                    $criteria[] = "last_login > $time";
                }
            }
            if ( !empty( $_POST['mail_lastlog_max'] ) ) {
                $f_mail_lastlog_max = trim( $_POST['mail_lastlog_max'] );
                $time = mktime( 0, 0, 0, substr( $f_mail_lastlog_max, 5, 2 ), substr( $f_mail_lastlog_max, 8, 2 ), substr( $f_mail_lastlog_max, 0, 4 ) );
                if ( $time > 0 ) {
                    $criteria[] = "last_login < $time";
                }
            }
            if ( !empty( $_POST['mail_idle_more'] ) && is_numeric( $_POST['mail_idle_more'] ) ) {
                $f_mail_idle_more = intval( trim( $_POST['mail_idle_more'] ) );
                $time = 60 * 60 * 24 * $f_mail_idle_more;
                $time = time() - $time;
                if ( $time > 0 ) {
                    $criteria[] = "last_login < $time";
                }
            }
            if ( !empty( $_POST['mail_idle_less'] ) && is_numeric( $_POST['mail_idle_less'] ) ) {
                $f_mail_idle_less = intval( trim( $_POST['mail_idle_less'] ) );
                $time = 60 * 60 * 24 * $f_mail_idle_less;
                $time = time() - $time;
                if ( $time > 0 ) {
                    $criteria[] = "last_login > $time";
                }
            }
        }
        if ( !empty( $_POST['mail_regd_min'] ) ) {
            $f_mail_regd_min = trim( $_POST['mail_regd_min'] );
            $time = mktime( 0, 0, 0, substr( $f_mail_regd_min, 5, 2 ), substr( $f_mail_regd_min, 8, 2 ), substr( $f_mail_regd_min, 0, 4 ) );
            if ( $time > 0 ) {
                $criteria[] = "user_regdate > $time";
            }
        }
        if ( !empty( $_POST['mail_regd_max'] ) ) {
            $f_mail_regd_max = trim( $_POST['mail_regd_max'] );
            $time = mktime( 0, 0, 0, substr( $f_mail_regd_max, 5, 2 ), substr( $f_mail_regd_max, 8, 2 ), substr( $f_mail_regd_max, 0, 4 ) );
            if ( $time > 0 ) {
                $criteria[] = "user_regdate < $time";
            }
        }
        if ( !empty( $criteria ) ) {
            if ( empty( $_POST['mail_inactive'] ) ) {
                $criteria[] = "level > 0";
            }
            $criteria_object = new CriteriaCompo();
            foreach ( $criteria as $c ) {
                list ( $field, $op, $value ) = split( ' ', $c );
                $criteria_object->add( new Criteria( $field, $value, $op ), 'AND' );
            }
            $member_handler = &zarilia_gethandler( 'member' );
            $getusers = &$member_handler->getUsers( $criteria_object );
            foreach ( $getusers as $getuser ) {
                if ( !in_array( $getuser->getVar( "uid" ), $added_id ) ) {
                    $added[] = $getuser;
                    $added_id[] = $getuser->getVar( "uid" );
                }
            }
        }
        if ( !empty( $_POST['mail_to_user'] ) ) {
            foreach ( $_POST['mail_to_user'] as $to_user ) {
                if ( !in_array( $to_user, $added_id ) ) {
                    $added[] = new ZariliaUser( $to_user );
                    $added_id[] = $to_user;
                }
            }
        }
        $added_count = count( $added );
		if ( $added_count > 0 ) {
            $mail_start = !empty( $_POST['mail_start'] ) ? $_POST['mail_start'] : 0;
            $mail_end = ( $added_count > ( $mail_start + 100 ) ) ? ( $mail_start + 100 ) : $added_count;
            $zariliaMailer = &getMailer();
            for ( $i = $mail_start; $i < $mail_end; $i++ ) {
                $zariliaMailer->setToUsers( $added[$i] );
            }
            $zariliaMailer->setFromName( stripslashes( $_POST['mail_fromname'] ) );
            $zariliaMailer->setFromEmail( stripslashes( $_POST['mail_fromemail'] ) );
            $zariliaMailer->setSubject( stripslashes( $_POST['mail_subject'] ) );
            $zariliaMailer->setBody( stripslashes( $_POST['mail_body'] ) );
            if ( in_array( "mail", $_POST['mail_send_to'] ) ) {
                $zariliaMailer->useMail();
            }
            if ( in_array( "pm", $_POST['mail_send_to'] ) && empty( $_POST['mail_inactive'] ) ) {
                $zariliaMailer->usePM();
            }

			$zariliaMailer->send( true );
            echo $zariliaMailer->getSuccess();
            echo $zariliaMailer->getErrors();
			if ( $added_count >= $mail_end ) {
                $form = new ZariliaThemeForm( _AM_SENDMTOUSERS, "mailusers", "index.php?fct=mailusers" );
                if ( !empty( $_POST['mail_to_group'] ) ) {
                    foreach ( $_POST['mail_to_group'] as $mailgroup ) {
                        $group_hidden = new ZariliaFormHidden( "mail_to_group[]", $mailgroup );
                        $form->addElement( $group_hidden );
                    }
                }
                $inactive_hidden = new ZariliaFormHidden( "mail_inactive", $mail_inactive );
                $lastlog_min_hidden = new ZariliaFormHidden( "mail_lastlog_min", htmlSpecialChars( stripslashes( $_POST['mail_lastlog_min'] ), ENT_QUOTES ) );
                $lastlog_max_hidden = new ZariliaFormHidden( "mail_lastlog_max", htmlSpecialChars( stripslashes( $_POST['mail_lastlog_max'] ), ENT_QUOTES ) );
                $regd_min_hidden = new ZariliaFormHidden( "mail_regd_min", htmlSpecialChars( stripslashes( $_POST['mail_regd_max'] ), ENT_QUOTES ) );
                $regd_max_hidden = new ZariliaFormHidden( "mail_regd_max", htmlSpecialChars( stripslashes( $_POST['mail_regd_max'] ), ENT_QUOTES ) );
                $idle_more_hidden = new ZariliaFormHidden( "mail_idle_more", htmlSpecialChars( stripslashes( $_POST['mail_idle_more'] ), ENT_QUOTES ) );
                $idle_less_hidden = new ZariliaFormHidden( "mail_idle_less", htmlSpecialChars( stripslashes( $_POST['mail_idle_less'] ), ENT_QUOTES ) );
                $fname_hidden = new ZariliaFormHidden( "mail_fromname", htmlSpecialChars( stripslashes( $_POST['mail_fromname'] ), ENT_QUOTES ) );
                $femail_hidden = new ZariliaFormHidden( "mail_fromemail", htmlSpecialChars( stripslashes( $_POST['mail_fromemail'] ), ENT_QUOTES ) );
                $subject_hidden = new ZariliaFormHidden( "mail_subject", htmlSpecialChars( stripslashes( $_POST['mail_subject'] ), ENT_QUOTES ) );
                $body_hidden = new ZariliaFormHidden( "mail_body", htmlSpecialChars( stripslashes( $_POST['mail_body'] ), ENT_QUOTES ) );
                $start_hidden = new ZariliaFormHidden( "mail_start", $mail_end );
                $to_hidden = new ZariliaFormHidden( "mail_send_to[]", "mail" );
                $op_hidden = new ZariliaFormHidden( "op", "send" );
                $submit_button = new ZariliaFormButton( "", "mail_submit", _AM_SENDNEXT, "submit" );
                $sent_label = new ZariliaFormLabel( _AM_SENT, sprintf( _AM_SENTNUM, $_POST['mail_start'] + 1, $mail_end, $added_count ) );
                $form->addElement( $sent_label );
                $form->addElement( $inactive_hidden );
                $form->addElement( $lastlog_min_hidden );
                $form->addElement( $lastlog_max_hidden );
                $form->addElement( $regd_min_hidden );
                $form->addElement( $regd_max_hidden );
                $form->addElement( $idle_more_hidden );
                $form->addElement( $idle_less_hidden );
                $form->addElement( $fname_hidden );
                $form->addElement( $femail_hidden );
                $form->addElement( $subject_hidden );
                $form->addElement( $body_hidden );
                $form->addElement( $to_hidden );
                $form->addElement( $op_hidden );
                $form->addElement( $start_hidden );
                $form->addElement( $submit_button );
                $form->display();
            } else {
                echo "<h4>" . _AM_SENDCOMP . "</h4>";
            }
        } else {
            echo "<h4>" . _AM_NOUSERMATCH . "</h4>";
        }
		break;
    /**
     */
    case 'form':
    default:
        zarilia_cp_header();
        $display_criteria = 1;
        $menu_handler->render( 1 );
        require_once ZAR_ROOT_PATH . "/addons/system/admin/mailusers/mailform.php";
        $form->display();
        break;
} // switch
zarilia_cp_footer();
?>