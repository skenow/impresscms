<?php
/**
 * ImpressCMS Update
 *
 * @copyright	The ImpressCMS Project http://www.impresscms.org/
 * @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
 * @category	ICMS
 * @package		Administration
 * @subpackage	Update
 * @since		2.0
 */

/* set get and post filters before including admin_header, if not strings */
$filter_get = array('id' => 'int');

$filter_post = array('id' => 'int');

/* set default values for variables. $op and $fct are handled in the header */
$id = 0;

/** common header for the admin functions */
include 'admin_header.php';

$clean_op = $op;

/* conventions used elsewhere: add{object}, mod, del */
$valid_op = array ("mod", "");

if (in_array($clean_op, $valid_op, TRUE)) {
    switch ($clean_op) {
        case "mod":

            break;

        default:
            icms_cp_header();

            $icmsAdminTpl->display(ICMS_MODULES_PATH . "/system/templates/admin/update/system_adm_update.html");
    }
}

icms_cp_footer();
