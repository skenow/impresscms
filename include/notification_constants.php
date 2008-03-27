<?php
// $Id: notification_constants.php,v 1.1 2007/03/16 02:39:06 catzwolf Exp $
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

// RMV-NOTIFY
define('ZAR_NOTIFICATION_MODE_SENDALWAYS', 0);
define('ZAR_NOTIFICATION_MODE_SENDONCETHENDELETE', 1);
define('ZAR_NOTIFICATION_MODE_SENDONCETHENWAIT', 2);
define('ZAR_NOTIFICATION_MODE_WAITFORLOGIN', 3);

define('ZAR_NOTIFICATION_METHOD_DISABLE', 0);
define('ZAR_NOTIFICATION_METHOD_PM', 1);
define('ZAR_NOTIFICATION_METHOD_EMAIL', 2);

define('ZAR_NOTIFICATION_DISABLE', 0);
define('ZAR_NOTIFICATION_ENABLEBLOCK', 1);
define('ZAR_NOTIFICATION_ENABLEINLINE', 2);
define('ZAR_NOTIFICATION_ENABLEBOTH', 3);

?>
