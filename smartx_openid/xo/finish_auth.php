<?php

function run2(&$success, &$msg, &$error) {
    $consumer = getConsumer();

    // Complete the authentication process using the server's
    // response.
    $response = $consumer->complete();

    // Check the response status.
    if ($response->status == Auth_OpenID_CANCEL) {
        // This means the authentication was cancelled.
        $msg = 'Verification cancelled.';
    } else if ($response->status == Auth_OpenID_FAILURE) {
        // Authentication failed; display the error message.
        $msg = "OpenID authentication failed: " . $response->message;
    } else if ($response->status == Auth_OpenID_SUCCESS) {
        // This means the authentication succeeded; extract the
        // identity URL and Simple Registration data (if it was
        // returned).
        $openid = $response->identity_url;
        $esc_identity = htmlspecialchars($openid, ENT_QUOTES);

        $sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
        $sreg = $sreg_resp->contents();

		// let's see if we have a user with this openid
		$member_handler =& xoops_gethandler('member');
      	$criteria = new CriteriaCompo();
      	$criteria->add(new Criteria('openid_url', $esc_identity));
      	$users =& $member_handler->getUsers($criteria);
		if ($users && count($users) > 0) {
			if (count($users) > 1) {
				/**
				 * more then 1 users found. This needs to be adressed in the futur providing the
				 * user with a select box containing the users found and asking him what user to use
				 */
				$msg .=  'More then one users found.';
			}
			$user = $users[0];
			$msg .=  'username of the user who just authenticated : ' . $user->getVar('uname');
			include_once(XOOPS_ROOT_PATH . '/include/checklogin.php');

		} else {
			$msg .=  'No related openid found';
		}
    }
}

include('header.php');

include_once(XOOPS_ROOT_PATH . '/include/checklogin.php');
exit;

require_once "common.php";

$xoTheme->addStylesheet(XO_URL . 'xo.css');

$success = $msg = $error = false;
//session_start();
run2($success, $msg, $error);

$xoopsTpl->assign('msg', $msg);
$xoopsTpl->assign('error', $error);
$xoopsTpl->assign('success', $success);

$xoopsTpl->display(XO_ROOT_PATH . 'templates/xo_index.html');

include_once(XOOPS_ROOT_PATH . '/footer.php');

?>