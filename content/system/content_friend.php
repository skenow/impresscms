<?php
// $Id: content_friend.php,v 1.1 2007/03/31 04:03:27 catzwolf Exp $
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
// no direct access
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

echo $_SERVER['HTTP_REFERER'];

if ( $is_send ) {
    // lets email this page
} else {
    $zariliaOption['template_main'] = 'system_friend.html';
    // lets setup the email form
}

?>