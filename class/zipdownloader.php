<?php
// $Id: zipdownloader.php,v 1.1 2007/03/16 02:38:59 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
include_once ZAR_ROOT_PATH.'/class/downloader.php';
include_once ZAR_ROOT_PATH.'/class/class.zipfile.php';

class ZariliaZipDownloader extends ZariliaDownloader
{
	function ZariliaZipDownloader($ext = '.zip', $mimyType = 'application/x-zip')
	{
		$this->archiver = new zipfile();
		$this->ext      = trim($ext);
		$this->mimeType = trim($mimyType);
	}

	function addFile($filepath, $newfilename=null)
	{
		// Read in the file's contents
		$fp = fopen($filepath, "r");
		$data = fread($fp, filesize($filepath));
		fclose($fp);
		$filename = (isset($newfilename) && trim($newfilename) != '') ? trim($newfilename) : $filepath;
		$this->archiver->addFile($data, $filename, filemtime($filename));
	}

	function addBinaryFile($filepath, $newfilename=null)
	{
		// Read in the file's contents
		$fp = fopen($filepath, "rb");
		$data = fread($fp, filesize($filepath));
		fclose($fp);
		$filename = (isset($newfilename) && trim($newfilename) != '') ? trim($newfilename) : $filepath;
		$this->archiver->addFile($data, $filename, filemtime($filename));
	}

	function addFileData(&$data, $filename, $time=0)
	{
		$this->archiver->addFile($data, $filename, $time);
	}

	function addBinaryFileData(&$data, $filename, $time=0)
	{
		$this->addFileData($data, $filename, $time);
	}

	function download($name, $gzip = true)
	{
		$this->_header( $name.$this->ext );
		echo $this->archiver->file();
	}
}
?>