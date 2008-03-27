<?php
$form = new ZariliaThemeForm( _AM_FINDUS, 'user_findform', @$addonversion['adminpath'] );
$uname_text = new ZariliaFormText( '', 'user_uname', 30, 60 );
$uname_match = new ZariliaFormSelectMatchOption( '', 'user_uname_match' );
$uname_tray = new ZariliaFormElementTray( _AM_UNAME, '&nbsp;' );
$uname_tray->addElement( $uname_match );
$uname_tray->addElement( $uname_text );
$form->addElement( $uname_tray );

$name_text = new ZariliaFormText( '', 'user_name', 30, 60 );
$name_match = new ZariliaFormSelectMatchOption( '', 'user_name_match' );
$name_tray = new ZariliaFormElementTray( _AM_REALNAME, '&nbsp;' );
$name_tray->addElement( $name_match );
$name_tray->addElement( $name_text );
$form->addElement( $name_tray );

$email_text = new ZariliaFormText( '', 'user_email', 30, 60 );
$email_match = new ZariliaFormSelectMatchOption( '', 'user_email_match' );
$email_tray = new ZariliaFormElementTray( _AM_EMAIL, '&nbsp;' );
$email_tray->addElement( $email_match );
$email_tray->addElement( $email_text );
$form->addElement( $email_tray );

$form->addElement( new ZariliaFormText( _AM_URLC, 'user_url', 30, 100 ), false );
$form->addElement( new ZariliaFormText( _AM_LASTLOGMORE, 'user_lastlog_more', 10, 5 ), false );
$form->addElement( new ZariliaFormText( _AM_LASTLOGLESS, 'user_lastlog_less', 10, 5 ), false );
$form->addElement( new ZariliaFormText( _AM_REGMORE, 'user_reg_more', 10, 5 ), false );
$form->addElement( new ZariliaFormText( _AM_REGLESS, 'user_reg_less', 10, 5 ), false );
$form->addElement( new ZariliaFormText( _AM_POSTSMORE, 'user_posts_more', 10, 5 ), false );
$form->addElement( new ZariliaFormText( _AM_POSTSLESS, 'user_posts_less', 10, 5 ), false );

$mailok_radio = new ZariliaFormRadio( _AM_SHOWMAILOK, 'user_mailok', 'both' );
$mailok_radio->addOptionArray( array( 'mailok' => _AM_MAILOK, 'mailng' => _AM_MAILNG, 'both' => _AM_BOTH ) );
$form->addElement( $mailok_radio );

$type_radio = new ZariliaFormRadio( _AM_SHOWTYPE, 'user_type', 'both' );
$type_radio->addOptionArray( array( 'actv' => _AM_ACTIVE, 'inactv' => _AM_INACTIVE, 'suspend' => _AM_SUSPENDED, 'both' => _AM_BOTH ) );
$form->addElement( $type_radio );

$sort_select = new ZariliaFormSelect( _AM_SORT, 'user_sort' );
$sort_select->addOptionArray( array( 'uname' => _AM_UNAME, 'email' => _AM_EMAIL, 'last_login' => _AM_LASTLOGIN, 'user_regdate' => _AM_REGDATE, 'posts' => _AM_POSTS ) );
$form->addElement( $sort_select );

$order_select = new ZariliaFormSelect( _AM_ORDER, 'user_order' );
$order_select->addOptionArray( array( 'ASC' => _AM_ASC, 'DESC' => _AM_DESC ) );
$form->addElement( $order_select );
$form->addElement( new ZariliaFormText( _AM_LIMIT, 'limit', 6, 2, '20' ), false );

$form->addElement( new ZariliaFormHidden( 'op', 'submit' ) );
$group = zarilia_cleanRequestVars( $_REQUEST, 'group', 0 );
if ( $group > 0 ) {
    $form->addElement( new ZariliaFormHidden( 'group', $group ) );
}
// if this is to find users for a specific group
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();
?>