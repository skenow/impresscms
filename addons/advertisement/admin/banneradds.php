<?php
/**
 *
 * @version $Id: banneradds.php,v 1.3 2007/04/21 09:40:17 catzwolf Exp $
 * @copyright 2006
 */
include 'admin_header.php';
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'defualt' );

switch ( $op ) {
    case 'edit':
        $add_id = zarilia_cleanRequestVars( $_REQUEST, 'add_id', 0 );
        $bannerAdd_obj = ( $add_id == 0 ) ? $banneradds_handler->create() : $banneradds_handler->get( $add_id );
        $caption = ( !$bannerAdd_obj->isNew() ) ? $caption = sprintf( _MA_AD_MODIFYCLIENT, $bannerAdd_obj->getVar( 'add_type' ) ) : _MA_AD_CREATECLIENT;
        $form = $bannerAdd_obj->banneraddsForm( $caption );
        /*
		* Output
		*/
        zarilia_cp_header();
        $menu_handler->render( 3 );
        $form->display();
        break;

    case 'save':
        $add_id = zarilia_cleanRequestVars( $_REQUEST, 'add_id', 0 );
        $bannerAdd_obj = ( $add_id > 0 ) ? $banneradds_handler->get( $add_id ) : $banneradds_handler->create();
        $bannerAdd_obj->setVars( $_REQUEST );
        if ( $banneradds_handler->insert( $bannerAdd_obj, false ) ) {
            $redirect_mess = ( $bannerAdd_obj->isNew() ) ? _MA_AD_CLIENT_CREATED : _DBUPDATED;
	        redirect_header( $_PHP_SELF, 1, $redirect_mess );
		} else {
            zarilia_cp_header();
            $menu_handler->render( 3 );
            $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_ERRORSAVEBANADD );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }
        break;

    case 'delete':
        $ok = zarilia_cleanRequestVars( $_REQUEST, 'ok', 0 );
        $add_id = zarilia_cleanRequestVars( $_REQUEST, 'add_id', 0 );
        $bannerAdd_obj = $banneradds_handler->get( $add_id );
        if ( !is_object( $bannerAdd_obj ) ) {
            zarilia_cp_header();
            $menu_handler->render( 3 );
            $GLOBALS['zariliaLogger']->sysRender();
            zarilia_cp_footer();
            exit();
        }

        if ( $ok ) {
            if ( !$banneradds_handler->delete( $bannerAdd_obj ) ) {
                zarilia_cp_header();
                $menu_handler->render( 3 );
                $GLOBALS['zariliaLogger']->setSysError( E_USER_ERROR, _MA_AD_ERRORDELBANNERADD );
                $GLOBALS['zariliaLogger']->sysRender();
                zarilia_cp_footer();
                exit();
            }
        } else {
            zarilia_cp_header();
            $menu_handler->render( 3 );
            zarilia_confirm(
                array( 'op' => 'delete',
                    'add_id' => $bannerAdd_obj->getVar( 'add_id' ),
                    'ok' => 1
                    ), $_PHP_SELF, sprintf( _MA_AD_SUREDELBANNERADD, $bannerAdd_obj->getVar( 'add_type' ) )
                );
        }
        break;

    case 'view':
        break;

    case 'default':
    default:
        $nav['start'] = zarilia_cleanRequestVars( $_REQUEST, 'start', 0 );
        $nav['sort'] = zarilia_cleanRequestVars( $_REQUEST, 'sort', 'bid' );
        $nav['order'] = zarilia_cleanRequestVars( $_REQUEST, 'order', 'DESC', XOBJ_DTYPE_TXTBOX );
        $nav['limit'] = zarilia_cleanRequestVars( $_REQUEST, 'limit', 20 );

        $tlist = new ZariliaTList();
        $tlist->AddHeader( 'add_id', '', 'center', true );
        $tlist->AddHeader( 'add_type', '', 'left', true );
        $tlist->AddHeader( 'add_sizew', '', 'center', true );
        $tlist->AddHeader( 'add_sizeh', '', 'center', true );
        $tlist->AddHeader( 'add_weekly', '', 'center', false );
        $tlist->AddHeader( 'add_monthly', '', 'center', false );
        $tlist->AddHeader( 'add_yearly', '', 'center', false );
        $tlist->AddHeader( 'ACTION', '', 'center', false );
        $tlist->setPrefix( '_MA_AD_' );

		$adds_arr = $banneradds_handler->getBannerAddObj( $nav );
		foreach ( $adds_arr['list'] as $obj ) {
			$add_id = $obj->getVar( 'add_id' );
            $op['edit'] = '<a href="' . $_PHP_SELF . '?op=edit&amp;add_id=' . $add_id . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';
            $op['edit'] .= '<a href="' . $_PHP_SELF . '?op=delete&amp;add_id=' . $add_id . '">' . zarilia_img_show( 'delete', _DELETE ) . '</a>';
            $tlist->add(
                array( $add_id,
                    $obj->getVar( 'add_type' ),
                    $obj->getVar( 'add_sizew' ),
                    $obj->getVar( 'add_sizeh' ),
                    $obj->getVar( 'add_weekly' ),
                    $obj->getVar( 'add_monthly' ),
                    $obj->getVar( 'add_yearly' ),
                    $op['edit'],
                    ) );
        }
        /*
		* Output
		*/
        zarilia_cp_header();
        $menu_handler->render( 3 );
        zarilia_admin_menu( _MD_AD_ACTION_BOX, array(
			"client.php?op=edit" => _MA_AD_ECLIENT_CREATE,
			"banners.php?op=edit" => _MA_AD_EBANNER_CREATE,
			"banneradds.php?op=edit" => _MA_AD_EBANNERADS_CREATE,
			)
		);
        $tlist->render();
        zarilia_pagnav( $adds_arr['count'], $nav['limit'], $nav['start'], 'start', 1, 'op=' . $op );
		break;
}
zarilia_cp_footer();

?>