<?php
// $Id: themeform.php,v 1.5 2007/05/09 14:14:27 catzwolf Exp $
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
 *
 * @package kernel
 * @subpackage form
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
/**
 * base class
 */
include_once ZAR_ROOT_PATH . "/class/zariliaform/form.php";

/**
 * Form that will output as a theme-enabled HTML table
 *
 * Also adds JavaScript to validate required fields
 *
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 * @package kernel
 * @subpackage form
 */
class ZariliaThemeForm extends ZariliaForm {
    /**
     * Insert an empty row in the table to serve as a seperator.
     *
     * @param string $extra HTML to be displayed in the empty row.
     * @param string $class CSS class name for <td> tag
     */
    function insertBreak( $extra = '', $class = '' )
    {
        $class = ( $class != '' ) ? " class='$class'" : '';
        if ( $extra ) {
            $extra = "<tr><th colspan='2' $class>$extra</th></tr>";
            $this->addElement( $extra );
        } else {
            $extra = "<tr><th colspan='2' $class>&nbsp;</th></tr>";
            $this->addElement( $extra );
        }
    }

    /**
     * ZariliaThemeForm::insertSplit()
     *
     * @param string $extra
     * @return
     */
    function insertSplit( $extra = '' )
    {
        if ( $extra ) {
            $extra = "
			  <tr>
			    <td colspan='2' class='foot'>&nbsp;</td>
			  </tr>
			</table><br />
			 <table width='100%' class='outer' cellspacing='1'>
			  <tr>
			   <th colspan='2'>$extra</th>
			  </tr>";
            $this->addElement( $extra );
        } else {
            $extra = "
			  <tr>
			   <td class='foot' colspan='2'>&nbsp;</td>
			  </tr>
			 </table>
			 <br />
			 <table width='100%' class='outer' cellspacing='1'>
			  <tr>
			   <th colspan='2'>&nbsp;</th>
			 </tr>";
            $this->addElement( $extra );
        }
    }

    /**
     * create HTML to output the form as a theme-enabled table with validation (without form tags)
     *
     * param   $elements array of elements
     *
     * @return string
     */
    function render_table( $elements )
    {
        global $zariliaOption, $zariliaTpl;
        $class = 'even';
        $ret = "<table width='100%' class='outer' cellspacing='1'>\n<tr>\n<th colspan='2'>" . $this->getTitle() . "</th>\n</tr>\n";
        foreach ( $elements as $ele ) {
            if ( !is_object( $ele ) ) {
                $ret .= $ele;
            } elseif ( !$ele->isHidden() ) {
                $colspan = ( $ele->getNocolspan() ) ? 1 : 0;
                $suppl = '';
                if ( $ele->getRequired() ) {
                    $suppl = ' *';
                }
                if ( $colspan == 1 ) {
                    $ret .= "
					 <tr valign='top' align='left'>
				      <td class='head' colspan='2'>" . $ele->getCaption() . "$suppl</td>
					 </tr>
                     <tr valign='top' align='left'>
	                  <td class='even' colspan='2'>" . $ele->render() . "</td>
					 </tr>
					";
                } else {
                    $ret .= "
						<tr valign='top' align='left'>
						<td class='head' width='35%'>" . $ele->getCaption() . "$suppl";
                    if ( $ele->getDescription() ) {
                        $ret .= '<p style="font-weight: normal;">' . $ele->getDescription() . '</p>';
                    }
                    $ret .= "
					  </td>\n
					  <td class='$class'>" . $ele->render() . "</td>\n
					</tr>\n";
                }
            } else {
                $ret .= $ele->render();
            }
        }
        // We have required fields - provide explanation for *
        $ret .= "<tr class='foot'>\n<td colspan='2'>* = " . _REQUIRED . "</td>\n</tr>\n";
        $ret .= "</table>\n";
        $ret .= "</div>\n";
        return $ret;
    }

    /**
     * create HTML to output the form as a theme-enabled table with validation.
     *
     * @return string
     */
    function render()
    {
        global $zariliaTpl, $zariliaOption;
/*        if ( !isset( $zariliaOption['multilanguage_loaded'] ) ) {
            $zariliaTpl->addScript( ZAR_URL . '/class/zariliaform/scripts/multilanguage.js' );
            $zariliaOption['multilanguage_loaded'] = true;
        }
        if ( !isset( $zariliaOption['multilanguage_forms_count'] ) ) {
            $zariliaOption['multilanguage_forms_count'] = 1;
        } else {
            $zariliaOption['multilanguage_forms_count']++;
        }*/
        $validation = $this->renderValidationJS( !false );
        $zariliaTpl->addScript( $validation, false );
        $forms = array( 'default' );
        if ( $this->_multilanguage ) {
            require_once ZAR_ROOT_PATH . '/class/mledit/mledit.php';
            $mledit = new MultiLanguageEditor();
            $ret = $mledit->render( $this, null, ( strval( $validation ) != '' ) );
        } else {
            $ret = "<form name='" . $this->getName() . "' id='" . $this->getName() . "' action='" . $this->getAction() . "' method='" . $this->getMethod() . "'";
            if ( $validation ) {
                $ret .= " onsubmit='return zariliaFormValidate_" . $this->getName() . "();'";
            }
            $ret .= " onsubmit=\"SubmitForm(this,'" . $this->getAction() . "');\"";
            $ret .= " " . $this->getExtra() . ">";
            $ret .= $this->render_table( $this->getElements() );
            $ret .= "</form><br />";
        }
        return $ret;
    }
}

?>