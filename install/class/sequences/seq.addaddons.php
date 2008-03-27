<?php
include '../mainfile.php';
require_once ZAR_ROOT_PATH . '/kernel/object.php';
require_once ZAR_ROOT_PATH . '/kernel/addon.php';
require_once ZAR_ROOT_PATH . '/addons/system/constants.php';
require_once ZAR_ROOT_PATH . '/include/functions.php';

include_once './class/dbmanager.php';
$database_manager = new db_manager();
$zariliaDB = &$database_manager->db;

/*set title and subtitle*/
$installer->setArgs( 'title', _INSTALL_L180 );
$installer->setArgs( 'subtitle', _INSTALL_L180a );

$addons = getDirList( ZAR_ROOT_PATH . '/addons/' );
foreach ( $addons as $name ) {
    $addon = new ZariliaAddon();
    $addon->loadInfoAsVar( $name );
    if ( $addon->getInfo( 'version' ) ) {
		$installer->addVars( 'dirname', $addon->getVar( 'dirname' ) );
		$installer->addVars( 'name', $addon->getVar( 'name' ) );
        $installer->addVars( 'version', $addon->getVar( 'version' ) );
        $installer->addVars( 'disabled', $addon->getVar( 'name' ) == "System" ? 'disabled="disabled" checked="checked"' : '' );
    }
}
if ( !isset( $_SESSION[$zariliaOption['InstallPrefix']]['sitelanguage'] ) || isset( $_POST['submit'] ) ) {
    unset( $_POST['submit'], $_POST['op'] );
    $_SESSION[$zariliaOption['InstallPrefix']]['sitelanguage'] = $_POST;
}
$installer->render( 'install.addaddons.tpl.php' );

?>