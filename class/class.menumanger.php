<?php
// $Id: class.menumanger.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaMManager
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: class.menumanger.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
 * @access public
 */
class ZariliaMManager {
    var $menu;
    var $menuId;
    var $menuClass;
    var $imagePath;
    var $id;
    var $firstlvl;

    /**
     * ZariliaMManager::ZariliaMManager()
     *
     * @param string $id
     * @param string $class
     */
    function ZariliaMManager( $id = 'jcssMenu', $class = 'jcssMenu' )
    {
        $this->menu = array();
        $this->setMenuId( $id );
        $this->setMenuClass( $class );
        $this->id = 0;
    }

    /**
     * ZariliaMManager::add()
     *
     * @param mixed $title
     * @param mixed $url
     * @param string $parent
     * @param string $group
     * @param string $icon
     * @return
     */
    // id 		= Id if actual menu root
    // pid 	= parent id of menu item
    // title	= title of menu
    // url		= Url of menu
    // group	= group of menu items
    // icon	= image url of icon
    // class	= class image of icon
    // item
    // function add( $id = 0, $pid = 0, $title = null, $url = null, $group = '', $icon = null, $class = null )
    // {
    // $name = trim( strtolower( $title ) );
    // $group = ( !empty( $group ) ) ? $group : '';
    // if ( $pid == 0 ) {
    // if ( !isset( $this->menu[$id] ) ) {
    // $this->menu[$id] = array( 'title' => $title, 'url' => $url, 'items' => array(), 'id' => $id );
    // }
    // } else if ( isset( $this->menu[$id] ) && $pid > 0 ) {
    // $this->menu[$pid]['items'][$group][$id] = array( 'title' => $title, 'url' => $url, 'icon' => $icon, 'class' => $class, 'id' => $pid );
    // /*
    // $new_id = ( isset( $this->menu[$pid]['items'][$group][$id]['id'] ) ) ? $this->menu[$pid]['items'][$group][$id] : 0;
    // echo $new_id;
    // if ( !isset( $this->menu[$pid]['items'][$group][$id] ) ) {
    // } else {
    // // $this->menu[$pid]['items'][$group][$id['items'][$group][$id]
    // }
    // */
    // }
    // /*
    // if ( !isset( $this->menu[$id]) && $pid == 0 ) {
    // $this->menu[$id] = array( 'title' => $title, 'url' => $url, 'items' => array(), 'pid' => $pid);
    // } elseif ( isset( $this->menu[$id]) && $pid > 0 ) {
    // $this->menu[$id]['items']['groups'][] = array( 'title' => $title, 'url' => $url, 'icon' => $icon, 'class' => $class );
    // } else {
    // return;
    // }
    // */
    // }
    function add( $title, $url, $root = 'root', $group = '', $level = 0, $icon = '' )
    {
        if ( !isset( $this->firstlvl ) ) {
            $this->firstlvl = $root;
        }
		if ( !isset( $this->menu[$root] ) ) {
                $this->menu[$root] = array( 'title' => $title, 'url' => $url, 'items' => array(), 'id' => $id );
        } else if ( !isset( $this->menu[$root]['items'][$group] )  ) {
                $this->menu[$root]['items'][$group] = array( 'title' => $title, 'url' => $url, 'items' => array(), 'icon' => $icon, 'class' => $class, 'id' => $pid );
		} else if ( $level == 2 ) {
        	echo $title;
		}
        // // $group = ( empty( $group ) === false ) ? $group : ( $parent . '_' . ( count( $this->menu[$parent] ) + 1 ) );
        // // $this->menu[$parent][$group][] = array( 'title' => $title, 'url' => $url, 'icon' => $icon );
        // if ( !isset( $this->menu[$parent] ) ) {
        // $this->menu[$parent] = array( 'title' => $title, 'url' => $url, 'icon' => $icon, 'class' => $class, 'id' => $pid );
        // } else {
        // $this->menu[$parent]['items'][] = array( 'title' => $title, 'url' => $url, 'icon' => $icon, 'class' => $class, 'id' => $pid );
        // }
    }

    function print_r_html()
    {
        print_r_html( $this->menu );
    }

    /**
     * ZariliaMManager::buildMenu()
     *
     * @param mixed $parent
     * @param mixed $level
     * @return
     */
    // function buildMenu( $parent, $level = 0 )
    // {
    // global $request_type;
    // $result = '<ul id="' . $this->menuId . '_' . $parent . '">';
    // if ( isset( $this->menu ) ) {
    // foreach( $this->menu as $menu ) {
    // print_r_html( $menu );
    // // for( $i = 0, $n = count( $links ); $i < $n; ++$i ) {
    // // $result .= '<li>';
    // // if ( !empty( $links[$i]['icon'] ) === false ) {
    // // $icon = " style=\"background-image: url('../images/admin_menu/" . $links[$i]['icon'] . "')\"";
    // // } else if ( !empty( $links[$i]['class'] ) === false ) {
    // // $icon = " style=\"background-image: url('../images/admin_menu/" . $links[$i]['class'] . ".png')\"";
    // // } else {
    // // $icon = " style=\"background-image: url('../images/admin_menu/blank.png')\"";
    // // }
    // // $result .= "<a href=\"" . $links[$i]['url'] . "\">" . "<span class=\"icon\"{$icon}>";
    // // $_text = "<span class=\"text\">" . $links[$i]['title'] . "</span>";
    // // if ( isset( $this->menu[$group] ) && $level > 0 ) {
    // // $result .= "<span class=\"submenu\">{$_text}</span>";
    // // } else {
    // // $result .= "<span>{$_text}</span>";
    // // }
    // // $result .= "</span></a>";
    // // if ( isset( $this->menu[$group] ) ) {
    // // $result .= $this->buildMenu( $group, ++$level );
    // // }
    // // $result .= '</li>';
    // // }
    // }
    // }
    // $result .= '</ul>';
    // return $result;
    // }
    function buildMenu( $parent, $level = 0 )
    {
        $ret = '<ul id="' . $this->menuId . '_' . $parent . '">';
        if ( isset( $this->menu[$parent] ) ) {
            foreach( $this->menu[$parent] as $group => $links ) {
                echo $group;
                // echo "processing group: $group<br />";
                for( $i = 0, $n = count( $links );$i < $n;++$i ) {
                    $ret .= '<li>';
                    $icon = ( empty( $links[$i]['icon'] ) === false ) ? " style=\"background-image: url('./ext/jcssmenu/images/icons/" . $links[$i]['icon'] . "')\"": '';
                    $ret .= "<a href=\"" . $links[$i]['url'] . "\">" . "<span class=\"icon\"{$icon}>";
                    // display link type
                    $_text = "<span class=\"text\">" . $links[$i]['title'] . "</span>";
                    if ( isset( $this->menu[$group] ) && $level > 0 ) {
                        $ret .= "<span class=\"submenu\">{$_text}</span>";
                    } else {
                        $ret .= "<span>{$_text}</span>";
                    }
                    $ret .= "</span></a>";
                    // build next part of the menu with the group
                    if ( isset( $this->menu[ $group ] ) ) {
                        $ret .= $this->buildMenu( $group, ++$level );
                    }
                    $ret .= '</li>';
                }
            }
        }
        $ret .= '</ul>';
        return $ret;
    }

    function display( $group = 'root' )
    {
        echo $this->print_r_html();
        return "\n" . '<div id="' . $this->menuId . '" class="' . $this->menuClass . '">' . $this->buildMenu( $this->firstlvl ) . '</div>' . "\n" . '<script type="text/javascript">cmDrawFromText(\'navbar\', \'hbr\', cmThemeOffice, \'ThemeOffice\');</script>' . "\n";
    }

    function setMenuClass( $class = 'jcssMenu' )
    {
        $this->menuClass = $class;
    }

    function setMenuId( $id = 'jcssMenu' )
    {
        $this->menuId = $id;
    }

    function setImagePath( $value = null )
    {
        $this->imagePath = htmlspecialchars( $value );
    }
} //end class

?>