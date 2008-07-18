<?php
/**
* Images Manager - DHTML Image Editor Tool - Resize Plugin
*
* Resize plugin for DHTML Image Editor Tool
*
* @copyright	The ImpressCMS Project http://www.impresscms.org/
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package		core
* @since		1.2
* @author		Rodrigo Pereira Lima (AKA TheRplima) <therplima@impresscms.org>
* @version		$Id: icms_plugin_version.php 1244 2008-03-18 17:09:11Z real_therplima $
*/

$plugversion['name'] = 'Resize Tool';
$plugversion['version'] = 1.00;
$plugversion['description'] = 'Plugin to allow the DHTML Image Editor resize images. Set the values in the form and click on the button to preview or resize the image. You can use values in pixel or percent.';
$plugversion['author'] = "Rodrigo Pereira Lima (AKA TheRplima) <therplima@impresscms.org>";
$plugversion['credits'] = "The ImpressCMS Project";
$plugversion['license'] = "GPL see LICENSE";
$plugversion['official'] = 1;
$plugversion['icon'] = 'resize.png';
$plugversion['folder'] = 'resize';
$plugversion['file'] = 'resize_image.php';
$plugversion['block_template'] = 'resize_image.html';
$plugversion['init_js_function'] = 'resize_progressBar();';
$plugversion['stop_js_function'] = 'resize_delpreview()';
?>