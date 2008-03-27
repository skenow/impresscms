<?php
// $Id: formbuttontray.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
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
 * ZariliaFormButtonTray
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: formbuttontray.php,v 1.1 2007/03/16 02:40:58 catzwolf Exp $
 * @access public
 **/
class ZariliaFormButtonTray extends ZariliaFormElement {
    /**
     * Value
     *
     * @var string
     * @access private
     */
    var $_value;

    /**
     * Type of the button. This could be either "button", "submit", or "reset"
     *
     * @var string
     * @access private
     */
    var $_type;

    /**
     * ZariliaFormButtonTray::ZariliaFormButtonTray()
     *
     * @param mixed $name
     * @param string $value
     * @param string $type
     * @param string $onclick
     **/
    function ZariliaFormButtonTray( $name, $value = "", $type = "submit", $onclick = "" ) {
        $this->setName( $name );
        $this->setValue( $value );
        $this->_type = $type;
        if ( $onclick ) {
            $this->setExtra( $onclick );
        } else {
            $this->setExtra( '' );
        }
    }

    /**
     * ZariliaFormButtonTray::getValue()
     *
     * @return
     **/
    function getValue() {
        return $this->_value;
    }

    /**
     * ZariliaFormButtonTray::setValue()
     *
     * @param mixed $value
     * @return
     **/
    function setValue( $value ) {
        $this->_value = $value;
    }

    /**
     * ZariliaFormButtonTray::getType()
     *
     * @return
     **/
    function getType() {
        return $this->_type;
    }

    /**
     * ZariliaFormButtonTray::render()
     *
     * @return
     **/
    function render() {
        $ret = '
			<input type="button" class="formbutton"  name="cancel"  id="cancel" value="' . _CANCEL . '" onClick="history.go(-1);return true;" />
			<input type="reset" class="formbutton"  name="reset"  id="reset" value="' . _RESET . '"  />
			<input type="' . $this->getType() . '" class="formbutton"  name="' . $this->getName() . '"  id="' . $this->getName() . '" value="' . $this->getValue() . '"' . $this->getExtra() . '  />';
    	return $ret;
	}
}

?>