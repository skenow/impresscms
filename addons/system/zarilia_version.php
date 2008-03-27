<?php
// $Id: zarilia_version.php,v 1.4 2007/05/05 11:10:53 catzwolf Exp $
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
$addonversion = array( 'name' => _MI_SYSTEM_NAME,
    'version' => '1.00',
    'description' => _MI_SYSTEM_DESC,
    'author' => 'John Neill',
    'credits' => 'Zarilia',
    'dirname' => 'system',
    'hasAdmin' => 1,
    'hasmain' => 0,
    'hasage' => 1,
    'adminindex' => 'index.php',
    'image' => 'settings.png'
    );
// Templates
$addonversion['templates'][1]['file'] = 'system_mediamanager.html';
$addonversion['templates'][1]['description'] = '';
$addonversion['templates'][2]['file'] = 'system_mediaupload.html';
$addonversion['templates'][2]['description'] = '';
$addonversion['templates'][3]['file'] = 'system_userinfo.html';
$addonversion['templates'][3]['description'] = '';
$addonversion['templates'][4]['file'] = 'system_userform.html';
$addonversion['templates'][4]['description'] = '';
$addonversion['templates'][5]['file'] = 'system_rss.html';
$addonversion['templates'][5]['description'] = '';
$addonversion['templates'][6]['file'] = 'system_comment.html';
$addonversion['templates'][6]['description'] = '';
$addonversion['templates'][7]['file'] = 'system_comments_flat.html';
$addonversion['templates'][7]['description'] = '';
$addonversion['templates'][8]['file'] = 'system_comments_thread.html';
$addonversion['templates'][8]['description'] = '';
$addonversion['templates'][9]['file'] = 'system_comments_nest.html';
$addonversion['templates'][9]['description'] = '';
$addonversion['templates'][11]['file'] = 'system_dummy.html';
$addonversion['templates'][11]['description'] = 'Dummy template file for holding non-template contents. This should not be edited.';
$addonversion['templates'][12]['file'] = 'system_notification_list.html';
$addonversion['templates'][12]['description'] = '';
$addonversion['templates'][13]['file'] = 'system_notification_select.html';
$addonversion['templates'][13]['description'] = '';
$addonversion['templates'][14]['file'] = 'system_rssindex.html';
$addonversion['templates'][14]['description'] = '';
$addonversion['templates'][15]['file'] = 'system_rssfeed.html';
$addonversion['templates'][15]['description'] = '';
$addonversion['templates'][16]['file'] = 'system_staticindex.html';
$addonversion['templates'][16]['description'] = '';
$addonversion['templates'][17]['file'] = 'system_errorpage.html';
$addonversion['templates'][17]['description'] = '';
/*Blog information*/
$addonversion['templates'][18]['file'] = 'system_blogindex.html';
$addonversion['templates'][18]['description'] = '';
$addonversion['templates'][19]['file'] = 'system_newsindex.html';
$addonversion['templates'][19]['description'] = '';
$addonversion['templates'][20]['file'] = 'system_stream.html';
$addonversion['templates'][20]['description'] = '';
$addonversion['templates'][21]['file'] = 'system_lostpass.html';
$addonversion['templates'][21]['description'] = '';
$addonversion['templates'][22]['file'] = 'system_registerform.html';
$addonversion['templates'][22]['description'] = '';
$addonversion['templates'][23]['file'] = 'system_edituserform.html';
$addonversion['templates'][23]['description'] = '';
$addonversion['templates'][24]['file'] = 'system_friend.html';
$addonversion['templates'][24]['description'] = '';
$addonversion['templates'][25]['file'] = 'system_avatarform.html';
$addonversion['templates'][25]['description'] = '';

// Blocks
$addonversion['blocks'][1]['file'] = "system_blocks.php";
$addonversion['blocks'][1]['name'] = _MI_SYSTEM_BNAME2;
$addonversion['blocks'][1]['description'] = "Shows user block";
$addonversion['blocks'][1]['show_func'] = "b_system_user_show";
$addonversion['blocks'][1]['template'] = 'system_block_user.html';
$addonversion['blocks'][1]['side'] = 0;

$addonversion['blocks'][2]['file'] = "system_blocks.php";
$addonversion['blocks'][2]['name'] = _MI_SYSTEM_BNAME3;
$addonversion['blocks'][2]['description'] = "Shows login form";
$addonversion['blocks'][2]['show_func'] = "b_system_login_show";
$addonversion['blocks'][2]['template'] = 'system_block_login.html';
$addonversion['blocks'][2]['side'] = 0;

$addonversion['blocks'][3]['file'] = "system_blocks.php";
$addonversion['blocks'][3]['name'] = _MI_SYSTEM_BNAME4;
$addonversion['blocks'][3]['description'] = "Shows search form block";
$addonversion['blocks'][3]['show_func'] = "b_system_search_show";
$addonversion['blocks'][3]['template'] = 'system_block_search.html';

$addonversion['blocks'][4]['file'] = "system_blocks.php";
$addonversion['blocks'][4]['name'] = _MI_SYSTEM_BNAME16;
$addonversion['blocks'][4]['description'] = "Shows headline news via RDF/RSS news feed";
$addonversion['blocks'][4]['options'] = 0;
$addonversion['blocks'][4]['show_func'] = 'b_system_rssshow';
$addonversion['blocks'][4]['template'] = 'system_block_rss.html';

$addonversion['blocks'][5]['file'] = "system_blocks.php";
$addonversion['blocks'][5]['name'] = _MI_SYSTEM_BNAME6;
$addonversion['blocks'][5]['description'] = "Shows the main navigation menu of the site";
$addonversion['blocks'][5]['show_func'] = "b_system_main_show";
$addonversion['blocks'][5]['template'] = 'system_block_mainmenu.html';
$addonversion['blocks'][5]['side'] = 0;

$addonversion['blocks'][6]['file'] = "system_blocks.php";
$addonversion['blocks'][6]['name'] = _MI_SYSTEM_BNAME7;
$addonversion['blocks'][6]['description'] = "Shows basic info about the site and a link to Recommend Us pop up window";
$addonversion['blocks'][6]['show_func'] = "b_system_info_show";
$addonversion['blocks'][6]['edit_func'] = "b_system_info_edit";
$addonversion['blocks'][6]['options'] = "320|190|poweredby.gif|1";
$addonversion['blocks'][6]['template'] = 'system_block_siteinfo.html';

$addonversion['blocks'][7]['file'] = "system_blocks.php";
$addonversion['blocks'][7]['name'] = _MI_SYSTEM_BNAME8;
$addonversion['blocks'][7]['description'] = "Displays users/guests currently online";
$addonversion['blocks'][7]['show_func'] = "b_system_online_show";
$addonversion['blocks'][7]['template'] = 'system_block_online.html';
$addonversion['blocks'][7]['liveupdate'] = true;

$addonversion['blocks'][8]['file'] = "system_blocks.php";
$addonversion['blocks'][8]['name'] = _MI_SYSTEM_BNAME9;
$addonversion['blocks'][8]['description'] = "Top posters";
$addonversion['blocks'][8]['show_func'] = "b_system_topposters_show";
$addonversion['blocks'][8]['options'] = "10|0";
$addonversion['blocks'][8]['edit_func'] = "b_system_topposters_edit";
$addonversion['blocks'][8]['template'] = 'system_block_topusers.html';
$addonversion['blocks'][8]['liveupdate'] = true;

$addonversion['blocks'][9]['file'] = "system_blocks.php";
$addonversion['blocks'][9]['name'] = _MI_SYSTEM_BNAME10;
$addonversion['blocks'][9]['description'] = "Shows most recent users";
$addonversion['blocks'][9]['show_func'] = "b_system_newmembers_show";
$addonversion['blocks'][9]['options'] = "10|0";
$addonversion['blocks'][9]['edit_func'] = "b_system_newmembers_edit";
$addonversion['blocks'][9]['template'] = 'system_block_newusers.html';
$addonversion['blocks'][9]['liveupdate'] = true;

$addonversion['blocks'][10]['file'] = "system_blocks.php";
$addonversion['blocks'][10]['name'] = _MI_SYSTEM_BNAME11;
$addonversion['blocks'][10]['description'] = "Shows most recent comments";
$addonversion['blocks'][10]['show_func'] = "b_system_comments_show";
$addonversion['blocks'][10]['options'] = "10";
$addonversion['blocks'][10]['edit_func'] = "b_system_comments_edit";
$addonversion['blocks'][10]['template'] = 'system_block_comments.html';
$addonversion['blocks'][10]['liveupdate'] = true;

$addonversion['blocks'][11]['file'] = "system_blocks.php";
$addonversion['blocks'][11]['name'] = _MI_SYSTEM_BNAME12;
$addonversion['blocks'][11]['description'] = "Shows notification options";
$addonversion['blocks'][11]['show_func'] = "b_system_notification_show";
$addonversion['blocks'][11]['template'] = 'system_block_notification.html';

$addonversion['blocks'][12]['file'] = "system_blocks.php";
$addonversion['blocks'][12]['name'] = _MI_SYSTEM_BNAME13;
$addonversion['blocks'][12]['description'] = "Shows theme selection box";
$addonversion['blocks'][12]['show_func'] = "b_system_themes_show";
$addonversion['blocks'][12]['options'] = "0|80";
$addonversion['blocks'][12]['edit_func'] = "b_system_themes_edit";
$addonversion['blocks'][12]['template'] = 'system_block_themes.html';

$addonversion['blocks'][13]['file'] = "system_blocks.php";
$addonversion['blocks'][13]['name'] = _MI_SYSTEM_BNAME15;
$addonversion['blocks'][13]['description'] = "Shows Language block";
$addonversion['blocks'][13]['show_func'] = "b_language_select_show";
$addonversion['blocks'][13]['edit_func'] = "b_language_select_edit";
$addonversion['blocks'][13]['template'] = 'system_block_language.html';

$addonversion['blocks'][14]['file'] = "system_blocks.php";
$addonversion['blocks'][14]['name'] = _MI_SYSTEM_BNAME17;
$addonversion['blocks'][14]['description'] = "Shows Streaming block";
$addonversion['blocks'][14]['show_func'] = "b_system_stream_show";
$addonversion['blocks'][14]['edit_func'] = "b_system_stream_edit";
$addonversion['blocks'][14]['template'] = 'system_block_streaming.html';
// Menu
$addonversion['adminpath'] = "index.php?fct=coreinfo";
$addonversion['category'] = ZAR_SYSTEM_HOME;

?>
