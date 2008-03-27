<?php
// $Id: tplform.php,v 1.1 2007/03/16 02:36:57 catzwolf Exp $
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

include_once ZAR_ROOT_PATH.'/class/zariliaformloader.php';
$form = new ZariliaThemeForm(_MD_EDITTEMPLATE, 'template_form', 'index.php', 'post', true);
$form->addElement(new ZariliaFormLabel(_MD_FILENAME, $tform['tpl_file']));
$form->addElement(new ZariliaFormLabel(_MD_FILEDESC, $tform['tpl_desc']));
$form->addElement(new ZariliaFormLabel(_MD_LASTMOD, formatTimestamp($tform['tpl_lastmodified'], 'l')));
$form->addElement(new ZariliaFormTextArea(_MD_FILEHTML, 'html', $tform['tpl_source'], 25, 70));
$form->addElement(new ZariliaFormHidden('id', $tform['tpl_id']));
$form->addElement(new ZariliaFormHidden('op', 'edittpl_go'));
$form->addElement(new ZariliaFormHidden('redirect', 'edittpl'));
$form->addElement(new ZariliaFormHidden('fct', 'tplsets'));
$form->addElement(new ZariliaFormHidden('moddir', $tform['tpl_addon']));
if ($tform['tpl_tplset'] != 'default') {
    $button_tray = new ZariliaFormElementTray('');
    $button_tray->addElement(new ZariliaFormButton('', 'previewtpl', _PREVIEW, 'submit'));
    $button_tray->addElement(new ZariliaFormButton('', 'submittpl', _SUBMIT, 'submit'));
    $form->addElement($button_tray);
} else {
    $form->addElement(new ZariliaFormButton('', 'previewtpl', _MD_VIEW, 'submit'));
}
?>