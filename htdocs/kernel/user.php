<?php
// $Id: user.php 506 2006-05-26 23:10:37Z skalpa $
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Author: Kazumi Ono (AKA onokazu)                                          //
// URL: http://www.myweb.ne.jp/, http://www.xoops.org/, http://jp.xoops.org/ //
// Project: The XOOPS Project                                                //
// ------------------------------------------------------------------------- //
/**
* Manage of users
*
* @copyright	http://www.xoops.org/ The XOOPS Project
* @copyright	XOOPS_copyrights.txt
* @copyright	http://www.impresscms.org/ The ImpressCMS Project
* @license	LICENSE.txt
* @package	core
* @since	XOOPS
* @author	http://www.xoops.org The XOOPS Project
* @author	modified by UnderDog <underdog@impresscms.org>
* @version	$Id: user.php 9520 2009-11-11 14:32:52Z pesianstranger $
*/



if(!defined('ICMS_ROOT_PATH')) {exit();}

class XoopsUser extends icms_member_user_Object {
	private $_deprecated;
	public function __construct(&$id) {
		parent::__construct($id);
		$this->_deprecated = icms_core_Debug::setDeprecated('icms_member_user_Object', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

class XoopsGuestUser extends XoopsUser {
	private $_deprecated;
	public function __construct(&$id) {
		parent::__construct($id);
		$this->_deprecated = icms_core_Debug::setDeprecated('icms_member_user_Object', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

class XoopsUserHandler extends icms_member_user_Handler {
	private $_deprecated;
	public function __construct(&$db) {
		parent::__construct($db);
		$this->_deprecated = icms_core_Debug::setDeprecated('icms_member_user_Handler', sprintf(_CORE_REMOVE_IN_VERSION, '1.4'));
	}
}

