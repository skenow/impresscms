<?php
include 'admin_header.php';
$_PHP_SELF = zarilia_getenv( 'PHP_SELF' );
/*
*
*/
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'default' );
switch ( $op ) {
    case 'edit':
        $cid = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0 );
        $opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
        $client_obj = ( $cid == 0 ) ? $client_handler->create() : $client_handler->get( $cid );
        $caption = ( !$client_obj->isNew() ) ? $caption = sprintf( _MA_AD_MODIFYCLIENT, $client_obj->getVar( 'name' ) ) : _MA_AD_CREATECLIENT;

        include_once ZAR_ROOT_PATH . '/class/class.menubar.php';
        $tabbar = new ZariliaTabMenu( $opt );
        $tabbar->addTabArray( array(
                _MA_AD_CLIDETAILS => 'client.php?op=edit&cid=' . $cid,
                _MA_AD_CLIOPTIONS => 'client.php?op=edit&cid=' . $cid
                ) );
        /*
		* Output
		*/
        $form = $client_obj->clientForm( $caption, $opt );
        zarilia_cp_header();
        $menu_handler->render( 1 );
        $tabbar->renderStart();
        $form->display();
        break;

    case 'save':
        if ( false == checkEmail( zarilia_trim( $_REQUEST['email'] ) ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_INVALIDEMAIL );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        $cid = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0 );
        $client_obj = ( $cid > 0 ) ? $client_handler->get( $cid ) : $client_handler->create();

        $_REQUEST['editownsendemail'] = zarilia_cleanRequestVars( $_REQUEST, 'editownsendemail', 0 );
        $_REQUEST['report'] = zarilia_cleanRequestVars( $_REQUEST, 'report', 0 );
        $_REQUEST['editownsettings'] = zarilia_cleanRequestVars( $_REQUEST, 'editownsettings', 0 );
        $_REQUEST['manageown'] = zarilia_cleanRequestVars( $_REQUEST, 'manageown', 0 );
        $_REQUEST['deactivate'] = zarilia_cleanRequestVars( $_REQUEST, 'deactivate', 0 );
        $client_obj->setVars( $_REQUEST );

        if ( $client_handler->insert( $client_obj, false ) ) {
            $redirect_mess = ( $client_obj->isNew() ) ? _MA_AD_CLIENT_CREATED : _DBUPDATED;
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
        createdir();
        redirect_header( $_PHP_SELF, 1, $redirect_mess );
        break;

    case 'delete':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $cid = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0 );
        $client_obj = $client_handler->get( $cid );
        if ( !is_object( $client_obj ) ) {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        if ( $ok ) {
            if ( !$client_handler->delete( $client_obj ) ) {
                zarilia_cp_header();
                $menu_handler->render( 1 );
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_ERRORDELCLIENT );
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_cp_footer();
                exit();
            } else {
                $sql = sprintf( "DELETE FROM %s WHERE cid = %u", $zariliaDB->prefix( "banner" ), $cid );
                $zariliaDB->Execute( $sql );
                redirect_header( $_PHP_SELF, 1, _DBUPDATED );
            }
        } else {
            zarilia_cp_header();
            $menu_handler->render( 1 );
            zarilia_confirm(
                array( 'op' => 'delete',
                    'cid' => $client_obj->getVar( 'cid' ),
                    'ok' => 1
                    ), $_PHP_SELF, sprintf( _MA_AD_SUREDELCLIENT, $client_obj->getVar( 'name' ) )
                );
        }
        break;

    case 'list':
    default:
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'cid' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 20 );

        $_client_obj = $client_handler->getClientsObj( $nav );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'cid', '', 'center', true );
        $tlist->AddHeader( 'name', '', 'center', true );
        $tlist->AddHeader( 'contact', '', 'left', true );
        $tlist->AddHeader( 'email', '', 'left', true );
        $tlist->AddHeader( 'count', '', 'center', false );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->setPrefix( '_MA_AD_' );
        if ( $_client_obj['count'] ) {
            foreach ( $_client_obj['list'] as $obj ) {
                $c_id = $obj->getVar( 'cid' );
                $CBanner_count = $banners_handler->getBannerCount( $c_id );
                $op['edit'] = '<a href="client.php?op=edit&amp;cid=' . $c_id . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
                $op['edit'] .= '<a href="client.php?op=delete&amp;cid=' . $c_id . '">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';
                $op['edit'] .= '<a href="banners.php?cid=' . $c_id . '">' . zarilia_img_show( 'view', _VIEW ) . '</a>';
                $op['edit'] .= '<a href="banners.php?op=edit&amp;cid=' . $c_id . '">' . zarilia_img_show( 'new', _MA_AD_NEWBANNER ) . '</a>';
                $op['edit'] .= '<a href="billing.php?cid=' . $c_id . '">' . zarilia_img_show( 'new', _MA_AD_NEWBANNER ) . '</a>';

                $tlist->add(
                    array( $c_id,
                        $obj->getVar( 'name' ),
                        $obj->getVar( 'contact' ),
                        $obj->getVar( 'email' ),
                        $CBanner_count,
                        $op['edit'],
                        ) );
            }
        }
        /*
		* Output
		*/
        zarilia_cp_header();
        $menu_handler->render( 1 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array( "client.php?op=edit" => _MA_AD_ECLIENT_CREATE,
                "banners.php?op=edit" => _MA_AD_EBANNER_CREATE,
                "banneradds.php?op=edit" => _MA_AD_EBANNERADS_CREATE,
                )
            );
        clientCountOutput( $_client_obj['count'] );
        $tlist->render();
        /*
        zarilia_legend(
            array(
                zarilia_img_show( 'view' ) => _MA_AD_ICO_VIEW_LEG,
                zarilia_img_show( 'edit' ) => _MA_AD_ICO_EDIT_LEG,
                zarilia_img_show( 'delete' ) => _MA_AD_ICO_DELETE_LEG
                )
            );
*/
        zarilia_pagnav( $_client_obj['count'], $nav['limit'], $nav['start'], 'start', 1, 'op=' . $op );
        break;
} // switch
zarilia_cp_footer();

?>
