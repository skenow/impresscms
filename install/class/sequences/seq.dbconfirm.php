<?php
if ( !$_POST )
{
	include '../mainfile.php';
}

include_once 'class/settingmanager.php';
$setting_manager = new setting_manager( true );
$setting_manager->readConstant();
$content = $setting_manager->checkData();
if ( !empty( $content ) )
{
    $installer->setArgs( 'title', _INSTALL_L93 );
    $installer->setArgs( 'subtitle', _INSTALL_L90a );
    $installer->setArgs( 'content', $content . $setting_manager->editform() );
}
else
{
    $installer->setArgs( 'title', _INSTALL_L90 );
    $installer->setArgs( 'subtitle', _INSTALL_L90b );
    $installer->setArgs( 'content', $setting_manager->confirmForm() );
}
$installer->render( $content );

?>