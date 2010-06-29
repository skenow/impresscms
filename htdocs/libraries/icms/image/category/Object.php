<?php
/**
 * Manage of Image categories
 *
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license	LICENSE.txt
 * @package	core
 * @since	XOOPS
 * @author	http://www.xoops.org The XOOPS Project
 * @author	modified by UnderDog <underdog@impresscms.org>
 * @version	$Id: imagecategory.php 19586 2010-06-24 11:48:14Z malanciault $
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
 * An image category
 *
 * These categories are managed through a {@link icms_image_category_Handler} object
 *
 * @package     kernel
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class icms_image_category_Object extends icms_core_Object
{
	var $_imageCount;

	/**
	 * Constructor
	 *
	 */
	function icms_image_category_Object()
	{
		parent::__construct();
		$this->initVar('imgcat_id', XOBJ_DTYPE_INT, null, false);
		$this->initVar('imgcat_pid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('imgcat_name', XOBJ_DTYPE_TXTBOX, null, true, 100);
		$this->initVar('imgcat_foldername', XOBJ_DTYPE_TXTBOX, null, true, 100);
		$this->initVar('imgcat_display', XOBJ_DTYPE_INT, 1, false);
		$this->initVar('imgcat_weight', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('imgcat_maxsize', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('imgcat_maxwidth', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('imgcat_maxheight', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('imgcat_type', XOBJ_DTYPE_OTHER, null, false);
		$this->initVar('imgcat_storetype', XOBJ_DTYPE_OTHER, null, false);
	}

	/**
	 * Set Image count to a value
	 * @param	int $value Value
	 */
	function setImageCount($value)
	{
		$this->_imageCount = (int) ($value);
	}

	/**
	 * Gets Image count
	 * @return	int _imageCount number of images
	 */
	function getImageCount()
	{
		return $this->_imageCount;
	}
}
?>