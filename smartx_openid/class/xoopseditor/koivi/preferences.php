<?php
/**
 * Editor framework for XOOPS
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		Taiwen Jiang (phppp or D.J.) <php_pp@hotmail.com>
 * @since		1.00
 * @version		$Id$
 * @package		xoopseditor
 */
//FULL TOOLBAR OPTIONS
define("_XK_P_FULLTOOLBAR", "floating,fontname,fontsize,formatblock,insertsymbols,newline,undo,redo,cut,copy,paste,pastespecial,separator,spellcheck,print, separator,bold,italic,underline,strikethrough,removeformat,separator,justifyleft,justifycenter,justifyright,justifyfull,newparagraph,separator,ltr,rtl,separator,insertorderedlist,insertunorderedlist,indent,outdent,newline,forecolor,hilitecolor,superscript,subscript,separator,quote,code,inserthorizontalrule,insertanchor,insertdate,separator,createlink,unlink,separator,insertimage,imagemanager,imageproperties,separator,createtable,cellalign,cellborders,cellcolor,toggleborders,themecss,togglemode,separator");

//SMALL TOOLBAR OPTIONS
define("_XK_P_SMALLTOOLBAR", "fontsize,forecolor,hilitecolor,separator,bold,italic,underline,strikethrough,separator,quote,code,separator,createlink,insertimage,imagemanager");

//TEXT DIRECTION(ltr / rtl)
define("_XK_P_TDIRECTION", "ltr");

//SKIN (default / xp)
define("_XK_P_SKIN", "default");


//WIDTH
define("_XK_P_WIDTH","100%");

//HEIGHT
define("_XK_P_HEIGHT","400px");

if(!defined("XOOPS_ROOT_PATH")) {
	if(!function_exists("get_koivi_root_path")){
		function get_koivi_root_path()
		{
			static $koivi_root_path;
			if(!isset($koivi_root_path)){
				$current_path = dirname(__FILE__);
				if ( DIRECTORY_SEPARATOR != "/" ) $current_path = str_replace( DIRECTORY_SEPARATOR, "/", $current_path);
				$koivi_root_path = substr($current_path, 0, stripos($current_path, basename(dirname(__FILE__))));
			}
			return $koivi_root_path;
		}
	}
	
	include get_koivi_root_path()."/xoopseditor.inc.php";
	if(!defined("XOOPS_UPLOAD_PATH")){
		die("Path error!");
	}
}

//PATH
$current_path = __FILE__;
if ( DIRECTORY_SEPARATOR != "/" ) $current_path = str_replace( strpos( $current_path, "\\\\", 2 ) ? "\\\\" : DIRECTORY_SEPARATOR, "/", $current_path);
define("_XK_P_PATH", substr(dirname($current_path), strlen(XOOPS_ROOT_PATH)));
?>
