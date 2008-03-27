<?php
// $Id: users.php,v 1.1 2007/03/16 02:38:03 catzwolf Exp $
// %%%%%%	Admin Addons Name  Users 	%%%%%
define( "_MA_AD_UID", "#" );
define( "_MA_AD_UNAME", "Username" );
define( "_MA_AD_RANK", "Rank" );
define( "_MA_AD_USER_REGDATE", "Registered" );
define( "_MA_AD_LAST_LOGIN", "Last Visit" );
define( "_MA_AD_IPADDRESS", "IP Address" );
define( "_MA_AD_STATUS", "Status" );
define( "_MA_AD_ACTIVE", "Active" );
define( "_MA_AD_SUSPENDED", "Suspended" );
define( "_MA_AD_UNOTACTIVE", "Inactive" );
define( "_MA_AD_LOGIN", "Login:" );
define( "_MA_AD_PASSWORD", "Password:" );
define( "_MA_AD_NICKNAME", "User Name (Display Name):" );
define( "_MA_AD_NAME", "Real Name:" );
define( "_MA_AD_EMAIL", "Email Adddress:" );
define( "_MA_AD_GROUPS", "Select User Groups:" );
define( "_MA_AD_RANKS", "User Rank:" );
define( "_MA_AD_BIRTHDATE", "Birthdate:" );
define( "_MA_AD_USERLEVEL", "User Level:" );
define( '_MA_AD_TIMEZONE', 'Time Zone:');
define( '_MA_AD_LOGINDETAILS', 'login Details' );
define( '_MA_AD_LANGUAGECHOICE', 'Language Choice:' );
define( '_MA_AD_THEME', 'Theme Choice:' );
define( '_MA_AD_EDITOR', 'Select Editor:' );
define( "_MA_AD_IAMOVER", "I certify that the above date entered is my true age." );
define( "_MA_AD_AOUTVTEAD", "Allow other users to view this email address" );
define( "_MA_AD_CREATEPASSWORD", "Generate Password:" );
define( '_MA_AD_MAILOK', 'Email Notifications:' );
define( '_MA_AD_MAILOK_DSC', 'Select to receive occasional email notices from administrators and moderators.' );
define( '_MA_AD_CDISPLAYMODE', 'Comments Display Mode:' );
define( '_MA_AD_CSORTORDER', 'Comments Sort Order:' );
define( "_AM_AYSYWTDU", "Are you sure you want to delete user %s?" );
define( "_AM_BYTHIS", "By doing this all the info for this user will be removed permanently." );
define( "_AM_YMCACF", "You must complete all required fields" );
define( "_AM_CNRNU", "Could not register new user." );
define( "_AM_EDEUSER", "Edit/Delete Users" );
define( "_AM_NICKNAME", "Nickname" );
define( "_AM_AGEVER_IPADDRESS", "IP Address" );
define( "_AM_REGISTER", "Registered Date" );
define( "_AM_LASTLOGIN", "Last Login" );
define( "_AM_ADDUSER", "Create User" );
define( "_AM_NAME", "Name" );
define( "_AM_EMAIL", "Email" );
define( "_AM_OPTION", "Option" );
define( "_AM_AVATAR", "Avatar" );
define( "_AM_NSRA", "No Special Rank Assigned" );
define( "_AM_NSRID", "No Special Ranks in Database" );
define( "_AM_ACCESSLEV", "Access Level" );
define( "_AM_SIGNATURE", "Signature" );
define( "_AM_PASSWORD", "Password" );
define( "_AM_INDICATECOF", "* indicates required fields" );
define( "_AM_NOTACTIVE", "This user has not been activated. Do you wish to activate this user?" );
define( "_AM_UPDATEUSER", "Update User" );
define( "_AM_USERINFO", "User Info" );
//define( "_AM_USERID", "User ID" );
define( "_AM_RETYPEPD", "Retype Password" );
define( "_AM_CHANGEONLY", "(for changes only)" );
define( "_AM_USERPOST", "User Posts" );
define( "_AM_STORIES", "Stories" );
define( "_AM_COMMENTS", "Comments" );
define( "_AM_PTBBTSDIYT", "Push the button below to synchronize data if you think the above user posts info does not seem to indicate the actual status" );
define( "_AM_SYNCHRONIZE", "Synchronize" );
define( "_AM_USERDONEXIT", "User doesn't exist!" );
define( "_AM_STNPDNM", "The Passwords you have entered do not match." );
define( "_AM_YMEBPWS", "You must enter both password fields." );
define( "_AM_CNGTCOM", "Could not get total comments" );
define( "_AM_CNGTST", "Could not get total stories" );
define( "_AM_CNUUSER", "Could not update user" );
define( "_AM_CNGUSERID", "Could not get user IDS" );
define( "_AM_LIST", "List" );
define( "_AM_NOUSERS", "No users selected" );
define( "_AM_DELALLUSERS", "Delete All selected Users?" );
/*
* New defines here
*/
define( "_AM_SENDEMAIL", "Send email to this user" );
define( "_AM_SENDPM", "Send Private Message to this user" );
define( "_AM_NEVERLOGGEDIN", "Never Logged In" );
define( "_AM_PERSONAL", "Personal" );
define( "_AM_NOTIFICATIONS", "Notifications" );
define( "_AM_PROFILE", "Profile" );
define( "_AM_SELECTMATCHTYPE", "Select Match Type" );
define( "_AM_MENUALLUSERS", "All Users" );
define( "_AM_MENUACTIVEUSERS", "Active Users" );
define( "_AM_MENUSUSUSERS", "Suspended Users" );
define( "_AM_MENUNEWUSERS", "New Users" );
define( "_AM_MENUSELECT", "Select: " );
define( "_AM_ALL", "All" );
define( "_AM_USER_SEARCH", "Search" );
define( "_AM_USER_SEARCHDEFAULT", "Default" );
define( "_AM_USER_SEARCHCUSTOM", "Custom" );
define( "_AM_USER_SEARCHTYPE", "Type" );
define( "_AM_USER_SEARCHGROUP", "Group" );
define( "_AM_USER_SEARCHLETTER", "Letter" );
/*
* User Error Output Defines
*/
$amin_url = ZAR_URL."/addons/system/index.php?fct=groups&amp;op=users&g_id=1";
define( "_AM_US_ERROR", "<strong>%s</strong> is in the webmaster group and cannot be deleted from the system. Please remove %s from the <a href=\"$amin_url\"><strong>webmaster</strong></a> group and try again." );
define( "_AM_US_USEREXISTS", "<strong>%s</strong> is in the webmaster group and cannot be deleted from the system. Please remove %s from the <a href=\"$amin_url\"><strong>webmaster</strong></a> group and try again." );
define( "_AM_US_NOTDELIDNOTFOUND", "Could not find the user you selected to delete." );
define( "_AM_US_NOTDELUSER", "The system could not delete %s from the database." );
define( "_AM_US_USERDELETED", "%s has been deleted from the database." );
define( "_AM_US_LOGINEXISTS", "Could not create a new user with the desired <strong>login</strong> name, this login name has already been taken by someone else. <br /><br />Please go back and choose another login name for this user." );
define( "_AM_US_UNAMEEXISTS", "Could not create a new user with the desired <strong>Username</strong>, this Username name has already been taken by someone else. <br /><br />Please go back and choose another login name for this user." );
define( "_AM_US_CANNOTBESAME", "Could not create new user, as both login name and user name are identical. For security reasons these fields cannot be the same." );

//%%%%%%		File Name formgenpassword.php 		%%%%%
define('_US_PG_TYPE','Choose Type');
define('_US_PG_UPPERCASE','Uppercase letters and numbers');
define('_US_PG_LOWERCASE','Lowercase letters and numbers');
define('_US_PG_MIXEDCASE','Mixed case letters and numbers');
define('_US_PG_CHARACTERS','%s Characters');
define('_US_PG_LENGTH','Choose length');

define( '_MA_INFO_NAME', 'User Administration' );
define( '_MA_INFO_DESCRIPTION', '' );
define( '_MA_INFO_AUTHOR', 'Zarilia Project' );
define( '_MA_INFO_LICENSE', 'GPL see LICENSE' );
define( '_MA_INFO_IMAGE', 'user_admin.png' );

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
define( '_MA_INFO_PATH', 'index.php?fct=users' );
define( '_MA_INFO_OFFICIAL', 1 );
define( '_MA_INFO_SYSTEM', 1 );
define( '_MA_INFO_HASADMIN', 1 );
?>