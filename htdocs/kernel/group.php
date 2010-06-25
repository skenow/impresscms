<?php
if (!defined('ICMS_ROOT_PATH')) die("ImpressCMS root path not defined");

class XoopsGroup extends icms_member_group_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_member_group_Object', 'This will be removed in version 1.4');
	}
}

class XoopsGroupHandler extends icms_member_group_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_member_group_Handler', 'This will be removed in version 1.4');
	}
}

class XoopsMembership extends icms_member_group_membership_Object {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_member_group_membership_Object', 'This will be removed in version 1.4');
	}
}

class XoopsMembershipHandler extends icms_member_group_membership_Handler {
	private $_deprecated;
	public function __construct() {
		parent::getInstance();
		$this->_deprecated = icms_deprecated('icms_member_group_membership_Handler', 'This will be removed in version 1.4');
	}
}