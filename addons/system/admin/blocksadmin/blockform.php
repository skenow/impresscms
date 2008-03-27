<?php
// $Id: blockform.php,v 1.2 2007/04/21 09:41:58 catzwolf Exp $
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
include_once ZAR_ROOT_PATH . "/class/zariliaformloader.php";

$form = new ZariliaThemeForm( $block['form_title'], 'blockform', 'index.php' );
if ( isset( $block['name'] ) ) {
    $form->addElement( new ZariliaFormLabel( _AM_NAME, $block['name'] ) );
}

$side_select = new ZariliaFormSelect( _AM_BLKTYPE, "bside", $block['side'] );
$side_select->addOptionArray(
    array(
        ZAR_SIDEBLOCK_LEFT => _AM_SBLEFT,
        ZAR_SIDEBLOCK_RIGHT => _AM_SBRIGHT,
        ZAR_CENTERBLOCK_LEFT => _AM_CBLEFT,
        ZAR_CENTERBLOCK_RIGHT => _AM_CBRIGHT,
        ZAR_CENTERBLOCK_CENTER => _AM_CBCENTER,
        ZAR_CENTERBLOCKDOWN_LEFT => _AM_CBLEFTDOWN,
        ZAR_CENTERBLOCKDOWN_RIGHT => _AM_CBRIGHTDOWN,
        ZAR_CENTERBLOCKDOWN_CENTER => _AM_CBCENTERDOWN,
        ZAR_BLOCK_INVISIBLE => _AM_NOTVISIBLE
        )
    );
$form->addElement( $side_select );
$form->addElement( new ZariliaFormText( _AM_WEIGHT, "bweight", 2, 5, $block['weight'] ) );
$mod_select = new ZariliaFormSelect( _AM_VISIBLEIN, "baddon", $block['addons'], 5, true );
$addon_handler = &zarilia_gethandler( 'addon' );
$criteria = new CriteriaCompo( new Criteria( 'hasmain', 1 ) );
$criteria->add( new Criteria( 'isactive', 1 ) );
$addon_list = &$addon_handler->getList( $criteria );
$addon_list[-1] = _AM_TOPPAGE;
$addon_list[0] = _AM_ALLPAGES;
ksort( $addon_list );
$mod_select->addOptionArray( $addon_list );
$form->addElement( $mod_select );

$form->addElement( new ZariliaFormText( _AM_TITLE, 'btitle', 50, 255, $block['title'] ), false );

if ( $block['is_custom'] ) {
    global $zariliaUser;
    $options['name'] = 'bcontent';
    $options['value'] = $block['content'];
    $textarea = new ZariliaFormEditor( _AM_CONTENT, $zariliaUser->getVar( 'editor' ), $options, $nohtml = false, $onfailure = "textarea", 1 );
    $textarea->setDescription( '<span style="font-size:x-small;font-weight:bold;">' . _AM_USEFULTAGS . '</span><br /><span style="font-size:x-small;font-weight:normal;">' . sprintf( _AM_BLOCKTAG1, '{X_SITEURL}', ZAR_URL . '/' ) . '</span>' );
    $form->addElement( $textarea, true );

    $ctype_select = new ZariliaFormSelect( _AM_CTYPE, 'bctype', $block['ctype'] );
    $ctype_select->addOptionArray( array( 'H' => _AM_HTML, 'P' => _AM_PHP, 'S' => _AM_AFWSMILE, 'T' => _AM_AFNOSMILE ) );
    $form->addElement( $ctype_select );
} else {
    if ( $block['template'] != '' ) {
        $tplfile_handler = &zarilia_gethandler( 'tplfile' );
        $btemplate = &$tplfile_handler->find( $GLOBALS['zariliaConfig']['template_set'], 'block', $block['bid'] );
        if ( count( $btemplate ) > 0 ) {
            $form->addElement( new ZariliaFormLabel( _AM_CONTENT, '<a href="' . ZAR_URL . '/addons/system/index.php?fct=tplsets&amp;op=edittpl&id=' . $btemplate[0]->getVar( 'tpl_id' ) . '">' . _AM_EDITTPL . '</a>' ) );
        } else {
            $btemplate2 = &$tplfile_handler->find( 'default', 'block', $block['bid'] );
            if ( count( $btemplate2 ) > 0 ) {
                $form->addElement( new ZariliaFormLabel( _AM_CONTENT, '<a href="' . ZAR_URL . '/addons/system/index.php?fct=tplsets&amp;op=edittpl&id=' . $btemplate2[0]->getVar( 'tpl_id' ) . '" target="_blank">' . _AM_EDITTPL . '</a>' ) );
            }
        }
    }
    if ( $block['edit_form'] != false ) {
        $form->addElement( new ZariliaFormLabel( _AM_OPTIONS, $block['edit_form'] ) );
    }
}
$cache_select = new ZariliaFormSelect( _AM_BCACHETIME, 'bcachetime', $block['cachetime'] );
$cache_select->addOptionArray( array( '0' => _NOCACHE, '30' => sprintf( _SECONDS, 30 ), '60' => _MINUTE, '300' => sprintf( _MINUTES, 5 ), '1800' => sprintf( _MINUTES, 30 ), '3600' => _HOUR, '18000' => sprintf( _HOURS, 5 ), '86400' => _DAY, '259200' => sprintf( _DAYS, 3 ), '604800' => _WEEK, '2592000' => _MONTH ) );
$form->addElement( $cache_select );
if ( isset( $block['bid'] ) ) {
    $form->addElement( new ZariliaFormHidden( 'bid', $block['bid'] ) );
}
$form->addElement( new ZariliaFormHidden( 'op', $block['op'] ) );
$form->addElement( new ZariliaFormHidden( 'fct', 'blocksadmin' ) );
$button_tray = new ZariliaFormElementTray( '', '&nbsp;' );
$button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
if ( $block['is_custom'] ) {
    $button_tray->addElement( new ZariliaFormButton( '', 'previewblock', _PREVIEW, "submit" ) );
}
$button_tray->addElement( new ZariliaFormButton( '', 'submitblock', _SUBMIT, 'submit' ) );
$form->addElement( $button_tray );

?>