<?php
// $Id: user_avatar.php,v 1.1 2007/05/05 11:11:34 catzwolf Exp $
// auth_zarilia.php - ZARILIA authentification class
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// //
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// //
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaUserAvatar
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: user_avatar.php,v 1.1 2007/05/05 11:11:34 catzwolf Exp $
 * @access public
 */
class ZariliaUserAvatar extends ZariliaAuth {
    var $_options = array();
    /**
     * ZariliaUserAvatar::ZariliaUserAvatar()
     */
    function ZariliaUserAvatar ()
    {
    }

    function addOptions( $key = null, $value = null )
    {
        if ( !is_array( $key ) ) {
            $this->option[$key] = $value;
        } else {
            foreach( $key as $k => $v ) {
                $this->option[$k] = $v;
            }
        }
    }

    function checkUser()
    {
        global $zariliaUser;
        $uid = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0, XOBJ_DTYPE_INT );
        if ( empty( $uid ) || $zariliaUser->getVar( 'uid' ) != $uid ) {
            redirect_header( ZAR_URL, 3, _US_NOEDITRIGHT );
            exit();
        } else {
            return $uid;
        }
    }

    function avatarupload()
    {
        global $zariliaUser, $zariliaDB;

        $uid = $this->checkUser();
        $zarilia_upload_file = array();
        if ( !empty( $_POST['zarilia_upload_file'] ) && is_array( $_POST['zarilia_upload_file'] ) ) {
            $zarilia_upload_file = $_POST['zarilia_upload_file'];
        }
        $config_handler = &zarilia_gethandler( 'config' );
        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
        if ( $zariliaConfigUser['avatar_allow_upload'] == 1 && $zariliaUser->getVar( 'posts' ) >= $zariliaConfigUser['avatar_minposts'] ) {
            require_once ZAR_ROOT_PATH . '/class/uploader.php';
            $uploader = new ZariliaMediaUploader( ZAR_UPLOAD_PATH, array( 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png', 'image/png' ), $zariliaConfigUser['avatar_maxsize'], $zariliaConfigUser['avatar_width'], $zariliaConfigUser['avatar_height'] );
            if ( $uploader->fetchMedia( $_POST['zarilia_upload_file'][0] ) ) {
                $uploader->setPrefix( 'cavt' );
                if ( $uploader->upload() ) {
                    $avt_handler = &zarilia_gethandler( 'avatar' );
                    $avatar = &$avt_handler->create();
                    $avatar->setVar( 'avatar_file', $uploader->getSavedFileName() );
                    $avatar->setVar( 'avatar_name', $zariliaUser->getVar( 'uname' ) );
                    $avatar->setVar( 'avatar_mimetype', $uploader->getMediaType() );
                    $avatar->setVar( 'avatar_display', 1 );
                    $avatar->setVar( 'avatar_type', 'C' );
                    if ( !$avt_handler->insert( $avatar ) ) {
                        @unlink( $uploader->getSavedDestination() );
                    } else {
                        $oldavatar = $zariliaUser->getVar( 'user_avatar' );
                        if ( !empty( $oldavatar ) && $oldavatar != 'blank.gif' && !preg_match( "/^savt/", strtolower( $oldavatar ) ) ) {
                            $avatars = &$avt_handler->getObjects( new Criteria( 'avatar_file', $oldavatar ) );
                            $avt_handler->delete( $avatars[0] );
                            $oldavatar_path = str_replace( "\\", "/", realpath( ZAR_UPLOAD_PATH . '/' . $oldavatar ) );
                            if ( 0 === strpos( $oldavatar_path, ZAR_UPLOAD_PATH ) && is_file( $oldavatar_path ) ) {
                                unlink( $oldavatar_path );
                            }
                        }
                        $sql = sprintf( "UPDATE %s SET user_avatar = %s WHERE uid = %u", $zariliaDB->prefix( 'users' ), $zariliaDB->qstr( $uploader->getSavedFileName() ), $zariliaUser->getVar( 'uid' ) );
                        $zariliaDB->Execute( $sql );
                        $avt_handler->addUser( $avatar->getVar( 'avatar_id' ), $zariliaUser->getVar( 'uid' ) );
                        redirect_header( 'index.php?page_type=avatar&t=' . time() . '&amp;uid=' . $zariliaUser->getVar( 'uid' ), 0, _US_PROFUPDATED );
                    }
                }
            }
            echo $uploader->getErrors();
			return;
        } else {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _AM_US_NOTDELIDNOTFOUND );
        }
    }

    function avatarchoose()
    {
        global $zariliaUser;

        $uid = $this->checkUser();
        $zariliaUser_avatar = zarilia_cleanRequestVars( $_REQUEST, 'user_avatar', '', XOBJ_DTYPE_TXTBOX );
        $zariliaUser_avatarpath = str_replace( "\\", "/", realpath( ZAR_UPLOAD_PATH . '/' . $zariliaUser_avatar ) );
        if ( 0 === strpos( $zariliaUser_avatarpath, ZAR_UPLOAD_PATH ) && is_file( $zariliaUser_avatarpath ) ) {
            $oldavatar = $zariliaUser->getVar( 'user_avatar' );
            $zariliaUser->setVar( 'user_avatar', $zariliaUser_avatar );
            $member_handler = &zarilia_gethandler( 'member' );
            if ( !$member_handler->insertUser( $zariliaUser ) ) {
                include ZAR_ROOT_PATH . '/header.php';
                echo $zariliaUser->getHtmlErrors();
                include ZAR_ROOT_PATH . '/footer.php';
                exit();
            }

            $avt_handler = &zarilia_gethandler( 'avatar' );
            if ( $oldavatar && $oldavatar != 'blank.gif' && !preg_match( "/^savt/", strtolower( $oldavatar ) ) ) {
                $avatars = &$avt_handler->getObjects( new Criteria( 'avatar_file', $oldavatar ) );
                if ( is_object( $avatars[0] ) ) {
                    $avt_handler->delete( $avatars[0] );
                }
                $oldavatar_path = str_replace( "\\", "/", realpath( ZAR_UPLOAD_PATH . '/' . $oldavatar ) );
                if ( 0 === strpos( $oldavatar_path, ZAR_UPLOAD_PATH ) && is_file( $oldavatar_path ) ) {
                    unlink( $oldavatar_path );
                }
            }
            if ( $zariliaUser_avatar != 'blank.gif' ) {
                $avatars = &$avt_handler->getObjects( new Criteria( 'avatar_file', $zariliaUser_avatar ) );
                if ( is_object( $avatars[0] ) ) {
                    $avt_handler->addUser( $avatars[0]->getVar( 'avatar_id' ), $zariliaUser->getVar( 'uid' ) );
                }
            }
        }
        redirect_header( 'index.php?page_type=userinfo&amp;uid=' . $zariliaUser->getVar( 'uid' ), 1, _US_PROFUPDATED );
    }

    function avatarDisplay()
    {
        zarilia_header( false );

        ?>
		<script language='javascript'>
		<!--
		function myimage_onclick(counter){
			window.opener.zariliaGetElementById("user_avatar").options[counter].selected = true;
			showAvatar();
			window.opener.zariliaGetElementById("user_avatar").focus();
			window.close();
		}
		function showAvatar() {
			window.opener.zariliaGetElementById("avatar").src='<?php echo ZAR_UPLOAD_URL;

        ?>/' + window.opener.zariliaGetElementById("user_avatar").options[window.opener.zariliaGetElementById("user_avatar").selectedIndex].value;
		}
		-->
		</script>
		</head><body>
		<h4><?php echo _US_AVATAR;

        ?></h4>
		<form name='avatars'>
		<table width='90%' align='center'><tr>
		<?php
        $avatar_handler = &zarilia_gethandler( 'avatar' );
        $avatarslist = &$avatar_handler->getAList( 's' );
		if (!((count($avatarslist)<2) && ($avatarslist['blank.png'] == _NONE))) {
        $cntavs = 0;
        $counter = isset( $_GET['start'] ) ? intval( $_GET['start'] ) : 0;
        foreach ( $avatarslist as $file => $name ) {
            echo '<td><img src="' . ZAR_UPLOAD_URL . '/' . $file . '" alt="' . $name . '" style="padding:10px; vertical-align:top;"  /><br />' . $name . '<br /><input name="myimage" type="button" value="' . _SELECT . '" onclick="myimage_onclick(' . $counter . ')" /></td>';
            $counter++;
            $cntavs++;
            if ( $cntavs > 8 ) {
                echo '</tr><tr>';
                $cntavs = 0;
            }
        }
        echo '</tr></table></form>';
        if ( @$_REQUEST['closebutton'] ) {
            echo '<div style="text-align:center;"><input class="formButton" value="' . _CLOSE . '" type="button" onclick="javascript:window.close();" /></div>';
        }
		} else {
			echo _US_NOAVATARS;
		}
        zarilia_footer();
        exit();
    }

    /**
     * ZariliaUserAvatar::isdefault()
     *
     * @return
     */
    function isdefault()
    {
        global $zariliaUser;

        require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

        $config_handler = &zarilia_gethandler( 'config' );
        $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );

        $form = new ZariliaThemeForm( _US_CHOOSEAVT, 'uploadavatar', 'edituser.php' );
        $avatar_select = new ZariliaFormSelect( '', 'user_avatar', $zariliaUser->getVar( 'user_avatar' ), 5 );
        $avatar_handler = &zarilia_gethandler( 'avatar' );
		$allitems = &$avatar_handler->getAList( 's' );
		$extra = '';
		if ((count($allitems)<2) && ($allitems['blank.png'] == _NONE)) {
		   $extra = 'disabled="disabled" ';
		}
        $avatar_select->addOptionArray( $allitems );
        $avatar_select->setExtra( "onchange='showImgSelected(\"avatar\", \"user_avatar\", \"\", \"\", \"" . ZAR_UPLOAD_URL . "\")' ".$extra );
        $avatar_tray = new ZariliaFormElementTray( _US_AVATAR, '&nbsp;' );
        $avatar_tray->addElement( $avatar_select );
		if (($file = $zariliaUser->getVar( 'user_avatar' ))=='') $file = 'blank.png';
        $avatar_tray->addElement( new ZariliaFormLabel( '', "<img src='" . ZAR_UPLOAD_URL . "/" . $file . "' name='avatar' valign='middle' id='avatar' alt='' /><br /><br /><a href=\"javascript:openWithSelfMain('" . ZAR_URL . "/index.php?page_type=avatar&amp;act=avatarDisplay','avatars',600,400);\">" . _LIST . "</a><br /><br />" ) );
        $form->addElement( $avatar_tray );

        $form->addElement( new ZariliaFormHidden( 'uid', $zariliaUser->getVar( 'uid' ) ) );
        $form->addElement( new ZariliaFormHidden( 'act', 'avatarchoose' ) );
        $form->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );

        $avatar_allow_upload = ( $zariliaConfigUser['avatar_allow_upload'] == 1 && $zariliaUser->getVar( 'posts' ) >= $zariliaConfigUser['avatar_minposts'] ) ? 1 : 0;
        $oldavatar = $zariliaUser->getVar( 'user_avatar' );
        $avatar_show = ( $oldavatar && $oldavatar != 'blank.png' ) ? 1 : 0;

        $content['form'] = $form;
        $content['file'] = 'avatar';
        $this->addOptions( 'template_main', 'system_avatarform.html' );
        $this->addOptions(
            array( 'title' => _US_AVATARHEADING,
                'subtitle' => _US_AVATARHEADING_DSC,
                'content' => $content,
                'menubar' => $menubar,
                'allowupload' => 1, // $avatar_allow_upload,
                'avatar_show' => $avatar_show, // $avatar_allow_upload,
                'avatar_width' => $zariliaConfigUser['avatar_width'],
                'avatar_height' => $zariliaConfigUser['avatar_height'],
                'avatar_maxsize' => $zariliaConfigUser['avatar_maxsize'],
                'page_type' => 'avatar'
                )
            );
        return $this->option;
    }
}

?>