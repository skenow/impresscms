<?php
// $Id: pagenav.php,v 1.2 2007/04/22 07:21:32 catzwolf Exp $
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
/**
 * Class to facilitate navigation in a multi page document/list
 *
 * @package kernel
 * @subpackage util
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaPageNav {
    /**
     * *#@+
     *
     * @access private
     */
    var $total;
    var $perpage;
    var $current;
    var $url;
    /**
     * *#@-
     */

    /**
     * Constructor
     *
     * @param int $total_items Total number of items
     * @param int $items_perpage Number of items per page
     * @param int $current_start First item on the current page
     * @param string $start_name Name for "start" or "offset"
     * @param string $extra_arg Additional arguments to pass in the URL
     */
    function ZariliaPageNav( $total_items, $items_perpage, $current_start, $start_name = "start", $extra_arg = "" ) {
        $this->total = intval( $total_items );
        $this->perpage = intval( $items_perpage );
        $this->current = intval( $current_start );
        if ( $extra_arg != '' && ( substr( $extra_arg, -5 ) != '&amp;' || substr( $extra_arg, -1 ) != '&' ) ) {
            $extra_arg .= '&amp;';
        }
        if ( $GLOBALS['zariliaAddon']->getVar( 'mid' ) == 1 ) {
            $this->url = $extra_arg . trim( $start_name ) . '=';
        } else {
            $this->url = $_SERVER['PHP_SELF'] . '?' . $extra_arg . trim( $start_name ) . '=';
        }
    }

    /**
     * Create text navigation
     *
     * @param integer $offset
     * @return string
     */
    function renderNav( $offset = 4 ) {
        $ret = '';
        if ( $this->total <= $this->perpage || $this->perpage == 0 ) {
            return $ret;
        }
        $total_pages = ceil( $this->total / $this->perpage );
        if ( $total_pages > 1 ) {
            $prev = $this->current - $this->perpage;
            if ( $prev >= 0 ) {
                $ret .= '<a href="' . $this->url . $prev . '">' . zarilia_img_show( 'n_previous', _PREVIOUS ) . '</a> ';
            }
            $counter = 1;
            $current_page = intval( floor( ( $this->current + $this->perpage ) / $this->perpage ) );
            while ( $counter <= $total_pages ) {
                if ( $counter == $current_page ) {
                    $ret .= '<b>(' . $counter . ')</b> ';
                } elseif ( ( $counter > $current_page - $offset && $counter < $current_page + $offset ) || $counter == 1 || $counter == $total_pages ) {
                    if ( $counter == $total_pages && $current_page < $total_pages - $offset ) {
                        $ret .= '... ';
                    }
                    $ret .= '<a href="' . $this->url . ( ( $counter - 1 ) * $this->perpage ) . '">' . $counter . '</a> ';
                    if ( $counter == 1 && $current_page > 1 + $offset ) {
                        $ret .= '... ';
                    }
                }
                $counter++;
            }
            $next = $this->current + $this->perpage;
            if ( $this->total > $next ) {
                $ret .= '<a href="' . $this->url . $next . '">' . zarilia_img_show( 'n_next', _NEXT ) . '</a> ';
            }
        }
        return $ret;
    }

    /**
     * Create a navigational dropdown list
     *
     * @param boolean $showbutton Show the "Go" button?
     * @return string
     */
    function renderSelect( $showbutton = false ) {
        if ( $this->total <= $this->perpage || $this->perpage == 0 ) {
            return $ret;
        }
        $total_pages = ceil( $this->total / $this->perpage );
        $ret = '';
        if ( $total_pages > 1 ) {
            $ret = '<form name="pagenavform">';
            $ret .= '<select name="pagenavselect" onchange="location=this.options[this.options.selectedIndex].value;">';
            $counter = 1;
            $current_page = intval( floor( ( $this->current + $this->perpage ) / $this->perpage ) );
            while ( $counter <= $total_pages ) {
                if ( $counter == $current_page ) {
                    $ret .= '<option value="' . $this->url . ( ( $counter - 1 ) * $this->perpage ) . '" selected="selected">' . $counter . '</option>';
                } else {
                    $ret .= '<option value="' . $this->url . ( ( $counter - 1 ) * $this->perpage ) . '">' . $counter . '</option>';
                }
                $counter++;
            }
            $ret .= '</select>';
            if ( $showbutton ) {
                $ret .= '&nbsp;<input type="submit" class="formbutton" value="' . _GO . '" />';
            }
            $ret .= '</form>';
        }
        return $ret;
    }

    /**
     * Create navigation with images
     *
     * @param integer $offset
     * @return string
     */
    function renderImageNav( $offset = 4 ) {
        if ( $this->total <= $this->perpage || $this->perpage == 0 ) {
            return $ret;
        }
        $total_pages = ceil( $this->total / $this->perpage );
        $ret = '';
        if ( $total_pages > 1 ) {
            $ret = '<table><tr>';
            $prev = $this->current - $this->perpage;
            if ( $prev >= 0 ) {
                $ret .= '<td class="pagneutral"><a href="' . $this->url . $prev . '">&lt;</a></td><td><img src="' . ZAR_URL . '/images/blank.gif" width="6" alt="" /></td>';
            }
            $counter = 1;
            $current_page = intval( floor( ( $this->current + $this->perpage ) / $this->perpage ) );
            while ( $counter <= $total_pages ) {
                if ( $counter == $current_page ) {
                    $ret .= '<td class="pagact"><b>' . $counter . '</b></td>';
                } elseif ( ( $counter > $current_page - $offset && $counter < $current_page + $offset ) || $counter == 1 || $counter == $total_pages ) {
                    if ( $counter == $total_pages && $current_page < $total_pages - $offset ) {
                        $ret .= '<td class="paginact">...</td>';
                    }
                    $ret .= '<td class="paginact"><a href="' . $this->url . ( ( $counter - 1 ) * $this->perpage ) . '">' . $counter . '</a></td>';
                    if ( $counter == 1 && $current_page > 1 + $offset ) {
                        $ret .= '<td class="paginact">...</td>';
                    }
                }
                $counter++;
            }
            $next = $this->current + $this->perpage;
            if ( $this->total > $next ) {
                $ret .= '
				<td>
				 <img src="' . ZAR_URL . '/images/blank.gif" width="6" alt="" />
				</td>
				<td class="pagneutral">
				 <a href="' . $this->url . $next . '">&gt;</a>
				</td>';
            }
            $ret .= '</tr></table>';
        }
        return $ret;
    }
}

?>