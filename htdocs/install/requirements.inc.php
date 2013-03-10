<?php
/**
 * Requirements for installing ImpressCMS
 *
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License (GPL) v3
 * @category	ICMS
 * @package		Administration
 * @subpackage	Installation
 * @since		2.0
 */

$requirements = array(
		'phpversion' => '5.3',
		'phpextensions' => array(
				'session',
				'pcre',
				'fopen',
				'gd',
				'mysql',
			),
		'phpsettings' => array(
				'memory_limit' => array('16MB', '>='),
			),
		'paths' => array(
				'cache',
				'modules',
				'templates_c',
				'uploads',
			),
	);

$options = array(
		'phpextensions' => array(
				'iconv',
				'xml',
				'curl',
				'bcmath',
				'openssl',
			),
	);

$recommendations = array();
