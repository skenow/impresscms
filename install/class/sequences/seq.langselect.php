<?php
include ( '../include/version.php' );
$installer->setArgs( 'title', sprintf( _INSTALL_L0, ZARILIA_NAME, ZARILIA_VERSION ) );
$installer->setArgs( 'subtitle', _INSTALL_L128 );
$installer->setArgs( 'dontshow', true );

/**
 */
$langarr = getDirList( './language/' );
foreach ( $langarr as $lang ) {
    $installer->addVars( 'languages', $lang );
    if ( strtolower( $lang ) == $_SESSION[$zariliaOption['InstallPrefix']]['language'] ) {
        $installer->addVars( 'selected', 'selected="selected"' );
    } else {
        $installer->addVars( 'selected', '' );
    }
}
$installer->render( 'install.langselect.tpl.php' );

?>