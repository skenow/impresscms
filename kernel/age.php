<?php
// $Id: age.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
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

global $zariliaConfig;
if ( is_readable( ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/age.php' ) ) {
    require ZAR_ROOT_PATH . '/language/' . $zariliaConfig['language'] . '/age.php';
} else {
    require ZAR_ROOT_PATH . '/language/english/age.php';
}
/**
 * Zarilia Age Class
 *
 * @package kernel
 * @author John Neill AKA Catzwolf
 * @copyright (c) 2006 Zarilia
 */
class ZariliaAge extends ZariliaObject {
    /**
     * constructor
     */
    function ZariliaAge() {
        $this->zariliaObject();
        $this->initVar( 'age_id', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'age_mid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'age_itemid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'age_dtitle', XOBJ_DTYPE_TXTBOX, null, false, 255 );
        $this->initVar( 'age_ip', XOBJ_DTYPE_TXTBOX, null, false, 15 );
        $this->initVar( 'age_uid', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'age_agreed', XOBJ_DTYPE_INT, null, false );
        $this->initVar( 'age_date', XOBJ_DTYPE_LTIME, time(), false );
        $this->initVar( 'age_gdate', XOBJ_DTYPE_TXTBOX, null, false, 15 );
    }

    function formEdit( $caption = '' ) {
        if ( is_readable( ZAR_ROOT_PATH . '/kernel/kernel_forms/age.php' ) ) {
            include ZAR_ROOT_PATH . '/kernel/kernel_forms/age.php';
        }
    }

    /**
     * parm: text: 		$name 		- redirct via op
     * parm: intval: 	$age_mid 	- this is the addon id we are working on
     * parm: intval: 	$age_itemid - this is the item within the addon we are working on
     * parm: text:		$item_name 	-
     * parm: array(): 	$hidden 	- an array of hidden items
     */
    function show_allowed( $caption, $name = 'age_check', $age_mid = 0, $age_itemid = 0, $item_name = '', $hidden = '', $return = false ) {
        $_date = time();
        $d = date( "j", $_date );
        $m = date( "m", $_date );
        $year = date( "Y", $_date );

        include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
        $sform = new ZariliaThemeForm( $caption, 'ageform', zarilia_getenv( 'PHP_SELF' ) );
        $titles_tray = new ZariliaFormElementTray( '', '<br />' );
        /**
         * Days
         */
        $date_select = new ZariliaFormSelect( _AD_AGE_DAY, 'day', $d );
        for ( $i = 1; $i <= 31; $i++ ) {
            $date_select->addOption( $i, $i );
        }
        /**
         * months
         */
        $mon_array = array( 1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec' );
        $mon_select = new ZariliaFormSelect( _AD_AGE_MONTH, 'mon', $m );
        $mon_select->addOptionArray( $mon_array );
        /**
         * years
         */
        $year_select = new ZariliaFormSelect( _AD_AGE_YEAR, 'year', $year );
        for ( $y = 1910; $y <= $year; $y++ ) {
            $year_select->addOption( $y, $y );
        }

        $agree_tray = new ZariliaFormElementTray( _AD_AGE_AGEVERICATION, "&nbsp;" );
        $agree_tray->addElement( $mon_select );
        $agree_tray->addElement( $date_select );
        $agree_tray->addElement( $year_select );
        $sform->addElement( $agree_tray );

        $titles_checkbox = new ZariliaFormCheckBox( '', "user_coppa_agree", 0 );
        $titles_checkbox->addOption( 1, _AD_AGE_IAMOVER );
        $titles_tray->addElement( $titles_checkbox );
        $sform->addElement( $titles_tray );

        $sform->addElement( new ZariliaFormHidden( "op", $name ) );
        $sform->addElement( new ZariliaFormHidden( "age_dtitle", $item_name ) );
        $sform->addElement( new ZariliaFormHidden( "age_itemid", $age_itemid ) );
        foreach ( $hidden as $k => $v ) {
            $sform->addElement( new ZariliaFormHidden( $k, $v ) );
        }
        if ( !$return ) {
            $sform->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
            $sform->display();
        } else {
            return $sform;
        }
    }

    function show_form( $caption, $name = 'age_check', $age_mid = 0, $age_itemid = 0, $item_name = '', $return = true ) {
        $_date = time();
        $d = date( 'j', $_date );
        $m = date( 'm', $_date );
        $year = date( 'Y', $_date );

        $mon_array = array( 1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec' );
        $mon_select = zarilia_getSelection( $mon_array, $m, $value = 'mon', $size = '1', $emptyselect = false , false, $noselecttext = "------------------", $extra = '', 0, $echo = false );

        for ( $i = 1; $i <= 31; $i++ ) {
            $day_array[$i] = $i;
        }
        $day_select = zarilia_getSelection( $day_array, $d, $value = 'day', $size = '1', $emptyselect = false , false, $noselecttext = "------------------", '', 0, false );

        for ( $y = 1910; $y <= $year; $y++ ) {
            $year_array[$y] = $y;
        }
        $year_select = zarilia_getSelection( $year_array, $year, $value = 'year', $size = '1', $emptyselect = false , false, $noselecttext = "------------------", '', 0, false );

        $checked = ( isset( $_SESSION['zariliaregister']['hidden']['user_coppa_agree'] ) && $_SESSION['zariliaregister']['hidden']['user_coppa_agree'] == 1 ) ? 'checked' : '';

        $sform = "<table width='100%' class='outer' cellspacing='1'>
			    <tr>
			      <th colspan='2'>" . _US_REG_PRIVACY_HEADING . "</th>
			    </tr>
			    <tr valign='top' align='left'>
			      <td class='head' width='35%'>" . _AD_AGE_AGEVERICATION . "</td>
			      <td class='even'>
				  	<div>" . _AD_AGE_MONTH . "&nbsp;" . $mon_select . "&nbsp;" . _AD_AGE_DAY . "&nbsp;" . $day_select . "&nbsp;" . _AD_AGE_YEAR . "&nbsp;" . $year_select . "</div>
				  </td>
			    </tr>
			    <tr valign='top' align='left'>
			      <td class='head' width='35%'> </td>
			      <td class='even'><div id=''>
			          <table cellpadding='0' cellspacing='0'>
			            <tr>
			              <td><input type='checkbox' id='user_coppa_agree' name='user_coppa_agree' value='1' $checked/>" . _AD_AGE_IAMOVER . "</td>
			            </tr>
			          </table>
			        </div></td>
			    </tr>
			    <input type='hidden' name='page_type' id='register' value='profile' />
			    <input type='hidden' name='age_dtitle' id='age_dtitle' value='register' />
			    <input type='hidden' name='age_itemid' id='age_itemid' value='11' />
			    <tr class='foot'>
			      <td colspan='2'></td>
			    </tr>
			  </table>";
        return $sform;
    }

    function getLinkedUserName( $linked = 1 ) {
        $ret = zarilia_getLinkedUnameFromId( $this->getVar( "age_uid" ), 0, $linked );
        return $ret;
    }

    function getAgreed() {
        $ret = ( $this->getVar( "age_agreed" ) ) ? _YES : _NO;
        return $ret;
    }

    function getMid() {
        if ( $this->getVar( 'age_mid' ) == 0 ) {
            $this->setVar( 'age_mid', 1 );
        }
        return $this->getVar( 'age_mid' );
    }
}

/**
 * ZariliaAgeHandler
 *
 * @package
 * @author John Neill AKA Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: age.php,v 1.3 2007/04/21 09:44:19 catzwolf Exp $
 * @access public
 */
class ZariliaAgeHandler extends ZariliaPersistableObjectHandler {
    var $age_modid;
    var $age_dtitle;
    var $age_ip;
    var $age_uid;
    var $age_rated;
    /**
     * ZariliaAgeHandler::ZariliaAgeHandler()
     *
     * @param  $db
     * @return
     */
    function ZariliaAgeHandler( &$db ) {
        $this->ZariliaPersistableObjectHandler( $db, 'age', 'zariliaage', 'age_id', 'age_dtitle' );
    }

    /**
     * ZariliaAgeHandler::getAges()
     *
     * @param  $db
     * @return
     */
    function &getAges( $limit = 0, $mod_id = 0, $pulldate, $start = 0, $sort = 'age_id', $order = 'DESC' ) {
        $addon_date = &$this->getaDate( $pulldate );

        $criteria = new CriteriaCompo();
        $criteria->setSort( $sort );
        $criteria->setOrder( $order );
        $criteria->setStart( $start );
        $criteria->setLimit( $limit );
        if ( $mod_id > 0 ) {
            $criteria->add( new Criteria( 'age_mid', $mod_id ) );
        }
        if ( $addon_date['begin'] && $addon_date['end'] ) {
            $criteria->add( new Criteria( 'age_date', $addon_date['begin'], '>=' ) );
            $criteria->add( new Criteria( 'age_date', $addon_date['end'], '<=' ) );
        }
        $criteria->add( new Criteria( 'age_agreed', '1' ) );
        return $this->getObjects( $criteria, false );
    }

    function getAgeObj( $nav = array(), $_mid = null, $_date = null ) {
        $criteria = new CriteriaCompo();
        // if ( !empty( $_date ) ) {
        // /* Calculates the date selected only, not the date till the last date entered in the database*/
        // $addon_date = &$this->getaDate( $_date );
        // $criteria->add( new Criteria( 'age_date', $addon_date['begin'], '>=' ) );
        // $criteria->add( new Criteria( 'age_date', $addon_date['end'], '<=' ) );
        // }
        if ( $_mid ) {
            if ( $_mid == 1 ) {
                $criteria->add( new Criteria( 'age_date', 0, 'OR' ) );
            }
            $criteria->add( new Criteria( 'age_mid', intval( $_mid ) ) );
        }
        $obj['count'] = $this->getCount( $criteria, false );

        $criteria->setSort( $nav['sort'] );
        $criteria->setOrder( $nav['order'] );
        $criteria->setStart( $nav['start'] );
        $criteria->setLimit( $nav['limit'] );
        $obj['list'] = $this->getObjects( $criteria, false );
        return $obj;
    }

    /**
     * ZariliaAgeHandler::doCalcAge()
     *
     * @return
     */
    function doCalcAge() {
        $tmonth = date( 'n' );
        $tday = date( 'j' );
        $tyear = date( 'Y' );

        $years = $tyear - $this->year;
        if ( $tmonth <= $this->month ) {
            if ( $this->month == $tmonth ) {
                if ( $this->day > $tday )
                    $years--;
            } else
                $years--;
        }
        return $years;
    }

    /**
     * ZariliaAgeHandler::age_allowed()
     *
     * @return
     */
    function age_allowed( $allowed_age = 0 ) {
        $age = $this->doCalcAge();
        if ( $age >= $allowed_age ) {
            $_age_new = $this->create();
            $_age_new->setVar( 'age_mid', ( $this->age_modid == 0 ) ? 1 : $this->age_modid );
            $_age_new->setVar( 'age_itemid', $this->age_itemid );
            $_age_new->setVar( 'age_dtitle', $this->age_dtitle );
            $_age_new->setVar( 'age_ip', $this->age_ip );
            $_age_new->setVar( 'age_uid', $this->age_uid );
            $_age_new->setVar( 'age_agreed', $this->user_coppa_agree );
            $_age_new->setVar( 'age_date', time() );
            $_date = $this->year . "-" . $this->month . "-" . $this->day;
            $_age_new->setVar( 'age_gdate', $_date );
            $ret = ( $this->insert( $_age_new, false ) ) ? true : false;
            return $ret;
        }
        return false;
    }

    /**
     * ZariliaAgeHandler::doItemCheck()
     *
     * @return
     */
    function doItemCheck( $age_modid = 0, $age_itemid = 0, $age_rated = 0, $age_title = '' ) {
        global $zariliaUser;

        $age_uid = is_object( $zariliaUser ) ? $zariliaUser->getVar( 'uid' ) : 0;
        $age_date = mktime ( 0, 0, 0, date( "m", time() ), date( "j", time() ), date( "Y", time() ) );

        $criteria = new CriteriaCompo();
        $criteria->add( new Criteria( 'age_dtitle', $age_title ) );
        $criteria->add( new Criteria( 'age_mid', intval( $age_modid ) ) );
        $criteria->add( new Criteria( 'age_itemid', intval( $age_itemid ) ) );
        $criteria->add( new Criteria( 'age_ip', getip() ) );
        $criteria->add( new Criteria( 'age_uid', $age_uid ) );
        $criteria->add( new Criteria( 'age_agreed', 1 ) );
        $criteria->add( new Criteria( 'age_date', $age_date, '>' ) );
        $count = $this->getCount( $criteria );
        return $count;
    }

    /**
     * ZariliaAgeHandler::doAgeCheck()
     *
     * @return
     */
    function doAgeCheck( $_REQUEST = array(), $allowed_age = 13, $show_allowed = 1 ) {
        $error = array();
        $this->user_coppa_agree = zarilia_cleanRequestVars( $_REQUEST, 'user_coppa_agree', 0 );
        $this->age_dtitle = zarilia_cleanRequestVars( $_REQUEST, 'age_dtitle', 'register', XOBJ_DTYPE_TXTBOX );
        $this->age_itemid = zarilia_cleanRequestVars( $_REQUEST, 'age_itemid', 0 );
        $this->check_done = zarilia_cleanRequestVars( $_REQUEST, 'check_done', 0 );
        $this->age_ip = getip();

        $this->month = intval( @$_REQUEST["mon"] );
        $this->day = intval( @$_REQUEST["day"] );
        $this->year = intval( @$_REQUEST["year"] );

        if ( $this->check_done ) {
            if ( !$this->user_coppa_agree ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, _AD_AGE_NOT_AGREE );
            }
            $this->allowed = $this->age_allowed( $allowed_age );
            if ( $this->allowed == false ) {
                $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, _AD_AGE_NOT_ALLOWED );
            }
        } else if ( $this->age_rated >= 4 && $this->check_done == 0 ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_NOTICE, _AD_AGE_NOT_ALLOWED );
        } else {
            return true;
        }
        return false;
    }

    /**
     * ZariliaAgeHandler::getaDate()
     *
     * @return
     */
    function getaDate( $exp_value = '', $exp_time = '', $useMonth = 0 ) {
        $_date_arr = array();
        $_date = ( $exp_value ) ? $exp_value : time();
        $d = date( "j", $_date ) ;
        $m = date( "m", $_date ) ;
        $y = date( "Y", $_date ) ;
        if ( $useMonth > 0 ) {
            /**
             * We use +1 for the the previous month and not the next here,
             * if the day var is set to 0 ( You would have thought a neg value would have been correct here but nope!
             * Bloody strange way of doing it if you ask me! :-/ )
             */
            $_date_arr['begin'] = mktime ( 0, 0, 0, $m, 1, $y );
            $_date_arr['end'] = mktime ( 0, 0, 0, $m + 1, 0, $y );
        } else {
            /**
             * 86400 = 1 day, while 86399 = 23 hours and 59 mins and 59 secs
             */
            $_date_arr['begin'] = mktime ( 0, 0, 0, $m, $d, $y );
            $_date_arr['end'] = mktime ( 23, 59, 59, $m, $d, $y );
        }
        return $_date_arr;
    }

    function &getAddon() {
        global $addon_handler;
        static $_cachedAddon_list;

        if ( !empty( $_cachedAddon_list ) ) {
            $_addon = &$_cachedAddon_list;
            return $_addon;
        } else {
            $addon_list = &$addon_handler->getList();
            $_cachedAddon_list = &$addon_list;
            return $addon_list;
        }
    }
}

?>