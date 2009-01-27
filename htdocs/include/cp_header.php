<?php 
// $Id: cp_header.php 506 2006-05-26 23:10:37Z skalpa $
/**
 * module files can include this file for admin authorization
 * the file that will include this file must be located under xoops_url/modules/module_directory_name/admin_directory_name/
 */
//error_reporting(0);
include_once '../../../mainfile.php';
include_once XOOPS_ROOT_PATH."/include/cp_functions.php";

global $xoopsConfig;
$config_handler = xoops_gethandler('config');
$moduleperm_handler = & xoops_gethandler( 'groupperm' );

if($xoopsUser)
{
	if($xoopsConfig['admin_use_mysession'] && $xoopsConfig['admin_session_name'] != '')
	{
		$admin_sess_name = $xoopsConfig['admin_session_name'];
	}
	else {$admin_sess_name = 'ICMSADSESSION';}
	if(!isset($_COOKIE[$admin_sess_name]) || !isset($_SESSION['icms_admin_fprint'])) {redirect_header(ICMS_URL.'/admin.php',3,_ADNOTLOGIN);}
	else
	{
		$adsess_handler =& xoops_gethandler('adminsession');
	
		$myts =& MyTextSanitizer::getInstance();
		$cookie_fprint = $myts->stripSlashesGPC($_COOKIE[$admin_sess_name]);
		if($cookie_fprint !== $_SESSION['icms_admin_fprint'] || !$adsess_handler->icms_sessionCheck($xoopsUser->getVar('pass')))
		{
			setcookie($_COOKIE[$admin_sess_name], '', time()-3600);
			unset($_COOKIE[$admin_sess_name], $_SESSION['xoopsAdminId'], $_SESSION['icms_admin_fprint']);
			redirect_header(ICMS_URL.'/',3,_UNAUTHADMINACCESS);
		}
	}
    $url_arr = explode('/',strstr($xoopsRequestUri,'/modules/'));
    $module_handler =& xoops_gethandler('module');
    $xoopsModule =& $module_handler->getByDirname($url_arr[2]);
    unset($url_arr);
    
    if ( !$moduleperm_handler->checkRight( 'module_admin', $xoopsModule->getVar( 'mid' ), $xoopsUser->getGroups() ) ) {
        redirect_header( XOOPS_URL . "/user.php", 1, _NOPERM );
    } 
} else {
    redirect_header( XOOPS_URL . "/user.php", 1, _NOPERM );
}

// set config values for this module
if ( $xoopsModule->getVar( 'hasconfig' ) == 1 || $xoopsModule->getVar( 'hascomments' ) == 1 ) {
    $config_handler = & xoops_gethandler( 'config' );
    $xoopsModuleConfig = & $config_handler->getConfigsByCat( 0, $xoopsModule->getVar( 'mid' ) );
}

// include the default language file for the admin interface
if ( file_exists( "../language/" . $xoopsConfig['language'] . "/admin.php" ) ) {
    include "../language/" . $xoopsConfig['language'] . "/admin.php";
}
elseif ( file_exists( "../language/english/admin.php" ) ) {
    include "../language/english/admin.php";
}
?>