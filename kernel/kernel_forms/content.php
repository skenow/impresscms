<?php
// $Id: content.php,v 1.4 2007/05/05 11:12:35 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

require_once ZAR_ROOT_PATH . '/class/zariliaformloader.php';
require_once ZAR_ROOT_PATH . '/class/class.menubar.php';

$category_handler = &zarilia_gethandler( 'category' );

global $addonversion, $zariliaUser, $_callback;

$opt = zarilia_cleanRequestVars( $_REQUEST, 'opt', 0 );
$pagetype = zarilia_cleanRequestVars( $_REQUEST, 'pagetype', 'static' );
$tabbar = new ZariliaTabMenu( $opt );
$url = $addonversion['adminpath'] . '&op=edit&amp;content_id=' . $this->getVar( 'content_id' );
$this_array = array( _MA_AD_CONTENT_INFO => $url );
if ( !$this->isNew() ) {
    $this_array = array_merge( $this_array, array(
            _MA_AD_CONTENT_PROP => $url,
            _MA_AD_CONTENT_DETAILS => $url,
            _MA_AD_CONTENT_META => $url,
            _MA_AD_CONTENT_MENUS => $url,
            )
        ) ;
}
$tabbar->addTabArray( $this_array );
$tabbar->renderStart( 0, 1 );

switch ( intval( $opt ) ) {
    case 0:
        $caption = ( !$this->isNew() ) ? sprintf( _MA_AD_ECONTENT_MODIFY, $this->getVar( 'content_title' ) ) : _MA_AD_ECONTENT_CREATE;
        $subcategory = ( $this->getVar( 'content_sid' ) == 0  ) ? true : false;
        if ( $pagetype == 'static' || $pagetype == 'blog' ) {
            $this->setVar( 'content_type', $pagetype );
        }

        $form = new ZariliaThemeForm( $caption, 'content_form', $addonversion['adminpath'] );
        $form->setExtra( 'enctype="multipart/form-data"' );
        switch ( $this->getVar( 'content_type' ) ) {
            case 'news':
            case 'links':
            case 'downloads':
            case 'articles':
            case 'faq':
            case 'rss':
            default:
                $category_sid = new ZariliaFormSelectSection( _MA_AD_ECONTENT_CSECTION, 'content_sid', $this->getVar( 'content_sid' ), 1, false, null, false );
                $category_sid->setDescription( _MA_AD_ECONTENT_CSECTION_DSC );
                $form->addElement( $category_sid );
                $_category_obj = $category_handler->getCategoryObj( null, null, $this->getVar( 'content_sid' ) );
				$category_id = new ZariliaFormTree( _MA_AD_ECONTENT_CCATEGORY, 'content_cid', 'category_title', '-', $this->getVar( 'content_cid' ), false, 0 );
                $category_id->addOptions( $_category_obj['list'], 'category_id', 'category_pid' );
                $form->addElement( $category_id, true );
                break;

            case 'static':
                $form->addElement( new ZariliaFormHidden( 'content_sid', 0 ) );
                $form->addElement( new ZariliaFormHidden( 'content_cid', 0 ) );
                // $this->setVar( 'content_sid', 0 );
                // $form->addElement( new ZariliaFormHidden( 'content_sid', 0 ) );
                break;

            case 'blog':
                $form->addElement( new ZariliaFormHidden( 'content_sid', 0 ) );
                $form->addElement( new ZariliaFormHidden( 'content_cid', 0 ) );
                // $this->setVar( 'content_sid', 0 );
                // $form->addElement( new ZariliaFormHidden( 'content_sid', 0 ) );
                // $category_sid = new ZariliaFormSelectSection( _MA_AD_ECATEGORY_CSECTION, 'content_sid', $this->getVar( 'content_sid' ), $subcategory, 0 );
                // $category_sid->setDescription( _MA_AD_SMILIES_SELECTIMAGE_DSC );
                // $form->addElement( $category_sid );
                break;
        }

        /*Set display name*/
        $content_title = new ZariliaFormText( _MA_AD_ECONTENT_TITLE, 'content_title', 50, 100, $this->getVar( 'content_title', 'e' ) );
        $content_title->setDescription( _MA_AD_ECONTENT_TITLE_DSC );
        $form->addElement( $content_title, true );

        /*Set display name*/
        $content_subtitle = new ZariliaFormText( _MA_AD_ECONTENT_SUBTITLE, 'content_subtitle', 50, 150, $this->getVar( 'content_subtitle', 'e' ) );
        $content_subtitle->setDescription( _MA_AD_ECONTENT_SUBTITLE_DSC );
        $form->addElement( $content_subtitle, true );

		//echo $GLOBALS['zariliaUser']->getVar( 'editor' ).'-0----------------------------';

        if ( $this->getVar( 'content_type' ) != 'static' ) {
            $options['name'] = 'content_intro';
            $options['value'] = $this->getVar( 'content_intro', 'e' );
            $content_intro = new ZariliaFormEditor( _MA_AD_ECONTENT_BODY, $GLOBALS['zariliaUser']->getVar( 'editor' ), $options );
            $content_intro->setDescription( _MA_AD_ECONTENT_BODY_DSC );
            $content_intro->setNocolspan( 1 );
            $form->addElement( $content_intro );
        }

        $options['name'] = 'content_body';
        $options['value'] = $this->getVar( 'content_body', 'e' );
        $content_body = new ZariliaFormEditor( _MA_AD_ECONTENT_BODY, $GLOBALS['zariliaUser']->getVar( 'editor' ), $options, $nohtml = false, $onfailure = 'textarea' );
        $content_body->setDescription( _MA_AD_ECONTENT_BODY_DSC );
        $content_body->setNocolspan( 1 );
        $form->addElement( $content_body );

        $content_image = new ZariliaFormSelectImg( _MA_AD_ECONTENT_IMAGE, 'content_images', $this->getVar( 'content_images' ), 'zarilia_image', 1 );
        $content_image->setDescription( _MA_AD_ECONTENT_IMAGE_DSC );
        $form->addElement( $content_image );

        $approve_checkbox = new ZariliaFormCheckBox( _MA_AD_ECONTENT_PUBLISH, 'content_approved', $this->getVar( 'content_approved' ) );
        $approve_checkbox->setDescription( _MA_AD_ECONTENT_PUBLISH_DSC );
        $approve_checkbox->addOption( 1, _MA_AD_ECONTENT_PUBLISH_CHECK );
        $form->addElement( $approve_checkbox, true );

        $display_checkbox = new ZariliaFormCheckBox( _MA_AD_ECONTENT_DISPLAY, 'content_display', $this->getVar( 'content_display' ) );
        $display_checkbox->setDescription( _MA_AD_ECONTENT_DISPLAY_DSC );
        $display_checkbox->addOption( 1, _MA_AD_ECONTENT_DISPLAY_CHECK );
        $form->addElement( $display_checkbox, false );
        break;

    case 1:
        $form = new ZariliaThemeForm( 'Page Properties', 'static_form', $addonversion['adminpath'] );
        $perm_handler = &zarilia_gethandler( 'groupperm' );
        $form->addElement( new ZariliaFormSelectGroup( _MD_AM_ECONTENT_RGRP, 'readgroup', true, $perm_handler->getGroupIds( 'content_read', $this->getVar( 'content_id' ) ), 5, true ) );
        /**
         */
        $content_alias = new ZariliaFormText( _MA_AD_ECONTENT_ALIAS, 'content_alias', 50, 100, $this->getVar( 'content_alias', 'e' ) );
        $content_alias->setDescription( _MA_AD_ECONTENT_TITLE_DSC );
        $form->addElement( $content_alias, false );

        /*Set display name*/
        $content_weight = new ZariliaFormText( _MA_AD_ECONTENT_WEIGHT, 'content_weight', 5, 5, $this->getVar( 'content_weight', 'e' ) );
        $content_weight->setDescription( _MA_AD_ECONTENT_WEIGHT_DSC );
        $form->addElement( $content_weight, false );

        $content_created = new ZariliaFormTextDateSelect( _MA_AD_ECONTENT_CREATED, 'content_created', 20, $this->getVar( 'content_created' ), true );
        $content_created->setDescription( _MA_AD_ECONTENT_CREATED_DSC );
        $form->addElement( $content_created, false );

        $content_published = new ZariliaFormTextDateSelect( _MA_AD_ECONTENT_PUBLISHED, 'content_published', 20, $this->getVar( 'content_published' ), true );
        $content_published->setDescription( _MA_AD_ECONTENT_PUBLISHED_DSC );
        $form->addElement( $content_published, false );

        $content_updated = new ZariliaFormTextDateSelect( _MA_AD_ECONTENT_UPDATED, 'content_updated', 20, $this->getVar( 'content_updated' ), true );
        $content_updated->setDescription( _MA_AD_ECONTENT_UPDATED_DSC );
        $form->addElement( $content_updated, false );

        $content_expired = new ZariliaFormTextDateSelect( _MA_AD_ECONTENT_EXPIRED, 'content_expired', 20, $this->getVar( 'content_expired' ), true );
        $content_expired->setDescription( _MA_AD_ECONTENT_EXPIRED_DSC );
        $form->addElement( $content_expired, false );

        $content_version = new ZariliaFormText( _MA_AD_ECONTENT_VERSION, 'content_version', 5, 10, $this->getVar( 'content_version', 'n' ), true );
        $content_version->setDescription( _MA_AD_ECONTENT_VERSION_DSC );
        $form->addElement( $content_version, false );
        $form->addElement( new ZariliaFormHidden( 'content_approved', $this->getVar( 'content_approved' ) ) );
        $form->addElement( new ZariliaFormHidden( 'content_display', $this->getVar( 'content_display' ) ) );
        break;

    case 2:
        $form = new ZariliaThemeForm( 'Page Properties', 'static_form', $addonversion['adminpath'] );
        $menu_selection = array( 0 => _MA_AD_CONTENT_GLOBAL, 1 => _MA_AD_CONTENT_SHOW, 2 => _MA_AD_CONTENT_HIDE );
        // if ( $this->getVar( 'content_attributes' ) ) {
        // $select_array = explode( ',' $this->getVar( 'content_attributes' ) );
        // } else {
        $select_array = array( 'content_dprint' => 0, 'content_dpdf' => 2, 'content_demail' => 0, 'content_dauthor' => 0, 'content_dpublish' => 0, 'content_dupdated' => 0, 'content_drating' => 0, 'content_dbbutton' => 0 );
        // }
        foreach( $select_array as $k => $v ) {
            $_constant = strtoupper( "_MA_AD_E" . $k );
            $_constantdsc = strtoupper( "_MA_AD_E" . $k . "_DSC" );
            $k = new ZariliaFormSelect( constant( $_constant ), $k, $v, 1, false );
            $k->setDescription( constant( $_constantdsc ) );
            $k->addOptionArray( $menu_selection, true );
            $form->addElement( $k, false );
            unset( $k );
        }
        /*
        foreach( $select_array as $k ) {
            $_constant = strtoupper( "_MA_AD_E" . $k );
            $_constantdsc = strtoupper( "_MA_AD_E" . $k."_DSC" );
			echo "define('".$_constant."','');<br />";
			echo "define('".$_constantdsc."','');<br />";

        }
*/

        /*
		$content_print = new ZariliaFormSelect( _MA_AD_ECONTENT_PRINTICON, 'content_dprint', $this->getVar( 'content_expired' ), 1, false );
        $content_print->setDescription( _MA_AD_ECONTENT_PRINTICON_DSC );
		$content_print->addOptionArray( $menu_selection, true );
        $form->addElement( $content_print, false );

        $content_pdf = new ZariliaFormSelect( _MA_AD_ECONTENT_PDFICON, 'content_dpdf', $this->getVar( 'content_pdf' ), 1, false );
        $content_pdf->setDescription( _MA_AD_ECONTENT_PDFICON_DSC );
		$content_pdf->addOptionArray( $menu_selection, true );
        $form->addElement( $content_pdf, false );

        $content_email = new ZariliaFormSelect( _MA_AD_ECONTENT_PDFICON, 'content_demail', $this->getVar( 'content_email' ), 1, false );
        $content_email->setDescription( _MA_AD_ECONTENT_PDFICON_DSC );
		$content_email->addOptionArray( $menu_selection, true );
        $form->addElement( $content_email, false );

        $content_author = new ZariliaFormSelect( _MA_AD_ECONTENT_PDFICON, 'content_dauthor', $this->getVar( 'content_dauthor' ), 1, false );
        $content_author->setDescription( _MA_AD_ECONTENT_PDFICON_DSC );
		$content_author->addOptionArray( $menu_selection, true );
        $form->addElement( $content_author, false );


        $content_updated = new ZariliaFormSelect( $caption, $name, $value = null, $size = 1, $multiple = false );
        $content_updated->addOptionArray( $menu_selection, true );
        $form->addElement( $content_updated, false );

        $content_updated = new ZariliaFormSelect( $caption, $name, $value = null, $size = 1, $multiple = false );
        $content_updated->addOptionArray( $menu_selection, true );
        $form->addElement( $content_updated, false );

        $content_updated = new ZariliaFormSelect( $caption, $name, $value = null, $size = 1, $multiple = false );
        $content_updated->addOptionArray( $menu_selection, true );
        $form->addElement( $content_updated, false );
*/
        /**
         */
        break;

    case 3:
        $form = new ZariliaThemeForm( 'Meta Tags', 'static_form', $addonversion['adminpath'] );
        $options['rows'] = 10;
        $options['cols'] = 75;
        /**
         */
        $options['name'] = 'content_meta';
        $options['value'] = $this->getVar( 'content_meta', 'e' );
        $content_meta = new ZariliaFormEditor( _MA_AD_ECONTENT_MDESCRIPTION, 'textarea', $options, $nohtml = false, $onfailure = "textarea" );
        $content_meta->setDescription( _MA_AD_ECONTENT_MDESCRIPTION_DSC );
        $content_meta->setNocolspan( 0 );
        $form->addElement( $content_meta );
        /**
         */
        $options['name'] = 'content_keywords';
        $options['value'] = $this->getVar( 'content_keywords', 'e' );
        $content_keywords = new ZariliaFormEditor( _MA_AD_ECONTENT_MKEYWORDS, 'textarea', $options, $nohtml = false, $onfailure = "textarea" );
        $content_keywords->setDescription( _MA_AD_ECONTENT_MKEYWORDS_DSC );
        $content_keywords->setNocolspan( 0 );
        $form->addElement( $content_keywords );
        $form->addElement( new ZariliaFormHidden( 'content_approved', $this->getVar( 'content_approved' ) ) );
        $form->addElement( new ZariliaFormHidden( 'content_display', $this->getVar( 'content_display' ) ) );
        break;

    case 4:
        if ( $this->getVar( 'content_sid' ) ) {
            $content_type = call_user_func( array( $_callback, 'getcType' ), $this->getVar( 'content_sid' ) );
        } else {
            $content_type = 'static';
        }

        $form = new ZariliaThemeForm( 'Add Menu', 'menu_form', 'index.php?fct=menus' );
        if ( !$this->isNew() ) {
            // $form = new ZariliaThemeForm( 'Add Menu', 'menu_form', 'index.php?fct=menus' );
            $menus_handler = &zarilia_gethandler( 'menus' );

            $menu = $menus_handler->getMenuItem( 0, $content_type, $this->getVar( 'content_id' ) );
            if ( !$menu ) {
                $menu = $menus_handler->create();
                $menu->setVar( 'menu_title', $this->getVar( 'content_title', 'e' ) );
                $menu->setVar( 'menu_type', 0 );
            }

            $section_menuside = new ZariliaFormSelectMenu( _MA_AD_MENU_SELECTION, 'menu_type', $menu->getVar( 'menu_type' ), 1, true );
            $section_menuside->setDescription( _MA_AD_MENU_SELECTION_DSC );
            $form->addElement( $section_menuside, false );

            $menu_title = new ZariliaFormText( _MA_AD_MENU_STITLE, 'menu_title', 50, 60, $menu->getVar( 'menu_title', 'e' ) );
            $menu_title->setDescription( _MA_AD_MENU_STITLE_DSC );
            $form->addElement( $menu_title, true );

            $menu_perm_handler = &zarilia_gethandler( 'groupperm' );
            $menu_perms = $menu_perm_handler->getGroupIds( 'menu_read', $menu->getVar( 'menu_id' ) );
            if ( !count( $menu_perms ) ) {
                $menu_perms = $menu_perm_handler->getGroupIds( 'content_read', $this->getVar( 'content_id' ) );
            }

            $section_accessside = new ZariliaFormSelectGroup( _MA_AD_MENU_SACCESS, 'menu_read', true, $menu_perms, 5, true );
            $section_accessside->setDescription( _MA_AD_MENU_SACCESS_DSC );
            $form->addElement( $section_accessside, false );
            // Hidden values//
            $form->addElement( new ZariliaFormHidden( 'menu_add', 1 ) );
            $form->addElement( new ZariliaFormHidden( 'menu_id', $menu->getVar( 'menu_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_mid', 1 ) );
            $form->addElement( new ZariliaFormHidden( 'menu_name', $content_type ) );
            $form->addElement( new ZariliaFormHidden( 'menu_sectionid', $this->getVar( 'content_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_link', '{X_SITEURL}/index.php?page_type=' . $content_type . '&id=' . $this->getVar( 'content_id' ) ) );
            $form->addElement( new ZariliaFormHidden( 'menu_display', 1 ) );
        }
        break;
}
/*hidden values*/
if ( $this->getVar( 'content_id' ) ) {
    $form->addElement( new ZariliaFormHidden( 'content_id', $this->getVar( 'content_id' ) ) );
}
$form->addElement( new ZariliaFormHidden( 'op', 'save' ) );
$form->addElement( new ZariliaFormHidden( 'content_opt', $opt ) );
$form->addElement( new ZariliaFormHidden( 'content_type', $this->getVar( 'content_type' ) ) );
//$form->addElement( new ZariliaFormHidden( 'content_uid', $zariliaUser->getVar( 'uid' ) ) );
/*button_tray*/
$form->addElement( new ZariliaFormButtontray( 'submit', _SUBMIT ) );
$form->display();

?>