<?php
/**
 * Class to Clean & Filter HTML for various uses.
 * Class uses external HTML Purifier for filtering.
 *
 * @category	ICMS
 * @package		Core
 * @since		1.3
 * @author		vaughan montgomery (vaughan@impresscms.org)
 * @author		ImpressCMS Project
 * @copyright	(c) 2007-2010 The ImpressCMS Project - www.impresscms.org
 * @version		$Id$
**/
/**
 *
 * HTML Purifier filters
 *
 * @category	ICMS
 * @package		Core
 *
 */
class icms_core_HTMLFilter extends icms_core_DataFilter {

	/**
	 * variable used by HTML Filter Library
	 **/
	public $purifier;

	/**
	 * Constructor
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Access the only instance of this class
	 * @return      object
	 * @static      $instance
	 * @staticvar   object
	 **/
	public static function getInstance() {
		static $instance;
		if (!isset($instance)) {
			$instance = new icms_core_HTMLFilter();
		}
		return $instance;
	}

// ----- Public Functions -----

	/**
	 * Gets the selected HTML Filter & filters the content
	 * @param    string  $html    input to be cleaned
	 * @TODO	allow the webmasters to select which HTML Filter they want to use such as
	 *			HTMLPurifier, HTMLLawed etc, for now we just have HTMLPurifier.
	 * @return   string
	 **/
	public function filterHTML($html) {
		$icmsSecurityConfigPurifier = icms::$securityconfig->getConfigsByCat(ICMS_SEC_CONF_PURIFIER);
		if ($icmsSecurityConfigPurifier['enable_purifier'] !== 0) {
			ICMS_PLUGINS_PATH;
			require_once ICMS_LIBRARIES_PATH . '/htmlpurifier/HTMLPurifier.standalone.php';
			require_once ICMS_LIBRARIES_PATH . '/htmlpurifier/HTMLPurifier.autoload.php';
			if ($icmsSecurityConfigPurifier['purifier_Filter_ExtractStyleBlocks'] !== 0) {
				require_once ICMS_PLUGINS_PATH . '/csstidy/class.csstidy.php';
			}
			// get the Config Data
			$icmsPurifyConf = self::getHTMLFilterConfig();
			// uncomment for specific config debug info
			//parent::filterDebugInfo('icmsPurifyConf', $icmsPurifyConf);

			$purifier = new HTMLPurifier($icmsPurifyConf);
			$html = $purifier->purify($html);
		}
		return $html;
	}

// ----- Private Functions -----

	/**
	 * Gets Custom Purifier configurations ** this function will improve in time **
	 * @return  array    $icmsPurifierConf
	 **/
	protected function getHTMLFilterConfig() {
		$icmsSecurityConfigPurifier = icms::$securityconfig->getConfigsByCat(ICMS_SEC_CONF_PURIFIER);

		$icmsPurifierConf = array(
            'HTML.DefinitionID' => $icmsSecurityConfigPurifier['purifier_HTML_DefinitionID'],
            'HTML.DefinitionRev' => $icmsSecurityConfigPurifier['purifier_HTML_DefinitionRev'],
            'HTML.Doctype' => $icmsSecurityConfigPurifier['purifier_HTML_Doctype'],
            'HTML.AllowedElements' => $icmsSecurityConfigPurifier['purifier_HTML_AllowedElements'],
            'HTML.AllowedAttributes' => $icmsSecurityConfigPurifier['purifier_HTML_AllowedAttributes'],
            'HTML.ForbiddenElements' => $icmsSecurityConfigPurifier['purifier_HTML_ForbiddenElements'],
            'HTML.ForbiddenAttributes' => $icmsSecurityConfigPurifier['purifier_HTML_ForbiddenAttributes'],
            'HTML.MaxImgLength' => $icmsSecurityConfigPurifier['purifier_HTML_MaxImgLength'],
            'HTML.TidyLevel' => $icmsSecurityConfigPurifier['purifier_HTML_TidyLevel'],
            'HTML.SafeEmbed' => $icmsSecurityConfigPurifier['purifier_HTML_SafeEmbed'],
            'HTML.SafeObject' => $icmsSecurityConfigPurifier['purifier_HTML_SafeObject'],
            'HTML.Attr.Name.UseCDATA' => $icmsSecurityConfigPurifier['purifier_HTML_AttrNameUseCDATA'],
			'HTML.FlashAllowFullScreen' => $icmsSecurityConfigPurifier['purifier_HTML_FlashAllowFullScreen'],
            'Output.FlashCompat' => $icmsSecurityConfigPurifier['purifier_Output_FlashCompat'],
            'CSS.DefinitionRev' => $icmsSecurityConfigPurifier['purifier_CSS_DefinitionRev'],
            'CSS.AllowImportant' => $icmsSecurityConfigPurifier['purifier_CSS_AllowImportant'],
            'CSS.AllowTricky' => $icmsSecurityConfigPurifier['purifier_CSS_AllowTricky'],
            'CSS.AllowedProperties' => $icmsSecurityConfigPurifier['purifier_CSS_AllowedProperties'],
            'CSS.MaxImgLength' => $icmsSecurityConfigPurifier['purifier_CSS_MaxImgLength'],
            'CSS.Proprietary' => $icmsSecurityConfigPurifier['purifier_CSS_Proprietary'],
            'AutoFormat.AutoParagraph' => $icmsSecurityConfigPurifier['purifier_AutoFormat_AutoParagraph'],
            'AutoFormat.DisplayLinkURI' => $icmsSecurityConfigPurifier['purifier_AutoFormat_DisplayLinkURI'],
            'AutoFormat.Linkify' => $icmsSecurityConfigPurifier['purifier_AutoFormat_Linkify'],
            'AutoFormat.PurifierLinkify' => $icmsSecurityConfigPurifier['purifier_AutoFormat_PurifierLinkify'],
            'AutoFormat.Custom' => $icmsSecurityConfigPurifier['purifier_AutoFormat_Custom'],
            'AutoFormat.RemoveEmpty' => $icmsSecurityConfigPurifier['purifier_AutoFormat_RemoveEmpty'],
            'AutoFormat.RemoveEmpty.RemoveNbsp' => $icmsSecurityConfigPurifier['purifier_AutoFormat_RemoveEmptyNbsp'],
            'AutoFormat.RemoveEmpty.RemoveNbsp.Exceptions' => $icmsSecurityConfigPurifier['purifier_AutoFormat_RemoveEmptyNbspExceptions'],
            'Core.EscapeNonASCIICharacters' => $icmsSecurityConfigPurifier['purifier_Core_EscapeNonASCIICharacters'],
            'Core.HiddenElements' => $icmsSecurityConfigPurifier['purifier_Core_HiddenElements'],
			'Core.NormalizeNewlines' => $icmsSecurityConfigPurifier['purifier_Core_NormalizeNewlines'],
            'Core.RemoveInvalidImg' => $icmsSecurityConfigPurifier['purifier_Core_RemoveInvalidImg'],
            'Core.Encoding' => _CHARSET,
            'Cache.DefinitionImpl' => 'Serializer',
            'Cache.SerializerPath' => ICMS_TRUST_PATH . '/cache/htmlpurifier',
            'URI.Host' => $icmsSecurityConfigPurifier['purifier_URI_Host'],
            'URI.Base' => $icmsSecurityConfigPurifier['purifier_URI_Base'],
            'URI.Disable' => $icmsSecurityConfigPurifier['purifier_URI_Disable'],
            'URI.DisableExternal' => $icmsSecurityConfigPurifier['purifier_URI_DisableExternal'],
            'URI.DisableExternalResources' => $icmsSecurityConfigPurifier['purifier_URI_DisableExternalResources'],
            'URI.DisableResources' => $icmsSecurityConfigPurifier['purifier_URI_DisableResources'],
            'URI.MakeAbsolute' => $icmsSecurityConfigPurifier['purifier_URI_MakeAbsolute'],
            'URI.HostBlacklist' => $icmsSecurityConfigPurifier['purifier_URI_HostBlacklist'],
            'URI.AllowedSchemes' => $icmsSecurityConfigPurifier['purifier_URI_AllowedSchemes'],
            'URI.DefinitionID' => $icmsSecurityConfigPurifier['purifier_URI_DefinitionID'],
            'URI.DefinitionRev' => $icmsSecurityConfigPurifier['purifier_URI_DefinitionRev'],
            'URI.AllowedSchemes' => $icmsSecurityConfigPurifier['purifier_URI_AllowedSchemes'],
            'Attr.AllowedFrameTargets' => $icmsSecurityConfigPurifier['purifier_Attr_AllowedFrameTargets'],
            'Attr.AllowedRel' => $icmsSecurityConfigPurifier['purifier_Attr_AllowedRel'],
            'Attr.AllowedClasses' => $icmsSecurityConfigPurifier['purifier_Attr_AllowedClasses'],
            'Attr.ForbiddenClasses' => $icmsSecurityConfigPurifier['purifier_Attr_ForbiddenClasses'],
            'Attr.DefaultInvalidImage' => $icmsSecurityConfigPurifier['purifier_Attr_DefaultInvalidImage'],
            'Attr.DefaultInvalidImageAlt' => $icmsSecurityConfigPurifier['purifier_Attr_DefaultInvalidImageAlt'],
            'Attr.DefaultImageAlt' => $icmsSecurityConfigPurifier['purifier_Attr_DefaultImageAlt'],
            'Attr.ClassUseCDATA' => $icmsSecurityConfigPurifier['purifier_Attr_ClassUseCDATA'],
            'Attr.IDPrefix' => $icmsSecurityConfigPurifier['purifier_Attr_IDPrefix'],
            'Attr.EnableID' => $icmsSecurityConfigPurifier['purifier_Attr_EnableID'],
            'Attr.IDPrefixLocal' => $icmsSecurityConfigPurifier['purifier_Attr_IDPrefixLocal'],
            'Attr.IDBlacklist' => $icmsSecurityConfigPurifier['purifier_Attr_IDBlacklist'],
            'Filter.ExtractStyleBlocks.Escaping' => $icmsSecurityConfigPurifier['purifier_Filter_ExtractStyleBlocks_Escaping'],
            'Filter.ExtractStyleBlocks.Scope' => $icmsSecurityConfigPurifier['purifier_Filter_ExtractStyleBlocks_Scope'],
            'Filter.ExtractStyleBlocks' => $icmsSecurityConfigPurifier['purifier_Filter_ExtractStyleBlocks'],
            'Filter.YouTube' => $icmsSecurityConfigPurifier['purifier_Filter_YouTube'],
		);
		return parent::cleanArray($icmsPurifierConf);
	}
}