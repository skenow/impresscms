<?php
// $Id: index.php,v 1.2 2007/04/21 09:42:24 catzwolf Exp $
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( 'Access Denied' );
}
require_once 'admin_menu.php';
require_once 'functions.php';
require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
/**
 */
$_callback = &zarilia_gethandler( 'member' );
$do_callback = ZariliaCallback::getSingleton();
$do_callback->setCallback( $_callback );
switch ( $op ) {
    case 'help':
    case 'about':
    case 'edit':
        $do_callback->setmenu( 2 );
        call_user_func( array( $do_callback, $op ) );
        break;

    case 'submit':
        $criteria = new CriteriaCompo();
        switch ( $op ) {
            case 'submit':
                add_critera_text( $criteria, 'uname', 'user_uname', 'user_uname_match' );
                add_critera_text( $criteria, 'name', 'user_name', 'user_name' );
                add_critera_text( $criteria, 'email', 'user_email', 'user_email_match' );
                add_critera_text( $criteria, 'email', 'user_url', ZAR_MATCH_CONTAIN, 1 );
                add_critera_text( $criteria, 'url', 'user_url', ZAR_MATCH_CONTAIN, 1 );
                add_critera_time( $criteria, 'last_login', 'user_lastlog_more' );
                add_critera_time( $criteria, 'last_login', 'user_lastlog_less' );
                add_critera_time( $criteria, 'user_regdate', 'user_reg_more' );
                add_critera_time( $criteria, 'user_regdate', 'user_reg_less' );

                if ( !empty( $_REQUEST['user_posts_more'] ) && is_numeric( $_REQUEST['user_posts_more'] ) ) {
                    $criteria->add( new Criteria( 'posts', intval( $_POST['user_posts_more'] ), '>' ) );
                }
                if ( !empty( $_REQUEST['user_posts_less'] ) && is_numeric( $_REQUEST['user_posts_less'] ) ) {
                    $criteria->add( new Criteria( 'posts', intval( $_POST['user_posts_less'] ), '<' ) );
                }
                $user_mailok = zarilia_cleanRequestVars( $_REQUEST, 'user_mailok', 0 );
                if ( !empty( $user_mailok ) ) {
                    if ( $user_mailok == 'mailng' ) {
                        $criteria->add( new Criteria( 'user_mailok', 0 ) );
                    } elseif ( $user_mailok == "mailok" ) {
                        $criteria->add( new Criteria( 'user_mailok', 1 ) );
                    } else {
                        $criteria->add( new Criteria( 'user_mailok', 0, '>=' ) );
                    }
                }
                $user_type = zarilia_cleanRequestVars( $_REQUEST, 'user_type', 0 );
                if ( $user_type ) {
                    if ( $user_type == "inactv" ) {
                        $criteria->add( new Criteria( 'level', 0, '=' ) );
                    } elseif ( $user_type == "actv" ) {
                        $criteria->add( new Criteria( 'level', 0, '>', 'OR' ) );
                        $criteria->add( new Criteria( 'level', 5, '<=' ) );
                    } elseif ( $user_type == "suspend" ) {
                        $criteria->add( new Criteria( 'level', 6, '=' ) );
                    } else {
                        $criteria->add( new Criteria( 'level', 0, '>=' ) );
                    }
                }
                $sort = zarilia_cleanRequestVars( $_REQUEST, 'user_sort', 'uname', XOBJ_DTYPE_TXTBOX );
                $order = zarilia_cleanRequestVars( $_REQUEST, 'user_order', 'ASC', XOBJ_DTYPE_TXTBOX );
                $limit = zarilia_cleanRequestVars( $_REQUEST, 'limit', 1 );
                $start = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
                $total = call_user_func( array( $_callback, 'getUserCount' ), $criteria );
                $criteria->setSort( $sort );
                $criteria->setOrder( $order );
                $criteria->setLimit( $limit );
                $criteria->setStart( $start );
                break;

            case 'newusers':
            default:
                $criteria->add( new Criteria( 'level', 0, '=' ) );
                $criteria->setSort( 'user_regdate' );
                $criteria->setOrder( 'DESC' );
                $total = call_user_func( array( $_callback, 'getUserCount' ), $criteria );
                break;
        } // switch
        $foundusers = call_user_func( array( $_callback, 'getUsers' ), $criteria, true );
        $display = ( $total < $limit ) ? $total : $limit;

        /**
         * Start of User Output
         */
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=form' => _AM_NEWSEARCH ) );
        echo '<div><b>' . _AM_RESULTS . '</b></div>';
        echo '<div>' . sprintf( _AM_USERSFOUND, $total ) . '</span></div><div style="padding-bottom: 12px;"><span>' . sprintf( _AM_DISPLAYING, $display, $total ) . '</div>';

        require_once ZAR_ROOT_PATH . '/class/class.tlist.php';
        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'uid', '5%', 'center', true );
        $tlist->AddHeader( 'uname', '150px', 'left', true );
        $tlist->AddHeader( 'rank', '', 'center', true );
        $tlist->AddHeader( 'status', '', 'center', false );
        $tlist->AddHeader( 'user_regdate', '', 'center', true );
        $tlist->AddHeader( 'last_login', '', 'center', true );
        $tlist->AddHeader( 'ipaddress', '', 'center', true );
        $tlist->AddHeader( '', '', 'center', 2 );
        $tlist->AddHeader( 'action', '10%', 'left' );
        $tlist->AddFormStart( 'post', $addonversion['adminpath'] . '&amp;op=' . $op, 'memberslist' );
        $i = 0;
        foreach ( $foundusers as $obj ) {
            $status = $obj->getVar( 'level' );
            $uid = $obj->getVar( 'uid' );
            switch ( $status ) {
                case 0:
                    $user_status = _MA_AD_UNOTACTIVE;
                    $class = 'notactive';
                    break;
                case 6:
                    $user_status = _MA_AD_SUSPENDED;
                    $class = 'suspended';
                    break;
                case 6:
                default:
                    $user_status = _MA_AD_ACTIVE;
                    if ( $i % 2 ) {
                        $class = 'odd';
                    } else {
                        $class = 'even';
                    }
                    break;
            } // switch
            $value = '<input type="checkbox" name="memberslist_id[]" id="memberslist_id[]" value="' . $obj->getVar( 'uid' ) . '" />';
            $ret = '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=edituser">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
            $ret .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=deluser">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';
            $ret .= '<a href="index.php?fct=mailusers&amp;uid=' . $uid . '&amp;type=0">' . zarilia_img_show( 'contact', _CONTACT ) . '</a>';
            if ( $status >= 1 && $status <= 5 ) {
                $ret .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=suspend">' . zarilia_img_show( 'suspend', _SUSPEND ) . '</a>';
                $ret .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=deactivate">' . zarilia_img_show( 'deactivate', _DEACTIVATE ) . '</a>';
            } else if ( $status == 0 || $status == 6 ) {
                $ret .= '<a href="index.php?fct=users&amp;uid=' . $uid . '&amp;op=activate">' . zarilia_img_show( 'activate', _ACTIVATE ) . '</a>';
            }
            $tlist->add(
                array( $obj->getVar( 'uid' ),
                    $obj->getUnameFromId( 0, 0, 1 ),
                    $obj->rank( true ),
                    $user_status,
                    $obj->getTimeStamp( null, 'user_regdate' ),
                    $obj->getTimeStamp( null, 'last_login' ),
                    $obj->getVar( 'ipaddress' ),
                    $obj->getCheckbox( $i, 'memberslist_id' ),
                    $ret
                    ), $class );
            $i++;
        }
        $group_array = array();
        $this_array = array( 'users' => _DELETE, 'mailusers' => _AM_SENDMAIL );

		$group = zarilia_cleanRequestVars( $_REQUEST, 'group', 0 );
        if ( $group > 0 ) {
            $add2group = call_user_func( array( $_callback, 'getGroup' ), $group );
            $group_array = array( 'groups' => sprintf( _AM_ADD2GROUP, $add2group->getVar( 'name' ) ) );
        }
        $total_array = array_merge( $this_array, $group_array );
        $ret = zarilia_getSelection( $total_array, 'users', 'fct', 1, 0, false, false, '', 0, false );
        if ( $group > 0 ) {
            $ret .= '<input type="hidden" name="groupid" value="' . $group . "' />";
        }
        $ret .= '&nbsp;<input type="submit" class="formbutton" name="submited" value="' . _SUBMIT . '" />';
        $tlist->addFooter( $ret );
        finduser_nav( $total, $limit, $start );
        $tlist->render();
        break;

    case 'form':
    default:
        zarilia_cp_header();
        $menu_handler->render( 0 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . '&amp;op=form' => _AM_NEWSEARCH ) );
        call_user_func( array( $do_callback, 'form' ) );
        break;
} // switch
zarilia_cp_footer();

?>