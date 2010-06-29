<?php
/**
 * Manage of images
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	core
 * @since	XOOPS
 * @author	http://www.xoops.org The XOOPS Project
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @version	$Id: image.php 19586 2010-06-24 11:48:14Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 *
 *
 * @package     kernel
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */

/**
 * An Image
 *
 * @package		kernel
 * @author		Kazumi Ono 	<onokazu@xoops.org>
 * @copyright	(c) 2000-2003 The Xoops Project - www.xoops.org
 */
class icms_image_Object extends icms_core_Object
{
	/**
	 * Info of Image file (width, height, bits, mimetype)
	 *
	 * @var array
	 */
	var $image_info = array();

	/**
	 * Constructor
	 **/
	function icms_image_Object()
	{
		parent::__construct();
		$this->initVar('image_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('image_name', XOBJ_DTYPE_OTHER, null, false, 30);
		$this->initVar('image_nicename', XOBJ_DTYPE_TXTBOX, null, true, 100);
		$this->initVar('image_mimetype', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('image_created', XOBJ_DTYPE_INT, null, false);
		$this->initVar('image_display', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('image_weight', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('image_body', XOBJ_DTYPE_SOURCE, null, true);
		$this->initVar('imgcat_id', XOBJ_DTYPE_INT, 0, false);
	}

	/**
	 * Function short description
	 *
	 * @param string  $path  the path to search through
	 * @param string  $type  the path type, url or other
	 * @param bool  $ret  return the information or keep it stored
	 *
	 * @return array  the array of image information
	 */
	function getInfo($path,$type='url',$ret=false){
		$path = (substr($path,-1) != '/')?$path.'/':$path;
		if ($type == 'url'){
			$img = $path.$this->getVar('image_name');
		}else{
			$img = $path;
		}
		$get_size = getimagesize($img);
		$this->image_info = array(
			'width' => $get_size[0],
			'height' => $get_size[1],
			'bits' => $get_size['bits'],
			'mime' => $get_size['mime']
		);
		if ($ret){
			return $this->image_info;
		}
	}
}
?>