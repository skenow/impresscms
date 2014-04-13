<?php
/**
 * -------------------------------------------------------------------------------------
 * Smarty plugin for xoops : outputfilter_xoRewriteModule.php
 *
 * Type			: outputfilter
 * Name			: xoRewriteModule
 * Version		: 1.0
 * Author:		: DuGris <http://www.dugris.info>
 * Purpose		: Url rewritting for modules/dirname
 * -------------------------------------------------------------------------------------
**/
include_once ICMS_ROOT_PATH . '/libraries/smarty/icms_plugins/xoSmartyFunctions.php';

function smarty_outputfilter_xoRewriteModule( $source, &$smarty ) {
	$xoRewriteModule = XoSmartyPluginGetSection( 'xoRewriteModule' );
	if ( !empty($xoRewriteModule) )  {
		foreach( $xoRewriteModule as $key => $value ) {
			$_SERVER['REQUEST_URI'] = str_replace( $value , '/modules/'.$key , @$_SERVER['REQUEST_URI'] ) ;
			$_SERVER['HTTP_REFERER'] = str_replace( $value , '/modules/'.$key , @$_SERVER['HTTP_REFERER'] ) ;
			$pattern[] = "~" . XOOPS_URL . "/modules/" . $key . "/~sUi";
			$replacements[] = XOOPS_URL . "/" . $value . "/";
			// except admin folder
			$pattern[] = "~" . XOOPS_URL . "/" . $value . "/admin/" . "~sUi";
			$replacements[] = XOOPS_URL . "/modules/" . $key . "/admin/";
		}
		//$GLOBALS['xoopsLogger']->addExtra("plugin smarty for xoops => xoRewriteModule ", "Loaded");
		$source = preg_replace($pattern, $replacements, $source);

		// works only for local website : write .htaccess file
		if ( !isset($_SESSION['xoRewriteModule']) || $_SESSION['xoRewriteModule'] != $xoRewriteModule ) {
			$_SESSION['xoRewriteModule'] = $xoRewriteModule ;
			$xoRewriteHtaccess = XoSmartyPluginGetSection( 'xoRewriteHtaccess' );
			if ( !empty($xoRewriteHtaccess) )  {
				foreach ($xoRewriteHtaccess as $key => $value) {
					if ( getenv(strtoupper($key)) == $value ) {
						xoRewriteModule_WriteHtaccess( $xoRewriteModule );
						break;
					}
				}
			}
		}
		// works only for local website : write .htaccess file
	}

	return $source;
}

function xoRewriteModule_WriteHtaccess( $xoRewriteModule ) {
	$htaccess = array();
	$htaccess = file(ICMS_ROOT_PATH . '/.htaccess');
	$start = array_search("#Xoops : Start xoRewriteModule\n", $htaccess);
	$end = array_search("#Xoops : End xoRewriteModule\n", $htaccess);

	$content[] = "\n#Icms : Start xoRewriteModule\n";
	$content[] = "RewriteEngine on\n";
	foreach( $xoRewriteModule as $key => $value ) {
		$tmp = "RewriteRule ^" . $value . "/(.*)$ modules/" . $key . "/$1 [L]\n";
		if ( ($cpt = array_search($tmp, $content)) == 0){
			$content[] = $tmp;
		}
	}
	$content[] = "#Icms : End xoRewriteModule\n";

	if ( $end != 0 ) {
		$replace = array_splice($htaccess, $start, ($end-$start+1), $content);
		file_put_contents(ICMS_ROOT_PATH . "/.htaccess", $htaccess);
	} else {
		if ( !empty($htaccess) ) {
			array_push ( $htaccess, implode("", $content) );
			file_put_contents(ICMS_ROOT_PATH . "/.htaccess", $htaccess);
		} else {
			file_put_contents(ICMS_ROOT_PATH . "/.htaccess", $content);
		}
	}
	//$GLOBALS['xoopsLogger']->addExtra("plugin smarty for xoops => xoRewriteModule ", "Write htaccess Loaded");
}
?>