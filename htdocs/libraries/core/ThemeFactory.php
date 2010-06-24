<?php
/**
 * icms_core_Theme component class file
 *
 * @copyright	The Xoops project http://www.xoops.org/
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author       Skalpa Keo <skalpa@xoops.org>
 * @since        2.3.0
 * @version		$Id: theme.php 19180 2010-05-02 10:54:45Z phoenyx $
 * @package 		core
 * @subpackage 	Templates
 */

/**
 * icms_core_ThemeFactory
 *
 * @author 		Skalpa Keo
 * @package		xos_opal
 * @subpackage	icms_core_Theme
 * @since        2.3.0
 */
class icms_core_ThemeFactory {

	public $xoBundleIdentifier = 'icms_core_ThemeFactory';
	/**
	 * Currently enabled themes (if empty, all the themes in themes/ are allowed)
	 * @public array
	 */
	public $allowedThemes = array();
	/**
	 * Default theme to instanciate if none specified
	 * @public string
	 */
	public $defaultTheme = 'iTheme';
	/**
	 * If users are allowed to choose a custom theme
	 * @public bool
	 */
	public $allowUserSelection = true;

	/**
	 * Instanciate the specified theme
	 */
	function &createInstance( $options = array(), $initArgs = array() ) {
		// Grab the theme folder from request vars if present
		if (@empty ( $options ['folderName'] )) {
			// xoops_theme_select still exists to keep compatibilitie ...
			if (($req = @$_REQUEST ['xoops_theme_select']) && $this->isThemeAllowed ( $req )) {
				$options ['folderName'] = $req;
				if (isset ( $_SESSION ) && $this->allowUserSelection) {
					$_SESSION [$this->xoBundleIdentifier] ['defaultTheme'] = $req;
				}
			} elseif (($req = @$_REQUEST ['theme_select']) && $this->isThemeAllowed ( $req )) {
				$options ['folderName'] = $req;
				if (isset ( $_SESSION ) && $this->allowUserSelection) {
					$_SESSION [$this->xoBundleIdentifier] ['defaultTheme'] = $req;
				}
			} elseif (isset ( $_SESSION [$this->xoBundleIdentifier] ['defaultTheme'] )) {
				$options ['folderName'] = $_SESSION [$this->xoBundleIdentifier] ['defaultTheme'];
			} elseif (@empty ( $options ['folderName'] ) || ! $this->isThemeAllowed ( $options ['folderName'] )) {
				$options ['folderName'] = $this->defaultTheme;
			}
			$GLOBALS['xoopsConfig']['theme_set'] = $options['folderName'];
		}
		$options['path'] = (is_dir(ICMS_MODULES_PATH.'/system/themes/'.$options['folderName']))?ICMS_MODULES_PATH.'/system/themes/' . $options['folderName']:XOOPS_THEME_PATH . '/' . $options['folderName'];
		$inst = new icms_core_Theme();
		foreach ( $options as $k => $v ) $inst->$k = $v;
		$inst->xoInit();
		return $inst;
	}

	/**
	 * Checks if the specified theme is enabled or not
	 * @param string $name
	 * @return bool
	 */
	function isThemeAllowed( $name ) {
		return ( empty( $this->allowedThemes ) || in_array( $name, $this->allowedThemes ) );
	}

}
?>