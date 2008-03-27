<?php
// $Id: cp_functions.php,v 1.4 2007/05/05 11:12:10 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
define( 'ZAR_CPFUNC_LOADED', 1 );
if (file_exists($file = ZAR_ROOT_PATH . "/addons/system/language/" . $zariliaConfig['language'] . "/admin.php")) {
	require_once ZAR_ROOT_PATH . "/addons/system/language/" . $zariliaConfig['language'] . "/admin.php";
} else {
	require_once ZAR_ROOT_PATH . "/addons/system/language/english/admin.php";
}
require_once ZAR_ROOT_PATH . "/addons/system/constants.php";

$list_array = array( 1 => '1', 2 => '2', 3 => '3', 5 => '5', 10 => '10', 15 => '15', 25 => '25', 50 => '50', 100 => '100', 0 => 'All' );
$display_array = array( '3' => _MD_AD_SHOWALL_BOX, '0' => _MD_AD_SHOWHIDDEN_BOX, '1' => _MD_AD_SHOWVISIBLE_BOX );
$op_url = array( 'index.php?fct=section&op=edit' => _MA_AD_ASECTION_CREATE, 'index.php?fct=category&op=edit' => _MA_AD_ACATEGORY_CREATE, 'index.php?fct=content&op=edit' => _MA_AD_ACONTENT_CREATE, 'index.php?fct=static&op=edit' => _MA_AD_ASTATIC_CREATE );
/**
 * zarilia_cp_header()
 *
 * @return
 */
/**
 * zarilia_cp_header()
 *
 * @return
 */
function zarilia_cp_header() {
    global $zariliaConfig, $zariliaUser, $zariliaOption, $zariliaTpl, $zariliaAddon, $addonversion;

    $addonperm_handler = &zarilia_gethandler( 'groupperm' );
    $addon_handler = &zarilia_gethandler( 'addon' );

    if ( $zariliaConfig['language'] == '' ) {
        $dir = ZAR_ROOT_PATH . '/language/';
        if ( is_dir( $dir ) ) {
            if ( $dh = opendir( $dir ) ) {
                while ( ( $file = readdir( $dh ) ) !== false ) {
                    if ( is_dir( $dir . $file ) && ( $file != 'CVS' ) && ( $file{0} != '.' ) ) {
                        $zariliaConfig['language'] = $file;
                        break;
                    }
                }
                closedir( $dh );
            }
        }
    }
    ob_start();
    /**
     * End Fix
     */
    // include Smarty template engine and initialize it
    require_once ZAR_ROOT_PATH . '/class/template.php';
    @$zariliaOption['theme_use_smarty'] = 1;
    $zariliaTpl = new ZariliaTpl();
    $zariliaTpl->zarilia_setCaching( 0 );
    $zariliaTpl->zarilia_setDebugging();
    if ( !headers_sent() ) {
        header ( 'Content-Type:text/html; charset=' . _CHARSET );
        header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
        header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
        header( 'Cache-Control: no-store, no-cache, must-revalidate' );
        header( 'Cache-Control: post-check=0, pre-check=0', false );
        header( 'Pragma: no-cache' );
    }
    $zariliaTpl->addMeta( 'content-type', 'text/html; charset=' . _CHARSET, true );
    $zariliaTpl->addMeta( 'content-language', _LANGCODE, true );
    $zariliaTpl->addCss( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/css/admin_style.css' );
    $zariliaTpl->addCss( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/js/ThemeOffice/admin_theme.css' );
    $zariliaTpl->addScript( ZAR_URL . '/include/javascript/zarilia.js' );
    $zariliaTpl->addScript( ZAR_URL . '/include/javascript/boxover.js' );
    $zariliaTpl->addScript( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/js/JSCookMenu.js' );
    $zariliaTpl->addScript( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/js/admin.js' );
    $zariliaTpl->addScript( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/js/ThemeOffice/theme.js' );
    $zariliaTpl->addIcon( ZAR_THEME_URL . '/' . $zariliaConfig['theme_set'] . '/favicon.ico', 'SHORTCUT ICON', '' );
    $zariliaTpl->addTitle( htmlspecialchars( $zariliaConfig['sitename'] ) . ' ' . htmlspecialchars( $zariliaConfig['slogan'], ENT_QUOTES ) );
    $zariliaTpl->assign(
        array( 'text_console_info' => _MD_AM_CONSOLE_INFO,
            'text_welcome' => _MD_AM_WELCOME,
            'user' => ucfirst( $zariliaUser->getUnameFromId( $zariliaUser->getVar( 'uid', 1 ) ) ),
            'text_loginip' => _MD_AM_IPLOGIN,
            'loginip' => getip(),
            'text_loginat' => _MD_AM_LOGINAT,
            'loginat' => $zariliaUser->getVar( 'last_login' ) ,
            'addon_info' => '<strong>Addons: </strong>' . $zariliaAddon->getVar( 'name' ),
            'metafooter' => '<strong>Zarilia</strong>'
            )
        );
    if ( isset( $_REQUEST['debug'] ) ) {
        if ( $_REQUEST['debug'] == 'rebuild' ) {
            unset( $_SESSION['administration'] );
        }
    }
    if ( !isset( $_SESSION['administration'] ) ) {
        zarilia_update_interface();
    } else {
		if (!isset($_SESSION['administration']['rebuild_time'])) {
			$_SESSION['administration']['rebuild_time'] = 1000;
		}
        if ( $_SESSION['administration']['rebuild_time'] < time() ) {
            zarilia_update_interface();
        }
    }
    ob_end_flush();
    ob_start();
}

/**
 * zarilia_update_interface()
 *
 * @return
 */
function zarilia_update_interface() {
    global $zariliaConfig, $zariliaUser, $zariliaOption, $zariliaTpl;

    $path = ZAR_ROOT_PATH . '/addons/';
    if ( $handle = opendir( $path ) ) {
        $_SESSION['administration']['toolbar'] = array();
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( is_dir( $path . $file ) && !( $file{0} == "." ) ) {
                foreach ( array( 'png', 'gif', 'jpg', 'jpeg' ) as $ext ) {
                    if ( file_exists( $path . $file . '/images/system/toolbar.' . $ext ) ) {
                        $_SESSION['administration']['toolbar'][$file]['image'] = ZAR_URL . '/addons/' . $file . '/images/system/toolbar.' . $ext;
                        break;
                    }
                }
                if ( !isset( $_SESSION['administration']['toolbar'][$file]['image'] ) ) {
                    continue;
                }
                zarilia_read_info( $addonversion, $path . $file );
                if ( isset( $addonversion['adminpath'] ) ) {
                    $_SESSION['administration']['toolbar'][$file]['url'] = ZAR_URL . '/addons/' . $file . '/' . $addonversion['adminpath'];
                } elseif ( $file == 'system' ) {
                    $_SESSION['administration']['toolbar'][$file]['url'] = ZAR_URL . '/addons/' . $file . '/index.php?fct=preferences&amp;op=config';
                } else {
                    $_SESSION['administration']['toolbar'][$file]['url'] = ZAR_URL . '/addons/' . $file . '/admin.php';
                }
                unset( $addonversion );
            }
        }
        closedir( $handle );
    } else {
        die( 'Can\'t read addons admin directory.' );
    }

    $objects = array( "blocks", "menu" );
    $path = ZAR_ROOT_PATH . '/addons/system/admin/';
    if ( $handle = opendir( $path ) ) {
        $_SESSION['administration']['blocks'] = array();
        $_SESSION['administration']['menu'] = array();
        $i = 0;
        while ( false !== ( $file = readdir( $handle ) ) ) {
            if ( is_dir( $path . $file ) && !( $file{0} == "." ) ) {
                zarilia_read_info( $addonversion, $path . $file );
                foreach( $objects as $object ) {
                    if ( isset( $addonversion[$object] ) ) {
                        if ( !isset( $_SESSION['administration'][$object] ) ) {
                            $_SESSION['administration'][$object] = array();
                        }
                        foreach( $addonversion[$object] as $name => $block ) {
                            if ( !isset( $_SESSION['administration'][$object][$name] ) ) {
                                $_SESSION['administration'][$object][$name] = array( 'title' => constant( '_MD_AM_' . strtoupper( $name ) . '_MTITLE' ), 'items' => array(), 'id' => $i++ );
                            }
                            foreach( $block as $item ) {
                                if ( !isset( $item['group'] ) ) {
                                    $group = '';
                                } else {
                                    $group = $item['group'];
                                    unset( $item['group'] );
                                }
                                $_SESSION['administration'][$object][$name]['items'][$group][] = $item;
                            }
                        }
                    }
                }
            }
        }
        closedir( $handle );
    } else {
        die( 'Can\'t read system addon admin directory.' );
    }

    foreach( $objects as $object ) {
        $_SESSION['administration'][$object . '2'] = array();
        foreach( $_SESSION['administration'][$object] as $name => $data ) {
            ksort( $data['items'] );
            $i = 0;
            $_SESSION['administration'][$object . '2'][$name]['title'] = $data['title'];
            $_SESSION['administration'][$object . '2'][$name]['id'] = $data['id'];
            foreach( $data['items'] as $group => $items ) {
                if ( $i > 0 ) {
                    $_SESSION['administration'][$object . '2'][$name]['items'][] = array( 'url' => '', 'title' => '<hr />' );
                }
                if ( !isset( $_SESSION['administration'][$object . '2'][$name]['items'] ) ) {
                    $_SESSION['administration'][$object . '2'][$name]['items'] = array();
                }
                $_SESSION['administration'][$object . '2'][$name]['items'] = array_merge( $_SESSION['administration'][$object . '2'][$name]['items'], $items );
                $i++;
            }
        }
        $_SESSION['administration'][$object] = $_SESSION['administration'][$object . '2'];
        unset( $_SESSION['administration'][$object . '2'] );
    }
    uksort( $_SESSION['administration']['menu'], "_zarilia_sort_menu" );
    ksort( $_SESSION['administration']['blocks'] );
    $_SESSION['administration']['rebuild_time'] = strtotime( "+20 minutes" );
}

function zarilia_read_info( &$addonversion, $path ) {
    global $zariliaConfig, $zariliaUser, $zariliaOption, $zariliaTpl;
    @include ZAR_ROOT_PATH . '/addons/system/language/' . $zariliaConfig['language'] . '/admin/' . basename( $path ) . '.php';
    $file = $path . '/zarilia_version.php';
    $addonversion = array();
    if ( !file_exists( $file ) ) {
        return;
    }
    include $file;
}

function _zarilia_sort_menu( $a, $b ) {
    if ( $a == $b ) return 0;
    if ( $b == 'info' ) return -1;
    if ( $b == 'misc' ) return ( $a == 'info' ) ? 1 : -1;
    if ( $a == 'addons' ) return 0;
    return strcmp( $a, $b );
}

function zarilia_cp_footer() {
    global $zariliaOption, $zariliaConfig, $zariliaLogger, $zariliaTpl, $zariliaCachedTemplateId;

    if ( !is_object( $zariliaTpl ) ) {
        zarilia_cp_header();
    }
    $zariliaTpl->assign_by_ref( 'system_menu', $_SESSION['administration']['menu'] );
    $zariliaTpl->assign_by_ref( 'blocks_menu', $_SESSION['administration']['blocks'] );
    $zariliaTpl->assign_by_ref( 'images_menu', $_SESSION['administration']['toolbar'] );
    if ( $zariliaConfig['gzip_compression'] == 1 && $encoding = tep_check_gzip() ) {
        header( 'Content-Encoding: ' . $encoding );
        $contents = ob_get_contents();
        $size = strlen( $contents );
        $crc = crc32( $contents );
        $contents = gzcompress( $contents, 9 );
        $contents = substr( $contents, 0, strlen( $contents ) - 4 );
        echo "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $zariliaTpl->assign( 'zarilia_contents', $contents );
        // echo $contents;
        echo pack( 'V', $crc );
        echo pack( 'V', $size );
    } else {
        $zariliaTpl->assign( 'zarilia_contents', ob_get_contents() );
    }
    $zariliaTpl->assign( 'zarilia_contents', ob_get_contents() );
    ob_clean();
    ob_end_flush();

    $theme_path = ZAR_THEME_PATH . '/' . $zariliaConfig['theme_set'] . '/js/ThemeOffice/';
    $zariliaTpl->display( ZAR_THEME_PATH . '/' . $zariliaConfig['theme_set'] . '/admin.html' );
    unset( $_SESSION['administration']['blocks'] );
    $zariliaLogger->stopTime();
    $zariliaLogger->render();
}

function zarilia_cp_icons( $_icon_array = array(), $key, $value = null, $extra = null ) {
    global $addonversion, $zariliaAddon;

    $ret = '';
    if ( $value ) {
        foreach( $_icon_array as $_op => $_icon ) {
            $_op = ( !is_numeric( $_op ) ) ? $_op : $_icon;
            $url = ( is_object( $zariliaAddon ) && $zariliaAddon->getVar( 'mid' ) > 1 ) ? $addonversion['adminpath'] . "?op=" . $_op . "&amp;{$key}=" . $value : $addonversion['adminpath'] . "&amp;op=" . $_op . "&amp;{$key}=" . $value;
            if ( $extra != null ) {
                $url .= $extra;
            }
            $ret .= "<a href='" . $url . "'>" . zarilia_img_show( $_icon, zarilia_constants( '_' . $_icon ) ) . "</a>";
        }
    }
    return $ret;
}

function zarilia_cp_legend( $led_array ) {
    $legend = '';
    /**
     * show legend
     */
    if ( is_array( $led_array ) ) {
        foreach( $led_array as $key ) {
            $legend .= "<div style='padding: 3;'>" . zarilia_img_show( $key ) . " " . zarilia_constants( "_" . $key . "_LEG" ) . "</div>\n";
        }
        if ( isset( $_SESSION['administration']['blocks'] ) ) {
        	$_SESSION['administration']['blocks']['\$\$Legend'] = array( 'title' => _BOX_LEGEND_TITLE, 'items' => $legend, 'id' => count( $_SESSION['administration']['blocks'] ) + 1 );
        }
    }
}

function zarilia_noSelection( $colspan = 0, $echo = true ) {
    $ret = "<tr>\n<td colspan='$colspan' style='text-align: center;' class='head'>" . _NOTHINGFOUND . "</td>\n</tr>\n";
    if ( $echo ) {
        echo $ret;
    } else {
        return $ret;
    }
}

function zarilia_listing_footer( $heading_arr = 0, $content = '', $echo = true ) {
    $content = ( !empty( $content ) ) ? $content : '&nbsp;';
    $ret = "<tr>\n<td colspan='" . $heading_arr . "' class='foot'>" . $content . "</td>\n</tr>\n</table>\n";
    if ( $echo ) {
        echo $ret;
    } else {
        return $ret;
    }
}

function zarilia_admin_menu() {
    global $zariliaAddon;
    $_SESSION['administration']['blocks'] = array();

    $i = 0;
    $args = func_get_args();
    $numargs = func_num_args();
    if ( ( !is_array( $args ) || !$args ) || $numargs == 0 ) {
        return false;
    }
    for ( $k = 0;$k < $numargs;$k += 2 ) {
        if ( @is_array( $args[$k + 1] ) ) {
            $rez = array();
            foreach ( $args[$k + 1] as $url => $title ) {
                $rez[] = array( 'url' => $url, 'title' => $title );
            }
        } elseif ( @is_string( $args[$k + 1] ) ) {
            $rez = $args[$k + 1];
        } else {
            return false;
        }
        $_SESSION['administration']['blocks'][$args[$k]] = array( 'title' => $args[$k], 'items' => $rez, 'id' => $i++ );
    }
}

function showHtmlCalendar( $display = true, $date = '' ) {
    $jstime = formatTimestamp( 'F j Y, H:i:s', time() );
    $value = ( $date == '' ) ? '' : strftime( '%Y-%m-%d %I:%M', $date );
    require_once ZAR_ROOT_PATH . '/class/calendar/calendar.php';
    $calendar = new DHTML_Calendar( ZAR_URL . '/class/calendar/', 'en', 'calendar-system', false );
    $calendar->load_files();
    return $calendar->make_input_field(
        array( 'firstDay' => 1, 'showsTime' => true, 'showOthers' => true, 'ifFormat' => '%Y-%m-%d %I:%M', 'timeFormat' => '12' ), // field attributes go here
        array( 'style' => '', 'name' => 'date1', 'value' => $value ), $display
        );
}

function zarilia_getImagebyMimetype( $name = '', $ext = 'png', $_title = '', $_align = '' ) {
    $_name = zarilia_getFileExtension( $name );
    return "<img src='" . ZAR_URL . "/addons/system/images/mimetypes/{$_name['ext']}.$ext' title='$_title' alt='$_title' align='$_align' />";
}

function zariliaMainAction( $drop = false ) {
    global $addonversion;
    $maintenance = array( $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=optimize" => _MD_AD_OPTIMIZE, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=analyze" => _MD_AD_ANALYZE, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=repair" => _MD_AD_REPAIR, $addonversion['adminpath'] . "&amp;op=maintenace&amp;act=truncate" => _MD_AD_CLEARENTRIES );
    if ( $drop == true ) {
        unset( $maintenance[3] );
    }

    return $maintenance;
}



?>