<?php
// $Id: section.php,v 1.1 2007/03/16 02:38:03 catzwolf Exp $
// %%%%%%	Admin Addons Name  AdminGroup 	%%%%%
define( '_MA_AD_SECTION_ID', '#' );
define( '_MA_AD_SECTION_TITLE', 'Title' );
define( '_MA_AD_SECTION_WEIGHT', 'Weight' );
define( '_MA_AD_SECTION_DISPLAY', 'Active' );
define( '_MA_AD_SECTION_PUBLISHED', 'Published Date' );
define( '_MA_AD_ESECTION_CREATE', 'Create Section' );
define( '_MA_AD_ESECTION_MODIFY', 'Modify Section' );

define( '_MA_AD_ESECTION_TITLE', 'Section Title:' );
define( '_MA_AD_ESECTION_TITLE_DSC', '' );
define( '_MA_AD_ESECTION_TYPE', 'Section Type:' );
define( '_MA_AD_ESECTION_TYPE_DSC', '' );

define( '_MD_AM_ESECTION_TEXT', 'Section Description:' );
define( '_MD_AM_ESECTION_RGRP', 'Access Groups:' );
define( '_MD_AM_ESECTION_WGRP', 'Submission Groups:' );
define( '_MA_AD_SMILIES_SELECTIMAGE', 'Select Section Image:' );
define( '_MA_AD_SMILIES_SELECTIMAGE_DSC', '' );
define( '_MA_AD_ESECTION_SIDE', 'Section Image Position:' );
define( '_MA_AD_ESECTION_SIDE_DSC', '' );
define( '_MA_AD_ESECTION_WEIGHT', 'Section Order:' );
define( '_MA_AD_ESECTION_WEIGHT_DSC', '' );
define( '_MA_AD_ESECTION_DISPLAY', 'Activate Section?' );
define( '_MA_AD_ESECTION_DISPLAY_DSC', '' );

define( '_MA_AD_SECTION_INFO', 'Information' );
define( '_MA_AD_SECTION_MENUS', 'Menu' );

define( '_MA_AD_SECTION_MENUTITLE', 'Menu Title:' );
define( '_MA_AD_SECTION_MENUTYPE', 'Menu Type:' );
define( '_MA_AD_SECTION_MENUALEVEL', 'Access Level:' );

/*Addons Information*/
if ( !defined( '_MA_INFO_NAME' ) ) {
    define( '_MA_INFO_NAME', 'Section Administration' );
    define( '_MA_INFO_DESCRIPTION', '' );
    define( '_MA_INFO_AUTHOR', 'Zarilia Project' );
    define( '_MA_INFO_LICENSE', 'GPL see LICENSE' );
    define( '_MA_INFO_IMAGE', 'section_admin.png' );

    define( '_MA_INFO_LEAD', 'John Neill, Raimondas Rimkevicius' );
    define( '_MA_INFO_CONTRIBUTORS', '' );
    define( '_MA_INFO_CREDITS', '' );
    define( '_MA_INFO_WEBSITE_URL', 'http://zarilia.com' );
    define( '_MA_INFO_WEBSITE_NAME', 'Zarilia Project' );
    define( '_MA_INFO_EMAIL', 'webmaster@zarilia.com' );
    define( '_MA_INFO_VERSION', '1.3' );
    define( '_MA_INFO_STATUS', 'Alpha' );
    define( '_MA_INFO_RELEASEDATE', 'Not Yet' );
    define( '_MA_INFO_DISCLAIMER', 'This is a alpha product and not to be used on a production website' );

    define( '_MA_INFO_DEMO_SITE_URL', 'http://zarilia.com' );
    define( '_MA_INFO_DEMO_SITE_NAME', 'Zarilia Demo' );
    define( '_MA_INFO_SUPPORT_SITE_URL', 'http://zarilia.com' );
    define( '_MA_INFO_SUPPORT_SITE_NAME', 'Zarilia Support Site' );
    define( '_MA_INFO_SUBMIT_BUG_URL', 'http://sourceforge.net/tracker/?group_id=140225&atid=745776' );
    define( '_MA_INFO_SUBMIT_BUG_NAME', 'Zarilia Bug Tracker' );
    define( '_MA_INFO_SUBMIT_FEATURE_URL', 'http://sourceforge.net/tracker/?group_id=140225&atid=745779' );
    define( '_MA_INFO_SUBMIT_FEATURE_NAME', 'Zarilia Feature Tracker' );
    define( '_MA_INFO_PATH', 'index.php?fct=section' );
    define( '_MA_INFO_OFFICIAL', 1 );
    define( '_MA_INFO_SYSTEM', 1 );
    define( '_MA_INFO_HASADMIN', 1 );
}

?>