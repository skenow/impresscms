<?php
/**
 * XOOPS authentification class
 * Authorization classes, xoops authorization class file
 * @copyright	http://www.xoops.org/ The XOOPS Project
 * @copyright	XOOPS_copyrights.txt
 * @copyright	http://www.impresscms.org/ The ImpressCMS Project
 * @license		LICENSE.txt
 * @category	ICMS
 * @package		Auth
 * @subpackage	Xoops
 * @since		XOOPS
 * @author		http://www.xoops.org The XOOPS Project
 * @author		modified by UnderDog <underdog@impresscms.org>
 * @version		SVN: $Id$
 */
/**
 * Authentification class for Native XOOPS
 * @category	ICMS
 * @package		Auth
 * @subpackage	Xoops
 * @author		Pierre-Eric MENUET <pemphp@free.fr>
 * @copyright	copyright (c) 2000-2003 XOOPS.org
 */
class icms_auth_Xoops extends icms_auth_Object {
	/**
	 * Authentication Service constructor
	 * constructor
	 * @param object $dao reference to dao object
	 */
	public function __construct(&$dao) {
		$this->_dao = $dao;
		$this->auth_method = 'xoops';
	}

	/**
	 *  Authenticate user
	 * @param string $uname
	 * @param string $pwd
	 * @return object {@link icms_member_user_Object} icms_member_user_Object object
	 */
	public function authenticate($uname, $pwd = null) {
		$member_handler = icms::handler('icms_member');
		$user = $member_handler->loginUser($uname, $pwd);
		icms::$session->enableRegenerateId = true;
		icms::$session->sessionOpen();
		if ($user == false) {
			icms::$session->destroy(session_id());
			$this->setErrors(1, _US_INCORRECTLOGIN);
		}
		return ($user);
	}

	/**
	 *  Authenticate Yubikey
	 * @param string $email
	 * @param string $pwd
	 * @param string $otp
	 * @return object {@link icms_member_user_Object} icms_member_user_Object object
	 */
	public function authenticateYubikey($email, $pwd = null, $otp = null) {
		$otp = strtolower($otp);

		$member = icms::handler('icms_member');
		$uname = $member->getUnameFromEmail($email);

		$tokenId = $member->getYubikeyToken($email);

		if (hash('sha256', substr($otp, 0, 12) . ":" . $email) == $tokenId)
		{
			$YubikeyId = $member->getYubikeyId($email);
			$YubikeySig = $member->getYubikeySig($email);

			$token = new icms_auth_Yubikey($YubikeyId, $YubikeySig);

			$token->setCurlTimeout(20);
			$token->setTimestampTolerance(500);

			if ($token->verify($otp)) {
				unset($YubikeyId, $YubikeySig, $tokenId);
				return self::authenticate($uname, $pwd);
			}
		} else {
			$this->setErrors(1, _US_INCORRECTYUBIKEY);
		}
		unset($YubikeyId, $YubikeySig, $tokenId);

		return false;
	}
}

