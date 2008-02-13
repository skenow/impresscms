<?php
// $Id$
// ------------------------------------------------------------------------ //
// XOOPS - PHP Content Management System                      				//
// Copyright (c) 2000 XOOPS.org                           					//
// <http://www.xoops.org/>                             						//
// ------------------------------------------------------------------------ //
// This program is free software; you can redistribute it and/or modify     //
// it under the terms of the GNU General Public License as published by     //
// the Free Software Foundation; either version 2 of the License, or        //
// (at your option) any later version.                                      //
// //
// You may not change or alter any portion of this comment or credits       //
// of supporting developers from this source code or any supporting         //
// source code which is considered copyrighted (c) material of the          //
// original comment or credit authors.                                      //
// //
// This program is distributed in the hope that it will be useful,          //
// but WITHOUT ANY WARRANTY; without even the implied warranty of           //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
// GNU General Public License for more details.                             //
// //
// You should have received a copy of the GNU General Public License        //
// along with this program; if not, write to the Free Software              //
// Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
// ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
* Class to integrate tinyeditor into XoopsEditors framework
*
* @author ralf57 based on koivi's class
* @copyright copyright (c) 2000-2003 XOOPS.org
* @package kernel
* @subpackage form
*/


class XoopsFormTinyeditorTextArea extends XoopsFormElement {

    var $value;
    var $name;
    var $width = "100%";
    var $height = "400px";
    var $url = "/modules/tinyeditor";
    var $xEditor;

	/**
	 * Constructor
	 *
     * @param	array   $configs  Editor Options
	 */
	function XoopsFormTinyeditorTextArea($configs) {

	    if(!empty($configs)) {
	        foreach($configs as $key => $val) {
	            if (method_exists($this, 'set'.Ucfirst($key))) {
	                $this->{'set'.Ucfirst($key)}($val);
	            } else {
	                $this->$key = $val;
	            }
	        }
	    }
	}

	function setConfig($configs) {
	    foreach($configs as $key=>$val){
			$this->$key = $val;
	    }
	}

    function getUrl() {
        return $this->url;
    }

    function setUrl($value) {
        $this->url = $value;
    }

    function getName() {
        return $this->name;
    }

    function setName($value) {
		$this->name = $value;
    }

    function getxEditor() {
    return $this->xEditor;
    }
    
	function setxEditor($value) {
      $this->xEditor = $value;
    }
    
    function getValue() {
        return $this->value;
    }

    function setValue($value) {
        $this->value = $value;
    }

	function setWidth($width) {
		if(!empty($width)){
			$this->width = $width;
		}
	}

	function getWidth() {
		return $this->width;
	}

    function setHeight($height)
    {
	    if(!empty($height)){
        	$this->height = $height;
    	}
    }
    function getHeight()
    {
        return $this->height;
    }

    /**
    * Prepare HTML for output
    *
    * @return string HTML
    */

	function render() {

		//global $xoopsUser, $xoopsModule, $_SERVER;
		global $_SERVER, $xoopsModule, $xoopsUser;

	$module_handler =& xoops_gethandler('module');
	$module =& $module_handler->getByDirname("tinyeditor");
	$config_handler =& xoops_gethandler('config');
	$moduleConfig =& $config_handler->getConfigsByCat(0, $module->getVar('mid'));
	$groups = (is_object($xoopsUser)) ? $xoopsUser->getGroups() : XOOPS_GROUP_ANONYMOUS;
	$module_id = $module->getVar('mid');

	$gperm_handler = &xoops_gethandler('groupperm');

		$compatible = false;

		if (eregi("opera/9.",$_SERVER['HTTP_USER_AGENT'])) {
			$compatible = true;
		} elseif (eregi("msie",$_SERVER['HTTP_USER_AGENT'])) {
			$val = explode(" ",stristr($_SERVER['HTTP_USER_AGENT'],"msie"));
			if((float)str_replace(";","",$val[1])>=5.5) $compatible = true;
		} elseif (eregi("mozilla",$_SERVER['HTTP_USER_AGENT'])) {
			$compatible = true;
		} elseif (eregi("safari", $_SERVER['HTTP_USER_AGENT'])) {
			$compatible = true;
		} elseif (eregi("netscape",$_SERVER['HTTP_USER_AGENT'])) {
			$val = explode("Netscape/",$_SERVER['HTTP_USER_AGENT']);
			$version = str_replace(" (ax)","",$val[1]);
			if ($version >= 7.1) $compatible = true;
		} else {
			$compatible = false;
		}

		//include_once XOOPS_ROOT_PATH.'/modules/tinyeditor/include/tinygroupperm.php';

		if ($gperm_handler->checkRight('TinyPerm', 3, $groups, $module_id) && $compatible == true) {
	//if ($compatible == true) {
								if ($this->getxEditor() == 1 || $this->getxEditor() == '') {
        include XOOPS_ROOT_PATH .''.$this->getUrl().'/include/initcode.php';
        } else {
        include_once XOOPS_ROOT_PATH.''.$this->getUrl().'/include/reducedinitcode.php';
        }

		$url = XOOPS_URL.''.$this->getUrl();
		// this is sooooo dirty and ugly, but the xoops-validation-script never gets the correct content, so I had to add a blank at the end of the textarea
		$form = "<textarea id=\"".$this->getName()."\" name=\"".$this->getName()."\" rows=\"1\" cols=\"1\" style=\"width:".$this->getWidth()."; height:".$this->getHeight()."\" class=\"mceEditor\">".$this->getValue()." </textarea>";
		} else {
			$hiddenText = "xoopsHiddenTexttinyed";
			$form = "<a name='moresmiley'></a><img onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/url.gif' alt='url' onclick='xoopsCodeUrl(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERWEBTITLE, ENT_QUOTES)."\");' />&nbsp;<img onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/email.gif' alt='email' onclick='javascript:xoopsCodeEmail(\"".$this->getName()."\", \"".htmlspecialchars(_ENTEREMAIL, ENT_QUOTES)."\");' />&nbsp;<img onclick='javascript:xoopsCodeImg(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERIMGURL, ENT_QUOTES)."\", \"".htmlspecialchars(_ENTERIMGPOS, ENT_QUOTES)."\", \"".htmlspecialchars(_IMGPOSRORL, ENT_QUOTES)."\", \"".htmlspecialchars(_ERRORIMGPOS, ENT_QUOTES)."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/imgsrc.gif' alt='imgsrc' />&nbsp;<img onmouseover='style.cursor=\"hand\"' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/imagemanager.php?target=".$this->getName()."\",\"imgmanager\",400,430);' src='".XOOPS_URL."/images/image.gif' alt='image' />&nbsp;<img src='".XOOPS_URL."/images/code.gif' onmouseover='style.cursor=\"hand\"' alt='code' onclick='javascript:xoopsCodeCode(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERCODE, ENT_QUOTES)."\");' />&nbsp;<img onclick='javascript:xoopsCodeQuote(\"".$this->getName()."\", \"".htmlspecialchars(_ENTERQUOTE, ENT_QUOTES)."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/quote.gif' alt='quote' /><br />\n";

		$sizearray = array("xx-small", "x-small", "small", "medium", "large", "x-large", "xx-large");
		$form .= "<select id='".$this->getName()."Size' onchange='setVisible(\"".$hiddenText."\");setElementSize(\"".$hiddenText."\",this.options[this.selectedIndex].value);'>\n";
		$form .= "<option value='SIZE'>"._SIZE."</option>\n";
		foreach ( $sizearray as $size ) {
			$form .=  "<option value='$size'>$size</option>\n";
		}
		$form .= "</select>\n";
		$fontarray = array("Arial", "Courier", "Georgia", "Helvetica", "Impact", "Verdana");
		$form .= "<select id='".$this->getName()."Font' onchange='setVisible(\"".$hiddenText."\");setElementFont(\"".$hiddenText."\",this.options[this.selectedIndex].value);'>\n";
		$form .= "<option value='FONT'>"._FONT."</option>\n";
		foreach ( $fontarray as $font ) {
			$form .= "<option value='$font'>$font</option>\n";
		}
		$form .= "</select>\n";
		$colorarray = array("00", "33", "66", "99", "CC", "FF");
		$form .= "<select id='".$this->getName()."Color' onchange='setVisible(\"".$hiddenText."\");setElementColor(\"".$hiddenText."\",this.options[this.selectedIndex].value);'>\n";
		$form .= "<option value='COLOR'>"._COLOR."</option>\n";
		foreach ( $colorarray as $color1 ) {
			foreach ( $colorarray as $color2 ) {
				foreach ( $colorarray as $color3 ) {
					$form .= "<option value='".$color1.$color2.$color3."' style='background-color:#".$color1.$color2.$color3.";color:#".$color1.$color2.$color3.";'>#".$color1.$color2.$color3."</option>\n";
				}
			}
		}
		$form .= "</select><span id='".$hiddenText."'>"._EXAMPLE."</span>\n";
		$form .= "<br />\n";
		$form .= "<img onclick='javascript:setVisible(\"".$hiddenText."\");makeBold(\"".$hiddenText."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/bold.gif' alt='bold' />&nbsp;<img onclick='javascript:setVisible(\"".$hiddenText."\");makeItalic(\"".$hiddenText."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/italic.gif' alt='italic' />&nbsp;<img onclick='javascript:setVisible(\"".$hiddenText."\");makeUnderline(\"".$hiddenText."\");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_URL."/images/underline.gif' alt='underline' />&nbsp;<img onclick='javascript:setVisible(\"".$hiddenText."\");makeLineThrough(\"".$hiddenText."\");' src='".XOOPS_URL."/images/linethrough.gif' alt='linethrough' onmouseover='style.cursor=\"hand\"' />&nbsp;&nbsp;<input type='text' id='".$this->getName()."Addtext' size='20' />&nbsp;<input type='button' onclick='xoopsCodeText(\"".$this->getName()."\", \"".$hiddenText."\", \"".htmlspecialchars(_ENTERTEXTBOX, ENT_QUOTES)."\")' class='formButton' value='"._ADD."' /><br /><br /><textarea id='".$this->getName()."' name='".$this->getName()."' onselect=\"xoopsSavePosition('".$this->getName()."');\" onclick=\"xoopsSavePosition('".$this->getName()."');\" onkeyup=\"xoopsSavePosition('".$this->getName()."');\" cols='50' rows='20' ".$this->getExtra()." style='width:100%; height: 400px;'>".$this->getValue()."</textarea><br />\n";
		$form .= $this->_renderSmileys();
		}
		return $form;

	}

function _renderSmileys()
	{
		$myts =& MyTextSanitizer::getInstance();
		$smiles =& $myts->getSmileys();
		$ret = '';
		if (empty($smileys)) {
			$db =& Database::getInstance();
			if ($result = $db->query('SELECT * FROM '.$db->prefix('smiles').' WHERE display=1')) {
				while ($smiles = $db->fetchArray($result)) {
					$ret .= "<img onclick='xoopsCodeSmilie(\"".$this->getName()."\", \" ".$smiles['code']." \");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_UPLOAD_URL."/".htmlspecialchars($smiles['smile_url'], ENT_QUOTES)."' alt='' />";
				}
			}
		} else {
			$count = count($smiles);
			for ($i = 0; $i < $count; $i++) {
				if ($smiles[$i]['display'] == 1) {
					$ret .= "<img onclick='xoopsCodeSmilie(\"".$this->getName()."\", \" ".$smiles[$i]['code']." \");' onmouseover='style.cursor=\"hand\"' src='".XOOPS_UPLOAD_URL."/".$myts->oopsHtmlSpecialChars($smiles['smile_url'])."' border='0' alt='' />";
				}
			}
		}
		$ret .= "&nbsp;[<a href='#moresmiley' onclick='javascript:openWithSelfMain(\"".XOOPS_URL."/misc.php?action=showpopups&amp;type=smilies&amp;target=".$this->getName()."\",\"smilies\",300,475);'>"._MORE."</a>]";
		return $ret;
	}

}

?>