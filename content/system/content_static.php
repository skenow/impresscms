<?php
// $Id: content_static.php,v 1.3 2007/05/05 11:11:53 catzwolf Exp $
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
// no direct access
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

$zariliaOption['template_main'] = 'system_staticindex.html';
$zariliaTpl->addMeta( 'meta_keywords', htmlspecialchars( $this->_content_obj->getVar( 'content_meta' ), ENT_QUOTES ) );
$zariliaTpl->addMeta( 'meta_description', htmlspecialchars( $this->_content_obj->getVar( 'content_keywords' ), ENT_QUOTES ) );

$zariliaTpl->assign( 'content_author', $this->_content_obj->getUser() );
$zariliaTpl->assign( 'content_title', $this->_content_obj->getVar( 'content_title' ) );
$zariliaTpl->assign( 'content_subtitle', $this->_content_obj->getVar( 'content_subtitle' ) );
$zariliaTpl->assign( 'content_published', $this->_content_obj->getVar( 'content_published' ) );
$zariliaTpl->assign( 'content_updated', $this->_content_obj->getVar( 'content_updated' ) );
$zariliaTpl->assign( 'content_body', $this->_content_obj->getVar( 'content_body' , 'c') );
$zariliaTpl->assign( 'content_icons', $this->_content_obj->getIcons() );
$zariliaTpl->assign( 'zarilia_pagetitle', $this->_content_obj->getVar( 'content_title' ) );

$zariliaTpl->assign( 'zarilia_showheader', false );

?>