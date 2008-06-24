<?php
/**
 * Class to Clean & Filter HTML for various uses.
 *
 * Class uses external HTML Purifier for filtering.
 *
 * @package	kernel
 * @subpackage	core
 *
 * @author	vaughan montgomery (vaughan@impresscms.org)
 * @author      ImpressCMS Project
 * @copyright	(c) 2007-2008 The ImpressCMS Project - www.impresscms.org
 **/
class icms_HTMLPurifier
{
	/**
	* variable used by HTMLPurifier Library
	**/
	var $purifier;

	function icms_HTMLPurifier()
	{
		require ICMS_ROOT_PATH.'/libraries/htmlpurifier/HTMLPurifier.standalone.php';
		require ICMS_ROOT_PATH.'/libraries/htmlpurifier/HTMLPurifier.autoload.php';
	}

	/**
	* Access the only instance of this class
	*
	* @return	object
     	*
     	* @static 	$purify_instance
     	* @staticvar   	object
	**/
	function &getPurifierInstance()
	{
		static $purify_instance;
		if (!isset($purify_instance))
		{
			$purify_instance = new icms_HTMLPurifier();
		}
		return $purify_instance;
	}

	/**
	* Gets Custom Purifier configurations ** this function is for future development **
	*
	* @param   string  $config configuration to use (must be numeric).
	* @return  string
	**/
	function icms_getPurifierConfig($config = 'system-global')
	{
	}

	/**
	* Allows HTML Purifier library to be called when required
	*
	* @param   string  $html input to be cleaned
	* @param   string  $config custom filtering config?
	* @param   string  $icms_PurifyConfig instanciate HTMLPurifier Library with default settings
	* @return  string
	**/
	function icms_html_purifier($html, $config = 'system-global')
	{
		$host_domain = icms_get_base_domain(ICMS_URL);
		$host_base = icms_get_url_domain(ICMS_URL);
		
		// sets default config settings for htmpurifier
		$icms_PurifyConfig = HTMLPurifier_Config::createDefault();

		$icms_PurifyConfig->set('HTML', 'DefinitionID', 'system-global');
		$icms_PurifyConfig->set('HTML', 'DefinitionRev', 1);
		// sets the path where HTMLPurifier stores it's serializer cache.
		if(is_dir(ICMS_PURIFIER_CACHE))
		{
			$icms_PurifyConfig->set('Cache', 'DefinitionImpl', 'Serializer');
			$icms_PurifyConfig->set('Cache', 'SerializerPath', ICMS_PURIFIER_CACHE);
		}
		else
		{
			$icms_PurifyConfig->set('Cache', 'DefinitionImpl', 'Serializer');
			$icms_PurifyConfig->set('Cache', 'SerializerPath', ICMS_ROOT_PATH.'/cache');
		}

		// the following config options in future could be defined from admin interface allowing more advanced customised configurations.

		// sets default system config options.
		$icms_PurifyConfig->set('Core', 'Encoding', _CHARSET); // sets purifier to use specified encoding. default = UTF-8
		$icms_PurifyConfig->set('HTML', 'Doctype', 'XHTML 1.0 Transitional'); // sets purifier to use specified Doctype when tidying etc.
		
		$icms_PurifyConfig->set('URI', 'DefinitionID', 'system-global');
		$icms_PurifyConfig->set('URI', 'DefinitionRev', 1);
		$icms_PurifyConfig->set('URI', 'Host', $host_domain); // sets host URI for filtering. this should be the base domain name. ie. impresscms.org and not community.impresscms.org.
		$icms_PurifyConfig->set('URI', 'Base', $host_base); // sets host URI for filtering. this should be the base domain name. ie. impresscms.org and not community.impresscms.org.
		$icms_PurifyConfig->set('URI', 'AllowedSchemes', array(	'http' => true,
									'https' => true,
									'mailto' => true,
									'ftp' => true,
									'nntp' => true,
									'news' => true,)); // sets allowed URI schemes to be allowed in Forms.
//		$icms_PurifyConfig->set('HTML', 'Allowed', 'a[href|title],em,p,blockquote,div,b,strong,li,ul');
		$icms_PurifyConfig->set('URI', 'HostBlacklist', ''); // array of domain names to filter out (blacklist).
		$icms_PurifyConfig->set('URI', 'DisableExternal', false); // if enabled will disable all links/images from outside your domain (requires Host being set)
//		$icms_PurifyDef = $icms_PurifyConfig->getHTMLDefinition(true);

		// Custom Configuration
		// these in future could be defined from admin interface allowing more advanced customised configurations.
		if($config = 'system-global' || $config = 'display') // config id level for display HTMLArea
		{
			$icms_PurifyConfig->set('HTML', 'DefinitionID', 'display');
			$icms_PurifyConfig->set('HTML', 'DefinitionRev', 1);
			// sets purifier to use medium level of filtering for w3c invalid code, cleans malicious code.
			// allowed options 'none', 'light', 'medium', 'heavy'
			$icms_PurifyConfig->set('HTML', 'TidyLevel', 'medium');
			$icms_PurifyConfig->set('Filter', 'YouTube', true); // setting to true will allow Youtube files to be embedded into your site & w3c validated.
			$icms_PurifyDef = $icms_PurifyConfig->getHTMLDefinition(true);
		}
		elseif($config = 'preview') // config id level for preview HTMLArea
		{
			$icms_PurifyConfig->set('HTML', 'DefinitionID', 'preview');
			$icms_PurifyConfig->set('HTML', 'DefinitionRev', 1);
			$icms_PurifyConfig->set('HTML', 'TidyLevel', 'light');
			// sets purifier to use light level of filtering for w3c invalid code, cleans malicious code.
			// allowed options 'none', 'light', 'medium', 'heavy'
			$icms_PurifyConfig->set('Filter', 'YouTube', true); // setting to true will allow Youtube files to be embedded into your site & w3c validated.
			$icms_PurifyDef = $icms_PurifyConfig->getHTMLDefinition(true);
		}

		$this->purifier = new HTMLPurifier($icms_PurifyConfig);

		$html = $this->purifier->purify($html);

		return $html;
	
	}

	/**
	 * Filters & Cleans HTMLArea form data submitted for display
	 *
	 * @param   string  $html
	 * @param   string  $config custom filtering config?
	 * @return  string
	 **/
	function &displayHTMLarea($html, $config = 'display')
	{
		// ################# Preload Trigger beforeDisplayTarea ##############
		global $icmsPreloadHandler;
		$icmsPreloadHandler->triggerEvent('beforedisplayHTMLarea', array(&$html, $config));
		
		$html = $this->icms_html_purifier($html, $config);

		// ################# Preload Trigger afterDisplayTarea ##############
		global $icmsPreloadHandler;
		$icmsPreloadHandler->triggerEvent('afterdisplayHTMLarea', array(&$html, $config));		
		return $html;
	}

	/**
	 * Filters & Cleans HTMLArea form data submitted for preview
	 *
	 * @param   string  $html
	 * @param   string  $config custom filtering config?
	 * @return  string
	 **/
	function &previewHTMLarea($html, $config = 'preview')
	{
		// ################# Preload Trigger beforeDisplayTarea ##############
		global $icmsPreloadHandler;
		$icmsPreloadHandler->triggerEvent('beforepreviewHTMLarea', array(&$html, $config));
		
		$html = $this->icms_html_purifier($html, $config);

		// ################# Preload Trigger afterDisplayTarea ##############
		global $icmsPreloadHandler;
		$icmsPreloadHandler->triggerEvent('afterpreviewHTMLarea', array(&$html, $config));		
		return $html;
	}

	/**
	 * Function to clean & sanitize $text makes safe for DB Queries.
	 * 
	 * @param integer $html
	 * @param string $value - $variable that is being escaped for query.
	 * @param string $config - allows a custom filter set.
	 * @return string
	 */
	function icms_escapeHTMLValue($value, $quotes = true, $config = 'system-global')
	{
		if (is_string($value))
		{
			$value = $this->icms_html_purifier($value, $config);
			if(get_magic_quotes_gpc)
			{
				$value = stripslashes($value);
	        	}
			$value = mysql_real_escape_string($value);
	    	}
	    	else if ($value === null)
		{
	        	$value = 'NULL';
		}
	    	else if (is_bool($value))
		{
	        	$value = $value ? 1 : 0;
	    	}
	    	else if (!is_numeric($value))
		{
	        	$value = mysql_real_escape_string($value);
	        	if ($quotes)
			{
	            		$value = '"' . $value . '"';
			}
 		}

		return $value;
	}

}
?>