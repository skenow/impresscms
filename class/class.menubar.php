<?php
// $Id: class.menubar.php,v 1.2 2007/03/30 22:05:45 catzwolf Exp $
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
 * ZariliaTabMenu
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: class.menubar.php,v 1.2 2007/03/30 22:05:45 catzwolf Exp $
 * @access public
 **/
class ZariliaTabMenu
{
    var $currentOption;

    var $_tab_array = array();
    var $_tblCol = array();
	var $_usescript;

    /**
     * Constructor
     *
     * @param integer $currentoption
     * @return
     */
    function ZariliaTabMenu( $currentoption = 0, $usescript = false )
    {
        $this -> currentOption = intval( $currentoption );
		if ($this -> _usescript = $usescript) {
			global $zariliaTpl;
			$zariliaTpl->addScriptSource('
				function SelectTab(id, id2) {
					var name = "tabMenuItem[" + id2 + "][" + id + "]";
					var items = document.getElementsByTagName("li");
					for(var i=0;i<items.length;i++) {
						if (items[i].id == name) {
							items[i].className = "currentTab";
						} else {
							items[i].className = "";
						}
					}
				}
				');

		}
    }

    /**
     * ZariliaTabMenu::addTab()
     *
     * @param  $value
     * @param string $name
     * @return
     */
    function addTab( $value, $name = "" )
    {
        $value = ( is_string( $value ) ) ? trim( $value ) : intval( $value );
        if ( $name != "" )
        {
            $this -> _tab_array[$value] = trim( $name );
        }
        else
        {
            $this -> _tab_array[$value] = $value;
        }
    }

    /**
     * ZariliaTabMenu::addTabArray()
     *
     * @param  $options
     * @return
     */
    function addTabArray( $options )
    {
        if ( is_array( $options ) )
        {
            foreach ( $options as $k => $v )
            {
                $this -> addTab( $k, $v );
            }
        }
    }

    /**
     * ZariliaTabMenu::render()
     *
     * @return
     */
    function renderStart( $asOutput = 0, $force = 0 )
    {
		global $zariliaTpl, $zariliaOption;

		if (!isset($zariliaOption['tabMenus_count'])) {
			$zariliaOption['tabMenus_count'] = 1;
		} else {
			$zariliaOption['tabMenus_count']++;
		}
        $i = $a = 0;

        while ( $a <= count( $this -> _tab_array ) )
        {
            $this -> _tblCol[$a] = '';
            $a++;
        } // while
        $this -> _tblCol[$this -> currentOption] = 'currentTab';

        $buttons = "<div id='buttonbars'><ul>";
        foreach ( $this -> _tab_array as $caption => $path )
        {
			$ext = ltrim( strrchr( $path, '.' ), '.' );
			if (!$this -> _usescript) {
				$extchar = (strlen($ext) > 3 || $force == 1 ) ? '&opt='.$i : '?opt='.$i;
			} else {
				$extchar = "SelectTab(".$i.",".$zariliaOption['tabMenus_count'].");";
			}
			$buttons .= "
			 <li name='tabMenuItem' id='tabMenuItem[".$zariliaOption['tabMenus_count']."][".$i."]' class='" . $this -> _tblCol[$i] . "'>
			  <a href='$path$extchar'>
			   <span>$caption</span>
			  </a>
			 </li>";
            $i++;
        }
        $buttons .= "</ul></div>";
        if ( $asOutput ) {
            return $buttons;
        } else {
			echo $buttons;
		}
    }
}

?>