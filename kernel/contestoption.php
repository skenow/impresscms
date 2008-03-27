<?php
// $Id: contestoption.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaContestOption
 *
 * @package
 * @author John
 * @copyright Copyright (c) 2006
 * @version $Id: contestoption.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 **/
class ZariliaContestOption extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaContestOption( $id = null )
    {
        $this->zariliaObject();
        $this->initVar( 'contest_id', XOBJ_DTYPE_INT, null, true );
        $this->initVar( 'contest_name', XOBJ_DTYPE_TXTBOX, null, true, 60 );
        $this->initVar( 'contest_email', XOBJ_DTYPE_TXTBOX, null, true, 60 );
        $this->initVar( 'contest_image', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'contest_main_image', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'contest_main_image_url', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'contest_box_image', XOBJ_DTYPE_TXTBOX, null, true, 255 );
        $this->initVar( 'contest_text', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'contest_prize', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'contest_wintext', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'contest_rules', XOBJ_DTYPE_TXTAREA, null, false, null );
        $this->initVar( 'contest_publishdate', XOBJ_DTYPE_INT, time(), false );
        $this->initVar( 'contest_expiredate', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'contest_wamount', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'contest_auto', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'contest_active', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'contest_emailadmin', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'contest_multientry', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'contest_date', XOBJ_DTYPE_INT, time(), true );
        $this->initVar( 'contest_complete', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'contest_members_only', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'contest_only_once', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'contest_numdays', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'contest_main', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'contest_wemailsent', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'contest_cat_id', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'dohtml', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'doxcode', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'dosmiley', XOBJ_DTYPE_INT, 1, false );
        $this->initVar( 'doimage', XOBJ_DTYPE_INT, 0, false );
        $this->initVar( 'dobr', XOBJ_DTYPE_INT, 1, false );
    }

    function getTimeStamp( $time = null, $var = 'user_regdate' )
    {
        $time = $this->getVar( $var );
        return ( strlen( strval( $time ) ) == 10 ) ? formatTimestamp( $time ) : 'Empty';
    }

    function wf_Groups()
    {
        global $zariliaAddon;
        include_once 'class.permissions.php';
        $perms = &new wfPermission( 'wf_cat_perm', 'wfcategory', 'Category Permissions' );
        return $perms->wfPermission_get( $this->getVar( 'categoryid' ) );
    }
}

/**
 * ZariliaContestOptionHandler
 *
 * @package
 * @author John Neill
 * @copyright Copyright (c) 2005
 * @version $Id: contestoption.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaContestOptionHandler extends ZariliaPersistableObjectHandler {
    var $db;

    /**
     * ZariliaContestOptionHandler::ZariliaContestOptionHandler()
     *
     * @param mixed $db
     * @return
     */
    function ZariliaContestOptionHandler( &$db )
    {
        $this->ZariliaPersistableObjectHandler( $db, 'contestoption', 'ZariliaContestOption', 'contest_id', 'contest_name' );
    }

    function &getContests( $limit = 0, $start = 0, $sort = 'contest_id', $order = 'ASC', $id_as_key = false )
    {
        $criteria = new CriteriaCompo();
        $criteria->setSort( $sort );
        $criteria->setOrder( $order );
        $criteria->setStart( $start );
        $criteria->setLimit( $limit );
        $obj = $this->getObjects( $criteria, $id_as_key );
		return $obj;
    }

    function &getmainContest( $id_as_key = false )
    {
        $criteria = new CriteriaCompo();
        $criteria->add ( new Criteria( 'contest_publishdate', 0, '>' ), 'AND' );
        $criteria->add ( new Criteria( 'contest_publishdate', time(), '<=' ), 'AND' );
        $criteria->add ( new Criteria( 'contest_expiredate', 0, '=' ), 'AND' );
        $criteria->add ( new Criteria( 'contest_expiredate', time(), '>' ), 'OR' );
        // $criteria -> add ( new Criteria( 'contest_main', 1, '=' ));
        $criteria->add ( new Criteria( 'contest_main', 1, '=' ), 'AND' );
        $criteria->add ( new Criteria( 'contest_active', 1, '=' ) );
        $criteria->setLimit( 1 );
        $_contest = $this->getObjects( $criteria, $id_as_key );
        return $_contest[0];
    }

    function &getActiveContests( $limit = 0, $start = 0, $sort = 'contest_id', $order = 'ASC', $id_as_key = false, $ignore = 0 )
    {
        $criteria = new CriteriaCompo();
        if ( $ignore > 0 ) {
            $criteria->add ( new Criteria( 'contest_id', intval( $ignore ), '!=' ) );
        }
        $criteria->add ( new Criteria( 'contest_publishdate', 0, '>' ), 'AND' );
        $criteria->add ( new Criteria( 'contest_publishdate', time(), '<=' ), 'AND' );
        $criteria->add ( new Criteria( 'contest_expiredate', 0, '=' ), 'AND' );
        $criteria->add ( new Criteria( 'contest_expiredate', time(), '>' ), 'OR' );
        $criteria->add ( new Criteria( 'contest_active', 1, '=' ) );

        $criteria->setSort( $sort );
        $criteria->setOrder( $order );
        $criteria->setStart( $start );
        $criteria->setLimit( $limit );
        $obj = $this->getObjects( $criteria, $id_as_key );
		return $obj;
    }

    function do_front_checks( &$obj )
    {
        $_date = time();
        if ( $this->getVar( '' ) == 0 ) {
            return 'This Contest is not active';
        }
        return false;
    }

    function check_zipcode( $zipcode )
    {
        if ( !preg_match( "/[^0-9]+$/ ", $zipcode ) ) {
            return true;
        } else {
            return false;
        }
    }

    function do_imagecheck( $_image )
    {
        $_path = ZAR_ROOT_PATH . '/images/articles/';
        $getimage = @getimagesize( $_path . $_image );
        return is_array( $getimage ) ? $_image : ''; //$_image;
    }

    function isActive( &$obj )
    {
        Global $zariliaUser;

        $contest_handler = &zarilia_gethandler( 'contest' );
        $contest_id = $obj->getVar( 'contest_id' );
        $time = time();

        // Check if actually published
        if ( $time > $obj->getVar( 'contest_expiredate' ) ) {
            if ( $obj->getVar( 'contest_complete' ) == 0 ) {
                echo "contest_complete";
				//Let do the contest closing stuff
/**
                $sql = "UPDATE " . $this->db_table . " SET contest_complete=1 WHERE contest_id=" . $contest_id;
                $result = $this->db->query( $sql );
                if ( !$result ) {
                    $_error = $this->db->error() . " : " . $this->db->errno();
                    $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_error, __FILE__, __LINE__ );
                    return false;
                } else {
                    $res = $this->sendadmincloseEmail( &$obj );
                }
//*/

                /**
                 * Check see if winning emails have been sent and if we will auto pick winners
                 */
                if ( $obj->getVar( 'contest_wemailsent' ) == 0 && $obj->getVar( 'contest_auto' ) == 1 ) {
/**
                    $sql = "UPDATE " . $this->db_table . " SET contest_wemailsent=1 WHERE contest_id=" . $contest_id;
                    if ( !$result = $this->db->query( $sql ) ) {
                        $_error = $this->db->error() . " : " . $this->db->errno();
                        $GLOBALS['zariliaLogger']->setSysError( E_USER_WARNING, $_error, __FILE__, __LINE__ );
                        return false;
                    } else {
//*/
                        /**
                         * Lets auto pick the winners
                         */
                        $criteria = new CriteriaCompo();
                        $contest_members = $contest_handler->getContestants( $contest_id );
                        foreach ( $contest_members as $k => $v ) {
                            $_members[$k] = $v;
                        }

						srand( time() );
                        $slot = array();
						for ( $i = 0; $i < $obj->getVar( 'contest_wamount' ); $i++ ) {
                            $random = rand( 0, sizeof( $_members )-1 );
                            $member_num = $_members[$random];
							//if (!in_array($member_num, $slot)) {
							   $slot[] = $member_num;
							//} else {
							//	$i--;
							//}
                        }
                    //}
                    print_r_html($slot);
					unset( $_members, $contest_members );
                } else {
				}
            }
        }
    }

    function sendadmincloseEmail( $obj = null )
    {
        global $zariliaConfig;
        $zariliaMailer = &getMailer();
        $zariliaMailer->useMail();
        $zariliaMailer->setTemplate( 'admin_contest_close.tpl' );
        $zariliaMailer->assign( 'SITENAME', $zariliaConfig['sitename'] );
        $zariliaMailer->assign( 'ADMINMAIL', $zariliaConfig['adminmail'] );
        $zariliaMailer->assign( 'CONTEST_NAME', $obj->getVar( 'contest_name' ) );
        $zariliaMailer->assign( 'CONTEST_DATE_END', $obj->getTimeStamp( null, 'contest_expiredate' ) );

		$member_handler = &zarilia_gethandler( 'member' );
        $zariliaMailer->setToGroups( $member_handler->getGroup( 1 ) );
        $zariliaMailer->setFromEmail( $zariliaConfig['adminmail'] );
        $zariliaMailer->setFromName( $zariliaConfig['sitename'] );
        $zariliaMailer->setSubject( sprintf( _CONTEST_CLOSE, $zariliaConfig['sitename'] ) );
        return ( $zariliaMailer->send( false ) ) ? true : false;
    }
}

?>