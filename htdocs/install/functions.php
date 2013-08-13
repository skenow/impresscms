<?php
/**
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) v3
 * @category	ICMS
 * @package		Administration
 * @subpackage	Installation
 * @since		2.0
 */

/**
 * Convert any string values for config options to numeric values
 *
 * @param	string	$val
 * @return	int		integer value of the string representation passed
 */
function toNumeric($val) {
	$val = trim($val);

	switch (strtolower(substr($val, -1))) {
		case 'm':
			$val = (int) substr($val, 0, -1) * 1048576;
			break;

		case 'k':
			$val = (int) substr($val, 0, -1) * 1024;
			break;

		case 'g':
			$val = (int) substr($val, 0, -1) * 1073741824;
			break;

		case 'b':
			switch (strtolower(substr($val, -2, 1))) {
				case 'm':
					$val = (int) substr($val, 0, -2) * 1048576;
					break;

				case 'k':
					$val = (int) substr($val, 0, -2) * 1024;
					break;

				case 'g':
					$val = (int) substr($val, 0, -2) * 1073741824;
					break;

				default :
					break;
			}
			break;

		default:
			break;
	}
	return $val;
}

/**
 * Compare 2 values, using version compare
 *
 * @param	string	$a	1st item to compare
 * @param	string	$b	2nd item to compare
 * @param	string	$operator comparison operator to be applied
 * @return	bool
 */
function compare_values($a, $b, $operator = '=') {
	return version_compare(toNumeric($a), toNumeric($b), $operator);
}
