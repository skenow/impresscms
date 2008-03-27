<?php
$installer->setArgs( 'title', _INSTALL_L80 );
$installer->setArgs( 'subtitle', _INSTALL_L80s );

$content = '';
if ( file_exists( './language/english/welcome.php' ) )
{
    include './language/english/welcome.php';
}
$content .= "<u><b>" . _INSTALL_L82 . "</b></u>";
// $content .= "<p><table width='100%' align='center' border=1><tr><td align='left'>\n";
$text = str_replace( 'gpl.html', '', file_get_contents( 'gpl.html' ) );
$content .= '<div class="license">' . $text . '</div>';
// $content .= "<iframe src='gpl.html' class='license' frameborder='0' scrolling='auto'></iframe>";
// $content .= "</td></tr></table></p>\n";
$installer->setArgs( 'content', $content );

$installer->render();

?>