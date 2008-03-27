-- 
-- Table structure for table `messages`
-- 

CREATE TABLE `messages` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `subject` varchar(255) NOT NULL default '',
  `from_userid` mediumint(8) unsigned NOT NULL default '0',
  `to_userid` mediumint(8) unsigned NOT NULL default '0',
  `is_trash` int(1) NOT NULL default '0',
  `is_saved` int(1) NOT NULL default '0',
  `time` int(11) unsigned NOT NULL default '0',
  `text` text NOT NULL,
  `msg` int(1) NOT NULL default '1',
  `priority` int(1) NOT NULL default '3',
  `track` int(1) NOT NULL default '0',
  `is_attachment` int(1) NOT NULL default '0',
  `attachment_type` varchar(255) NOT NULL default '',
  `trash_date` int(11) NOT NULL,
  `read_date` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `to_userid` (`to_userid`),
  KEY `msgidfromuserid` (`id`,`from_userid`),
  KEY `touseridmsg` (`to_userid`)
) ENGINE=MyISAM;

-- 
-- Table structure for table `messages_buddy`
-- 

CREATE TABLE `messages_buddy` (
  `buddy_id` mediumint(8) unsigned NOT NULL auto_increment,
  `buddy_desc` varchar(255) NOT NULL,
  `buddy_owner` mediumint(8) NOT NULL,
  `buddy_uid` mediumint(8) unsigned NOT NULL default '0',
  `buddy_name` varchar(160) NOT NULL default '0',
  `buddy_allow` int(1) NOT NULL default '0',
  `buddy_date` int(11) NOT NULL,
  PRIMARY KEY  (`buddy_id`),
  KEY `to_userid` (`buddy_name`),
  KEY `msgidfromuserid` (`buddy_id`,`buddy_uid`),
  KEY `touseridmsg` (`buddy_name`)
) ENGINE=MyISAM;

-- 
-- Table structure for table `messages_sent`
-- 

CREATE TABLE `messages_sent` (
  `mid` mediumint(8) unsigned NOT NULL auto_increment,
  `id` mediumint(8) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL default '',
  `from_userid` mediumint(8) unsigned NOT NULL default '0',
  `to_userid` mediumint(8) unsigned NOT NULL default '0',
  `is_trash` int(1) NOT NULL default '0',
  `is_saved` int(1) NOT NULL default '0',
  `time` int(11) unsigned NOT NULL default '0',
  `text` text NOT NULL,
  `msg` int(1) NOT NULL default '1',
  `priority` int(1) NOT NULL default '3',
  `track` int(1) NOT NULL default '0',
  `is_attachment` int(1) NOT NULL default '0',
  `attachment_type` varchar(255) NOT NULL default '',
  `trash_date` int(11) NOT NULL,
  `read_date` int(11) NOT NULL,
  PRIMARY KEY  (`mid`),
  KEY `to_userid` (`to_userid`),
  KEY `touseridmsg` (`to_userid`),
  KEY `msgidfromuserid` (`mid`,`from_userid`)
) ENGINE=MyISAM;