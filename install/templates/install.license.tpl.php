<?php

$title = _INSTALL_L80;
$subtitle = _INSTALL_L80s;
/* content */

$content = '';

if ( file_exists( './language/' . $_SESSION[$zariliaOption['InstallPrefix']]['language'] . '/welcome.php' ) ) {
    include './language/' . $_SESSION[$zariliaOption['InstallPrefix']]['language'] . '/welcome.php';
}

$content .= "<u><b>" . _INSTALL_L82 . "</b></u>";
//$content .= "<p><table width='100%' align='center' border=1><tr><td align='left'>\n";
$content .= '<div class="license">'.str_replace('gpl.html','',file_get_contents('gpl.html')).'</div>';
//$content .= "<iframe src='gpl.html' class='license' frameborder='0' scrolling='auto'></iframe>";
//$content .= "</td></tr></table></p>\n";

?>