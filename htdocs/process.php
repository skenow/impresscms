<?php

if (get_magic_quotes_gpc()) {
    $GLOBALS['clean_func'] = function ($value) {
        return is_array($value)?array_map($GLOBALS['clean_func'], $value):stripslashes($value);
    };
    foreach(array('_GET', '_REQUEST', '_POST') as $name)
        $$name = $GLOBALS['clean_func']($$name);
    unset($GLOBALS['clean_func'], $$name);
}

define('ICMS_SKIP_CHECK_SITEOFFLINE', 1);

include "mainfile.php";

$action_handler = icms::handler('icms_action');

if (isset($_SERVER['HTTP_ICMS_ENCODING'])) {
    $_SERVER['HTTP_ICMS_REQUEST'] = base64_decode($_SERVER['HTTP_ICMS_REQUEST']);
    switch ($_SERVER['HTTP_ICMS_ENCODING']) {
        case 'zlib':
        case 'zlib/inflate':
            $_SERVER['HTTP_ICMS_REQUEST'] = gzinflate($_SERVER['HTTP_ICMS_REQUEST']);
        break;
        case 'zlib/compress':
            $_SERVER['HTTP_ICMS_REQUEST'] = gzuncompress($_SERVER['HTTP_ICMS_REQUEST']);
        break;
        case 'bzip2':
        case 'bzip2/compress':
            $_SERVER['HTTP_ICMS_REQUEST'] = bzdecompress($_SERVER['HTTP_ICMS_REQUEST']);
        break;
        case 'lzf':
        case 'lzf/compress':
            $_SERVER['HTTP_ICMS_REQUEST'] = lzf_decompress($_SERVER['HTTP_ICMS_REQUEST']);
        break;
    }
} else {
    $_SERVER['HTTP_ICMS_REQUEST'] = json_encode($_REQUEST);
}

$data = json_decode($_SERVER['HTTP_ICMS_REQUEST'], true);
$format = isset($data['format'])?$data['format']:'json';
$show_headers = isset($data['show_headers'])?(bool)intval($data['show_headers']):false;
$logging_enabled = isset($data['logging_enabled'])?(bool)intval($data['logging_enabled']):false;
$base_controls = isset($data['base_controls'])?$data['base_controls']:array();

$action_handler->output_format = $format;
if ($show_headers)
    $action_handler->includeHeadersInResponse();

$member_handler = icms::handler('icms_member');
$group = $member_handler->getUserBestGroup((icms::$user instanceof icms_member_user_Object) ? icms::$user->getVar('uid') : 0);

if (isset($data['actions']))
    $action_handler->execActionFromArray($data['actions']);

if (!empty($base_controls)) {
    $controls_handler = icms::handler('icms_controls');
    $required = array();
    $diff = array_diff($controls_handler::$renderedControlTypes, $base_controls);

    if (is_array($diff) && !empty($diff)) {
        foreach ($diff as $m_type) {
            $ctl = $controls_handler->make($m_type);
            foreach ($ctl->getRequiredURLs() as $type => $urls)
                if (isset($required[$type]))
                    $required[$type] = array_merge($required[$type], $urls);
                else
                    $required[$type] = $urls;
        }
        unset($m_type, $urls);
    }

    if (!empty($required))
        $action_handler->response->add('load_files', $required);
    unset($diff, $required);
}

if ($logging_enabled)
    $action_handler->includeLoggingInfoInResponse();

$content = $action_handler->render();

//$_SERVER['HTTP_ICMS_ENCODING'] = 'zlib';

if (isset($_SERVER['HTTP_ICMS_ENCODING'])) {   
    switch ($_SERVER['HTTP_ICMS_ENCODING']) {
        case 'zlib':
        case 'zlib/inflate':           
             $content = base64_encode(gzdeflate($content));
        break;
        case 'zlib/compress':
            $content = base64_encode(gzcompress($content));
        break;
        case 'bzip2':
        case 'bzip2/compress':
            $content = base64_encode(bzcompress($content));
        break;
        case 'lzf':
        case 'lzf/compress':
            $content = base64_encode(lzf_compress($content));
        break;
    }
}

echo $content;