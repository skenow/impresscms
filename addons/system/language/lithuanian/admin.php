<?php
// $Id: admin.php,v 1.2 2007/03/30 22:05:29 catzwolf Exp $
// %%%%%%	File Name  index.php 	%%%%%
/*
* Admin Credits
*/
define( '_MD_AM_ADMIN_AUTHOR', 'John Neill, ' );
define( '_MD_AM_ADMIN_CREDITS', 'Zarilia Project' );

/*Admin Menu titles*/
define( '_MD_AM_CONFIG_MTITLE', 'Configuration' );
define( '_MD_AM_INTERFACE_MTITLE', 'Interface' );
define( '_MD_AM_MISC_MTITLE', 'Misc Tools' );
define( '_MD_AM_ADDONS_MTITLE', 'Addons' );
define( '_MD_AM_USERS_MTITLE', 'Users' );
define( '_MD_AM_INFO_MTITLE', 'Information and Help' );
define( '_MD_AM_MEDIA_MTITLE', 'Media' );
define( '_MD_AM_MENU_MTITLE', 'Menu' );
define( '_MD_AM_CONTENT_MTITLE', 'Content' );
/*
* Admin Addons Information
*/
/**
 */
define( '_MD_AM_AGEVER', 'Age Administration' );
define( '_MD_AM_AGEVER_DSC', '' );
define( '_MD_AM_AGEVER_MENU', 'Age Verification' );
/**
 */
define( '_MD_AM_AVATARS', 'Avatar Administration' );
define( '_MD_AM_AVATARS_DSC', '' );
define( '_MD_AM_AVATARS_MENU', 'Avatars' );
/**
 */
define( '_MD_AM_BANS', 'Banner Administration' );
define( '_MD_AM_BANS_DSC', '' );
define( '_MD_AM_BANS_MENU', 'Banners' );
/**
 */
define( '_MD_AM_BKAD', 'Blocks Administration' );
define( '_MD_AM_BKAD_DSC', '' );
define( '_MD_AM_BKAD_MENU', 'Blocks' );
/**
 */
define( '_MD_AM_COREINFO', 'Core Information' );
define( '_MD_AM_COREINFO_DSC', '' );
define( '_MD_AM_COREINFO_MENU', 'Core Information' );
define( '_MD_AM_COREINFO_PHP_MENU', 'PHP Information' );
define( '_MD_AM_COREINFO_CP_MENU', 'System Information' );
/**
 */
define( '_MD_AM_COMMENTS', 'Comment Administration' );
define( '_MD_AM_COMMENTS_DSC', '' );
define( '_MD_AM_COMMENTS_MENU', 'Comments' );
/**
 */
define( '_MD_AM_DEVELOPERS', 'Developers Tool' );
define( '_MD_AM_DEVELOPERS_DSC', '' );
define( '_MD_AM_DEVELOPERS_MENU', 'Developers Tools' );
/**
 */
define( '_MD_AM_EVENTS', 'Event Administration' );
define( '_MD_AM_EVENTS_DSC', '' );
define( '_MD_AM_EVENTS_MENU', 'Schedule Events' );
/**
 */
define( '_MD_AM_CPANEL', 'Control Panel' );
define( '_MD_AM_CPANEL_DSC', '' );
define( '_MD_AM_CPANEL_MENU', '' );
/**
 */
define( '_MD_AM_ADGS', 'Group Administration' );
define( '_MD_AM_ADGS_DSC', '' );
define( '_MD_AM_ADGS_MENU', 'Group Manager' );
/**
 */
define( '_MD_AM_MLUS', 'Mail Administration' );
define( '_MD_AM_MLUS_DSC', '' );
define( '_MD_AM_MLUS_MENU', 'Mail Users' );
/**
 */
define( '_MD_AM_IMAGES', 'Media Administration' );
define( '_MD_AM_IMAGES_DSC', '' );
define( '_MD_AM_IMAGES_MENU', 'Media' );
define( '_MD_AM_IMAGESNEW_MENU', 'New Media Category' );
define( '_MD_AM_IMAGESUPLOADER_MENU', 'Media Uploader' );
/**
 */
define( '_MD_AM_MIMETYPE', 'Mimetype Administration' );
define( '_MD_AM_MIMETYPE_DSC', '' );
define( '_MD_AM_MIMETYPE_MENU', 'Mimetypes' );
/**
 */
define( '_MD_AM_MDAD', 'Addons Administration' );
define( '_MD_AM_MDAD_DSC', '' );
define( '_MD_AM_MDAD_MENU', 'Addons Administration' );
/**
 */
define( '_MD_AM_MULTISITE', 'Multi Site Administration' );
define( '_MD_AM_MULTISITE_DSC', '' );
define( '_MD_AM_MULTISITE_MENU', 'Multi Site' );
/**
 */
define( '_MD_AM_MULTILANGUAGE', 'Multi Language Administration' );
define( '_MD_AM_MULTILANGUAGE_DSC', '' );
define( '_MD_AM_MULTILANGUAGE_MENU', 'Multi Language' );
/**
 */
define( '_MD_AM_RANK', 'Rank Administration' );
define( '_MD_AM_RANK_DSC', '' );
define( '_MD_AM_RANK_MENU', 'User Ranks' );
/**
 */
define( '_MD_AM_PREF', 'Preference Administration' );
define( '_MD_AM_PREF_DSC', '' );
define( '_MD_AM_PREF_MENU', 'Preferences' );
/**
 */
define( '_MD_AM_PROFILES', 'Profile Administration' );
define( '_MD_AM_PROFILES_DSC', '' );
define( '_MD_AM_PROFILES_MENU', 'Profiles' );
define( '_MD_AM_PROFILES_CAT', 'Profile Category' );
define( '_MD_AM_PROFILES_LIST', 'Profile List' );
define( '_MD_AM_PROFILES_NEW', 'New Profile' );
/**
 */
define( '_MD_AM_SMLS', 'Smilie Administration' );
define( '_MD_AM_SMLS_DSC', '' );
define( '_MD_AM_SMLS_MENU', 'Smilies' );
/**
 */
define( '_MD_AM_TPLSETS', 'Template Administration' );
define( '_MD_AM_TPLSETS_DSC', '' );
define( '_MD_AM_TPLSETS_MENU', 'Templates' );
/**
 */
define( '_MD_AM_TRANSLATE', 'Translation Administration' );
define( '_MD_AM_TRANSLATE_DSC', '' );
define( '_MD_AM_TRANSLATE_MENU', 'Translations' );
/**
 */
define( '_MD_AM_USER', 'User Administration' );
define( '_MD_AM_USER_DSC', '' );
define( '_MD_AM_USER_MENU', 'User Administration' );
define( '_MD_AM_USERNEW_MENU', 'New User' );
/**
 */
define( '_MD_AM_FINDUSER_MENU', 'User Search' );
/**
 */
define( '_MD_AM_VRSN_MENU', 'Version' );
/**
 */
define( '_MD_AM_MENUS_MENU', 'Menus' );
define( '_MD_AM_MENUS_MAINMENU', 'Main Menu' );
define( '_MD_AM_MENUS_USERMENU', 'User Menu' );
define( '_MD_AM_MENUS_TOPMENU', 'Top Menu' );
define( '_MD_AM_MENUS_FOOTERMENU', 'Footer Menu' );
/*
*
*/
define( '_MD_AM_SECTION_MENU', 'Section' );
define( '_MD_AM_SECURITY_MENU', 'Security' );
define( '_MD_AM_TRASH_MENU', 'Trash Items' );
/*
*
*/
define( '_MD_AM_RSS_MENU', 'RSS/RDS Feeds' );
/**
 */
define( '_MD_AM_CATEGORY_MENU', 'Category' );
define( '_MD_AM_STATIC_MENU', 'Static Content' );
define( '_MD_AM_CONTENT_MENU', 'All Content Items' );
define( '_MD_AM_ERRORS_MENU', 'Error Administration' );


//define( '_MD_AM_CONTEST', 'Contest' );
define( '_MD_AM_STREAMING_MENU', 'Streaming' );
/* Box Language Defines */

define( '_MA_AD_', '' );
define( '_MD_AD_ACTION_BOX', 'Action' );
define( '_MD_AD_FILTER_BOX', 'Filter' );
define( '_MD_AD_SEARCH_BOX', 'Search' );
define( '_MD_AD_MAINTENANCE_BOX', 'Maintenance' );
define( '_MD_AD_ADDON_MENU_BOX', 'Addon Menu' );
/**/
define('_MA_AD_ASECTION_CREATE','Create Section');
define('_MA_AD_ACATEGORY_CREATE','Create Category');
define('_MA_AD_ACONTENT_CREATE','Create Page');
define('_MA_AD_ASTATIC_CREATE','Create Static Page');

define('_MD_AD_OPTIMIZE','Optimize Table');
define('_MD_AD_ANALYZE','Analyze Table');
define('_MD_AD_REPAIR','Repair Table');
define('_MD_AD_CLEARENTRIES','Empty Table');

define( '_MD_AD_DISPLAY_BOX', 'Display:' );
define( '_MD_AD_DISPLAYAMOUNT_BOX', 'Display Amount:' );
define( '_MD_AD_DISPLAY_SECTION', 'Display Section:' );
define( '_MD_AD_SHOWALL_BOX', 'Show All' );
define( '_MD_AD_SHOWHIDDEN_BOX', 'Show Deactive' );
define( '_MD_AD_SHOWVISIBLE_BOX', 'Show Active' );

define( '_MD_AD_DOTABLE', 'Do you wish to %s this table?' );
define( '_MD_AD_DOTABLEFINSHED', 'Table has been %s' );
/*Main Panel Language defines*/
// Group permission phrases
define( '_MD_AM_PERMADDNG', 'Could not add %s permission to %s for group %s' );
define( '_MD_AM_PERMS', 'System Wide Permissions' );
define( '_MD_AM_PERM_ADD', 'Selected Permissions now Updated' );
define( '_MD_AM_PERMADDOK', '%u -- %s' );
define( '_MD_AM_PERM_ADDED', '<br />Added to %s group<br />' );
define( '_MD_AM_PERMRESETNG', 'Could not reset group permission for addon %s' );
define( '_MD_AM_PERMADDNGP', 'All parent items must be selected.' );
define( '_MD_AM_NOTDELTE', '<strong>Notice:</strong> %s could not be deleted.<br />' );
define( '_MD_AM_HASDELTE', '%s has be deleted.' );
define( '_MD_AM_FILEDELETE_TITLE', 'Cache Folder Cleared' );
define( '_MD_AM_NOTWRITEABLE', '<strong>WARNING:</strong> Cache Folder not writable on the server.' );
define( '_MD_AM_NOFILESTODELETE', '<strong>Notice:</strong> No files found to delete in cache folder.' );
/*
* Cpanel Information
*/
define( '_MD_AM_CONSOLE_INFO', 'Console Info' );
define( '_MD_AM_WELCOME', 'Welcome : ' );
define( '_MD_AM_PREFS', 'Preferences' );
define( '_MD_AM_ADMININDEX', 'Admin Index' );
define( '_MD_AM_ADMINBREADCRUMB', 'Admin: ' );
define( '_MD_AM_IPLOGIN', 'IP Address: ' );
define( '_MD_AM_LOGINAT', 'Login Time: ' );
/**
 */
// Admin Add'ons Names
define( '_MD_AM_DELETEALL', 'Warning: This will delete all items! Are you sure you want to do this?' );
define( '_MD_AM_WAYSYWTDTR', 'Warning: You are about to delete this item <i>%s</i>?' );
define( '_MD_AM_FAILDEL', 'ERROR: Failed deleting %s from the database.' );
/*
*
*/
define( "_MD_AM_USERSTATS", "Members Stats" );
define( "_MD_AM_TOTALUSERS", "Member Information" );
define( "_MD_AM_NEWMEMBERS", "Members New" );
define( "_MD_AM_MEMBERSONLINE", "Members Online" );
define( "_MD_AM_ACTIVEUSERS", "Members Active:" );
define( "_MD_AM_INACTIVEUSERS", "Members Inactive:" );
define( "_MD_AM_SUSPENDEDUSERS", "Members Suspended:" );
define( "_MD_AM_NEWTODAY", "Registered Today: " );
define( "_MD_AM_LASTSEVENDAYS", "Last 7 Days: " );
define( "_MD_AM_LAST30DAYS", "Last 30 Days: " );
define( "_MD_AM_REGISTERED", "Registered: " );
define( "_MD_AM_NEWESTMEMBERS", "Newest Members " );

/*
* Configuration Menu Items
*/
define( '_MD_AM_GENERAL', 'General' );
define( '_MD_AM_GENERAL_DSC', 'General' );
define( '_MD_AM_USERSETTINGS', 'User Info' );
define( '_MD_AM_USERSETTINGS_DSC', 'User Info' );
define( '_MD_AM_LOCALE', 'locale' );
define( '_MD_AM_LOCALE_DSC', 'Locale' );
define( "_MD_AM_SERVER", "Server" );
define( "_MD_AM_SERVER_DSC", "Server desc" );
define( '_MD_AM_CENSOR', 'Censoring' );
define( '_MD_AM_CENSOR_DSC', '' );
define( '_MD_AM_METAFOOTER', 'Meta Tags' );
define( '_MD_AM_METAFOOTER_DSC', 'Meta Tags' );
define( '_MD_AM_SEARCH', 'Search' );
define( '_MD_AM_SEARCH_DSC', 'Search' );
define( "_MD_AM_MAILER", "Mail" );
define( "_MD_AM_MAILER_DSC", "Mail" );
define( "_MD_AM_MESSAGE", "Messages" );
define( "_MD_AM_MESSAGE_DSC", "Messages" );
define( "_MD_AM_MIMETYPES", "Mimetypes" );
define( "_MD_AM_MIMETYPES_DSC", "Mimetypes" );
define( "_MD_AM_AGE", "Age" );
define( "_MD_AM_AGE_DSC", "Age" );
define( "_MD_AM_THUMBNAILS", "Media" );
define( "_MD_AM_THUMBNAILS_DSC", "Media" );
define( '_MD_AM_COPPA', 'Coppa' );
define( '_MD_AM_COPPA_DSC', '' );
define( "_MD_AM_AUTHENTICATION", "Authentication" );
define( "_MD_AM_AUTHENTICATION_DSC", "Authentication desc" );

define ('_UPDATE_SELECTED','Update Selected');



?>
