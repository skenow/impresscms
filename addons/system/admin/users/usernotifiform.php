<?php
$form->addElement( new ZariliaFormRadioYN( _MA_AD_MAILOK, 'user_mailok', $user->getVar( 'user_mailok', 'E' ) ), true );
$umode_select = new ZariliaFormSelect( _MA_AD_CDISPLAYMODE, "umode", $user->getVar( "umode" ) );
$umode_select->addOptionArray(
    array( "nest" => _NESTED,
        "flat" => _FLAT,
        "thread" => _THREADED
        )
    );
$form->addElement( $umode_select );

$uorder_select = new ZariliaFormSelect( _MA_AD_CSORTORDER, "uorder", $user->getVar( "uorder" ) );
$uorder_select->addOptionArray(
    array( "0" => _OLDESTFIRST,
        "1" => _NEWESTFIRST
        )
    );
$form->addElement( $uorder_select );

include_once ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/notification.php';
include_once ZAR_ROOT_PATH . '/include/notification_constants.php';
$notify_method_select = new ZariliaFormSelect( _NOT_NOTIFYMETHOD, 'notify_method', $user->getVar( "notify_method" ) );
$notify_method_select->addOptionArray(
    array(
        ZAR_NOTIFICATION_METHOD_DISABLE => _NOT_METHOD_DISABLE,
        ZAR_NOTIFICATION_METHOD_PM => _NOT_METHOD_PM,
        ZAR_NOTIFICATION_METHOD_EMAIL => _NOT_METHOD_EMAIL
        )
    );
$form->addElement( $notify_method_select );

$notify_mode_select = new ZariliaFormSelect( _NOT_NOTIFYMODE, 'notify_mode', $user->getVar( "notify_mode" ) );
$notify_mode_select->addOptionArray(
    array(
        ZAR_NOTIFICATION_MODE_SENDALWAYS => _NOT_MODE_SENDALWAYS,
        ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE => _NOT_MODE_SENDONCE,
        ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT => _NOT_MODE_SENDONCEPERLOGIN )
    );
$form->addElement( $notify_mode_select );

?>