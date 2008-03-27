<?php

	//loading Zarilia Configuration
	require_once 'C:/xampp/htdocs/skycommunity/mainfile.php';

	$Configuration['DATABASE_HOST'] = ZAR_DB_HOST;
	$Configuration['DATABASE_NAME'] = ZAR_DB_NAME;
	$Configuration['DATABASE_USER'] = ZAR_DB_USER;
	$Configuration['DATABASE_PASSWORD'] = ZAR_DB_PASS;
	$Configuration['DATABASE_TABLE_PREFIX'] = ZAR_DB_PREFIX.'_vanilla_';
	$Configuration['DATABASE_CHARACTER_ENCODING'] = 'utf8';

	$DatabaseTables['User'] = ZAR_DB_PREFIX . '_users';

	$DatabaseColumns['User']['UserID'] = 'uid';
	$DatabaseColumns['User']['Name'] = 'uname';
	$DatabaseColumns['User']['Password'] = 'pass';
	$DatabaseColumns['User']['Email'] = 'email';
	$DatabaseColumns['User']['DateFirstVisit'] = 'user_regdate';

	$DatabaseColumns['User']['Icon'] = 'user_avatar';
	$DatabaseColumns['User']['Picture'] = 'user_avatar';

	$DatabaseColumns['User']['user_nicename'] = 'name';
	$DatabaseColumns['User']['user_url'] = 'url';
	$DatabaseColumns['User']['user_activation_key'] = 'actkey';
	$DatabaseColumns['User']['user_status'] = 'user_status';
	$DatabaseColumns['User']['display_name'] = 'uname';

	$DatabaseColumns['User']['RemoteIp'] = 'ipaddress';
	$DatabaseColumns['User']['DateLastActive'] = 'last_login';
	$DatabaseColumns['User']['UtilizeEmail'] = 'user_mailok';

	// Map Vanilla to Drupal's sessions table
//	$DatabaseTables['Sessions'] = ZAR_DB_PREFIX.'session';
 
	// and the columns
//	$DatabaseColumns['Sessions']['UserID'] = 'uid';
//	$DatabaseColumns['Sessions']['SessionID'] = 'sess_id';
	

/*
ALTER TABLE users
  ADD `RoleID` int(2) NOT NULL DEFAULT '3',
  ADD `StyleID` int(3) NOT NULL DEFAULT '1',
  ADD `CustomStyle` varchar(255) DEFAULT NULL,
  ADD `FirstName` varchar(50) NOT NULL DEFAULT '',
  ADD `LastName` varchar(50) NOT NULL DEFAULT '',
  ADD `EmailVerificationKey` varchar(50) DEFAULT NULL,
  ADD `ShowName` enum('1','0') NOT NULL DEFAULT '1',
  ADD `Attributes` text NULL,
  ADD `CountDiscussions` int(8) NOT NULL DEFAULT '0',
  ADD `CountVisit` int(8) NOT NULL DEFAULT '0',
  ADD `CountComments` int(8) NOT NULL DEFAULT '0',
  ADD `LastDiscussionPost` datetime DEFAULT NULL,
  ADD `DiscussionSpamCheck` int(11) NOT NULL DEFAULT '0',
  ADD `LastCommentPost` datetime DEFAULT NULL,
  ADD `CommentSpamCheck` int(11) NOT NULL DEFAULT '0',
  ADD `UserBlocksCategories` enum('1','0') NOT NULL DEFAULT '0',
  ADD `DefaultFormatType` varchar(20) DEFAULT NULL,
  ADD `Discovery` text,
  ADD `Preferences` text,
  ADD `SendNewApplicantNotifications` enum('1','0') NOT NULL DEFAULT '0';*/

//  ADD `DateLastActive` datetime NOT NULL DEFAULT '2006-06-06 00:00:00',
//  ADD `RemoteIp` varchar(100) NOT NULL DEFAULT '',
//  ADD `Icon` varchar(255) DEFAULT NULL,
//  ADD `Email` varchar(200) NOT NULL DEFAULT '',
//  ADD `VerificationKey` varchar(50) NOT NULL DEFAULT '',
//  ADD `UtilizeEmail` enum('1','0') NOT NULL DEFAULT '0',

	//$DatabaseTables['Role'] = $Configuration['DATABASE_TABLE_PREFIX'] . 'Role';
	?>