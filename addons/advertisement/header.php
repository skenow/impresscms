<?php 
// $Id: header.php,v 1.1 2007/03/16 02:34:23 catzwolf Exp $
include "../../mainfile.php" ;
require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
require_once ZAR_ROOT_PATH . "/addons/advertisement/include/functions.php";

$client_handler = &zarilia_getaddonhandler( 'clients', 'advertisement', false );
$banner_handler = &zarilia_getaddonhandler( 'banners', 'advertisement', false );
//$banneradds_handler = &zarilia_getaddonhandler( 'banneradds', 'advertisement', false );
$_PHP_SELF = zarilia_getenv( 'PHP_SELF' );
?>
