<?php
// $Id: formselectimg.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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

/**
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */

/**
 * A select field
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaFormSelectImg extends ZariliaFormSelect {
    var $_name;
    var $_value = array();
    var $_id;
    var $_imgcat_id;

    var $_category = '/';
    var $_options = array();
    var $_multiple = false;
    var $_size = 10;
    /**
     * Constructor
     *
     * @param string $caption Caption
     * @param string $name "name" attribute
     * @param mixed $value Pre-selected value (or array of them).
     * @param int $size Number or rows. "1" makes a drop-down-list
     * @param bool $multiple Allow multiple selections?
     */
    function ZariliaFormSelectImg( $caption, $name, $value = null, $id = 'zarilia_image', $imgcat_id, $size = 5 ) {
        $this->setCaption( $caption );
        $this->_name = $name;
        if ( isset( $value ) ) {
            $this->setValue( $value );
        }
        $this->_id = $id ;
        $this->_imgcat_id = $imgcat_id;
        $this->_size = $size;
    }

    /**
     * Are multiple selections allowed?
     *
     * @return bool
     */
    function isMultiple() {
        return $this->_multiple;
    }

    /**
     * Get the name
     *
     * @return int
     */
    function getName() {
        return $this->_name;
    }

    /**
     * Get the size
     *
     * @return int
     */
    function getSize() {
        return $this->_size;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    function getCategory() {
        return $this->_category;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    function setCategory( $value ) {
        return $this->_category = $value;
    }

    /**
     * Set Category
     *
     * @return int
     */
    function getDir() {
        return $this->_dir;
    }

    /**
     * Set Category
     *
     * @return int
     */
    function setDir( $value ) {
        return $this->_dir = $value;
    }

    /**
     * Get an array of pre-selected values
     *
     * @return array
     */
    function getImgcat_id() {
        return $this->_imgcat_id;
    }

    /**
     * Set pre-selected values
     *
     * @param  $value mixed
     */
    function setValue( $value ) {
        $this->_value = $value;
    }

    /**
     * ZariliaFormSelectImg::getImage()
     *
     * @return
     */
    function getImage() {
        $value = $this->getValue();
        $image = explode( '|', $value );
        return ( is_array( $image ) ) ? $image : $value;
    }
    /**
     * Add an option
     *
     * @param string $value "value" attribute
     * @param string $name "name" attribute
     */
    function addOption( $value, $name = "" ) {
        if ( $name != "" ) {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }

    /**
     * Add multiple options
     *
     * @param array $options Associative array of value->name pairs
     */
    function addOptionArray( $options ) {
        if ( is_array( $options ) ) {
            foreach ( $options as $k => $v ) {
                $this->addOption( $k, $v );
            }
        }
    }

    /**
     * Get all options
     *
     * @return array Associative array of value->name pairs
     */
    function getOptions() {
        return $this->_options;
    }

    /**
     * Prepare HTML for output
     *
     * @return string HTML
     */
    function render() {
        if ( $this->_imgcat_id > 0 && $useimagemanger = 0 ) {
            $image_handler = &zarilia_gethandler( 'image' );
            $imgcat_handler = &zarilia_gethandler( 'imagecategory' );
            $image_cat_obj = $imgcat_handler->get( $this->_imgcat_id );
            if ( $image_cat_obj ) {
                $art_image_array = $image_handler->getList( $this->_imgcat_id, null, 'image' );
                $this->setCategory( $image_cat_obj->getVar( 'imgcat_dirname' ) );
            } else {
                $art_image_array = ZariliaLists::getImgListAsArray( ZAR_UPLOAD_PATH . '/' . $this->getCategory() );
            }
        } else {
            $art_image_array = ZariliaLists::getImgListAsArray( ZAR_UPLOAD_PATH . '/' . $this->getCategory() );
        }

        $image_array = array();
        if ( $this->getValue() ) {
            $image_array = explode( '|', $this->getValue() );
            if ( count( $image_array ) == 1 ) {
                $image_size = @getimagesize ( ZAR_UPLOAD_PATH . '/' . $this->getCategory() . '/' . $this->getValue() );
                $image_array[0] = $this->getValue();
                $image_array[1] = ( $image_size[0] > 300 ) ? '300' : $image_size[0];
                $image_array[2] = ( $image_size[1] > 250 ) ? '250' : $image_size[1];
            }
        } else {
            $this->setValue('');
			$image_array[0] = '';
            $image_array[1] = 0;
            $image_array[2] = 0;
        }

        $ret = "
         <table border='0' width='100%' cellspacing='0' cellpadding='0' >
          <tr>
           <td width='35%' valign='top' align='left'>";
        $ret .= "<select ";
        if ( $this->isMultiple() != false ) {
            $ret .= " name='" . $this->getName() . "[]' id='" . $this->getName() . "[]' multiple='multiple' ";
        } else {
            $ret .= " name='" . $this->getName() . "' id='" . $this->getName() . "' ";
        }
		$ret .= " onchange='chooseImage(this, \"" . $this->_id . "\", \"" . ZAR_UPLOAD_URL . '/' . $this->getCategory() . "\", \"\")'>";

		/**
         */
        $ret .= " onchange='chooseImage(this, \"" . $this->_id . "\", \"" . ZAR_UPLOAD_URL . '/' . $this->getCategory() . "\", \"\")'>";
		$result = array_merge( array( '' => 'No Selection' ), $art_image_array );
        foreach ( $result as $value => $name ) {
			$image_name = explode( '.', $name );
			$imagesize2 = @getimagesize( ZAR_UPLOAD_PATH . "/" . $this->getCategory() . "/" . $value );
            $imagewidth = ( $imagesize2[0] > 300 ) ? '300' : $imagesize2[0];
            $imageheight = ( $imagesize2[1] > 250 ) ? '250' : $imagesize2[1];
            unset( $imagesize );

            $ret .= "<option value='" . htmlspecialchars( $value, ENT_QUOTES ) . "|$imagewidth|$imageheight'";
            if ( !$value || !isset( $value ) || empty( $value ) ) {
                $value = '';
            }
            if ( trim( $value ) == $image_array[0] ) {
                $ret .= " selected='selected'";
            }
            $ret .= ">" . $image_name[0] . "</option>\n";
        }
		/**/
		$image = $image_array[0];
		$image_display = ZAR_UPLOAD_URL . '/' . $this->getCategory() . '/' . $image_array[0];
		$ret .= "	</select></div><br /><br />
			<div id=\"" . $this->_id . "\" style=\"padding: 10px; text-align: center; border:1px solid black;\">
			  <img src='" . ZAR_UPLOAD_URL . "/" . $this->getCategory() . "/" . $image_array[0] . "' onclick='openWithSelfMain(\"".ZAR_UPLOAD_URL . "/" . $this->getCategory() . "/" . $image_array[0]."\",\"image\" );' align='absmiddle' width='{$image_array[1]}' height='{$image_array[2]}' />
			 </div>
			</td>
		   </tr>
		  </table>";
        return $ret;
    }
}

?>