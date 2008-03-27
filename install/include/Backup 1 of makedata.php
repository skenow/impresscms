<?php
// $Id: Backup\0401\040of\040makedata.php,v 1.1 2007/04/21 09:44:38 catzwolf Exp $
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
if ( !function_exists( 'getip' ) ) {
    function getip() {
        if ( isset( $_SERVER ) ) {
            if ( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
                $realip = $_SERVER["HTTP_X_FORWARDED_FOR"];
            } elseif ( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
                $realip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                $realip = $_SERVER["REMOTE_ADDR"];
            }
        } else {
            if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
                $realip = getenv( 'HTTP_X_FORWARDED_FOR' );
            } elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {
                $realip = getenv( 'HTTP_CLIENT_IP' );
            } else {
                $realip = getenv( 'REMOTE_ADDR' );
            }
        }
        return $realip;
    }
}

function getServerAddress() {
    if ( $_SERVER['SERVER_ADDR'] ) {
        return $_SERVER['SERVER_ADDR'];
    }
    $ifconfig = shell_exec( '/sbin/ifconfig eth0' );
    preg_match( '/addr:([\d\.]+)/', $ifconfig, $match );
    return $match[1];
}

function make_data( &$dbm, $login, $uname, $pass, $email, $language ) {
    $tables = array();
    global $zariliaOption;

    $registered = time();
    $site_lang = $_SESSION[$zariliaOption['InstallPrefix']]['sitelanguage'];
    $stl_name = $site_lang['name'];
    $stl_code = $site_lang['code'];
    $stl_image = $site_lang['image'];
    $stl_path = $site_lang['path'];
    $stl_charset = $site_lang['charset'];

    $dbm->insert( 'tplset', " VALUES (1, 'default', 'Zarilia Default Template Set', '', " . time() . ")" );

    $ip = getip();
    $dbm->insert( 'users', "VALUES (1, '" . addslashes( $login ) . "', '', '" . addslashes( $uname ) . "', '" . addslashes( $email ) . "', '', '', $registered, '', '', 1, '', '" . addslashes( $pass ) . "', 7, 0, 7, 1, '', 0.0, 0, '', 0, 1, 0, 1, '" . addslashes( $ip ) . "', 0, 1, '" . addslashes( $language ) . "', 'dhtmltextarea', 0, 0, '', 0)" );
    $dbm->insert( 'language_base', "VALUES (1, 0, '" . addslashes( $stl_name ) . "', '" . addslashes( $stl_path ) . "', '" . addslashes( $stl_code ) . "', '" . addslashes( $stl_charset ) . "', '" . addslashes( $stl_image ) . "')" );
    $server_address = getServerAddress();
    if ( empty( $server_address ) ) {
        $server_address = '127.0.0.1';
    }
    $time_diff_val = date( 'O' );
    $time_diff = floatval( substr( $time_diff_val, 0, 1 ) . ( substr( $time_diff_val, 1, 2 ) + substr( $time_diff_val, 3, 2 ) / 60 ) );
    $dbm->insert( 'config', "VALUES
			(1, 0, 1, 1, 'sitename', '_MD_AM_SITENAME', 'Your Site Name', '_MD_AM_SITENAMEDSC', 'textbox', 'text', 0, 0),
			(2, 0, 1, 1, 'slogan', '_MD_AM_SLOGAN', 'Your Site Slogan', '_MD_AM_SLOGANDSC', 'textbox', 'text', 2, 0),
			(3, 0, 13, 1, 'language', '_MD_AM_LANGUAGE', '" . addslashes( $language ) . "', '_MD_AM_LANGUAGEDSC', 'language', 'other', 4, 0),
			(4, 0, 1, 1, 'startpage', '_MD_AM_STARTPAGE', '0', '_MD_AM_STARTPAGEDSC', 'startpage', 'other', 6, 0),
			(5, 0, 13, 1, 'server_TZ', '_MD_AM_SERVERTZ', '" . $time_diff . "', '_MD_AM_SERVERTZDSC', 'timezone', 'float', 8, 0),
			(6, 0, 13, 1, 'default_TZ', '_MD_AM_DEFAULTTZ', '" . $time_diff . "', '_MD_AM_DEFAULTTZDSC', 'timezone', 'float', 10, 0),
			(7, 0, 1, 1, 'theme_set', '_MD_AM_DTHEME', 'default', '_MD_AM_DTHEMEDSC', 'theme', 'other', 12, 0),
			(8, 0, 1, 1, 'anonymous', '_MD_AM_ANONNAME', '" . addslashes( _INSTALL_ANON ) . "', '_MD_AM_ANONNAMEDSC', 'textbox', 'text', 15, 0),
			(9, 0, 15, 1, 'gzip_compression', '_MD_AM_USEGZIP', '0', '_MD_AM_USEGZIPDSC', 'yesno', 'int', 16, 0),
			(10, 0, 15, 1, 'usercookie', '_MD_AM_USERCOOKIE', '', '_MD_AM_USERCOOKIEDSC', 'textbox', 'text', 18, 0),
			(11, 0, 15, 1, 'session_expire', '_MD_AM_SESSEXPIRE', '15', '_MD_AM_SESSEXPIREDSC', 'textbox', 'int', 22, 0),
			(13, 0, 15, 1, 'debug_mode', '_MD_AM_DEBUGMODE', 'a:1:{i:0;s:1:\"0\";}', '_MD_AM_DEBUGMODEDSC', 'select_multi', 'array', 0, 0),
			(14, 0, 15, 1, 'my_ip', '_MD_AM_MYIP', '" . addslashes( $server_address ) . "', '_MD_AM_MYIPDSC', 'textbox', 'text', 29, 0),
			(15, 0, 15, 1, 'use_ssl', '_MD_AM_USESSL', '0', '_MD_AM_USESSLDSC', 'yesno', 'int', 30, 0),
			(16, 0, 15, 1, 'session_name', '_MD_AM_SESSNAME', 'zarilia_session', '_MD_AM_SESSNAMEDSC', 'textbox', 'text', 20, 0),
			(17, 0, 2, 1, 'minpass', '_MD_AM_MINPASS', '5', '_MD_AM_MINPASSDSC', 'textbox', 'int', 2, 0),
			(18, 0, 2, 1, 'minuname', '_MD_AM_MINUNAME', '3', '_MD_AM_MINUNAMEDSC', 'textbox', 'int', 2, 0),
			(19, 0, 2, 1, 'new_user_notify', '_MD_AM_NEWUNOTIFY', '1', '_MD_AM_NEWUNOTIFYDSC', 'yesno', 'int', 4, 0),
			(20, 0, 2, 1, 'new_user_notify_group', '_MD_AM_NOTIFYTO', '1', '_MD_AM_NOTIFYTODSC', 'group', 'int', 6, 0),
			(21, 0, 2, 1, 'activation_type', '_MD_AM_ACTVTYPE', '0', '_MD_AM_ACTVTYPEDSC', 'select', 'int', 8, 0),
			(22, 0, 2, 1, 'activation_group', '_MD_AM_ACTVGROUP', '1', '_MD_AM_ACTVGROUPDSC', 'group', 'int', 10, 0),
			(23, 0, 2, 1, 'uname_test_level', '_MD_AM_UNAMELVL', '0', '_MD_AM_UNAMELVLDSC', 'select', 'int', 12, 0),
			(24, 0, 2, 1, 'avatar_allow_upload', '_MD_AM_AVATARALLOW', '1', '_MD_AM_AVATARALWDSC', 'yesno', 'int', 14, 0),
			(27, 0, 2, 1, 'avatar_width', '_MD_AM_AVATARW', '80', '_MD_AM_AVATARWDSC', 'textbox', 'int', 16, 0),
			(28, 0, 2, 1, 'avatar_height', '_MD_AM_AVATARH', '80', '_MD_AM_AVATARHDSC', 'textbox', 'int', 18, 0),
			(29, 0, 2, 1, 'avatar_maxsize', '_MD_AM_AVATARMAX', '35000', '_MD_AM_AVATARMAXDSC', 'textbox', 'int', 20, 0),
			(30, 0, 1, 1, 'adminmail', '_MD_AM_ADMINML', '" . addslashes( $adminmail ) . "', '_MD_AM_ADMINMLDSC', 'textbox', 'text', 3, 0),
			(31, 0, 2, 1, 'self_delete', '_MD_AM_SELFDELETE', '1', '_MD_AM_SELFDELETEDSC', 'yesno', 'int', 22, 0),
			(32, 0, 1, 1, 'com_mode', '_MD_AM_COMMODE', 'nest', '_MD_AM_COMMODEDSC', 'select', 'text', 34, 0),
			(33, 0, 1, 1, 'com_order', '_MD_AM_COMORDER', '0', '_MD_AM_COMORDERDSC', 'select', 'int', 36, 0),
			(34, 0, 2, 1, 'bad_unames', '_MD_AM_BADUNAMES', 'a:3:{i:0;s:9:\"webmaster\";i:1;s:14:\"^administrator\";i:2;s:6:\"^admin\";}', '_MD_AM_BADUNAMESDSC', 'textarea', 'array', 24, 0),
			(35, 0, 2, 1, 'bad_emails', '_MD_AM_BADEMAILS', 'a:2:{i:0;s:15:\"yourwebsite.com\";i:1;s:11:\"zarilia.com\";}', '_MD_AM_BADEMAILSDSC', 'textarea', 'array', 26, 0),
			(36, 0, 2, 1, 'maxuname', '_MD_AM_MAXUNAME', '25', '_MD_AM_MAXUNAMEDSC', 'textbox', 'int', 3, 0),
			(37, 0, 1, 1, 'bad_ips', '_MD_AM_BADIPS', 'a:1:{i:0;s:0:\"\";}', '_MD_AM_BADIPSDSC', 'textarea', 'array', 42, 0),
			(38, 0, 3, 1, 'meta_keywords', '_MD_AM_METAKEY', 'Zarilia, zarilia', '_MD_AM_METAKEYDSC', 'textarea', 'text', 0, 0),
			(39, 0, 3, 1, 'meta_footer', '_MD_AM_FOOTER', 'Powered by Zarilia Beta V1.0 <a target=\"_blank\" href=\"http://zarilia.com/\">Zarilia CMS</a>', '_MD_AM_FOOTERDSC', 'textarea', 'text', 20, 0),
			(40, 0, 4, 1, 'censor_enable', '_MD_AM_DOCENSOR', '0', '_MD_AM_DOCENSORDSC', 'yesno', 'int', 0, 0),
			(41, 0, 4, 1, 'censor_words', '_MD_AM_CENSORWRD', 'a:2:{i:0;s:4:\"fuck\";i:1;s:4:\"shit\";}', '_MD_AM_CENSORWRDDSC', 'textarea', 'array', 1, 0),
			(42, 0, 4, 1, 'censor_replace', '_MD_AM_CENSORRPLC', '#OOPS#', '_MD_AM_CENSORRPLCDSC', 'textbox', 'text', 2, 0),
			(43, 0, 3, 1, 'meta_robots', '_MD_AM_METAROBOTS', 'index,follow', '_MD_AM_METAROBOTSDSC', 'select', 'text', 2, 0),
			(44, 0, 5, 1, 'enable_search', '_MD_AM_DOSEARCH', '1', '_MD_AM_DOSEARCHDSC', 'yesno', 'int', 0, 0),
			(45, 0, 5, 1, 'keyword_min', '_MD_AM_MINSEARCH', '5', '_MD_AM_MINSEARCHDSC', 'textbox', 'int', 1, 0),
			(46, 0, 2, 1, 'avatar_minposts', '_MD_AM_AVATARMP', '0', '_MD_AM_AVATARMPDSC', 'textbox', 'int', 15, 0),
			(47, 0, 1, 1, 'enable_badips', '_MD_AM_DOBADIPS', '1', '_MD_AM_DOBADIPSDSC', 'yesno', 'int', 40, 0),
			(48, 0, 3, 1, 'meta_rating', '_MD_AM_METARATING', 'general', '_MD_AM_METARATINGDSC', 'select', 'text', 4, 0),
			(49, 0, 3, 1, 'meta_author', '_MD_AM_METAAUTHOR', 'Zarilia', '_MD_AM_METAAUTHORDSC', 'textbox', 'text', 6, 0),
			(50, 0, 3, 1, 'meta_copyright', '_MD_AM_METACOPYR', 'Copyright ï¿½ 2007', '_MD_AM_METACOPYRDSC', 'textbox', 'text', 8, 0),
			(51, 0, 3, 1, 'meta_description', '_MD_AM_METADESC', 'Zarilia - Dynamic Object Oriented content Management System and Web Application Framework', '_MD_AM_METADESCDSC', 'textarea', 'text', 1, 0),
			(52, 0, 2, 1, 'allow_chgmail', '_MD_AM_ALLWCHGMAIL', '0', '_MD_AM_ALLWCHGMAILDSC', 'yesno', 'int', 3, 0),
			(53, 0, 15, 1, 'use_mysession', '_MD_AM_USEMYSESS', '0', '_MD_AM_USEMYSESSDSC', 'yesno', 'int', 19, 0),
			(54, 0, 2, 1, 'reg_dispdsclmr', '_MD_AM_DSPDSCLMR', '1', '_MD_AM_DSPDSCLMRDSC', 'yesno', 'int', 30, 0),
			(55, 0, 2, 1, 'reg_disclaimer', '_MD_AM_REGDSCLMR', '" . addslashes( _INSTALL_DISCLMR ) . "', '_MD_AM_REGDSCLMRDSC', 'htmltextarea', 'text', 32, 0),
			(56, 0, 2, 1, 'allow_register', '_MD_AM_ALLOWREG', '1', '_MD_AM_ALLOWREGDSC', 'yesno', 'int', 0, 0),
			(57, 0, 1, 1, 'theme_fromfile', '_MD_AM_THEMEFILE', '1', '_MD_AM_THEMEFILEDSC', 'yesno', 'int', 13, 0),
			(58, 0, 1, 1, 'closesite', '_MD_AM_CLOSESITE', '1', '_MD_AM_CLOSESITEDSC', 'yesno', 'int', 26, 0),
			(59, 0, 1, 1, 'closesite_okgrp', '_MD_AM_CLOSESITEOK', 'a:1:{i:0;s:1:\"1\";}', '_MD_AM_CLOSESITEOKDSC', 'group_multi', 'array', 27, 0),
			(60, 0, 1, 1, 'closesite_text', '_MD_AM_CLOSESITETXT', '" . _INSTALL_L165 . "', '_MD_AM_CLOSESITETXTDSC', 'textarea', 'text', 28, 0),
			(61, 0, 15, 1, 'sslpost_name', '_MD_AM_SSLPOST', 'zarilia_ssl', '_MD_AM_SSLPOSTDSC', 'textbox', 'text', 31, 0),
			(62, 0, 1, 1, 'addon_cache', '_MD_AM_MODCACHE', 'a:1:{i:0;s:0:\"\";}', '_MD_AM_MODCACHEDSC', 'addon_cache', 'array', 50, 0),
			(24049, 0, 1, 1, 'gatherstats', '_MD_AM_GATHERSTATS', '0', '_MD_AM_GATHERSTATS_DSC', 'yesno', 'int', 10, 0),
			(63, 0, 1, 1, 'template_set', '_MD_AM_DTPLSET', 'default', '_MD_AM_DTPLSETDSC', 'tplset', 'other', 14, 0),
			(64, 0, 6, 1, 'mailmethod', '_MD_AM_MAILERMETHOD', 'mail', '_MD_AM_MAILERMETHODDESC', 'select', 'text', 4, 0),
			(65, 0, 6, 1, 'smtphost', '_MD_AM_SMTPHOST', 'a:1:{i:0;s:0:\"\";}', '_MD_AM_SMTPHOSTDESC', 'textarea', 'array', 6, 0),
			(66, 0, 6, 1, 'smtpuser', '_MD_AM_SMTPUSER', '', '_MD_AM_SMTPUSERDESC', 'textbox', 'text', 7, 0),
			(67, 0, 6, 1, 'smtppass', '_MD_AM_SMTPPASS', '', '_MD_AM_SMTPPASSDESC', 'password', 'text', 8, 0),
			(68, 0, 6, 1, 'sendmailpath', '_MD_AM_SENDMAILPATH', '/usr/sbin/sendmail', '_MD_AM_SENDMAILPATHDESC', 'textbox', 'text', 5, 0),
			(69, 0, 6, 1, 'from', '_MD_AM_MAILFROM', '', '_MD_AM_MAILFROMDESC', 'textbox', 'text', 1, 0),
			(70, 0, 6, 1, 'fromname', '_MD_AM_MAILFROMNAME', '', '_MD_AM_MAILFROMNAMEDESC', 'textbox', 'text', 2, 0),
			(71, 0, 15, 1, 'sslloginlink', '_MD_AM_SSLLINK', 'https://', '_MD_AM_SSLLINKDSC', 'textbox', 'text', 33, 0),
			(72, 0, 1, 1, 'theme_set_allowed', '_MD_AM_THEMEOK', 'a:2:{i:0;s:7:\"default\";i:1;s:7:\"zarilia\";}', '_MD_AM_THEMEOKDSC', 'theme_multi', 'array', 13, 0),
			(73, 0, 6, 1, 'fromuid', '_MD_AM_MAILFROMUID', '1', '_MD_AM_MAILFROMUIDDESC', 'user', 'int', 3, 0),
			(25592, 0, 2, 1, 'showimagetype', '_MD_AM_SHOWIMAGETYPE', '3', '_MD_AM__SHOWIMAGETYPE', 'select', 'int', 101, 0),
			(22790, 0, 2, 1, 'user_restrict', '_MD_AM_USER_RESTRICT', '0', '_MD_AM_USER_RESTRICTDSC', 'yesno', 'int', 1, 0),
			(22791, 0, 2, 1, 'duplicate_login', '_MD_AM_USER_LRESTRICT', '0', '_MD_AM_USER_LRESTRICTDSC', 'yesno', 'int', 2, 0),
			(22792, 0, 11, 1, 'actual_age', '_MD_AM_COPPAAGE', '13', '_MD_AM_COPPAAGE_DSC', 'textbox', 'int', 1, 0),
			(22793, 0, 11, 1, 'coppa_directtext', '_MD_AM_COPPA_DIRECTTEXT', '', '_MD_AM_COPPA_DIRECTTEXT_DSC', 'htmltextarea', 'text', 103, 0),
			(20404, 0, 15, 1, 'debug_mode_okgrp', '_MD_AM_DEBUGMODEOK', 'a:1:{i:0;s:1:\"1\";}', '_MD_AM_DEBUGMODEOKDSC', 'group_multi', 'array', 2, 0),
			(20403, 0, 15, 1, 'debug_level', '_MD_AM_DEBUGLEVEL', '6143', '_MD_AM_DEBUGLEVELDSC', 'select', 'text', 1, 0),
			(20405, 0, 13, 1, 'timestamp', '_MD_AM_TIME', 'm/d/Y H:i', '_MD_AM_TIMEDESC', 'textbox', 'text', 11, 0),
			(22784, 0, 11, 1, 'show_coppa', '_MD_AM_COPPA_REGISTER', '1', '_MD_AM_COPPA_REGISTER_DSC', 'yesno', 'int', 0, 0),
			(22785, 0, 11, 1, 'coppa_email', '_MD_AM_COPPA_EMAIL', '" . addslashes( $adminmail ) . "', '_MD_AM_COPPA_EMAIL_DSC', 'textbox', 'textbox', 1, 0),
			(22786, 0, 11, 1, 'coppa_fax', '_MD_AM_COPPA_FAX', '', '" . addslashes( _MD_AM_COPPA_FAX_DSC ) . "', 'textbox', 'textbox', 2, 0),
			(22787, 0, 11, 1, 'coppa_text', '_MD_AM_COPPA_TEXT', '_MD_AM_COPPA_FAX_DSC', '_MD_AM_COPPA_TEXT_DSC', 'htmltextarea', 'text', 3, 0),
			(22839, 0, 8, 1, 'img_prefix', '_MD_AM_IMGPREFIX', 'img', '_MD_AM_IMGPREFIX_DSC', 'textbox', 'text', 0, 0),
			(22840, 0, 2, 1, 'use_img_ver', '_MD_AM_IMGVER', '0', '_MD_AM_IMGVER_DSC', 'yesno', 'int', 99, 0),
			(23226, 0, 14, 1, 'ldap_uid_attr', '_MD_AM_LDAP_UID_ATTR', 'uid', '_MD_AM_LDAP_UID_ATTR_DESC', 'textbox', 'text', 60, 0),
			(23225, 0, 14, 1, 'ldap_base_dn', '_MD_AM_LDAP_BASE_DN', 'ou=Employees,o=Company', '_MD_AM_LDAP_BASE_DN_DESC', 'textbox', 'text', 59, 0),
			(23224, 0, 14, 1, 'ldap_server', '_MD_AM_LDAP_SERVER', 'your directory server', '_MD_AM_LDAP_SERVER_DESC', 'textbox', 'text', 58, 0),
			(23223, 0, 14, 1, 'ldap_port', '_MD_AM_LDAP_PORT', '389', '_MD_AM_LDAP_PORT', 'textbox', 'int', 57, 0),
			(23222, 0, 14, 1, 'ldap_givenname_attr', '_MD_AM_LDAP_GIVENNAME_ATTR', 'givenname', '_MD_AM_LDAP_GIVENNAME_ATTR_DSC', 'textbox', 'text', 56, 0),
			(23221, 0, 14, 1, 'ldap_surname_attr', '_MD_AM_LDAP_SURNAME_ATTR', 'sn', '_MD_AM_LDAP_SURNAME_ATTR_DESC', 'textbox', 'text', 55, 0),
			(23220, 0, 14, 1, 'ldap_name_attr', '_MD_AM_LDAP_NAME_ATTR', 'cn', '_MD_AM_LDAP_NAME_ATTR_DESC', 'textbox', 'text', 54, 0),
			(23219, 0, 14, 1, 'ldap_mail_attr', '_MD_AM_LDAP_MAIL_ATTR', 'mail', '_MD_AM_LDAP_MAIL_ATTR_DESC', 'textbox', 'text', 53, 0),
			(23218, 0, 14, 1, 'auth_method', '_MD_AM_AUTHMETHOD', 'codeplus', '_MD_AM_AUTHMETHODDESC', 'select', 'text', 52, 0),
			(23227, 0, 14, 1, 'ldap_uid_asdn', '_MD_AM_LDAP_UID_ASDN', '0', '_MD_AM_LDAP_UID_ASDN_DESC', 'yesno', 'int', 61, 0),
			(23228, 0, 14, 1, 'ldap_manager_dn', '_MD_AM_LDAP_MANAGER_DN', 'manager_dn', '_MD_AM_LDAP_MANAGER_DN_DESC', 'textbox', 'text', 62, 0),
			(23229, 0, 14, 1, 'ldap_manager_pass', '_MD_AM_LDAP_MANAGER_PASS', 'manager_pass', '_MD_AM_LDAP_MANAGER_PASS_DESC', 'textbox', 'text', 63, 0),
			(23230, 0, 14, 1, 'ldap_version', '_MD_AM_LDAP_VERSION', '3', '_MD_AM_LDAP_VERSION_DESC', 'textbox', 'text', 64, 0),
			(23974, 0, 1, 1, 'quickredirect', '_MD_AM_QUICKREDIRECT', '0', '_MD_AM_QUICKREDIRECTDSC', 'yesno', 'int', 99, 0),
			(23975, 0, 1, 1, 'useshorturls', '_MD_AM_SHORTURL', '0', '_MD_AM_SHORTURLDSC', 'yesno', 'int', 100, 0),
			(24086, 0, 1, 1, 'admin_default', '_MD_AM_AEDITOR', 'textarea', '_MD_AM_AEDITORDSC', 'editor', 'text', 110, 0),
			(24088, 0, 1, 1, 'user_default', '_MD_AM_UEDITOR', 'dhtmltextarea', '_MD_AM_UEDITORDSC', 'editor', 'text', 112, 0),
			(24087, 0, 1, 1, 'user_select', '_MD_AM_UCHOICE', 'a:2:{i:0;s:8:\"textarea\";i:1;s:13:\"dhtmltextarea\";}', '_MD_AM_UCHOICEDSC', 'editor_multi', 'array', 111, 0),
			(24089, 0, 1, 1, 'rows', '_MD_AM_DEFAULTROWS', '15', '_MD_AM_DEFAULTROWSDSC', 'textbox', 'int', 113, 0),
			(24090, 0, 1, 1, 'cols', '_MD_AM_DEFAULTCOLS', '75', '_MD_AM_DEFAULTCOLSDSC', 'textbox', 'int', 114, 0),
			(24091, 0, 1, 1, 'width', '_MD_AM_DEFAULTWIDTH', '100', '_MD_AM_DEFAULTWIDTHDSC', 'textbox', 'text', 115, 0),
			(24092, 0, 1, 1, 'height', '_MD_AM_DEFAULTHEIGHT', '200', '_MD_AM_DEFAULTHEIGHTDSC', 'textbox', 'text', 116, 0),
			(24093, 0, 13, 1, 'charset', '_MD_AM_CHARSET', 'utf-8', '_MD_AM_CHARSETDSC', 'charset', 'textbox', 0, 0),
			(24094, 0, 13, 1, 'multibyte', '_MD_AM_MULTIBYTE', '0', '_MD_AM_MULTIBYTEDSC', 'yesno', 'int', 2, 0),
			(24095, 0, 3, 1, 'metalang', '_MD_AM_LANGUAGE', 'english', '_MD_AM_LANGUAGEDSC', 'language', 'text', 0, 0),
			(24134, 0, 7, 1, 'allow_pm', '_MD_AM_ALLOWPM', '1', '_MD_AM_ALLOWPMDSC', 'yesno', 'int', 0, 0),
			(24197, 0, 7, 1, 'message_okgrp', '_MD_AM_MESSAGE_GROUP', 'a:5:{i:0;s:1:\"1\";i:1;s:1:\"2\";i:2;s:1:\"4\";i:3;s:1:\"5\";i:4;s:1:\"6\";}', '_MD_AM_MESSAGE_DSC', 'group_multi', 'array', 4, 0),
			(24135, 0, 13, 1, 'multiLanguageEditorStyle', '_MD_AM_MLEDITSTYLE', 'simple_edit', '_MD_AM_MLEDITSTYLEDSC', 'mledit', 'text', 11, 0),
			(75, 0, 16, 1, 'events_enable', '_MD_AM_EVENTSENABLE', '1', '_MD_AM_EVENTSENABLE_DESC', 'yesno', 'int', 0, 1),
			(24214, 0, 16, 1, 'events_system', '_MD_AM_EVENTSSYSTEM', 'internal', '_MD_AM_EVENTSSYSTEM_DESC', 'select', 'text', 0, 1),
			(24215, 0, 16, 1, 'events_helper', '_MD_AM_EVENTSHELPER', '', '_MD_AM_EVENTSHELPER_DESC', 'text', 'text', 0, 1),
			(24216, 0, 16, 1, 'events_helper_arguments', '_MD_AM_EVENTSHCMD', '', '_MD_AM_EVENTSHCMD_DESC', '', '', 0, 0),
			(25591, 0, 2, 1, 'showimagever', '_MD_AM_SHOWIMAGEVER', '0', '_MD_AM_SHOWIMAGEVERDSC', 'yesno', 'int', 100, 0),
			(25593, 0, 2, 1, 'pass_level', '_MD_AM_PASSLEVEL', '60', '_MD_AM_PASSLEVEL_DESC', 'select', 'int', 1, 0),
			(25594, 0, 15, 1, 'gzip_compression_level', '_MD_AM_COMPRESSIONLVL', 'a:1:{i:0;s:1:\"0\";}', '_MD_AM_COMPRESSIONLVL_DESC', 'select_multi', 'array', 17, 0),
			(25705, 20, 0, 0, 'notification_enabled', '_NOT_CONFIG_ENABLE', '3', '_NOT_CONFIG_ENABLEDSC', 'select', 'int', 0, 0),
			(25706, 20, 0, 0, 'notification_events', '_NOT_CONFIG_EVENTS', 'a:8:{i:0;s:15:\"thread-new_post\";i:1;s:15:\"thread-bookmark\";i:2;s:16:\"forum-new_thread\";i:3;s:14:\"forum-new_post\";i:4;s:14:\"forum-bookmark\";i:5;s:16:\"global-new_forum\";i:6;s:15:\"global-new_post\";i:7;s:19:\"global-new_fullpost\";}', '_NOT_CONFIG_EVENTSDSC', 'select_multi', 'array', 1, 0);
		" );
}

function installAddon( &$dbm, $mid = 0, $addon = '', $language ) {
    if ( !$mid || empty( $addon ) ) {
        return false;
    }

    if ( file_exists( "../addons/${addon}/language/${language}/addoninfo.php" ) ) {
        include "../addons/${addon}/language/${language}/addoninfo.php";
    } else {
        include "../addons/${addon}/language/english/addoninfo.php";
        $language = 'english';
    }

    $addonversion = array();
    require_once "../addons/${addon}/zarilia_version.php";
    $addon = $addonversion['dirname'];
    $addonName = $addonversion['name'];
    $hasconfig = isset( $addonversion['config'] ) ? 1 : 0;
    $hasmimetype = isset( $addonversion['mimetype'] ) ? 1 : 0;
    $hasage = isset( $addonversion['hasage'] ) ? 1 : 0;
    $hassubmit = isset( $addonversion['hassubmit'] ) ? 1 : 0;
    $hasmain = 0;
    $time = time();

    if ( isset( $addonversion['hasMain'] ) && $addonversion['hasMain'] == 1 ) {
        $hasmain = 1;
    }
    $dbm->insert( "addons", "VALUES ( ${mid}, '${addonName}', 100, " . $time . ", 0, 1, '${addon}', ${hasmain}, 1, 0, ${hasconfig}, 0, 0, ${hasage}, ${hasmimetype}, ${hassubmit})" );
    $dbm->insert( "group_permission", " VALUES (0," . ZAR_GROUP_ADMIN . ",1,${mid},'addon_admin')" );
    $dbm->insert( "group_permission", " VALUES (0," . ZAR_GROUP_USERS . ",1,${mid}, 'addon_read')" );
    $dbm->insert( "group_permission", " VALUES (0," . ZAR_GROUP_MODERATORS . ",1,${mid},'addon_read')" );
    $dbm->insert( "group_permission", " VALUES (0," . ZAR_GROUP_SUBMITTERS . ",1,${mid},'addon_read')" );
    $dbm->insert( "group_permission", " VALUES (0," . ZAR_GROUP_SUBSCRIPTION . ",1,${mid},'addon_read')" );

    if ( isset( $addonversion['sqlfile']['mysql'] ) ) {
        $dbm->queryFromFile( "../addons/${addon}/" . $addonversion['sqlfile']['mysql'] );
    }

    if ( is_array( $addonversion['templates'] ) && count( $addonversion['templates'] ) > 0 ) {
        $template_path = ( $addon == 'system' ) ? ZAR_ROOT_PATH . '/themes/default/addons/system/' : ZAR_ROOT_PATH . '/addons/' . $addon . '/templates/';
        foreach ( $addonversion['templates'] as $tplfile ) {
            if ( $fp = fopen( $template_path . $tplfile['file'], 'r' ) ) {
                $newtplid = $dbm->insert( 'tplfile', " VALUES (0, ${mid}, '${addon}', 'default', '" . addslashes( $tplfile['file'] ) . "', '" . addslashes( $tplfile['description'] ) . "', " . $time . ", " . $time . ", 'addon')" );
                if ( filesize( $template_path . $tplfile['file'] ) > 0 ) {
                    $tplsource = fread( $fp, filesize( $template_path . $tplfile['file'] ) );
                } else {
                    $tplsource = "";
                }
                fclose( $fp );
                $dbm->insert( 'tplsource', " (tpl_id, tpl_source) VALUES (" . $newtplid . ", '" . addslashes( $tplsource ) . "')" );
            }
        }
    }

    if ( is_array( $addonversion['blocks'] ) && count( $addonversion['blocks'] ) > 0 ) {
        $template_path = ( $addon == 'system' ) ? ZAR_ROOT_PATH . '/themes/default/addons/system/blocks/' : ZAR_ROOT_PATH . '/addons/' . $addon . '/templates/blocks/';
        $type = ( $addon == 'system' ) ? "S" : "M";
        foreach ( $addonversion['blocks'] as $func_num => $newblock ) {
            if ( $fp = fopen( $template_path . $newblock['template'], 'r' ) ) {
                // The following checking is dependence on the structure of system addon.
                if ( in_array( $newblock['template'], array( 'system_block_login.html', 'system_block_mainmenu.html' ) ) ) {
                    $side = 0;
                } else {
                    $side = 9;
                }
                $options = !isset( $newblock['options'] ) ? '' : trim( $newblock['options'] );
                $edit_func = !isset( $newblock['edit_func'] ) ? '' : trim( $newblock['edit_func'] );
                $newbid = $dbm->insert( 'newblocks', " VALUES (0, ${mid}, " . $func_num . ", '" . addslashes( $options ) . "', '" . addslashes( $newblock['name'] ) . "', '" . addslashes( $newblock['name'] ) . "', '', ${side}, 0, '${type}', 'H', 1, '${addon}', '" . addslashes( $newblock['file'] ) . "', '" . addslashes( $newblock['show_func'] ) . "', '" . addslashes( $edit_func ) . "', '" . addslashes( $newblock['template'] ) . "', 0, " . $time . ", '" . addslashes( $newblock['description'] ) . "', 0)" );
                $newtplid = $dbm->insert( 'tplfile', " VALUES (0, ${newbid}, '${addon}', 'default', '" . addslashes( $newblock['template'] ) . "', '" . addslashes( $newblock['description'] ) . "', " . $time . ", " . $time . ", 'block')" );
                if ( filesize( $template_path . $newblock['template'] ) > 0 ) {
                    $tplsource = fread( $fp, filesize( $template_path . "/" . $newblock['template'] ) );
                } else {
                    $tplsource = "";
                }
                fclose( $fp );
                $dbm->insert( 'tplsource', " (tpl_id, tpl_source) VALUES ( ${newtplid}, '" . addslashes( $tplsource ) . "')" );
                $dbm->insert( "group_permission", " VALUES (0, " . ZAR_GROUP_ADMIN . ", ${newbid}, 1, 'block_read')" );
                $dbm->insert( "group_permission", " VALUES (0, " . ZAR_GROUP_USERS . ", ${newbid}, 1, 'block_read')" );
                $dbm->insert( "group_permission", " VALUES (0, " . ZAR_GROUP_ANONYMOUS . ", ${newbid}, 1, 'block_read')" );
                $dbm->insert( "group_permission", " VALUES (0, " . ZAR_GROUP_MODERATORS . ", ${newbid}, 1, 'block_read')" );
                $dbm->insert( "group_permission", " VALUES (0, " . ZAR_GROUP_SUBMITTERS . ", ${newbid}, 1, 'block_read')" );
                $dbm->insert( "group_permission", " VALUES (0, " . ZAR_GROUP_SUBSCRIPTION . ", ${newbid}, 1, 'block_read')" );

                if ( $side == 0 ) {
                    $dbm->insert( "block_addon_link", " VALUES (${newbid}, 0)" );
                } else {
                    $dbm->insert( "block_addon_link", " VALUES (${newbid}, -1)" );
                }
            }
        }
    }
    if ( isset( $modversion['config'] ) ) {
        $count = 0;
        foreach ( $modversion['config'] as $configInfo ) {
            $name = addslashes( $configInfo['name'] );
            $title = addslashes( $configInfo['title'] );
            $desc = addslashes( $configInfo['description'] );
            $formtype = addslashes( $configInfo['formtype'] );
            $valuetype = addslashes( $configInfo['valuetype'] );
            $default = addslashes( $configInfo['default'] );
            if ( $valuetype == "array" ) {
                $default = serialize( explode( '|', trim( $default ) ) );
            }
            $conf_id = $dbm->insert( "config", " VALUES (0, ${mid}, 0, '${name}', '${title}', '${default}', '${desc}', '${formtype}', '${valuetype}', ${count}, 0, 0)" );
            if ( isset( $configInfo['options'] ) && is_array( $configInfo['options'] ) ) {
                foreach ( $configInfo['options'] as $key => $value ) {
                    $dbm->insert( "configoption", " VALUES (0, '${key}', '${value}', ${conf_id})" );
                }
            }
            $count++;
        }
    }
}

?>
