<?php
/**
 * Core class for managing comments
 *
 * @package     core
 * @subpackage	comment
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright 	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @since		XOOPS
 * @version		$Id: comment.php 19431 2010-06-16 20:46:34Z david-sf $
 * @deprecated	Use icms_comment_Object, instead
 * @toto		Remove this in version 1.4
 */

class XoopsComment extends icms_comment_Object {

	/**
	 * Constructor
	 **/
	function XoopsComment() {
		parent::__construct();
		$this->setErrors = icms_deprecated('icms_comment_Object', 'This will be removed in version 1.4');
	}
}

/**
 * XOOPS comment handler class.
 *
 * This class is responsible for providing data access mechanisms to the data source
 * of XOOPS comment class objects.
 *
 *
 * @package     kernel
 * @subpackage  comment
 *
 * @author	    Kazumi Ono	<onokazu@xoops.org>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 * @deprecated	Use icms_comment_Handler instead
 * @todo		Remove in version 1.4
 */
class XoopsCommentHandler extends icms_comment_Handler {

}