<?php
// $installer->assign( 'reports', $mm->report() );
// $installer->assign( 'message', _INSTALL_L62 );
$installer->setArgs( 'title', _INSTALL_L90 );
$installer->setArgs( 'subtitle', _INSTALL_L90c );

$installer->addVars( 'databasecheck', _OKIMG . ' ' . _INSTALL_L90d );
// do database stuff here*/
include_once '../mainfile.php';
include_once './class/dbmanager.php';
$database_manager = new db_manager();
if ( ! $database_manager->isConnectable() )
{
    $installer->addVars( 'databasecheck', _NGIMG . _INSTALL_L106 );
    $installer->addVars( 'msgs', _INSTALL_L106a );
    $installer->setArgs( 'backstep', array( 'dbform', _INSTALL_B_DBFORM ) );
    $installer->setArgs( 'nextstep', ' ' );
}
else
{
    $installer->addVars( 'databasecheck', _OKIMG . _INSTALL_L108 );
    if ( ! $database_manager->dbExists() )
    {
        unset( $_SESSION[$zariliaOption['InstallPrefix']]['installcomplete'] );
		$installer->addVars( 'databasecheck', _NGIMG . sprintf( _INSTALL_L109, ZAR_DB_NAME ) );
        $installer->addVars( 'msgs', _INSTALL_L21 . ': <b>' . ZAR_DB_NAME . '</b>' );
        $installer->addVars( 'msgs', _INSTALL_L22 );
        $installer->setArgs( 'nextstep', array( 'dbcreate', _INSTALL_L40 ) );
    }
    else
    {
        $installer->addVars( 'databasecheck', _OKIMG . sprintf( _INSTALL_L110, ZAR_DB_NAME ) );
        if ( !$database_manager->tableExists( 'users' ) )
        {
            $installer->addVars( 'msgs', _INSTALL_L111 );
            $installer->setArgs( 'nextstep', array( 'createTables', _INSTALL_B_CREATETABLES ) );

		}
        elseif ( !$database_manager->UserExists() ) {
			$installer->addVars( 'msgs', '' );
            $installer->addVars( 'checks', _NGIMG . _INSTALL_L131 );
            $installer->setArgs( 'nextstep', array( 'siteInit', _INSTALL_L91  ) );
        } else
        {

			$installer->addVars( 'msgs', _INSTALL_L131 );
            $installer->addVars( 'checks', _NGIMG . _INSTALL_L131 );
            $installer->setArgs( 'nextstep', '' );
        }
    }
}
$installer->render( 'install.dbsave.tpl.php' );

?>