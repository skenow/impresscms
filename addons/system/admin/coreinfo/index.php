<?php
// $Id: index.php,v 1.2 2007/04/21 09:42:10 catzwolf Exp $
if ( !isset( $GLOBALS['zariliaUser'] ) ) getZariliaLogo();
if ( !is_object( $GLOBALS['zariliaUser'] ) || !is_object( $GLOBALS['zariliaAddon'] ) || !$GLOBALS['zariliaUser']->isAdmin( $GLOBALS['zariliaAddon']->getVar( 'mid' ) ) ) {
	return ZariliaErrorHandler_HtmlError( $title = 'Error', $heading = 'Access denied', $description = 'Maybe you don`t have permisions.', $image = '', $show_button = false );
}

if ( isset( $zariliaOption['dyncontent'] ) ) {
	switch ( getQueryVar( 'op', 0 ) ) {
        case 'php':
            $_info = array( 0 => INFO_GENERAL, 1 => INFO_CONFIGURATION, 2 => INFO_MODULES, 3 => INFO_ENVIRONMENT, 4 => INFO_VARIABLES, 5 => INFO_LICENSE );
            $content = '<div>' . php_info( $_info[getQueryVar( 'opt' )] ) . '<div>';
            break;
        case 'zarilia':
        default:
            $content = '<table width="100%" cellspacing="1" cellpadding="2" class="outer" >';
            switch ( getQueryVar( 'opt' ) ) {
                case 2: // license
					include_once ZAR_ROOT_PATH . '/class/class.textsanitizer.php';
//                    echo $zariliaConfig["language"];
					$mts = new MyTextSanitizer();
                    $file = ZAR_ROOT_PATH . '/language/' . $GLOBALS['zariliaConfig']['language'] . '/license.txt';
                    $content .= '<tr>';
                    $content .= '<td width="80%">' . ( ( !is_readable( $file ) )?'License file in selected language doesn\'t exists!':$mts->displayTarea( file_get_contents( $file ) ) ) . '</td>';
                    $content .= '</tr>';
                    break;
                case 1: // variable
                    $content .= '<tr>';
                    $content .= '<th>Variable</th><th>Value</th>';
                    $content .= '</tr>';
                    foreach ( array ( 'zariliaOption', 'zariliaAjax' ) as $var ) {
                        if ( isset( ${$var} ) )
                            foreach ( ${$var} as $k => $v ) {
                            $content .= '<tr>';
                            $rez = $v;
                            if ( is_array( $rez ) )
                                $rez = nl2br( str_replace( "  ", "&nbsp; ", var_export( $rez, true ) ) );
                            $content .= '<td class="head" valign="top" align="left">' . $var . '["' . $k . '"]' . '</td><td class="even" valign="top" align="left">' . $rez . '</td>';
                            $content .= '</tr>';
                        }
                    }
                    break;
                case 0: // general
                default:
                    include_once ZAR_ROOT_PATH . '/include/version.php';
                    $content .= '<tr>';
                    $content .= '<th colspan="2"><img src="' . ZAR_URL . str_replace( '\\', '/', substr( __FILE__, strlen( ZAR_ROOT_PATH ) ) ) . '"></th>';
                    $content .= '</tr>';
                    $content .= '<tr>';
                    $content .= '<td class="head" valign="top" align="left" width="50%">Version</td><td class="even" valign="top" align="left">' . ZARILIA_VERSION . '</td>';
                    $content .= '</tr>';
                    $content .= '<tr>';
                    $content .= '<td class="head" valign="top" align="left" width="50%">Homepage</td><td class="even" valign="top" align="left"><a href="http://www.zarilia.com" target="_blank">http://www.zarilia.com</a></td>';
                    $content .= '</tr>';
                    $content .= '<tr>';
                    $content .= '<td class="head" valign="top" align="left" width="50%">CVS</td><td class="even" valign="top" align="left"><a href="http://zarilia.cvs.sourceforge.net/zarilia/" target="_blank">http://zarilia.cvs.sourceforge.net/zarilia/</a></td>';
                    $content .= '</tr>';
            }
            $content .= "</table>";
            break;
    }
    return $content;
} else {
	require_once "admin_menu.php";
    require_once ZAR_ROOT_PATH . '/class/controls/dyntabs/control.class.php';
    zarilia_cp_header();
    $group_list = array( 'zarilia' => 'Zarilia Info', 'php' => 'Php Info' );
    $op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'php', XOBJ_DTYPE_TXTBOX );
    $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
    $tabs = array();
    $url = '%sa_dir%' . $fct . '/index.php?op=' . $op;
    zarilia_admin_menu( 'Actions', array( "index.php?fct=coreinfo&amp;op=php" => _MD_AM_PHPINFOHEADING, "index.php?fct=coreinfo&amp;op=zarilia" => _MD_AM_ZARILIAINFOHEADING ) );
    switch ( $op ) {
        /*Edit Avatar*/
        // case 'help':
        // $menu_handler->render( 0 );
        // if ( file_exists( ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php" ) ) {
        //  @include ZAR_ROOT_PATH . "/addons/system/admin/" . $fct . "/admin_help.php";
        // }
        // break;
        // /*Edit Avatar*/
        // case 'about':
        // $menu_handler->render( 4 );
        // require_once( ZAR_ROOT_PATH . "/class/class.about.php" );
        // $zarilia_about = new ZariliaAbout();
        // $zarilia_about->display();
        // break;
        case 'php':
	        $menu_handler->render( 0 );
            $_info = array( 0 => INFO_GENERAL, 1 => INFO_CONFIGURATION, 2 => INFO_MODULES, 3 => INFO_ENVIRONMENT, 4 => INFO_VARIABLES, 5 => INFO_LICENSE );
            $_text = array( 0 => _MD_CINFO_GENERAL, 1 => _MD_CINFO_CONFIGURATION, 2 => _MD_CINFO_ADDONS, 3 => _MD_CINFO_ENVIRONMENT, 4 => _MD_CINFO_VARIABLES, 5 => _MD_CINFO_LICENSE );
            foreach ( $_info as $k => $v ) {
                $tabs[ ucfirst( strtolower( $_text[$k] ) ) ] = $url . '&amp;opt=' . $k;
            }
            break;

        case 'zarilia':
        default:
	        $menu_handler->render( 1 );
            $_text = array( 0 => _MD_CINFO_GENERAL, 1 => _MD_CINFO_VARIABLES, 2 => _MD_CINFO_LICENSE );
            foreach ( $_text as $k => $v ) {
                $tabs[ ucfirst( strtolower( $_text[$k] ) ) ] = $url . '&amp;opt=' . $k;
            }
            break;
    }
    $dt = new ZariliaControl_DynTabs( $tabs );
    echo $dt->render();
    zarilia_cp_footer();
}
$tabbar = new ZariliaTabMenu( $opt );
exit();

function php_info( $type = INFO_GENERAL )
{
    ob_start();
    phpinfo( $type );
    $php_info = ob_get_contents();
    ob_end_clean();
    $php_info = str_replace( "</body></html>", "", $php_info );
    $php_info = str_replace( 'border="0" cellpadding="3" width="600"', 'width="100%" cellspacing="1" cellpadding="2" class="outer"', $php_info );
    $php_info = str_replace( 'class="e"', 'class="head"', $php_info );
    $php_info = str_replace( 'class="v"', 'class="even"', $php_info );
    $php_info = str_replace( ";", "; ", $php_info );
    $php_info = str_replace( ",", ", ", $php_info );
    $php_info = str_replace( "h2", "h4", $php_info );
    $offset = strpos( $php_info, "<table" );
    return substr( $php_info, $offset );
}

function getZariliaLogo()
{
    $value = "";
    header( 'content-type: image/gif;' );
    echo base64_decode( $value );
    exit();
}
?>