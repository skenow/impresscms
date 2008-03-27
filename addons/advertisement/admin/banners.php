<?php
/**
 *
 * @version $Id: banners.php,v 1.3 2007/04/21 09:40:17 catzwolf Exp $
 * @copyright 2006
 */
include 'admin_header.php';
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'default' );
switch ( $op ) {
    case 'edit':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        $cid = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0 );
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );

        $button = ( $bid > 0 ) ? _MA_AD_MODIFY : _MA_AD_CREATE;
        $banner_obj = ( $bid == 0 ) ? $banners_handler->create() : $banners_handler->get( $bid );
        $caption = ( !$banner_obj->isNew() ) ? $caption = sprintf( _MA_AD_MODIFYBANNER, $banner_obj->getVar( 'bannername' ) ) : _MA_AD_CREATECLIENT;
        // if ( $banner_obj->getVar('cid') == 0 ) {
        // zarilia_cp_header();
        // $menu_handler->render( 2 );
        // $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_ERRORNOCIDSELECTED );
        // $GLOBALS['zariliaLogger']->sysRender();
        // zarilia_cp_footer();
        // exit();
        // }
        include_once ZAR_ROOT_PATH . '/class/class.menubar.php';
        $tabbar = new ZariliaTabMenu( $opt );
        $tabbar->addTabArray( array(
                _MA_AD_CLIDETAILS => 'banners.php?op=edit&cid=' . $cid . '&amp;bid=' . $bid,
                _MA_AD_SHOWCODE => 'banners.php?op=edit&cid=' . $cid . '&amp;bid=' . $bid
                ) );
        /*
		*
		*/
        zarilia_cp_header();
        $menu_handler->render( 2 );
        echo $banner_obj->showImage();
        $tabbar->renderStart();
        $form = $banner_obj->bannerForm( $caption, $cid, $opt );
        $form->display();
        break;

    case 'save':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
        $cid = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0 );
        $imptotal = zarilia_cleanRequestVars( $_REQUEST, 'imptotal', 0 );
        $impadded = zarilia_cleanRequestVars( $_REQUEST, 'impadded', 0 );

        if ( !$cid ) {
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, "No Advertiser selected, please go back and select one" );
        }

        $banner_obj = ( $bid > 0 ) ? $banners_handler->get( $bid ) : $banners_handler->create();
        $banner_obj->setVars( $_REQUEST );
        $banner_obj->setVar( 'imptotal', $imptotal + $impadded );

        if ( !$GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            $banners_handler->setUpload( $banner_obj );
        }

        if ( $GLOBALS['zariliaLogger']->getSysErrorCount() ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        if ( $banner_obj->isNew() ) {
            $_banner_published = time();
        } else {
            $_banner_published = ( isset( $_REQUEST['publishdate']['date'] ) && $_REQUEST['publishdate']['date'] != '' ) ? strtotime( $_REQUEST['publishdate']['date'] ) : $banner_obj->getVar( 'publishdate' );
        }
        $banner_obj->setVar( 'publishdate', $_banner_published );
        $bannerpayments = zarilia_cleanRequestVars( $_REQUEST, 'bannerpayments', 0 );
        if ( $bannerpayments > 0 && $bannerpayments <= 3 ) {
            $date = $banner_obj->getVar( 'publishdate' );
            $newdate = getdate( $date );
            switch ( $bannerpayments ) {
                case 1:
                    $enddate = mktime( $newdate['hours'], $newdate['minutes'], $newdate['seconds'], $newdate['mon'], $newdate['mday'] + 7, $newdate['year'] );
                    break;
                case 2:
                    $enddate = mktime( $newdate['hours'], $newdate['minutes'], $newdate['seconds'], $newdate['mon'] + 1, $newdate['mday'], $newdate['year'] );
                    break;
                case 3:
                    $enddate = mktime( $newdate['hours'], $newdate['minutes'], $newdate['seconds'], $newdate['mon'], $newdate['mday'], $newdate['year'] + 1 );
                    break;
            } // switch
            $banner_obj->setVar( 'expiredate', $enddate );
        } else {
            $expiredate = ( isset( $_REQUEST['expiredate']['date'] ) && !empty( $_REQUEST['expiredate']['date'] ) ) ? $_REQUEST['expiredate']['date'] : '';
            $banner_obj->setVar( 'expiredate', $expiredate );
        }

        if ( $banners_handler->insert( $banner_obj, false ) ) {
            $redirect_mess = ( $banner_obj->isNew() ) ? _MA_AD_CLIENT_CREATED : _DBUPDATED;
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_ERRORSAVECLIENT );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        /*
		* create dir for client images etc
		*/
        redirect_header( $_PHP_SELF, 1, $redirect_mess );
        break;

    case 'delete':
        $bid = zarilia_cleanRequestVars( $_REQUEST, 'bid', 0 );
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $banner_obj = $banners_handler->get( $bid );
        if ( !is_object( $banner_obj ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        switch ( $ok ) {
            case 0:
            default:
                zarilia_cp_header();
                $menu_handler->render( 1 );
                zarilia_confirm(
                    array( 'op' => 'delete',
                        'bid' => $banner_obj->getVar( 'bid' ),
                        'ok' => 1
                        ), $addonversion['adminpath'], sprintf( _MD_AM_WAYSYWTDTR, $banner_obj->getVar( 'bannername' ) )
                    );
                break;
            case 1:
                if ( !$banners_handler->delete( $banner_obj ) ) {
                    zarilia_cp_header();
                    $menu_handler->render( 1 );
                                $GLOBALS['zariliaLogger']->sysRender();
                    zarilia_cp_footer();
                    exit();
                } else {
                    redirect_header( $addonversion['adminpath'], 1, _DBUPDATED );
                }
                break;
        } // switch
        break;

    case 'default':
    default:
        include_once ZAR_ROOT_PATH . '/class/class.menubar.php';
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'bid' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 20 );
        $cid = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0 );
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
        /*
		* Tab menu
		*/
        $tabbar = new ZariliaTabMenu( $opt );
        $tabbar->addTabArray( array(
                _AM_CURACTBNR => 'banners.php?cid=' . $cid,
                _AM_FINISHBNR => 'banners.php?cid=' . $cid,
                _AM_AUTOPUBLISH => 'banners.php?cid=' . $cid,
                _AM_DEACTIVATED => 'banners.php?cid=' . $cid
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
                $tlist->AddHeader( 'bid', '', 'center', true );
                $tlist->AddHeader( 'cidn', '', 'center', true );
                $tlist->AddHeader( 'impmade', '', 'center', true );
                $tlist->AddHeader( 'impleft', '', 'center', true );
                $tlist->AddHeader( 'clicks', '', 'center', true );
                $tlist->AddHeader( 'nclicks', '', 'center', false );
                $tlist->AddHeader( 'ACTION', '', 'center', false );
                $tlist->setPrefix( '_MA_AD_' );
                break;
            case 1:
            case 2:
            case 3:
                $tlist->AddHeader( 'bid', '', 'center', true );
                $tlist->AddHeader( 'cidn', '', 'left', true );
                $tlist->AddHeader( 'impmade', '', 'center', true );
                $tlist->AddHeader( 'clicks', '', 'center', true );
                $tlist->AddHeader( 'nclicks', '', 'center', false );
                $tlist->AddHeader( 'publishdate', '', 'center', false );
                $tlist->AddHeader( 'expiredate', '', 'center', false );
                $tlist->AddHeader( 'ACTION', '', 'center', false );
                $tlist->setPrefix( '_MA_AD_' );
                break;
        }

        $banners_handler->expireimpressions();
        if ($banner_arr = $banners_handler->getBannersObj( $nav, $criteria, $opt )) {
		if ($banner_arr['count']===false) {
			zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender( E_USER_ERROR, 'Error' );
			return;
		}
        foreach ( $banner_arr['list'] as $obj ) {
            $b_id = $obj->getVar( 'bid' );
            $c_id = $obj->getVar( 'cid' );
            switch ( $opt ) {
                case 0:
                    $op['edit'] = '<a href="' . $_PHP_SELF . '?op=edit&amp;bid=' . $b_id . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
                    $op['edit'] .= '<a href="' . $_PHP_SELF . '?op=delete&amp;&amp;bid=' . $b_id . '">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';
                    $op['edit'] .= '<a href="' . $_PHP_SELF . '?cid=' . $c_id . '">' . zarilia_img_show( 'view', _VIEW ) . '</a>';
                    $tlist->add(
                        array( $obj->getVar( 'bid' ),
                            $obj->getClientName(),
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
                case 3:
                    $op['edit'] = '<a href="' . $_PHP_SELF . '?op=edit&amp;bid=' . $b_id . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
                    $op['edit'] .= '<a href="' . $_PHP_SELF . '?op=delete&amp;bid=' . $b_id . '">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';
                    $op['edit'] .= '<a href="' . $_PHP_SELF . '?cid=' . $c_id . '">' . zarilia_img_show( 'view', _VIEW ) . '</a>';
                    $expiredate = $obj->getVar( 'expiredate' );
                    $publishdate = $obj->getVar( 'publishdate' );
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
		}
        $_client_buttons = array( "index.php?fct=banners&op=billinginfo" => 'Billing Information', "index.php?fct=banners&op=clientedit" => 'Create New Client', "banners.php?op=edit&cid=" . $cid => 'Create New Banner' );
        /*start of output*/
        zarilia_cp_header();
        $menu_handler->render( 2 );
        echo $client_handler->getClientInfo( $cid );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( "client.php?op=edit" => _MA_AD_ECLIENT_CREATE,
                "banners.php?op=edit" => _MA_AD_EBANNER_CREATE,
                "banneradds.php?op=edit" => _MA_AD_EBANNERADS_CREATE,
                )
            );
        $tabbar->renderStart();
        $tlist->render();
        zarilia_pagnav( $banner_arr['count'], $nav['limit'], $nav['start'], 'start', 1, 'opt=' . $opt . '&amp;cid=' . $cid );
        break;
}
zarilia_cp_footer();

?>