<?php
/**
 * $Id: index.php,v 1.2 2007/04/21 09:40:26 catzwolf Exp $
 */
/*
* Include Message Addons Header
*/
include_once "header.php";
$zariliaOption['template_main'] = 'ads_index.html';
/*
* Display Output
*/
zarilia_mod_header();
$zariliaTpl->assign( 'welcomeuser', sprintf( _MD_ADS_WELCOMEUSER, $zariliaUser->getVar( 'uname' ) ) );
/*
* Get Client Banners
*/
$clientObj = $client_handler->getUserInfo( $zariliaUser->getVar( 'uid' ) );
$zariliaTpl->assign( 'member', array( 'cid' => $clientObj->getVar( 'cid' ), 'name' => $clientObj->getVar( 'name' ), 'address' => $clientObj->getVar( 'address' ), 'city' => $clientObj->getVar( 'city' ), 'state' => $clientObj->getVar( 'state' ), 'zipcode' => $clientObj->getVar( 'zipcode' ), 'country' => $clientObj->getVar( 'country' ), 'contact' => $clientObj->getVar( 'contact' ), 'email' => $clientObj->getVar( 'email' ), 'telephone' => $clientObj->getVar( 'telephone' ) ) );
include_once ZAR_ROOT_PATH . '/class/class.menubar.php';
$nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
$nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'bid' );
$nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
$nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 20 );
$cid = $clientObj->getVar( 'cid' );
$opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
/*
* Tab menu
*/
$tabbar = new ZariliaTabMenu( $opt );
$tabbar->addTabArray( array( 
        _AM_CURACTBNR => 'index.php?cid=' . $cid,
        _AM_FINISHBNR => 'index.php?cid=' . $cid,
        _AM_AUTOPUBLISH => 'index.php?cid=' . $cid 
        ) );
/*
* 
*/
$tlist = new ZariliaTList();
$criteria = new CriteriaCompo();
if ( $cid ) {
    $criteria->add ( new Criteria( 'cid', $cid, '=' ) );
} 
switch ( $opt ) {
    case 0:
        $criteria->add ( new Criteria( 'publishdate', 0, '>' ) );
        $criteria->add ( new Criteria( 'publishdate', time(), '<=' ), 'AND' );
        $criteria->add ( new Criteria( 'active', 1, '=' ), 'AND' );
        $criteria->add ( new Criteria( 'expiredate', 0, '=' ) );
        $criteria->add ( new Criteria( 'expiredate', time(), '>' ), 'OR' );
        /*
		* 
		*/
        $tlist->AddHeader( 'bid', '', 'center', true );
        $tlist->AddHeader( 'bannername', '', 'center', true );
        $tlist->AddHeader( 'impmade', '', 'center', true );
        $tlist->AddHeader( 'impleft', '', 'center', true );
        $tlist->AddHeader( 'clicks', '', 'center', true );
        $tlist->AddHeader( 'nclicks', '', 'center', false );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->setPrefix( '_MD_ADS_' );
        break;
    case 1:
    case 2:
        switch ( $opt ) {
            case 1:
                $criteria->add ( new Criteria( 'expiredate', time(), '<=' ) );
                $criteria->add ( new Criteria( 'expiredate', 0, '>' ) );
                $criteria->add ( new Criteria( 'active', 0, '=' ), 'AND' );
                break;
            case 2:
                $criteria->add ( new Criteria( 'publishdate', time(), '>' ) );
                break;
        } // switch
        $tlist->AddHeader( 'bid', '', 'center', true );
        $tlist->AddHeader( 'bannername', '', 'left', true );
        $tlist->AddHeader( 'impmade', '', 'center', true );
        $tlist->AddHeader( 'clicks', '', 'center', true );
        $tlist->AddHeader( 'nclicks', '', 'center', false );
        $tlist->AddHeader( 'publishdate', '', 'center', false );
        $tlist->AddHeader( 'expiredate', '', 'center', false );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->setPrefix( '_MD_ADS_' );
        break;
} 
$criteria->setSort( $nav['sort'] );
$criteria->setOrder( $nav['order'] );
$criteria->setLimit( $nav['limit'] );
$criteria->setStart( $nav['start'] );
/*
* Get banners
*/
$banner_arr = $banner_handler->getObjects( $criteria );
$banner_count = $banner_handler->getCount( $criteria );
foreach ( $banner_arr as $obj ) {
    $b_id = $obj->getVar( 'bid' );
    $c_id = $obj->getVar( 'cid' );
    switch ( $opt ) {
        case 0:
            $op['edit'] = '<a href="' . $_PHP_SELF . '?op=edit&amp;bid=' . $b_id . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
            $op['edit'] .= '<a href="' . $_PHP_SELF . '?cid=' . $c_id . '">' . zarilia_img_show( 'view', _VIEW ) . '</a>';
            $tlist->add( 
                array( $obj->getVar( 'bid' ),
                    $obj->getVar( 'bannername' ),
                    $obj->getVar( 'impmade' ),
                    $obj->getLeft(),
                    $obj->getVar( 'clicks' ),
                    $obj->getPercent(),
                    $op['edit'] 
                    ) 
                );
            break;
        case 1:
        case 2:
            $op['edit'] = '<a href="' . $_PHP_SELF . '?op=edit&amp;bid=' . $b_id . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
            $op['edit'] .= '<a href="' . $_PHP_SELF . '?cid=' . $c_id . '">' . zarilia_img_show( 'view', _VIEW ) . '</a>';
            $tlist->add( 
                array( $obj->getVar( 'bid' ),
                    $obj->getClientName(),
                    $obj->getVar( 'impmade' ),
                    $obj->getVar( 'clicks' ),
                    $obj->getPercent(),
                    $obj->formatTimeStamp( 'publishdate' ),
                    $obj->formatTimeStamp( 'expiredate' ),
                    $op['edit'] 
                    ) 
                );
            break;
    } 
} 
$zariliaTpl->assign( 'tabmenu', $tabbar->renderStart( 1 ) );
$zariliaTpl->assign( 'tlist', $tlist->render() );
zarilia_mod_footer();

?>
