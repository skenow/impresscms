<?php
/**
 * Contains the basis classes for managing any SEO-enabled objects derived from icms_ipf_Objects
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @package		icms_ipf_Object
 * @since		1.1
 * @author		marcan <marcan@impresscms.org>
 * @version		$Id: icmspersistableseoobject.php 19628 2010-06-25 15:41:19Z malanciault $
 */

if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

/**
 * icms_ipf_Object base SEO-enabled class
 *
 * Base class representing a single icms_ipf_Object with "search engine optimisation" capabilities
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @package		icms_ipf_Object
 * @since		1.1
 * @author		marcan <marcan@impresscms.org>
 * @version		$Id: icmspersistableseoobject.php 19628 2010-06-25 15:41:19Z malanciault $
 */
class icms_ipf_seo_Object extends icms_ipf_Object {
	function icms_ipf_seo_Object() {
		$this->initCommonVar("meta_keywords");
		$this->initCommonVar("meta_description");
		$this->initCommonVar("short_url");
		$this->seoEnabled = true;
	}

	/**
	 * Return the value of the short_url field of this object
	 *
	 * @return string
	 */
	function short_url()
	{
		return $this->getVar('short_url');
	}

	/**
	 * Return the value of the meta_keywords field of this object
	 *
	 * @return string
	 */
	function meta_keywords()
	{
		return $this->getVar('meta_keywords');
	}

	/**
	 * Return the value of the meta_description field of this object
	 *
	 * @return string
	 */
	function meta_description()
	{
		return $this->getVar('meta_description');
	}
}

?>