<?php
include_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';

$path = ZAR_UPLOAD_PATH . "/cache";
$show_submit = 1;

$atvarslisting = array();

$form = new ZariliaThemeForm( _MA_AD_EAVATAR_BATCH, 'avatar_form', $addonversion['adminpath'] );
$form->setExtra( 'enctype="multipart/form-data"' );
$graph_array = &ZariliaLists::getImgListAsArray( $path );

$form->setExtra( 'enctype="multipart/form-data"' );
$graph_array = &ZariliaLists::getImgListAsArray( $path );
if ( !$graph_array ) {
    $show_submit = 0;
    $graph_array = array( _MA_AD_EAVATAR_NOTHINGUPLOAD => 0 );
}
$smallimage_select = new ZariliaFormSelect( '', 'avatar_images', '', 10, true );
$smallimage_select->addOptionArray( $graph_array );
$smallimage_tray = new ZariliaFormElementTray( _MA_AD_EAVATAR_BATCHAVATARS, '&nbsp;' );
$smallimage_tray->setDescription( _MA_AD_EAVATAR_BATCHAVATARS_DSC );
$smallimage_tray->addElement( $smallimage_select );
$form->addElement( $smallimage_tray );

$ele1 = new ZariliaFormText( _MA_AD_EAVATAR_DWEIGHT, 'avatar_weight', 3, 4, 0 );
$ele1->setDescription( _MA_AD_EAVATAR_DWEIGHT_DSC );
$form->addElement( $ele1 );

$ele2 = new ZariliaFormRadioYN( _MA_AD_EAVATAR_AUTOWEIGHT, 'avatar_autoweight', 1 , ' ' . _YES . '', ' ' . _NO . '' );
$ele2->setDescription( _MA_AD_EAVATAR_AUTOWEIGHT_DSC );
$form->addElement( $ele2 );

$ele3 = new ZariliaFormRadioYN( _MA_AD_EAVATAR_DISPLAY, 'avatar_display', 1 , ' ' . _YES . '', ' ' . _NO . '' );
$ele3->setDescription( _MA_AD_EAVATAR_DISPLAY_DSC );
$form->addElement( $ele3 );

$ele4 = new ZariliaFormRadioYN( _MA_AD_EAVATAR_DELETE, 'avatar_remove', 0 , ' ' . _YES . '', ' ' . _NO . '' );
$ele4->setDescription( _MA_AD_EAVATAR_DELETE_DSC );
$form->addElement( $ele4 );

$form->addElement( new ZariliaFormHidden( 'op', 'batchsave' ) );

$button_tray = new ZariliaFormElementTray( '', '' );
$button_tray->addElement( new ZariliaFormButton( '', 'cancel', _CANCEL, 'button', 'onClick="history.go(-1);return true;"' ) );
$button_tray->addElement( new ZariliaFormButton( '', 'reset', _RESET, 'reset' ) );
if ( $show_submit ) {
    $button_tray->addElement( new ZariliaFormButton( '', 'submit', _SUBMIT, 'submit' ) );
}
$form->addElement( $button_tray );
$form->display();

?>