<?php

if( ! defined( 'ICMS_TRUST_PATH' ) ) die( 'set ICMS_TRUST_PATH into mainfile.php' ) ;

$mydirname = basename( dirname( dirname( __FILE__ ) ) ) ;
$mydirpath = dirname( dirname( __FILE__ ) ) ;
require $mydirpath.'/mytrustdirname.php' ; // set $mytrustdirname

require ICMS_TRUST_PATH.'/modules/'.$mytrustdirname.'/admin_menu.php' ;

?>