<?php
/**
 * Requirements for installing ImpressCMS
 *
 * @copyright
 * @license
 * @category	ICMS
 * @package		Administration
 * @subpackage	Installation
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