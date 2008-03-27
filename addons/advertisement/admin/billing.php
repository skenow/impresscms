<?php
include 'admin_header.php';
$_PHP_SELF = zarilia_getenv( 'PHP_SELF' );
/*
* 
*/
$op = zarilia_cleanRequestVars( $_REQUEST, 'op', 'defualt' );
switch ( $op ) {
    case 'view':
        $cid = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0 );
		
		zarilia_cp_header();
        $menu_handler->render( 4 );
        echo $client_handler->getClientInfo( $cid );
		break;
    
	case 'default':
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
        foreach ( $_client_obj['list'] as $obj ) {
            $c_id = $obj->getVar( 'cid' );
            $CBanner_count = $banners_handler->getBannerCount( $c_id );
            $op['edit'] = '<a href="' . $_PHP_SELF . '?op=view&amp;cid=' . $c_id . '">' . zarilia_img_show( 'edit', _EDIT ) . '</a>';

            $tlist->add( 
                array( $c_id,
                    $obj->getVar( 'name' ),
                    $obj->getVar( 'contact' ),
                    $obj->getVar( 'email' ),
                    $CBanner_count,
                    $op['edit'], 
                    ) );
        } 
        /*
		* Output
		*/
        zarilia_cp_header();
        $menu_handler->render( 4 );

        clientCountOutput( $_client_obj['count'] );
        echo $client_handler->getClientInfo( $cid );
        $tlist->render();
        zarilia_pagnav( $_client_obj['count'], $nav['limit'], $nav['start'], 'start', 1, 'op=' . $op );
        break;
} // switch
zarilia_cp_footer();

?>
