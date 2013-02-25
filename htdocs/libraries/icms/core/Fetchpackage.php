<?php
/**
 * Created by JetBrains PhpStorm.
 * User: David
 * Date: 25/02/13
 * Time: 16:33
 * To change this template use File | Settings | File Templates.
 */

defined('ICMS_ROOT_PATH') or die("ImpressCMS root path not defined");

class icms_core_fetchpackage
{
    public $remote_file;
    public $file_name;
    public $local_file;
    public $dlcache_folder;

    /**
     * Retrieve the remote file
     * @return bool
     * @throws ErrorException
     * @throws Exception
     */
    public function fetch_remote_file()
    {

        try{
            $local_file = $dlcache_folder . '/' . basename($remote_file);
            $fp = fopen($local_file, 'w');
            if ($fp == FALSE){throw new Exception('fetch_remote_file: Could not create file.')}
            $ch = curl_init($remote_file);
            if (curl_errno($ch) > 0)
            {throw new Exception ('fetch_remote_file: cUrl init failed.');}
            curl_setopt($ch, CURLOPT_FILE, $fp);
            $data = curl_exec($ch);
            if (curl_errno($ch) > 0) {throw new Exception ('fetch_remote_file: Download of ' . $remote_file . ' failed. (cUrl Errno:'. curl_errno($ch) .')'); }
            curl_close($ch);
            fclose($fp);

            return TRUE;
        }

        catch(Exception $e)
        {
            $response_array['status'] = 'error';
            $response_array['msg'] = $e->getMessage();

            return FALSE;
        }

    }

    /**
     * Uncompresses the archive file in its current location.
     * @todo check if the module folder of the current archive already exists, and remove before
     * unpacking the new one. We don't want to merge two versions!
     * @param $local_file the location of the file that needs to be unzipped in the same folder
     * @return bool
     * @throws Exception
     */
    public function uncompress_file()
    {
    try {
    if (file_exists($filename)) {
    $zip = new ZipArchive;
    if ($zip->open($filename) <> TRUE) {throw new Exception('Zip file failed');}

    $zip->extractTo($extract_location . '/packages');
    $zip->close();
    }
    else{throw new Exception('uncompress_file: Zip file does not exist');}
    }
    catch(Exception $e)
    {
    $response_array['status'] = 'error';
    $response_array['msg'] = $e->getMessage();

    return FALSE;
    }
    }

    /**
     * Verify the validity of the file by checking for the hash signature.
     * @param $local_file
     * @param $verification_hash
     * @return bool
     * @throws Exception
     */
    public function verify_hash($local_file, $verification_hash)
    {
    try{
    $local_hash = sha1_file($local_file);
    if ($local_hash <> $verification_hash)
    { throw new Exception('Hash incorrect (original: ' . $verification_hash . ', local file: ' . $local_hash . ')');
    }
    else
    {
    return TRUE;
    }
    }
    catch(Exception $e)
    {
    $response_array['status'] = 'error';
    $response_array['msg'] = $e->getMessage();

    return FALSE;
    }
    }

    public function __construct()
    {
        echo 'check';
        //$this->dlcache_folder = "ICMS_TRUST_PATH" . '/dlcache';
/*        try{
        if (!is_writable($this->dlcache_folder))
        {
            throw new Exception("download cache below trustpath is not writable")
        }}
        catch (Exception $e){
            echo $e->getMessage() . " \n";
        }
     */
    }

}
