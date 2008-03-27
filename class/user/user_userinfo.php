<?php
// $Id: user_userinfo.php,v 1.6 2007/05/09 14:14:22 catzwolf Exp $
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
 * ZariliaUserUserinfo
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2007
 * @version $Id: user_userinfo.php,v 1.6 2007/05/09 14:14:22 catzwolf Exp $
 * @access public
 */
class ZariliaUserUserinfo extends ZariliaAuth
{
    var $user;
    /**
     * Authentication Service constructor
     */
    function ZariliaUserUserinfo ()
    {
    }

    function doInformation()
    {
        require_once ZAR_ROOT_PATH . '/class/zarilialists.php';

        global $zariliaUser, $zariliaUserIsAdmin, $config_handler;

        $ret = array();
        $ret['user_uid'] = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0 );
        if ( is_object( $zariliaUser ) && ( $ret['user_uid'] == $zariliaUser->getVar( 'uid' ) ) )
        {
            /**
             * We select the current zarilia user for account information
             */
            $this->user = &$zariliaUser;
            $ret['user_ownpage'] = true;
            $zariliaConfigUser = &$config_handler->getConfigsByCat( ZAR_CONF_USER );
            $ret['user_candelete'] = ( $zariliaConfigUser['self_delete'] == 1 ) ? true : false;
        }
        else
        {
            /**
             * We select the current zarilia user for account information
             */
            $member_handler = &zarilia_gethandler( 'member' );
            $this->user = &$member_handler->getUser( $ret['user_uid'] );
            $ret['user_ownpage'] = false;
        }

        if ( !is_object( $this->user ) )
        {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _US_SELECTNG );
            return false;
        }
        $ret['template_main'] = 'system_userinfo.html';
        $ret['user_ownpage'] = ( $zariliaUserIsAdmin ) ? $this->user->getVar( 'uid' ) : $ret['user_ownpage'];
        $ret['user_online_image'] = ( $this->user->isOnline() ) ? ZAR_UPLOAD_URL.'/user_online.png' : ZAR_UPLOAD_URL.'/user_offline.png';
        $ret['user_online_status'] = ( $this->user->isOnline() ) ? 'Online' : 'Offline';
        $ret['user_lastlogin'] = $this->user->getVar( 'last_login' );
        $ret['user_avatarurl'] = $this->user->avatar();
        $ret['user_viewemail'] = ( $zariliaUserIsAdmin || $this->user->getVar( 'user_viewemail' ) ) ? $this->user->email( "S", true ) : '&nbsp;';
        $ret['user_rankimage'] = $this->user->rank( true );
        $rank = $this->user->rank();

        $ret['user_ranktitle'] = $rank->getVar( 'rank_title' );
        $ret['user_realname'] = ( $this->user->getVar( 'name' ) ) ? $this->user->getVar( 'name' ) : $this->user->getVar( 'uname' );
        $ret['user_uname'] = $this->user->getVar( 'uname' );
        $ret['user_joindate'] = $this->user->getVar( 'user_regdate' );
        $ret['user_posts'] = $this->user->getVar( 'posts' );
        $ret['user_usrmedpref'] = ZariliaLists::usermedia( $this->user->getVar( 'user_usrmedpref' ) );
        $ret['user_usrlevel'] = ZariliaLists::userlevel( $this->user->getVar( 'user_usrlevel' ) );
        return $ret;
    }

    function posts()
    {
        global $zariliaUser;

		$ret = $this->doInformation();
        $addon_handler = &zarilia_gethandler( 'addon' );
        $criteria = new CriteriaCompo( new Criteria( 'hassearch', 1 ) );
        $criteria->add( new Criteria( 'isactive', 1 ) );
        $mids = &array_keys( $addon_handler->getList( $criteria ) );
        if ( count( $mids ) )
        {
            $gperm_handler = &zarilia_gethandler( 'groupperm' );
            $groups = ( is_object( $zariliaUser ) && !empty( $zariliaUser ) ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
			foreach ( $mids as $mid )
            {
                if ( $gperm_handler->checkRight( 'addon_read', $mid, $groups ) )
                {
                    $addon = &$addon_handler->get( $mid );
                    $results = &$addon->search( '', '', 5, 0, $this->user->getVar( 'uid' ) );
                    $count = count( $results );
                    if ( is_array( $results ) && $count > 0 )
                    {
                        for ( $i = 0; $i < $count; $i++ )
                        {
                            $results[$i]['link'] = 'addons/' . $addon->getVar( 'dirname' ) . '/' . $results[$i]['link'];
                            $title = ucfirst( strtolower( $results[$i]['title'] ) );
                            $results[$i]['title'] = htmlSpecialChars( stripslashes( $title ), ENT_QUOTES );
                            $results[$i]['time'] = $results[$i]['time'] ? formatTimestamp( $results[$i]['time'] ) : '';
                        }
                        $showall_link = '';
                        if ( $count == 5 )
                        {
                            $showall_link = '<a href="search.php?op=showallbyuser&amp;mid=' . $mid . '&amp;uid=' . $this->user->getVar( 'uid' ) . '">' . _US_SHOWALL . '</a>';
                        }
                        $ret['addons'] = array( 'name' => $addon->getVar( 'name' ), 'results' => $results, 'showall_link' => $showall_link );
                        $zariliaTpl->append( 'addons', array( 'name' => $addon->getVar( 'name' ), 'results' => $results, 'showall_link' => $showall_link ) );
                    }
                    unset( $addon );
                }
            }
            unset( $criteria );
        }
        return $ret;
    }

    /**
     * ZariliaUserUserinfo::isdefault()
     *
     * @return
     */
    function isdefault()
    {
        $ret = $this->doInformation();

        $profilecat_handler = &zarilia_gethandler( 'profilecategory' );
        $_array = $profilecat_handler->getList( 0 );
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', key( $_array ) );
        if ( is_array( $_array ) && count( $_array ) )
        {
            if ( count( $_array ) > 1 )
            {
                $extra = "onchange=\"location='" . ZAR_URL . "/index.php?page_type=userinfo&amp;uid=" . $ret['user_uid'] . "&amp;opt='+this.options[this.selectedIndex].value\"";
                $ret['user_tabs'] = zarilia_getSelection( $_array, $opt, 'opt', 1, 0 , false, "", $extra, 0, false );
            }
            else if ( isset( $_array[1] ) )
            {
                // dummy
            }
            else
            {
                $opt = 0;
            }
            /**
             * Get
             */
            $profile_id = zarilia_cleanRequestVars( $_REQUEST, 'profile_id', $opt );
            $userprofile_handler = &zarilia_gethandler( 'userprofile' );
            $form = &$userprofile_handler->displayUserProfile( $profile_id, $ret['user_uid'], $opt );
            $ret['user_form'] = $form;
            $ret['lang_opt'] = $_array[$profile_id];
            $ret['show_posts'] = 1;
        }
        return $ret;
    }

    function sendemail()
    {
        echo "<a href=\"mailto:?subject=send a page url&body=$PHP_SELF\">Send this page to a friend</a>";
        return '';
    }
}

?>