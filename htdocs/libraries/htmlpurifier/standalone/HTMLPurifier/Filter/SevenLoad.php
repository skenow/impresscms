<?php

class HTMLPurifier_Filter_SevenLoad extends HTMLPurifier_Filter
{
    
    public $name = 'SevenLoad';
    
    public function preFilter($html, $config, $context) {
		$pre_regex = '#<object[^>]+>.+?'.'http://([a-z]{2}).sevenload.com/pl/([A-Za-z0-9\-_])/(+/\d+x\d+)/swf.+?</object>#s';
		$pre_replace = '<span class="sevenload-embed">\2</span>';
		return preg_replace($pre_regex, $pre_replace, $html);
    }
    
    public function postFilter($html, $config, $context) {
        $post_regex = '#<span class="wegame-embed">([A-Za-z0-9\-_]+)</span>#';
        $post_replace = '<object width="500" height="408" '.
            'data="http://www.sevenload.com/pl/\1swf">'.
            '<param name="movie" value="http://www.sevenload.com/pl/\1/swf"></param>'.
            '<param name="wmode" value="transparent"></param>'.
            '<!--[if IE]>'.
            '<embed src="http://www.sevenload.com/pl/\1/swf"'.
            'type="application/x-shockwave-flash"'.
            'wmode="transparent" width="500" height="408" />'.
            '<![endif]-->'.
            '</object>';
        return preg_replace($post_regex, $post_replace, $html);
    }
    
}