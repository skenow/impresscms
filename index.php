<?php
// $Id: index.php,v 1.5 2007/05/05 11:10:55 catzwolf Exp $
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


if ( isset( $_REQUEST['page_type'] ) && strval( $_REQUEST['page_type'] ) )
{
    $zariliaOption['pagetype'] = strval( strip_tags( $_REQUEST['page_type'] ) );
}
include 'mainfile.php';
global $zariliaConfig;
/*
* WE ARE CHANGING THIS TO ALLOW FOR NEW TYPE CONTENT MANAGEMENT
*/
$cont = array();
$cont['id'] = zarilia_cleanRequestVars( $_REQUEST, 'id', 0, XOBJ_DTYPE_INT );
$cont['cid'] = zarilia_cleanRequestVars( $_REQUEST, 'cid', 0, XOBJ_DTYPE_INT );
$cont['uid'] = zarilia_cleanRequestVars( $_REQUEST, 'uid', 0, XOBJ_DTYPE_INT );
$cont['page_type'] = zarilia_cleanRequestVars( $_REQUEST, 'page_type', null, XOBJ_DTYPE_TXTBOX );
$cont['content_type'] = zarilia_cleanRequestVars( $_REQUEST, 'content_type', 'static', XOBJ_DTYPE_TXTBOX );
$cont['act'] = zarilia_cleanRequestVars( $_REQUEST, 'act', '', XOBJ_DTYPE_TXTBOX );
$cont['direct'] = zarilia_cleanRequestVars( $_REQUEST, 'direct', false, XOBJ_DTYPE_TXTBOX );

$zariliaOption['show_cblock'] = ( $cont['page_type'] != null ) ? 0 : 1;


// check if start page is defined
if (( isset( $zariliaConfig['startpage'] ) && !empty( $zariliaConfig['startpage'] ) ) && (!$cont['page_type'])) {
    header( 'Location: ' . ZAR_URL . '/addons/' . $zariliaConfig['startpage'] . '/' );
    exit();
}
else
{
    $no_return = 1;
    switch ( $cont['page_type'] )
    {
        case 'user':
        case 'register':
        case 'userinfo':
        case 'edituser':
        case 'notifications':
        case 'avatar':
            include ZAR_ROOT_PATH . '/class/user/userfactory.php';
            $zariliaUserAuth = &ZariliaUserFactory::getUserAction( $cont['page_type'] );
            if ( $zariliaUserAuth )
            {
                if ( empty( $cont['act'] ) )
                {
                    $cont['act'] = 'isdefault';
                }
				include 'header.php';
				$zariliaOption['header.included'] = true;
                $ret = call_user_func( array( $zariliaUserAuth, $cont['act'] ) );
            }
            break;

        case 'default':
        default:
            $content_handler = &zarilia_gethandler( 'content' );
            if ( $cont['page_type'] == 'download' )
            {
                $content_handler->getDownload( $cont );
            } elseif ( $cont['page_type'] == 'backend' || $cont['act'] == 'dopdf' || $cont['act'] == 'print' || $cont['act'] == 'pda' || $cont['direct'] == true )
            {
                $content_handler->getContent( $cont );
                $content_handler->render();
            }
            else
            {
                include 'header.php';
                $content_handler->getContent( $cont );
                $content_handler->render();
                include 'footer.php';
            }
            $no_return = 0;
            break;
    } // switch
}

if ( $no_return )
{
    if ( $GLOBALS['zariliaLogger']->getSysErrorCount() )
    {
        if (!isset($zariliaOption['header.included'])) include 'header.php';
        $GLOBALS['zariliaLogger']->sysRender();
        include 'footer.php';
        exit();
    }
    if ( is_array( $ret ) )
    {
        $zariliaOption['template_main'] = &$ret['template_main'];
        if (!isset($zariliaOption['header.included'])) include 'header.php';        
        $zariliaTpl->assign( $ret );
        if ( isset( $ret['content']['form'] ) && (method_exists($ret['content']['form'], 'assign' )))
        {
            $ret['content']['form']->assign( $zariliaTpl );
        }
        include 'footer.php';
    }
    else
    {
        include 'header.php';
        echo $ret;
        unset( $ret );
        include 'footer.php';
    }
}
exit();

?>