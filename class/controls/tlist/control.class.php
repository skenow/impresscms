<?php
// $Id: dyntabs.php,v 1.1 2007/03/16 02:40:28 catzwolf Exp $
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

// Loading base class
require_once ZAR_ROOT_PATH.'/class/controls/dyncontent/control.class.php';
include_once ZAR_ROOT_PATH . '/class/class.menubar.php';

/**
 * DHTML/Ajax Dynamic Content control
 * 
 * @package kernel
 * @subpackage ajax
 */
class ZariliaControl_TList
	extends ZariliaControl {

	var $_headers;
    var $_data = array();
    var $_pre_fix = '_MA_AD_';
    var $_output = false;
    var $_footer;
    var $_hidden;
    var $_path;
    var $_formName;
    var $_formAction;

    /**
     * Tlist::Tlist()
     *
     * @return
     */
    function ZariliaControl_TList( $headers = array(), $name = null) {
        $this->_headers = $headers;
		$this->ZariliaControl('TList',$name);
    }

    function AddHeader( $name, $size = 0, $align = 'left', $islink = false ) {
        $this->_headers[] = array( 'name' => $name, 'width' => $size, 'align' => $align, 'islink' => $islink );
        $this->_headers_count = count( $this->_headers );
    }

    function setPrefix( $value ) {
        $this->_pre_fix = ( isset( $value ) ) ? strval( $value ) : '_MA_AD_';
    }

    function setOutput( $value = true ) {
        $this->_output = ( $value == true ) ? true : false;
    }

    function setPath( $value ) {
        $this->_path = strval( $value );
    }

    function setOp( $value ) {
        $this->_op = strval( $value );
    }

    function add( $data, $class = null, $isarray = false ) {
        if ( $isarray ) {
            foreach ( $data as $value ) {
                $this->_data[] = array( $value, $class );
            }
        } else {
            $this->_data[] = array( $data, $class );
        }
    }

    function import( $array ) {
        foreach ( $array as $a_rrays ) {
            $this->add( $a_rrays, $class = null, $isarray = false );
        }
    }

    function addHidden( $value, $name = "" ) {
        if ( $name != "" ) {
            $this->_hidden[$value] = $name;
        } else {
            $this->_hidden[$value] = $value;
        }
    }

    function addHiddenArray( $options, $multi = true ) {
        if ( is_array( $options ) ) {
            if ( $multi == true ) {
                foreach ( $options as $k => $v ) {
                    $this->addHidden( $k, $v );
                }
            } else {
                foreach ( $options as $k ) {
                    $this->addHidden( $k, $k );
                }
            }
        }
    }

    function noselection() {
        $ret = "<tr>\n<td colspan='" . $this->_headers_count . "' class='emptylist'>" . _NOTHINGFOUND . "</td>\n</tr>\n";
        if ( $this->_output ) {
            echo $ret;
        } else {
            return $ret;
        }
    }

    function addFooter( $value = '' ) {
        $this->_footer = $value;
    }

    function footer_listing( $align = 'right' ) {
        $ret = "<tr style='text-align: $align;'>\n<td colspan='" . $this->_headers_count . "' class='foot'>";
        if ( $this->_footer ) {
            if ( count( $this->_data ) ) {
                $ret .= $this->_footer;
            }
        }
        $ret .= "</td>\n</tr>\n</table>\n";
        if ( $this->_output ) {
            echo $ret;
        } else {
            return $ret;
        }
    }

    function AddFormStart( $method = 'post', $op = '', $name = '' ) {
        $this->_formName = strval( $name );
        if ( $this->_formName != '' ) {
            $this->_formAction .= "<form style='margin: 0px;' method='" . $method . "' op='" . $op . "' id='" . $this->_formName . "' name='" . $this->_formName . "' >";
        }
    }

    function addFormEnd() {
        if ( $this->_formName ) {
            return '</form>';
        }
    }

    /**
     * ZariliaTList::render()
     *
     * @return
     */
    function render( $return = false ) {
        global $addonversion;

        $ret = $this->_formAction;
        $count = count( $this->_headers );

        $ret .= "<table width='100%' cellpadding='0' cellspacing='1' class='outer' summary=''>\n";
        $ret .= "<tr style='text-align: center;'>\n";
        foreach ( $this->_headers as $value ) {
            $width = ( isset( $value['width'] ) ) ? "style='width: " . $value['width'] . ";'" : '';
            $ret .= "<th $width align={$value['align']}>\n";
            if ( intval( $value['islink'] ) == 2 ) {
                $ret .= zarilia_constants( $this->_pre_fix . $value['name'] );
                $ret .= "<input name='" . $value['name'] . "_checkall' id='" . $value['name'] . "_checkall' onclick='zariliaCheckAll(\"" . $this->_formName . "\", \"" . $value['name'] . "_checkall\");' type='checkbox' value='Check All' />";
            } elseif ( $value['islink'] == true ) {
                $ret .= "<a href='" . $addonversion['adminpath'] . "&";
                if ( $this->_path ) {
                    $ret .= $this->_path . "&amp;";
                }
                $ret .= "sort=" . $value['name'] . "&amp;order=ASC'>" . zarilia_img_show( 'down' ) . "</a>";
                $ret .= zarilia_constants( $this->_pre_fix . $value['name'] );
                $ret .= "<a href='" . $addonversion['adminpath'] . "&";
                if ( $this->_path ) {
                    $ret .= $this->_path . "&amp;";
                }
                $ret .= "sort=" . $value['name'] . "&amp;order=DESC'>" . zarilia_img_show( 'up' ) . "</a>";
            } else {
                $ret .= zarilia_constants( $this->_pre_fix . $value['name'] );
            }
            unset( $constant );
            $ret .= "</th>\n";
        }
        $ret .= "</tr>\n";
        if ( count( $this->_data ) ) {
            foreach ( $this->_data as $data ) {
                if ( !empty( $data[1] ) ) {
                    $class = $data[1];
                } else {
                    $class = ( isset( $class ) && $class == 'even' ) ? 'odd' : 'even';
                }
                $ret .= "<tr class='" . $class . "'>\n";
                $i = 0;
                foreach ( $data[0] as $value ) {
                    $ret .= "<td align='" . $this->_headers[$i]['align'] . "'>" . $value . "</td>\n";
                    $i++;
                }
                $ret .= "</tr>\n";
            }
        } else {
            $ret .= $this->noselection();
        }
        if ( count( $this->_hidden ) ) {
            foreach( $this->_hidden as $k => $v ) {
                $ret .= "<input type='hidden' name='" . $v . "[" . $k . "]' id='" . $v . "[]' value='" . $k . "' />\n";
            }
        }
        $ret .= $this->footer_listing();
        $ret .= $this->addFormEnd();
		$this->_value = $ret;
        if ( $return == true ) {
            return parent::render();
        } else {
            echo parent::render();
        }
    }

}

?>