<?php 
// $Id: header.php,v 1.1 2007/03/16 02:35:03 catzwolf Exp $
include "../../mainfile.php" ;
require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
require_once ZAR_ROOT_PATH . "/addons/messages/include/functions.php";

$pm_handler = &zarilia_getaddonhandler( 'message', 'messages', false );
$buddy_handler = &zarilia_getaddonhandler( 'buddy', 'messages', false );
$msgsent_handler = &zarilia_getaddonhandler( 'messagesent', 'messages', false );
?>
