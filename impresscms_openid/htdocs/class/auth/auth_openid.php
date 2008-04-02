<?php
/**
* Authentification class for OpenID protocol
*
* This class handles the authentication of a user with its openid. If the the authenticate openid
* is not found in the users database, the user will be able to create his account on this site or
* associate its openid with is already registered account. This process is also taking into
* consideration $xoopsConfigUser['activation_type'].
*
* @copyright	The ImpressCMS Project http://www.impresscms.org/
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License (GPL)
* @package		openid
* @since		1.1
* @author		malanciault <marcan@impresscms.org)
* @credits		Sakimura <http://www.sakimura.org/> Evan Prodromou <http://evan.prodromou.name/>
* @version		$Id$
*/

class XoopsAuthOpenid extends XoopsAuth {

	/**
	 * @var bool $openidNotFound will be TRUE if the openid was not found
	 */
	var $openidNotFound=false;


	/**
	 * @var string $displayid $displayid fetch from the openid authentication
	 */
	var $displayid;

	/**
	 * @var string $openid openid used for this authentication
	 */
	var $openid;
	
	/**
	 * Authentication Service constructor
	 */
	function XoopsAuthOpenid (&$dao) {
		$this->_dao = $dao;
		$this->auth_method = 'openid';
	}

	/**
	 * Authenticate using the OpenID protocol
	 *
     * @return bool successful?
	 */
	function authenticate() {
		require_once ICMS_LIBRARIES_ROOT_PATH . "/phpopenid/occommon.php";

		// session_start();
		
		// check to see if we alredy have an OpenID response in SESSION
		if (isset($_SESSION['openid_response'])) {
			$response = $_SESSION['openid_response'];
		} else {
			// Complete the authentication process using the server's response.
			$consumer = getConsumer();//1123
			$return_to = getReturnTo();//1123
			//$response = $consumer->complete($_GET);
			$response = $consumer->complete($return_to);//1123
			$_SESSION['openid_response']=$response;
		}
		
		if ($response->status == Auth_OpenID_CANCEL) {
		    // This means the authentication was cancelled.
		    $this->setErrors('100', 'Verification cancelled.');
		} else if ($response->status == Auth_OpenID_FAILURE) {
		    $this->setErrors('101', "OpenID authentication failed: " . $response->message);
			/**
			 * This can be uncommented to display the $_REQUEST array. This is usefull for
			 * troubleshooting purposes
			 */
			 //$this->setErrors('102', "REQUEST info: <pre>" . var_export($_REQUEST, true) . "</pre>");
			 return false;
		} else if ($response->status == Auth_OpenID_SUCCESS) {
		    // This means the authentication succeeded.
			$this->displayid = $response->getDisplayIdentifier();
			$this->openid = $response->identity_url;
			$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
			$sreg = $sreg_resp->contents();
			$_SESSION['openid_sreg']=$sreg;

		    // $openid = $response->identity_url;
		    $esc_identity = htmlspecialchars($this->openid, ENT_QUOTES);

		    $success = "You have successfully verified $esc_identity (" . $this->displayid . ") as your identity.";

		    if ($response->endpoint->canonicalID) {
		        $success .= '  (XRI CanonicalID: '.$response->endpoint->canonicalID.') ';
		    }

			/**
			 * This can be uncommented to display the $success info for troubleshooting purposes
			 */
		    //$this->setErrors('103', $success);

			// Do we already have a user with this openid
			$member_handler = & xoops_gethandler('member');
			$criteria = new CriteriaCompo();
			$criteria->add(new Criteria('openid', $this->openid));
			$users =& $member_handler->getUsers($criteria);
			if ($users && count($users) > 0) {
				return $users[0];
		    } else {
		    	/*
		    	 * This openid was not found in the users table.Let's ask the user if he wants
		    	 * to create a new user account on the site or else login with his already registered
		    	 * account
		    	 */
				$this->openidNotFound = true;
				return false;
		    }
		}
	}
}

?>