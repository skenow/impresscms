<?php
include '../mainfile.php';
include './include/makedata.php';
include_once './class/dbmanager.php';

$installer->setArgs( 'title', _INSTALL_L190 );
$installer->setArgs( 'subtitle', _INSTALL_L191 );

$language = $_SESSION[$zariliaOption['InstallPrefix']]['language'];
if ( !isset( $_SESSION[$zariliaOption['InstallPrefix']]['installcomplete'] ) ) {
    $_SESSION[$zariliaOption['InstallPrefix']]['installcomplete'] = 1;
    $users_array = $_SESSION[$zariliaOption['InstallPrefix']]['admin'];
    $login = $users_array['adminlogin'];
    $uname = $users_array['adminuname'];
    $pass = md5( $users_array['adminpass'] );
    $email = $users_array['adminmail'];

    $database_manager = new db_manager;

    /* Start of install procedure */
    $database_manager->queryFromFile( ZAR_ROOT_PATH . '/install/sql/' . ZAR_DB_TYPE . '.data.sql' );
    make_data( $database_manager, $login, $uname, $pass, $email, $language );
    $installer->setArgs( 'db_mysql_heading', _INSTALL_L203 );
    $installer->addVar( 'db_mysql_info', $database_manager->report() );

    /*install system addons */
    $dbm_addoninstall = new db_manager;
    $addon_content = _INSTALL_L200;
    installAddon( $database_manager, 1, 'system', $language );
    $addon_content .= _INSTALL_L201;

    /*install selected addons */
    if ( isset( $_POST['addons'] ) ) {
        $mid = 2;
        foreach ( $_POST['addons'] as $name ) {
            if ( $name == 'system' ) continue;
            installAddon( $dbm_addoninstall, $mid, $name, $language );
            $addon_content .= sprintf( _INSTALL_L202 , ucfirst( $name ) );
            $mid++;
        }
    }
    $installer->setArgs( 'db_addon_heading', $addon_content );
    $installer->addVar( 'db_addon_info', $dbm_addoninstall->report() );
    $installer->render( 'install.install_it.tpl.php' );
} else {
    $installer->setArgs( 'content', _INSTALL_L192 );
    $installer->render();
}

?>