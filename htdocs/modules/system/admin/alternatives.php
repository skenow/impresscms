<?php
/**
 * ImpressCMS AUTOTASKS
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package		Administration
 * @subpackage	Autotasks
 * @since		1.2 alpha 2
 * @author		MekDrop <mekdrop@gmail.com>
 * @version		SVN: $Id$
 */

/* set get and post filters before including admin_header, if not strings */
$filter_post = array();

$filter_get = array();

$_GET['fct'] = null;

/* set default values for variables. $op and $fct are handled in the header */

/** common header for the admin functions */
include "admin_header.php";

$icms_admin_handler = icms::handler('icms_alternative');

/**
 * Method for editing autotask entries
 * 
 * @param boolean	$showmenu		This parameter is not used - why is it here?
 * @param int		$autotasksid	The unique identifier for the autotask
 * @param boolean	$clone			Indicator if an autotask is being created from another
 */
function alternatives_edit($showmenu = FALSE, $autotasksid = 0, $clone = FALSE) {
	global $icms_admin_handler, $icmsAdminTpl;

	icms_cp_header();
	$autotasksObj = $icms_admin_handler->get($autotasksid);

	if (!$clone && !$autotasksObj->isNew()) {
		$sform = $autotasksObj->getForm(_CO_ICMS_ALTERNATIVES_MODIFIED, 'addalternative');
		$sform->assign($icmsAdminTpl);
		$icmsAdminTpl->display('db:admin/alternatives/system_adm_alternatives.html');
	} else {
		Throw New Exception('Unsupported!');
	}
}

switch ($op) {
	case "mod":

		$autotasksid = isset($sat_id) ? (int) ($sat_id) : 0;
		alternatives_edit(TRUE, $autotasksid);
		break;

	case "addalternative":
		$controller = new icms_ipf_Controller($icms_admin_handler);
		$controller->storeFromDefaultForm(_CO_ICMS_ALTERNATIVES_CREATED, _CO_ICMS_AUTOTASKS_MODIFIED, ICMS_URL . '/modules/system/admin.php?fct=alternatives');
		break;

	case "del":
		$controller = new icms_ipf_Controller($icms_admin_handler);
		$controller->handleObjectDeletion();
		break;

	default:
		icms_cp_header();            

		$objectTable = new icms_ipf_view_Table($icms_admin_handler, FALSE, array('edit'));
		$objectTable->addColumn(new icms_ipf_view_Column('name', 'left', FALSE));
                $objectTable->addColumn(new icms_ipf_view_Column('type', 'left', FALSE));
                $objectTable->addColumn(new icms_ipf_view_Column('default', 'left', FALSE));

		$icmsAdminTpl->assign('icms_alternatives_table', $objectTable->fetch());
		$icmsAdminTpl->display('db:admin/alternatives/system_adm_alternatives.html');

		break;
}

icms_cp_footer();
