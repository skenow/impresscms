<?php
require_once "occommon.php";
include_once("../../mainfile.php");
include_once("../../header.php");

// session_start();
// Complete the authentication process using the server's response.
$consumer = getConsumer();//1123
$return_to = getReturnTo();//1123
//$response = $consumer->complete($_GET);
$response = $consumer->complete($return_to);//1123
$_SESSION['openid_response']=$response;


if ($response->status == Auth_OpenID_CANCEL) {
    // This means the authentication was cancelled.
    $msg = 'Verification cancelled.';
} else if ($response->status == Auth_OpenID_FAILURE) {
    $msg = "OpenID authentication failed: " . $response->message;
	echo "msg: " . $msg;
	echo "<br /><pre>";
	print_r($_REQUEST);
	echo "</pre>";
} else if ($response->status == Auth_OpenID_SUCCESS) {
    // This means the authentication succeeded.
	$displayId = $response->getDisplayIdentifier();
	$cid = $response->identity_url;
	$sreg_resp = Auth_OpenID_SRegResponse::fromSuccessResponse($response);
	$sreg = $sreg_resp->contents();
	$_SESSION['openid_sreg']=$sreg;
	
    // $openid = $response->identity_url;
    $esc_identity = htmlspecialchars($cid, ENT_QUOTES);

    $success = "You have successfully verified $esc_identity 
    ($displayId) as your identity.";


    if ($response->endpoint->canonicalID) {
        $success .= '  (XRI CanonicalID: '.$response->endpoint->canonicalID.') ';
    }

    $query = "SELECT * from " . $xoopsDB->prefix('openid_localid') . 
     " WHERE openid='".$cid."'";
    $res = $xoopsDB->query($query,1);
    //$numrows =　$xoopsDB->getRowsNum($res);
    $row = $xoopsDB->fetchArray($res);

    if($row) {
    	// He is already registered into the map. 
    	$lid = $row['localid'];    	
 		$criteria = new CriteriaCompo(new Criteria('uname', $lid ));
		$user_handler =& xoops_gethandler('user');
		$users =& $user_handler->getObjects($criteria, false);
		$user = $users[0] ;
		unset( $users ) ;
		$xoopsUser = $user;
		if (false != $user && $user->getVar('level') > 0) {
			$member_handler =& xoops_gethandler('member');
			$user->setVar('last_login', time());
			if (!$member_handler->insertUser($user)) {
			}
			$_SESSION['xoopsUserId'] = $user->getVar('uid');
			$_SESSION['xoopsUserGroups'] = $user->getGroups();
			$user_theme = $user->getVar('theme');
			if (in_array($user_theme, $xoopsConfig['theme_set_allowed'])) {
				$_SESSION['xoopsUserTheme'] = $user_theme;
			}
			//include_once(XOOPS_ROOT_PATH.'/footer.php');
			header("Location: " . $_SESSION['frompage']);
			unset($_SESSION['frompage']);
		}

    } else {
    	$xoopsOption['template_main'] = 'openid_new_user.html';
		setlocale(LC_ALL,"ja_JP.EUC");
		if(preg_match('/^http/',$displayId)){
			if($sreg['nickname']!='') {
				$unam = alphaonly($sreg['nickname']);
			} else {
				$unam = "";
			}
		} else {
			$unam = $displayId;
		}
		$xoopsTpl->assign('displayId', $displayId);
		$xoopsTpl->assign('cid', $cid);
		$xoopsTpl->assign('unam', $unam);
		$xoopsTpl->assign('existinguser',_OD_EXISTINGUSER);
		$xoopsTpl->assign('loginbelow',_OD_LOGINBELOW);
		$xoopsTpl->assign('xoopsuname', _OD_XOOPSUNAME);
		$xoopsTpl->assign('xoopspass', _OD_XOOPSPASS);
		$xoopsTpl->assign('nonmember', _OD_NONMEMBER);
		$xoopsTpl->assign('enterwantedname', _OD_ENTERWANTEDNAME);
		$xoopsTpl->assign('screenamelabel', _OD_SCREENNAMELABEL);
		$xoopsTpl->assign('youropenid', _OD_YOUR_OPENID);
		include_once XOOPS_ROOT_PATH.'/footer.php';
    }
    
}

?>
