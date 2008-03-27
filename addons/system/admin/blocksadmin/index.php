<?php
// $Id: index.php,v 1.3 2007/04/21 09:41:58 catzwolf Exp $
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
if ( !is_object( $zariliaUser ) || !is_object( $zariliaAddon ) || !$zariliaUser->isAdmin( $zariliaAddon->getVar( 'mid' ) ) ) {
    exit( 'Access Denied' );
}
require_once 'admin_menu.php';
require_once ZAR_ROOT_PATH . '/class/zariliablock.php';
require ZAR_ROOT_PATH . '/addons/system/admin/blocksadmin/blocksadmin.php';

$bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
switch ( $op ) {
    case 'help':
        zarilia_cp_header();
        $menu_handler->render( 0 );
        if ( file_exists( ZAR_ROOT_PATH . '/addons/system/admin/' . $fct . '/admin_help.php' ) ) {
            include ZAR_ROOT_PATH . '/addons/system/admin/' . $fct . '/admin_help.php';
        }
        break;

    case 'about':
        zarilia_cp_header();
        $menu_handler->render( 3 );
        require_once( ZAR_ROOT_PATH . '/class/class.about.php' );
        $zarilia_about = new ZariliaAbout();
        $zarilia_about->display();
        break;

    case 'order':
        $bid = $_REQUEST['bid'];
        foreach ( array_keys( $bid ) as $i ) {
            if ( $_REQUEST['oldweight'][$i] != $_REQUEST['weight'][$i] || $_REQUEST['oldside'][$i] != $_REQUEST['side'][$i] || $_REQUEST['oldbcachetime'][$i] != $_REQUEST['bcachetime'][$i] ) {
                order_block( $_REQUEST['bid'][$i], $_REQUEST['weight'][$i], $_REQUEST['side'][$i], $_REQUEST['bcachetime'][$i] );
            }
        }
        redirect_header( $addonversion['adminpath'], 15, _DBUPDATED );
        break;

    case 'save':
        save_block( $_REQUEST['bside'], $_REQUEST['bweight'], $_REQUEST['btitle'], $_REQUEST['bcontent'], $_REQUEST['bctype'], $_REQUEST['baddon'], $_REQUEST['bcachetime'] );
        break;

    case 'update':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        $bcachetime = isset( $_REQUEST['bcachetime'] ) ? intval( $_REQUEST['bcachetime'] ) : 0;
        $options = isset( $_REQUEST['options'] ) ? $_REQUEST['options'] : array();
        $bcontent = isset( $_REQUEST['bcontent'] ) ? $_REQUEST['bcontent'] : '';
        $bctype = isset( $_REQUEST['bctype'] ) ? $_REQUEST['bctype'] : '';
        update_block( $bid, $_REQUEST['bside'], $_REQUEST['bweight'], $_REQUEST['btitle'], $bcontent, $bctype, $bcachetime, $_REQUEST['baddon'], $options );
        break;

    case 'delete_ok':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        delete_block_ok( $bid );
        break;

    case 'delete':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        zarilia_cp_header();
        $menu_handler->render( 0 );
        delete_block( $bid );
        break;

    case 'newblock':
    case 'edit':
    case 'create':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        zarilia_cp_header();
        $menu_handler->render( 2 );
        edit_block( $bid );
        break;

    case 'clone':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        clone_block( $bid );
        break;

    case 'clone_ok':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        $bcachetime = isset( $_REQUEST['bcachetime'] ) ? intval( $_REQUEST['bcachetime'] ) : 0;
        $options = isset( $_REQUEST['options'] ) ? $_REQUEST['options'] : array();
        clone_block_ok( $bid, $_REQUEST['bside'], $_REQUEST['bweight'], $bcachetime, $_REQUEST['baddon'], $options );
        break;

    case 'list':
        zarilia_cp_header();
        $menu_handler->render( 1 );
        $addon_handler = &zarilia_gethandler( 'addon' );
        $member_handler = &zarilia_gethandler( 'member' );
        $addonid = zarilia_cleanRequestVars( $_REQUEST, 'addonid', 1 );
        $selmod = zarilia_cleanRequestVars( $_REQUEST, 'selmod', -1 );
        $selvis = zarilia_cleanRequestVars( $_REQUEST, 'selvis', 3 );
        $selgrp = zarilia_cleanRequestVars( $_REQUEST, 'selgrp', 0 );
        if ( $selmod == -2 ) {
            $showonlymod = 1;
            $selgrp = 0;
        } else {
            $showonlymod = 0;
        }
        $cachetimes = array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK, '2592000' => _MONTH );
        $side_options = array(
            ZAR_SIDEBLOCK_LEFT => _AM_SBLEFT,
            ZAR_SIDEBLOCK_RIGHT => _AM_SBRIGHT,
            ZAR_CENTERBLOCK_LEFT => _AM_CBLEFT,
            ZAR_CENTERBLOCK_RIGHT => _AM_CBRIGHT,
            ZAR_CENTERBLOCK_CENTER => _AM_CBCENTER,
            ZAR_CENTERBLOCKDOWN_LEFT => _AM_CBLEFTDOWN,
            ZAR_CENTERBLOCKDOWN_RIGHT => _AM_CBRIGHTDOWN,
            ZAR_CENTERBLOCKDOWN_CENTER => _AM_CBCENTERDOWN,
            ZAR_BLOCK_INVISIBLE_EDIT => _AM_NOTVISIBLE );

        $group_list = &$member_handler->getGroupList();
        $group_list = array_merge( $group_list, array( 0 => '#' . _AM_UNASSIGNED ) );
        $yes_list = array( 3 => _AM_ALL, 0 => _AM_HIDDEN, 1 => _AM_VISABLE );

        $criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
        $criteria->add( new Criteria( 'isactive', 1 ) );
        $addon_list = &$addon_handler->getList( $criteria );
        $toponlyblock = false;
        $addon_list[1] = "System";
        $addon_list[0] = "Custom Blocks";
        natcasesort( $addon_list );

        $criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
        $criteria->add( new Criteria( 'isactive', 1 ) );
        $addon_list2 = &$addon_handler->getList( $criteria );
        $toponlyblock = false;
        $addon_list2[-2] = _AM_DISPLAYALL;
        $addon_list2[-1] = _AM_TOPPAGE;
        $addon_list2[0] = _AM_ALLPAGES;
        ksort( $addon_list2 );

        $content = '<form action="' . $addonversion['adminpath'] . '" method="get">
			<input type="hidden" name="op" value="list" />
			<div class="sidetitle">' . _AM_DISPLAYINADDON . '</div>
			<div class="sidecontent">' . zarilia_getSelection( $addon_list, $addonid, "addonid", 1, 0, false, false, "style=\"width: 90%\" onchange=\"location='" . $addonversion['adminpath'] . "&amp;op=" . $op . "&amp;selmod=$selmod&amp;selvis=$selvis&amp;selgrp=$selgrp&amp;addonid='+this.options[this.selectedIndex].value\"" , 0, false ) . '</div>
			<div class="sidetitle">' . _AM_DISPLAYVISIBLE . '</div>
			<div class="sidecontent">' . zarilia_getSelection( $addon_list2, $selmod, "selmod", 1, 0, false, false, "style=\"width: 90%\" onchange=\"location='" . $addonversion['adminpath'] . "&amp;op=" . $op . "&amp;addonid=$addonid&amp;selvis=$selvis&amp;selgrp=$selgrp&amp;selmod='+this.options[this.selectedIndex].value\"", 0, false ) . '</div>';

        if ( $showonlymod == 0 ) {
            $content .= '<div class="sidetitle">' . _AM_GROUP . '</div><div class="sidecontent">' . zarilia_getSelection( $group_list, $selgrp, "selgrp", 1, 0, false, false, "style=\"width: 90%\" onchange=\"location='" . $addonversion['adminpath'] . "&amp;op=" . $op . "&amp;addonid=$addonid&amp;selvis=$selvis&amp;selmod=$selmod&amp;selgrp='+this.options[this.selectedIndex].value\"" , 0, false ) . '</div>';
        }
        $content .= '<div class="sidetitle">' . _AM_SHOW . '</div><div class="sidecontent">' . zarilia_getSelection( $yes_list, $selvis, "selvis", 1, 0, false, false, "style=\"width: 90%\" onchange=\"location='" . $addonversion['adminpath'] . "&amp;op=" . $op . "&amp;addonid=$addonid&amp;selmod=$selmod&amp;selgrp=$selgrp&amp;selvis='+this.options[this.selectedIndex].value\"", 0, false ) . '</div></form>';

        zarilia_admin_menu(
            _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_BLOCK_CREATE, "index.php?fct=groups" => 'Modify Groups' ),
            _MD_AD_FILTER_BOX, $content
            );
        if ( $showonlymod == 1 ) {
            $block_arr = &ZariliaBlock::getByAddon( $addonid, $asobject = true, $selvis );
        } else if ( $selgrp == 8 ) {
            $block_arr = &ZariliaBlock::getNonGroupedBlocks( $selmod, $addonid, $selvis, 'b.side,b.weight,b.bid' );
        } else {
            $block_arr = &ZariliaBlock::getAllByGroupAddonAdmin( $selgrp, $selmod, $addonid, $toponlyblock, $selvis, 'b.side, b.weight, b.bid' );
        }
        // $class = 'even';
        $addon_list2 = &$addon_handler->getList();
        $addon_list2[0] = '&nbsp;';

        echo "<br />
		<form action='index.php' name='blockadmin' method='post'>
		 <table width='100%' cellpadding='2' cellspacing='1' class='outer'>
		  <tr>
		   <th width='2%'>&nbsp;</th>
			<th align='center'>" . _AM_ID . "</th>
			<th>" . _AM_TITLE . "</th>
			<th>" . _AM_ADDON . "</th>
			<th align='center' nowrap='nowrap'>" . _AM_SIDE . "</th>
			<th style='text-align: center;'>" . _AM_BCACHETIME . "</th>
			<th align='center'>" . _AM_WEIGHT . "</th>
			<th align='center'>" . _ACTION . "</th>
		   </tr>";

        /**
         * for custom blocks
         */
        if ( count( $block_arr ) > 0 ) {
            foreach ( array_keys( $block_arr ) as $i ) {
                $bid = $block_arr[$i]->getVar( "bid" ) ;
                $title = ( $block_arr[$i]->getVar( "title" ) == "" ) ? "&nbsp;" : $block_arr[$i]->getVar( "title" );
                $name = $block_arr[$i]->getVar( "name" );
                $description = $block_arr[$i]->getVar( "description" ) ? $block_arr[$i]->getVar( "description" ) : "No information available";
                $addon = ( $block_arr[$i]->getVar( 'mid' ) != 0 ) ? $addon_list2[$block_arr[$i]->getVar( 'mid' )] : "Custom Block";
                $side = $block_arr[$i]->getVar( "side" );
                $bcachetime = $block_arr[$i]->getVar( "bcachetime" );
                $weight = $block_arr[$i]->getVar( "weight" );
                /**
                 * delete link if it is cloned block
                 */
                $delete_link = ( $block_arr[$i]->getVar( "block_type" ) == 'D' || $block_arr[$i]->getVar( "block_type" ) == 'C' ) ? "<a href='" . $addonversion['adminpath'] . "&amp;op=delete&amp;bid=$bid'>" . zarilia_img_show( 'delete', _DELETE ) . "</a>" : "";
                /**
                 * clone link if it is marked as cloneable block
                 */
                $can_clone = false;
                if ( $block_arr[$i]->getVar( "block_type" ) == 'D' || $block_arr[$i]->getVar( "block_type" ) == 'C' ) {
                    $can_clone = true ;
                }
                $clone_link = ( $can_clone ) ? "<a href='" . $addonversion['adminpath'] . "&amp;op=clone&amp;bid=$bid'>" . zarilia_img_show( 'clone' ) . "</a>" : '';
                echo "
				 <tr valign='top'>
				  <td align='center' class='head'>" . zarilia_img_show( 'info', $description ) . "</td>
				  <td class='even' align='center'>" . $bid . "</td>
				  <td class='even'><b>Display:</b> $title <br /><b>Block Name:</b> $name</td>
				  <td class='even'>" . $addon . "</td>
				  <td align='center' class='even'>";
                zarilia_getSelection( $side_options, $side, "side[$i]" );
                echo "</td><td class='even' style='text-align: center;'>";
                zarilia_getSelection( $cachetimes, $bcachetime, "bcachetime[$i]" );
                echo "
				</td>
				<td class='even' align='center'><input type='text' name='weight[$i]' value='" . $weight . "' size='5' maxlength='5' /></td>
				<td class='even' style='text-align: center;'><a href='" . $addonversion['adminpath'] . "&amp;op=edit&amp;bid=$bid'>" . zarilia_img_show( 'edit' ) . "</a> {$delete_link} {$clone_link}</td></tr>
				<input type='hidden' name='oldside[$i]' value='" . $side . "' />
				<input type='hidden' name='oldweight[$i]' value='" . $weight . "' />
				<input type='hidden' name='oldbcachetime[$i]' value='" . $bcachetime . "' />
				<input type='hidden' name='bid[$i]' value='" . $bid . "' />";
            }
        } else {
            echo "<tr valign='top'><td class='even' style='text-align: center;' colspan='8'><b>" . _AM_NOBLOCKSTODISPLAY . "</b></td></tr>";
        }
        echo "<tr><td class='foot' align='right' colspan='8'>";
        if ( count( $block_arr ) > 0 ) {
            echo "<input type='hidden' name='fct' value='blocksadmin' />
			  <input type='hidden' name='op' value='order' />
			  <input type='submit' name='submit' class='formbutton'  value='" . _SUBMIT . "' />";
        }
        echo "</td>
			</tr>
		  </table>
		  </form>
		  <br /><br />";
        $button = array( 'edit', 'delete', 'clone' );
        zarilia_cp_legend( $button );
        break;

    case 'index':
    case 'default':
        zarilia_cp_header();
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( $addonversion['adminpath'] . "&amp;op=edit" => _MA_AD_BLOCK_CREATE ) );
        $menu_handler->render( 0 );
        break;
} // switch
zarilia_cp_footer();

?>