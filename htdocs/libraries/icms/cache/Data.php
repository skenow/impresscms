<?php

class icms_cache_Data {
    
    public static function save($module, $itemname, $dataname, $data) {
        if (!$data)
            return false;        
        $filename = ICMS_CACHE_PATH . '/data/' . $module . '/' . $itemname;
        if (!file_exists($filename))
            mkdir($filename, 0777, true);        
        $filename .= '/' . sha1($dataname) . '-' . strlen($dataname);
        
        file_put_contents($filename  .  '.dat', json_encode($data));
	if (!file_exists($filename . '.nfo'))
	    file_put_contents($filename  .  '.nfo', $dataname);
    return true;
    }
    
    public static function get($module, $itemname, $dataname, $lifetime = -1) {
        $filename = ICMS_CACHE_PATH . '/data/' . $module . '/' . $itemname . '/' . sha1($dataname) . '-' . strlen($dataname) . '.dat';
        if (!file_exists($filename))
            return null;
        return json_decode(file_get_contents($filename), true);
    }
    
}