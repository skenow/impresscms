<?php
// $Id: zarilia_version.php,v 1.1 2007/03/16 02:35:03 catzwolf Exp $
$addonversion['name'] = _MI_PERSONAL_MESSAGE;
$addonversion['version'] = 1.0;
$addonversion['description'] = _MI_PERSONAL_MESSAGE_DESC;
$addonversion['author'] = "John Neill";
$addonversion['credits'] = "Zarilia";
$addonversion['license'] = "GPL see LICENSE";
$addonversion['official'] = 1;
$addonversion['dirname'] = "messages";
// Admin things
$addonversion['hasAdmin'] = 1;
$addonversion['adminindex'] = "admin/index.php";
/*
* Mysql file
*/
$addonversion['sqlfile']['mysql'] = "sql/mysql.sql";
/*
* Mysql Tables
*/
$addonversion['tables'] = array("messages", "messages_buddy", "messages_sent");
$addonversion['globaltables'] = array("messages", "messages_buddy", "messages_sent");

// install
// $addonversion['onInstall'] = 'include/install.php';
// update
// $addonversion['onUpdate'] = 'include/update.php';
// Templates
// Menu
$addonversion['hasMain'] = 1;

/*
* Blocks
*/
$addonversion['blocks'][] = array( 'file' => 'buddy_block.php',
    'name' => _MI_BLOCK_BUDDY_LIST,
    'description' => _MI_BLOCK_BUDDY_LIST_DESC,
    'show_func' => 'b_buddy_show',
    'edit_func' => 'b_buddy_edit',
    'template' => 'message_buddy_list.html'
    );

/*
* Configs
*/
/**
$addonversion['config'][1] = array( 'name' => 'prunesubject',
    'title' => _MI_CONFIG_SUBJECT,
    'description' => _MI_CONFIG_SUBJECT_DESC,
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => _MI_CONFIG_SUBJECTDEFAULT );

$addonversion['config'][2] = array( 'name' => 'prunemessage',
    'title' => _MI_CONFIG_MESSAGE,
    'description' => _MI_CONFIG_MESSAGE_DESC,
    'formtype' => 'textarea',
    'valuetype' => 'text',
    'default' => _MI_CONFIG_MESSAGEDEFAULT );
//*/

?>