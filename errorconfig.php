<?php

/**
 *
 * @version $Id: errorconfig.php,v 1.1 2007/04/21 09:40:28 catzwolf Exp $
 * @copyright 2006
 */

$admin_email = 'webmaster@usersite.com';
$admin_sitename = 'Zarilia CMS';
$filename = 'error_log.txt';
$dobacktrace = 0;

/*Select the debugging output options*/
$error_tofile = false; 	//Save debugging information to file
$error_tomail = false;	//Send debugging information to choosen email address
$error_stdsql = true;	//Save debugging information to database

/*path inside the Zarilia path scope. No starting/trailing slashes*/
$log_path = 'logs';
/*these are the php error numbers, select an error type that you wish to be notified of*/
$logtypes = array( E_ERROR, E_WARNING, E_PARSE, E_NOTICE, E_USER_ERROR, E_USER_WARNING, E_USER_NOTICE, E_RECOVERABLE_ERROR, E_STRICT );
$developerdebug = false;

?>