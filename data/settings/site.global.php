<?php exit(); ?>
return array (
  'tables' => 
  array (
    'users' => 0,
    'configoption' => 1,
    'config' => 1,
    'events' => 1,
    'group_permission' => 1,
    'groups' => 1,
    'groups_users_link' => 1,
    'language_base' => 0,
    'language_ext' => 0,
    'addons' => 1,
    'newblocks' => 1,
    'online' => 1,
    'tplfile' => 1,
    'tplset' => 1,
    'tplsource' => 1,
    'session' => 0,
    'block_addon_link' => 1,
    'ranks' => 0,
    'zarilianotifications' => 0,
    'configcategory' => 0,
    'smiles' => 0,
    'streaming' => 0,
    'security' => 0,
    'avatar' => 0,
    'mediacategory' => 0,
    'zariliacomments' => 0,
    'errors' => 0,
    'profile' => 1,
    'messages' => 0,
    'messages_buddy' => 0,
    'messages_sent' => 0,
    'avatar_user_link' => 0,
    'mimetypes' => 1,
	'imgset_tplset_link' => 1
  ),
  'db' => 
  array (
    'type' => 'mysql',
    'prefix' => 'cztsd',
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'name' => 'zarilia',
    'pconnect' => '0',
  ),
  'groups' => 
  array (
    1 => 'ADMIN',
    2 => 'USERS',
    3 => 'ANONYMOUS',
    4 => 'MODERATORS',
    5 => 'SUBMITTERS',
    6 => 'SUBSCRIPTION',
    7 => 'BANNED',
  ),
  'security' => array
  (
	'passkey' => 'Please change ME!.. ;-)',
	'encryption' => 'Internal XorBase64'
  ),
  'path' => 
  array (
    'root' => substr(dirname(__FILE__),0,-strlen('data/settings')+1), 
    'check' => 1,
  ),
  'sites' => 
  array (
  ),
);