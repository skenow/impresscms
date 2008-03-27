<?php
// $Id: comment_post.php,v 1.1 2007/03/16 02:36:17 catzwolf Exp $
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
include './admin_header.php';

zarilia_admin_menu( '', _MD_AM_COMMMAN, $op );
include ZAR_ROOT_PATH.'/include/comment_post.php';
?>