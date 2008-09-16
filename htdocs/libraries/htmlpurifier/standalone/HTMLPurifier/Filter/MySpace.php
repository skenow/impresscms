<?php

class HTMLPurifier_Filter_MySpace extends HTMLPurifier_Filter
{
    
    public $name = 'MySpace';
    
    public function preFilter($html, $config, $context) {
		$pre_regex = '#<embed\s+src="http://([A-Za-z0-9\-_]+).myspace.com/services/media/embed,aspx/m=([0-9]+)"></embed>#s';
		$pre_replace = '<span class="myspace-embed">\2</span>';
		return preg_replace($pre_regex, $pre_replace, $html);
    }
    
    public function postFilter($html, $config, $context) {
        $post_regex = '#<span class="myspace-embed">([0-9]+)</span>#';
        $post_replace = '<object width="425" height="360" '.
            'data="http://mediaservices.myspace.com/services/media/embed.aspx/m=\1,t=1">'.
            '<param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m=\1,t=1"></param>'.
            '<param name="wmode" value="transparent"></param>'.
            '<!--[if IE]>'.
            '<embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m=\1,t=1"'.
            'type="application/x-shockwave-flash"'.
            'wmode="transparent" width="425" height="360" />'.
            '<![endif]-->'.
            '</object>';
        return preg_replace($post_regex, $post_replace, $html);
    }
    
}