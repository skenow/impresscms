<?php
include_once './class/class_siteinfo.php';
$site = new siteinfo_manager();
$site->createNew( $_POST );
if ( !$site->saveSiteinfo() )
{
    /* Error: Message Could not save file to disk */
	$installer->setArgs( 'title', _INSTALL_L90 );
    $installer->setArgs( 'subtitle', _INSTALL_L90e );
    // error message here
    $installer->setArgs( 'content', _INSTALL_L90g );
    $installer->error();
    exit();
}
else
{
    $content = _INSTALL_L90d;
}

$installer->setArgs( 'title', _INSTALL_L90 );
$installer->setArgs( 'subtitle', _INSTALL_L90a );
$installer->setArgs( 'content', $content );
$installer->render();
?>