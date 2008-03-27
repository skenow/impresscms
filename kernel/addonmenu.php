<?php
// $Id: addonmenu.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaAddonMenuHandler
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: addonmenu.php,v 1.2 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaAddonMenuHandler extends ZariliaAddonHandler {
    var $_menutop = array();
    var $_menutabs = array();
    var $_obj;
    var $_header;
    /**
     * ZariliaAddonMenuHandler::ZariliaAddonMenuHandler()
     */
    function ZariliaAddonMenuHandler() {
        global $zariliaAddon;
        $this->_obj = &$zariliaAddon;
    }

    function getAddon( $addon ) {
        $this->_obj = &$addon;
    }

    function addMenuTop( $value, $name = "" ) {
        if ( $name != "" ) {
            $this->_menutop[$value] = $name;
        } else {
            $this->_menutop[$value] = $value;
        }
    }

    function addMenuTopArray( $options, $multi = true ) {
        if ( is_array( $options ) ) {
            if ( $multi == true ) {
                foreach ( $options as $k => $v ) {
                    $this->addOptionTop( $k, $v );
                }
            } else {
                foreach ( $options as $k ) {
                    $this->addOptiontop( $k, $k );
                }
            }
        }
    }

    function addMenuTabs( $value, $name = "" ) {
        if ( $name != "" ) {
            $this->_menutabs[$value] = $name;
        } else {
            $this->_menutabs[$value] = $value;
        }
    }

    function addMenuTabsArray( $options, $multi = true ) {
        if ( is_array( $options ) ) {
            if ( $multi == true ) {
                foreach ( $options as $k => $v ) {
                    $this->addMenuTabsTop( $k, $v );
                }
            } else {
                foreach ( $options as $k ) {
                    $this->addMenuTabsTop( $k, $k );
                }
            }
        }
    }

    function addHeader( $value ) {
        $this->_header = $value;
    }

    function breadcrumb_nav( $basename = "Home" ) {
        global $PHP_SELF, $bc_site, $bc_label;
        $site = $bc_site;
        $return_str = "<A HREF=\"/\">$basename</A>";
        $str = substr( dirname( $PHP_SELF ), 1 );

        $arr = split( '/', $str );
        $num = count( $arr );

        if ( $num > 1 ) {
            foreach( $arr as $val ) {
                $return_str .= ' &gt; <a href="' . $site . $val . '/">' . $bc_label[$val] . '</a>';
                $site .= $val . '/';
            }
        } elseif ( $num == 1 ) {
            $arr = $str;
            $return_str .= ' &gt; <a href="' . $bc_site . $arr . '/">' . $bc_label[$arr] . '</a>';
        }
        return $return_str;
    }

    function render( $currentoption = 1, $return = false ) {
        global $addonversion;
        $_dirname = $this->_obj->vars['dirname']['value'];
        $i = 0;

        /*
		* Selects current menu tab
		*/
//        foreach ( $this->_menutabs as $k => $menus ) {
//            $menuItems[] = $menus;
  //      } 
		$menuItems =  array_values($this->_menutabs);
		$count = count($menuItems)-1;
		if ($currentoption>$count) $currentoption = $count;
        $breadcrumb = $menuItems[$currentoption];
        $menuItems[$currentoption] = 'current';

        $menu = "<h3 class='admin_header'>Addons: " . $this->_obj->vars['name']['value'] . "</h3>\n";
        $menu .= "<div id='buttontop_mod'>";
        $menu .= "<table style='width: 100%; padding: 0;' cellspacing='0'>\n<tr>";
        $menu .= "<td style='font-size: 10px; text-align: left; color: #2F5376; padding: 0 6px; line-height: 18px;'>";
        $menu .= "<a class='nobutton_mod' href='" . ZAR_URL . "/addons/system/index.php?fct=preferences&amp;op=showaddon&amp;mod=" . $this->_obj->vars['mid']['value'] . "'>" . _MD_AM_PREFS . "</a>";
        foreach ( $this->_menutop as $k => $v ) {
            $menu .= " | <a href=\"$k\">$v</a>";
        }
        $menu .= "</td>";
        // $breadcrumb = ''; //explode( ".", basename( $_SERVER['SCRIPT_NAME'] ) );
        $menu .= "<td style='text-align: right;'><strong>" . _MD_AM_ADMINBREADCRUMB . "</strong> " . $breadcrumb . "</td>";
        $menu .= "</tr>\n</table>\n";
        $menu .= "</div>\n";
        $menu .= "<div id='buttonbar_mod'><ul>";
        foreach ( $this->_menutabs as $k => $v ) {
            $menu .= "<li id='" . $menuItems[$i] . "'><a href=\"$k\"><span>$v</span></a></li>\n";
            $i++;
        }
        $menu .= "</ul>\n</div>\n";
        if ( $this->_header ) {
            $menu .= "<h4 class='admin_header'>";
            if ( isset( $addonversion['name'] ) ) {
                if ( $addonversion['image'] && $this->_obj->vars['mid']['value'] == 1 ) {
                    $system_image = ZAR_URL . '/addons/system/images/system/' . $addonversion['image'];
                } else {
                    $system_image = ZAR_URL . '/addons/' . $_dirname . '/images/' . $addonversion['image'];
                }
                $menu .= "<img src='$system_image' align='middle' height='32' width='32' alt='' />";
                $menu .= " " . $addonversion['name'] . "</h4>\n";
            } else {
                $menu .= " " . $this->_header . "</h4>\n";
            }
        }
        unset( $this->_obj );
        if ( $return == true ) {
            return $menu;
        } else {
            echo $menu;
        }
    }
}

?>