<?php
include_once '../mainfile.php';

include_once './class/dbmanager.php';
$database_manager = new db_manager;

$installer->setArgs( 'title', _INSTALL_L90 );
$installer->setArgs( 'subtitle', _INSTALL_L90c );

$result = $database_manager->createDB();
if ( $result == '1007' || $result == '1' ) {
    $installer->setArgs( 'content', sprintf( _INSTALL_L45, ZAR_DB_NAME ) );
    $installer->setArgs( 'backstep', array( 'dbform', _INSTALL_B_DBFORM ) );
    $installer->setArgs( 'nextstep', array( 'createtables', _INSTALL_L90h ) );
} else {
    $content = $result;
	$installer->setArgs( 'content', sprintf( _INSTALL_L45a, ZAR_DB_NAME ) );
    $installer->setArgs( 'backstep', array( 'dbform', _INSTALL_B_DBFORM ) );
    $installer->setArgs( 'nextstep', ' ' );
}
$installer->render();

?>