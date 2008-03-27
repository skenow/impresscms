<?php
include ( '../include/version.php' );
$installer->setArgs( 'title', _INSTALL_L81 );
$installer->setArgs( 'subtitle', sprintf( _INSTALL_L81a, ZARILIA_VERSION, ZARILIA_RELEASED ) );

$link = 'http://www.zarilia.com';
$status = 'status=yes,toolbar=yes,scrollbars=yes,titlebar=yes,menubar=yes,resizable=yes,directories=yes,location=yes';
$release = strtotime( ZARILIA_RELEASED );
$now = strtotime( 'now' );
$age = ( $now - $release ) / 86400;
$age = intval( round( $age ) );
if ( $age > 1 || $age < 1 ) {
    $daytext = 'days';
} else {
    $daytext = 'day';
}
$version = '
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
			<tr>
			<td colspan="2" style="text-align: center;">
			<h3 style="font-size: 12px;">
			<p style="font-weight: normal; padding: 5px; margin: 0px; font-size: 12px;">
			This version of ' . ZARILIA_NAME . ' ' . ZARILIA_VERSION . ' is</p>
			<div style="font-size: 12px; color: #191970;">' . $age . ' ' . $daytext . ' old</div>';
$version .= '<div style="margin-top: 10px;"><input name="button" class="mainbutton" type="submit" value="Check for newer version" onclick="window.open( \'' . $link . '\' , \'win2\', \'' . $status . '\'); return false;" /></div>
			   </h3>
			  </td>
			 </tr>
		    </table>';

$content = "
		 <h1>" . _INSTALL_L181 . "</h1>
		 <table width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">
		  <tr>
		    <td class='install-text'> " . _INSTALL_L181a . "</td>
		    <td class='form-block'>" . $version . "</td>
		  </tr>
		 </table>";

include_once 'class/class.check.php';
$chk = new CheckIfDependsOn();
$info = $chk->check_version( PHP_VERSION, '4.3.3', 'PHP' );
$info .= $chk->check_extention( 'gd' );
$info .= $chk->check_function( 'mysql_connect' );
$info .= $chk->check_extention( 'xml' );
$info .= $chk->check_extention( 'pcre' );
$info .= $chk->check_extention( 'mbstring', 'Only is used with multibyte charsets' );
$info .= $chk->check_extention( 'zlib', 'Only used for zlib compression' );

$content .= "
		 <h1>" . _INSTALL_L178 . "</h1>
		 <table width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">
		  <tr>
		    <td class='install-text'> " . _INSTALL_L178a . "</td>
		    <td class='form-block'><table>" . $info . "</table></td>
		  </tr>
		 </table>";

$php_recommended_settings = array( array ( 'Safe Mode', 'safe_mode', 0 ),
    array ( 'Display Errors', 'display_errors', 1 ),
    array ( 'File Uploads', 'file_uploads', 1 ),
    array ( 'Magic Quotes GPC', 'magic_quotes_gpc', 0 ),
    array ( 'Magic Quotes Runtime', 'magic_quotes_runtime', 0 ),
    array ( 'Register Globals', 'register_globals', 0 ),
    array ( 'Output Buffering', 'output_buffering', 0 ),
    array ( 'Session auto start', 'session.auto_start', 0 ),
    array ( 'Allow url fopen', 'allow_url_fopen', 0 ),

    );

$recomended = '';
foreach( $php_recommended_settings as $_recomend ) {
    $recomended .= $chk->check_ini( $_recomend[0], $_recomend[1], $_recomend[2] );
}

$content .= "
		 <h1>" . _INSTALL_L183 . "</h1>
		 <table width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">
		  <tr>
		    <td class='install-text'> " . _INSTALL_L183a . "</td>
		    <td class='form-block'><table>" . $recomended . "</table></td>
		  </tr>
		 </table>";

$writeok = getWithSubDirsDirList( '../data/' );
$error = false;
$permissions = "";
foreach ( $writeok as $wok ) {
    if ( !is_dir( "../" . $wok ) ) {
        if ( file_exists( "../" . $wok ) ) {
            @chmod( "../" . $wok, 0666 );
            $permissions .= $chk->check_iswritable( "../" . $wok );
        }
    } else {
        @chmod( "../" . $wok, 0777 );
        $permissions .= $chk->check_iswritable( "../" . $wok );
    }
}

$content .= "
		 <h1>" . _INSTALL_L182 . "</h1>
		 <table width=\"100%\" cellspacing=\"1\" cellpadding=\"2\" border=\"1\">
		  <tr>
		    <td class='install-text'> " . _INSTALL_L182a . "</td>
		    <td class='form-block'><table>" . $permissions . "</table></td>
		  </tr>
		 </table>";

$content .= "<p>" . _INSTALL_L87 . "</p>";
$installer->setArgs( 'content', $content );
$installer->render();

?>