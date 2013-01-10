<?php
/**
 * Form control creating a textbox for an object derived from icms_ipf_Object
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package		ipf
 * @subpackage	form
 * @since		1.2
 * @author		MekDrop <mekdrop@gmail.com>
 * @version		$Id$
 */

defined('ICMS_ROOT_PATH') or die("ImpressCMS root path not defined");

class icms_ipf_form_elements_Source extends icms_form_elements_Textarea {
	/*
	 * Editor's class instance
	 */
	private $_editor = null;

	/**
	 * Constructor
	 * @param	object    $object   reference to targetobject (@link icms_ipf_Object)
	 * @param	string    $key      the form name
	 */
	public function __construct($object, $key) {
		global $icmsConfig;

		parent::__construct($object->vars[$key]['form_caption'], $key, $object->getVar($key, 'e'));
                
                $vars = $object->getControl($key);
                
                $handler = icms::handler('icms_controls');
                $this->_editor = $handler->make('sourceedit', $vars);                
	}

	/**
	 * Renders the editor
	 * @return	string  the constructed html string for the editor
	 */
	public function render() {
		return $this->_editor->render();
	}
}