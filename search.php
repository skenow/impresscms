<?php
// $Id: search.php,v 1.2 2007/04/21 09:40:28 catzwolf Exp $
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
$zariliaOption['pagetype'] = "search";

include 'mainfile.php';
$config_handler = &zarilia_gethandler( 'config' );
$zariliaConfigSearch = &$config_handler->getConfigsByCat( ZAR_CONF_SEARCH );

if ( $zariliaConfigSearch['enable_search'] != 1 ) {
    header( 'Location: ' . ZAR_URL . '/index.php' );
    exit();
} 

extract( $_GET );
extract( $_POST, EXTR_OVERWRITE );

$op = isset( $op ) ? trim( $op ) : "search";
$query = isset( $query ) ? trim( $query ) : "";
$andor = isset( $andor ) ? trim( $andor ) : "AND";
$mid = isset( $mid ) ? intval( $mid ) : 0;
$uid = isset( $uid ) ? intval( $uid ) : 0;
$start = isset( $start ) ? intval( $start ) : 0;
$queries = array();

if ( $op == "results" && $query == "" ) {
    redirect_header( "search.php", 1, _SR_PLZENTER );
    exit();
} 

if ( $op == "showall" && ( $query == "" || empty( $mid ) ) ) {
    redirect_header( "search.php", 1, _SR_PLZENTER );
    exit();
} 

if ( $op == "showallbyuser" && ( empty( $mid ) || empty( $uid ) ) ) {
    redirect_header( "search.php", 1, _SR_PLZENTER );
    exit();
} 

$groups = ( $zariliaUser ) ? $zariliaUser->getGroups() : ZAR_GROUP_ANONYMOUS;
$gperm_handler = &zarilia_gethandler( 'groupperm' );
$available_addons = $gperm_handler->getItemIds( 'addon_read', $groups );

if ( $op == 'search' ) {
    include ZAR_ROOT_PATH . '/header.php';
    include 'include/searchform.php';
    $search_form->display();
    include ZAR_ROOT_PATH . '/footer.php';
    exit();
} 

if ( $andor != "OR" && $andor != "exact" && $andor != "AND" ) {
    $andor = "AND";
} 

$myts = &MyTextSanitizer::getInstance();
if ( $op != 'showallbyuser' ) {
    if ( $andor != "exact" ) {
        $ignored_queries = array(); // holds kewords that are shorter than allowed minmum length
        $temp_queries = preg_split( '/[\s,]+/', $query );
        foreach ( $temp_queries as $q ) {
            $q = trim( $q );
            if ( strlen( $q ) >= $zariliaConfigSearch['keyword_min'] ) {
                $queries[] = $myts->addSlashes( $q );
            } else {
                $ignored_queries[] = $myts->addSlashes( $q );
            } 
        } 
        if ( count( $queries ) == 0 ) {
            redirect_header( 'search.php', 2, sprintf( _SR_KEYTOOSHORT, $zariliaConfigSearch['keyword_min'] ) );
            exit();
        } 
    } else {
        $query = trim( $query );
        if ( strlen( $query ) < $zariliaConfigSearch['keyword_min'] ) {
            redirect_header( 'search.php', 2, sprintf( _SR_KEYTOOSHORT, $zariliaConfigSearch['keyword_min'] ) );
            exit();
        } 
        $queries = array( $myts->addSlashes( $query ) );
    } 
} 
switch ( $op ) {
    case "results":
        $addon_handler = &zarilia_gethandler( 'addon' );
        $criteria = new CriteriaCompo( new Criteria( 'hassearch', 1 ) );
        $criteria->add( new Criteria( 'isactive', 1 ) );
        $criteria->add( new Criteria( 'mid', "(" . implode( ',', $available_addons ) . ")", 'IN' ) );
        $addons = &$addon_handler->getObjects( $criteria, true );
        if ( empty( $mids ) || !is_array( $mids ) ) {
            unset( $mids );
            $mids = array_keys( $addons );
        } 
        include ZAR_ROOT_PATH . "/header.php";
        echo "<h3>" . _SR_SEARCHRESULTS . "</h3>\n";
        echo _SR_KEYWORDS . ':';
        if ( $andor != 'exact' ) {
            foreach ( $queries as $q ) {
                echo ' <b>' . htmlspecialchars( stripslashes( $q ) ) . '</b>';
            } 
            if ( !empty( $ignored_queries ) ) {
                echo '<br />';
                printf( _SR_IGNOREDWORDS, $zariliaConfigSearch['keyword_min'] );
                foreach ( $ignored_queries as $q ) {
                    echo ' <b>' . htmlspecialchars( stripslashes( $q ) ) . '</b>';
                } 
            } 
        } else {
            echo ' "<b>' . htmlspecialchars( stripslashes( $queries[0] ) ) . '</b>"';
        } 
        echo '<br />';
        foreach ( $mids as $mid ) {
            $mid = intval( $mid );
            if ( in_array( $mid, $available_addons ) ) {
                $addon = &$addons[$mid];
                $results = &$addon->search( $queries, $andor, 5, 0 );
                echo "<h4>" . htmlSpecialChars( $addon->getVar( 'name' ), ENT_QUOTES ) . "</h4>";
                $count = count( $results );
                if ( !is_array( $results ) || $count == 0 ) {
                    echo "<p>" . _SR_NOMATCH . "</p>";
                } else {
                    for ( $i = 0; $i < $count; $i++ ) {
                        if ( isset( $results[$i]['image'] ) && $results[$i]['image'] != "" ) {
                            echo "<img src='addons/" . $addon->getVar( 'dirname' ) . "/" . $results[$i]['image'] . "' alt='" . htmlSpecialChars( $addon->getVar( 'name' ), ENT_QUOTES ) . "' />&nbsp;";
                        } else {
                            echo "<img src='images/icons/posticon2.gif' alt='" . htmlSpecialChars( $addon->getVar( 'name' ) ) . "' width='26' height='26' />&nbsp;";
                        } 
                        echo "<b><a href='addons/" . $addon->getVar( 'dirname' ) . "/" . $results[$i]['link'] . "'>" . htmlSpecialChars( $results[$i]['title'] ) . "</a></b><br />\n";
                        echo "<small>";
                        $results[$i]['uid'] = intval( $results[$i]['uid'] );
                        if ( !empty( $results[$i]['uid'] ) ) {
                            $uname = ZariliaUser::getUnameFromId( $results[$i]['uid'] );
                            echo "&nbsp;&nbsp;<a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $results[$i]['uid'] . "'>" . $uname . "</a>\n";
                        } 
                        echo $results[$i]['time'] ? " (" . formatTimestamp( intval( $results[$i]['time'] ) ) . ")" : "";
                        echo "</small><br />\n";
                    } 
                    if ( $count == 5 ) {
                        $search_url = ZAR_URL . '/search.php?query=' . urlencode( stripslashes( implode( ' ', $queries ) ) );
                        $search_url .= "&mid=$mid&op=showall&andor=$andor";
                        echo '<br /><a href="' . $search_url . '">' . _SR_SHOWALLR . '</a></p>';
                    } 
                } 
            } 
            unset( $results );
            unset( $addon );
        } 
        include "include/searchform.php";
        $search_form->display();
        break;
    case "showall":
    case 'showallbyuser':
        include ZAR_ROOT_PATH . "/header.php";
        $addon_handler = &zarilia_gethandler( 'addon' );
        $addon = &$addon_handler->get( $mid );
        $results = &$addon->search( $queries, $andor, 20, $start, $uid );
        $count = count( $results );
        if ( is_array( $results ) && $count > 0 ) {
            $next_results = &$addon->search( $queries, $andor, 1, $start + 20, $uid );
            $next_count = count( $next_results );
            $has_next = false;
            if ( is_array( $next_results ) && $next_count == 1 ) {
                $has_next = true;
            } 
            echo "<h4>" . _SR_SEARCHRESULTS . "</h4>\n";
            if ( $op == 'showall' ) {
                echo _SR_KEYWORDS . ':';
                if ( $andor != 'exact' ) {
                    foreach ( $queries as $q ) {
                        echo ' <b>' . htmlspecialchars( stripslashes( $q ) ) . '</b>';
                    } 
                } else {
                    echo ' "<b>' . htmlspecialchars( stripslashes( $queries[0] ) ) . '</b>"';
                } 
                echo '<br />';
            } 
            printf( _SR_SHOWING, $start + 1, $start + $count );
            echo "<h5>" . htmlSpecialChars( $addon->getVar( 'name' ), ENT_QUOTES ) . "</h5>";
            for ( $i = 0; $i < $count; $i++ ) {
                if ( isset( $results[$i]['image'] ) && $results[$i]['image'] != '' ) {
                    echo "<img src='addons/" . $addon->getVar( 'dirname' ) . "/" . $results[$i]['image'] . "' alt='" . htmlSpecialChars( $addon->getVar( 'name' ) ) . "' />&nbsp;";
                } else {
                    echo "<img src='images/icons/posticon2.gif' alt='" . htmlSpecialChars( $addon->getVar( 'name' ) ) . "' width='26' height='26' />&nbsp;";
                } 
                echo "<b><a href='addons/" . $addon->getVar( 'dirname' ) . "/" . $results[$i]['link'] . "'>" . htmlSpecialChars( $results[$i]['title'] ) . "</a></b><br />\n";
                echo "<small>";
                $results[$i]['uid'] = intval( $results[$i]['uid'] );
                if ( !empty( $results[$i]['uid'] ) ) {
                    $uname = ZariliaUser::getUnameFromId( $results[$i]['uid'] );
                    echo "&nbsp;&nbsp;<a href='" . ZAR_URL . "/index.php?page_type=userinfo&uid=" . $results[$i]['uid'] . "'>" . $uname . "</a>\n";
                } 
                echo $results[$i]['time'] ? " (" . formatTimestamp( intval( $results[$i]['time'] ) ) . ")" : "";
                echo "</small><br />\n";
            } 
            echo '<table><tr>';
            $search_url = ZAR_URL . '/search.php?query=' . urlencode( stripslashes( implode( ' ', $queries ) ) );
            $search_url .= "&mid=$mid&op=$op&andor=$andor";
            if ( $op == 'showallbyuser' ) {
                $search_url .= "&uid=$uid";
            } 
            if ( $start > 0 ) {
                $prev = $start - 20;
                echo '<td align="left">';
                $search_url_prev = $search_url . "&start=$prev";
                echo '<a href="' . htmlspecialchars( $search_url_prev ) . '">' . _SR_PREVIOUS . '</a></td>';
            } 
            echo '<td>&nbsp;&nbsp;</td>';
            if ( false != $has_next ) {
                $next = $start + 20;
                $search_url_next = $search_url . "&start=$next";
                echo '<td align="right"><a href="' . htmlspecialchars( $search_url_next ) . '">' . _SR_NEXT . '</a></td>';
            } 
            echo '</tr></table><p>';
        } else {
            echo '<p>' . _SR_NOMATCH . '</p>';
        } 
        include "include/searchform.php";
        $search_form->display();
        echo '</p>';
        break;
} 
include ZAR_ROOT_PATH . "/footer.php";

?>