<?php
include_once '../mainfile.php';
include_once './class/dbmanager.php';
$database_manager = new db_manager;

$tables = array();
$result = $database_manager->queryFromFile( ZAR_ROOT_PATH . '/install/sql/' . ZAR_DB_TYPE . '.structure.sql' );
$installer->addVar( 'reports', $database_manager->report() );
if ( !$result )
{
	$installer->setArgs( 'content', _INSTALL_L114 );
    $installer->setArgs( 'backstep', array( 'start', _INSTALL_B_DBFORM ) );
    $installer->setArgs( 'shownext', ' ' );
}
else
{
    $installer->setArgs( 'message', _INSTALL_L115 );
}

$installer->setArgs( 'title', 'Create Tables FIX ME' );
$installer->setArgs( 'subtitle', 'Installer will attempt to create Selected Tables FIX ME!' );
$installer->render( 'install.createtable.tpl.php' );

?>