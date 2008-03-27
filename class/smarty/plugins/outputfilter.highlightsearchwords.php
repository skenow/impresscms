<?php
/*
 * Smarty plugin
 *
 * Author:   André Fielder < mail [at] andrefiedler [dot] de >
 *
 * -------------------------------------------------------------
 * File:     outputfilter.highlight_search_words.php
 * Type:     outputfilter
 * Name:     highlight_search_words
 * Purpose:  Highlights words whitch were searched thrue an
 *           searchengine below. A css class named searchWords 
 *           musst be available.
 * -------------------------------------------------------------
 *
 * @version 1.0
 * @copyright 2005, André Fiedler
 */

function smarty_outputfilter_highlight_search_words($output, &$smarty) {
	$search_engines = array();
	
	//---------------ENGINE-------VAR-NAME--------
	$search_engines['google']     = 'q';
	$search_engines['yahoo']      = 'p';
	$search_engines['lycos']      = 'query';
	$search_engines['altavista']  = 'q';
	$search_engines['alltheweb']  = 'q';
	$search_engines['excite']     = 'search';
	$search_engines['msn']        = 'q';
	//--------------------------------------------
	
	$url = parse_url($_SERVER['HTTP_REFERER']);
	foreach($search_engines as $engine_name => $var_name) {
		if(preg_match("/($engine_name)/i", $url['host'])) {
		   parse_str($url['query'], $query_vars); 
		   $words = explode(' ', urldecode($query_vars[$var_name]));
		   foreach($words as $k => $word) {
				if(trim($word) != '') {
					$pattern[$k] = "/((<[^>]*)|$word)/ie";
					$replace[$k] = '"\2"=="\1"? "\1":"<span class=\"searchWords\">\1</span>"';
				}
			}
			$output = preg_replace($pattern, $replace, $output); 	  	
		}
	}	
	return $output;
}
?>