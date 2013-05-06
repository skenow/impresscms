<?php

/**
 * @property string $dirname    Dirname for module
 * @property string $file       File for template
 * @property int $ref_id     ID of module
 * @property string $description Description of template
 * @property string $type       Type of template
 */
class action_system_groupperm_Insert extends icms_action_base_Module {

    public function __construct($params = array()) {
        $this->initVar('name', self::DTYPE_STRING, false);
        $this->initVar('itemid', self::DTYPE_INTEGER, false);
        $this->initVar('groupid', self::DTYPE_INTEGER, false);

        parent::__construct($params);
    }

    public function exec(\icms_action_Response &$response) {
        $gperm_handler = icms::handler('icms_member_groupperm');
        $mperm = & $gperm_handler->create();
        $mperm->setVar('gperm_groupid', $this->groupid);
        $mperm->setVar('gperm_itemid', $this->itemid);
        $mperm->setVar('gperm_name', $this->name);
        $mperm->setVar('gperm_modid', 1);
        if (!$gperm_handler->insert($mperm))
            return $response->error(sprintf('&nbsp;&nbsp;<span style="color:#ff0000;">' . _MD_AM_ADMIN_PERM_ADD_FAIL . '</span>', '<strong>' . icms_conv_nr2local($this->groupid) . '</strong>'));
        $response->msg(sprintf('&nbsp;&nbsp;' . _MD_AM_ADMIN_PERM_ADDED, '<strong>' . icms_conv_nr2local($this->groupid) . '</strong>'));
    }

}