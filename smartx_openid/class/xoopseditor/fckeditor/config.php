<?php

// include_once('../../../../../../../../../../mainfile.php');

$Config['Enabled'] = true ;

//// Only admin users can use it.
//if (is_object($GLOBALS['xoopsUser']) && $GLOBALS['xoopsUser']->isAdmin()) { 
//	$Config['Enabled'] = true ;
//}


// TODO - Will non-admin user have access?
// TODO - Will user have their private upload folder?

// Path to uploaded files relative to the document root.
$Config['UserFilesPath'] = '/uploads/' ;

?>