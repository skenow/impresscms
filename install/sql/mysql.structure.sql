# phpMyAdmin SQL Dump
# version 2.9.2
# http://www.phpmyadmin.net
#
# Host: localhost
# Generation Time: Apr 01, 2007 at 04:43 PM
# Server version: 5.0.27
# PHP Version: 5.2.1
#
# Database: zarilia
#
# --------------------------------------------------------

#
# Table structure for table addons
#

CREATE TABLE addons (
  mid smallint(5) unsigned NOT NULL auto_increment,
  name varchar(150) NOT NULL default '',
  version smallint(5) unsigned NOT NULL default '100',
  last_update int(10) unsigned NOT NULL default '0',
  weight smallint(3) unsigned NOT NULL default '0',
  isactive tinyint(1) unsigned NOT NULL default '0',
  dirname varchar(25) NOT NULL default '',
  hasmain tinyint(1) unsigned NOT NULL default '0',
  hasadmin tinyint(1) unsigned NOT NULL default '0',
  hassearch tinyint(1) unsigned NOT NULL default '0',
  hasconfig tinyint(1) unsigned NOT NULL default '0',
  hascomments tinyint(1) unsigned NOT NULL default '0',
  hasnotification tinyint(1) unsigned NOT NULL default '0',
  hasage tinyint(1) unsigned NOT NULL default '0',
  hasmimetype tinyint(1) unsigned NOT NULL default '0',
  hassubmit tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (mid),
  KEY hasmain (hasmain),
  KEY hasadmin (hasadmin),
  KEY hassearch (hassearch),
  KEY hasnotification (hasnotification),
  KEY dirname (dirname),
  KEY name (name(15))
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table age
#

CREATE TABLE age (
  age_id int(5) NOT NULL auto_increment,
  age_itemid int(11) NOT NULL default '0',
  age_uid int(5) NOT NULL default '0',
  age_agreed tinyint(1) NOT NULL default '0',
  age_date int(11) NOT NULL default '0',
  age_gdate date NOT NULL default '0000-00-00',
  age_ip varchar(15) NOT NULL default '',
  age_mid int(5) NOT NULL default '0',
  age_dtitle varchar(255) NOT NULL default '',
  PRIMARY KEY  (age_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table avatar
#

CREATE TABLE avatar (
  avatar_id mediumint(8) unsigned NOT NULL auto_increment,
  avatar_file varchar(30) NOT NULL default '',
  avatar_name varchar(100) NOT NULL default '',
  avatar_mimetype varchar(30) NOT NULL default '',
  avatar_created int(10) NOT NULL default '0',
  avatar_display tinyint(1) unsigned NOT NULL default '0',
  avatar_weight smallint(5) unsigned NOT NULL default '0',
  avatar_type char(1) NOT NULL default '',
  avatar_uid mediumint(8) NOT NULL default '0',
  avatar_usercount mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (avatar_id),
  KEY avatar_type (avatar_type,avatar_display)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table avatar_user_link
#

CREATE TABLE avatar_user_link (
  avatar_id mediumint(8) unsigned NOT NULL default '0',
  user_id mediumint(8) unsigned NOT NULL default '0',
  KEY avatar_user_id (avatar_id,user_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table block_addon_link
#

CREATE TABLE block_addon_link (
  block_id mediumint(8) unsigned NOT NULL default '0',
  addon_id smallint(5) NOT NULL default '0',
  KEY addon_id (addon_id),
  KEY block_id (block_id)
) ENGINE=MyISAM ;
# --------------------------------------------------------

#
# Table structure for table category
#

CREATE TABLE category (
  category_id mediumint(8) NOT NULL auto_increment,
  category_pid mediumint(8) NOT NULL,
  category_sid mediumint(3) NOT NULL default '0',
  category_title varchar(150) NOT NULL,
  category_description text NOT NULL,
  category_image varchar(150) NOT NULL,
  category_weight tinyint(1) NOT NULL default '0',
  category_display tinyint(1) NOT NULL default '1',
  category_published int(11) NOT NULL,
  category_imageside varchar(4) NOT NULL default 'left',
  category_type varchar(10) NOT NULL,
  category_header varchar(150) NOT NULL,
  category_footer varchar(150) NOT NULL,
  category_body text NOT NULL,
  UNIQUE KEY category_id (category_id),
  KEY category_title (category_display,category_published,category_weight)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table config
#

CREATE TABLE config (
  conf_id smallint(5) unsigned NOT NULL auto_increment,
  conf_modid smallint(5) unsigned NOT NULL default '0',
  conf_catid smallint(5) unsigned NOT NULL default '0',
  conf_sectid smallint(5) unsigned NOT NULL default '1',
  conf_name varchar(25) NOT NULL default '',
  conf_title varchar(30) NOT NULL default '',
  conf_value text NOT NULL,
  conf_desc text NOT NULL,
  conf_formtype varchar(15) NOT NULL default '',
  conf_valuetype varchar(10) NOT NULL default '',
  conf_order smallint(5) unsigned NOT NULL default '0',
  conf_required tinyint(2) NOT NULL default '0',
  PRIMARY KEY  (conf_id),
  KEY conf_mod_cat_id (conf_modid,conf_catid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table configcategory
#

CREATE TABLE configcategory (
  confcat_id smallint(5) unsigned NOT NULL auto_increment,
  confcat_name varchar(25) NOT NULL default '',
  confcat_order smallint(5) unsigned NOT NULL default '0',
  confcat_display smallint(5) NOT NULL default '1',
  PRIMARY KEY  (confcat_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table configoption
#

CREATE TABLE configoption (
  confop_id mediumint(8) unsigned NOT NULL auto_increment,
  confop_name varchar(255) NOT NULL default '',
  confop_value varchar(255) NOT NULL default '',
  conf_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (confop_id),
  KEY conf_id (conf_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table content
#

CREATE TABLE content (
  content_id int(11) unsigned NOT NULL auto_increment,
  content_sid int(11) unsigned NOT NULL default '0',
  content_cid int(11) unsigned NOT NULL default '0',
  content_uid int(11) unsigned NOT NULL default '0',
  content_alias varchar(100) NOT NULL default '',
  content_created int(11) unsigned NOT NULL default '0',
  content_published int(11) unsigned NOT NULL default '0',
  content_updated int(11) unsigned NOT NULL default '0',
  content_expired int(11) unsigned NOT NULL default '0',
  content_title varchar(100) NOT NULL default '',
  content_subtitle varchar(100) NOT NULL default '',
  content_intro text NOT NULL,
  content_body mediumtext NOT NULL,
  content_images text NOT NULL,
  content_summary text NOT NULL,
  content_counter int(11) NOT NULL default '0',
  content_type varchar(10) NOT NULL default 'static',
  content_hits int(11) NOT NULL default '0',
  content_version decimal(3,2) NOT NULL default '0.00',
  content_approved smallint(1) NOT NULL default '0',
  content_meta text NOT NULL,
  content_keywords text NOT NULL,
  content_weight tinyint(5) NOT NULL default '0',
  content_display tinyint(1) NOT NULL default '1',
  content_spotlight tinyint(1) NOT NULL default '0',
  content_spotlightmain tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (content_id),
  KEY idx_sid (content_sid),
  KEY idx_cid (content_cid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table errors
#

CREATE TABLE errors (
  errors_id mediumint(8) NOT NULL auto_increment,
  errors_title varchar(60) NOT NULL,
  errors_description text NOT NULL,
  errors_no mediumint(8) NOT NULL,
  errors_date int(11) NOT NULL,
  errors_ip varchar(20) NOT NULL default '',
  errors_report text NOT NULL,
  errors_hash varchar(40) NOT NULL,
  UNIQUE KEY errors_id (errors_id),
  KEY errors_no (errors_date,errors_ip)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table events
#

CREATE TABLE events (
  event_id int(11) NOT NULL auto_increment,
  event_type int(1) NOT NULL,
  event_uid int(11) NOT NULL,
  event_condition int(11) NOT NULL,
  event_code text NOT NULL,
  event_comment text NOT NULL,
  PRIMARY KEY  (event_id),
  KEY type (event_type,event_uid,event_condition)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table group_permission
#

CREATE TABLE group_permission (
  gperm_id int(10) unsigned NOT NULL auto_increment,
  gperm_groupid smallint(5) unsigned NOT NULL default '0',
  gperm_itemid mediumint(8) unsigned NOT NULL default '0',
  gperm_modid mediumint(5) unsigned NOT NULL default '0',
  gperm_name varchar(50) NOT NULL default '',
  PRIMARY KEY  (gperm_id),
  KEY groupid (gperm_groupid),
  KEY itemid (gperm_itemid),
  KEY gperm_modid (gperm_modid,gperm_name(10))
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table groups
#

CREATE TABLE groups (
  groupid smallint(5) unsigned NOT NULL auto_increment,
  name varchar(50) NOT NULL default '',
  description text NOT NULL,
  group_type varchar(10) NOT NULL default '',
  PRIMARY KEY  (groupid),
  KEY group_type (group_type)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table groups_users_link
#

CREATE TABLE groups_users_link (
  linkid mediumint(8) unsigned NOT NULL auto_increment,
  groupid smallint(5) unsigned NOT NULL default '0',
  uid mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (linkid),
  KEY groupid_uid (groupid,uid)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table imgset
#

CREATE TABLE imgset (
  imgset_id smallint(5) unsigned NOT NULL auto_increment,
  imgset_name varchar(50) NOT NULL default '',
  imgset_refid mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (imgset_id),
  KEY imgset_refid (imgset_refid)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table imgset_tplset_link
#

CREATE TABLE imgset_tplset_link (
  imgset_id smallint(5) unsigned NOT NULL default '0',
  tplset_name varchar(50) NOT NULL default '',
  KEY tplset_name (tplset_name(10))
) ENGINE=MyISAM ;

# --------------------------------------------------------

#
# Table structure for table imgsetimg
#

CREATE TABLE imgsetimg (
  imgsetimg_id mediumint(8) unsigned NOT NULL auto_increment,
  imgsetimg_file varchar(50) NOT NULL default '',
  imgsetimg_body blob NOT NULL,
  imgsetimg_imgset smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (imgsetimg_id),
  KEY imgsetimg_imgset (imgsetimg_imgset)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table language_base
#

CREATE TABLE language_base (
  lang_id int(8) unsigned NOT NULL auto_increment,
  weight int(4) NOT NULL default '1',
  lang_name varchar(255) NOT NULL default '',
  lang_desc varchar(255) NOT NULL default '',
  lang_code varchar(255) NOT NULL default '',
  lang_charset varchar(255) NOT NULL default '',
  lang_image varchar(255) NOT NULL default '',
  PRIMARY KEY  (lang_id),
  KEY lang_name (lang_name)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table language_ext
#

CREATE TABLE language_ext (
  lang_id int(8) unsigned NOT NULL auto_increment,
  weight int(4) NOT NULL default '1',
  lang_name varchar(255) NOT NULL default '',
  lang_desc varchar(255) NOT NULL default '',
  lang_code varchar(255) NOT NULL default '',
  lang_charset varchar(255) NOT NULL default '',
  lang_image varchar(255) NOT NULL default '',
  lang_base varchar(255) NOT NULL default '',
  PRIMARY KEY  (lang_id),
  KEY lang_name (lang_name),
  KEY lang_base (lang_base)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table languages
#

CREATE TABLE languages (
  id int(11) NOT NULL auto_increment,
  sname char(2) NOT NULL,
  fname varchar(20) NOT NULL,
  flag text NOT NULL,
  enabled int(1) NOT NULL,
  visible int(1) NOT NULL,
  path text NOT NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY sname (sname),
  UNIQUE KEY fname (fname),
  KEY enabled (enabled),
  KEY visible (visible)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table media
#

CREATE TABLE media (
  media_id mediumint(8) unsigned NOT NULL auto_increment,
  media_name varchar(30) NOT NULL default '',
  media_nicename varchar(255) NOT NULL default '',
  media_ext varchar(4) NOT NULL,
  media_caption text NOT NULL,
  media_mimetype varchar(30) NOT NULL default '',
  media_created int(10) unsigned NOT NULL default '0',
  media_display tinyint(1) unsigned NOT NULL default '0',
  media_weight smallint(5) unsigned NOT NULL default '0',
  media_cid smallint(5) unsigned NOT NULL default '0',
  media_dirname varchar(255) NOT NULL default 'uploads',
  media_uid tinyint(4) NOT NULL default '0',
  media_filesize mediumint(8) NOT NULL,
  PRIMARY KEY  (media_id),
  KEY media_cid (media_cid),
  KEY media_display (media_display)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table menus
#

CREATE TABLE mediacategory (
  media_cid smallint(5) unsigned NOT NULL auto_increment,
  media_ctitle varchar(100) NOT NULL default '',
  media_cmaxsize int(8) unsigned NOT NULL default '0',
  media_cmaxwidth smallint(3) unsigned NOT NULL default '0',
  media_cmaxheight smallint(3) unsigned NOT NULL default '0',
  media_cdisplay tinyint(1) unsigned NOT NULL default '0',
  media_cweight smallint(3) unsigned NOT NULL default '0',
  media_ctype char(1) NOT NULL default '',
  media_cdirname varchar(255) NOT NULL default 'uploads',
  media_cdescription text NOT NULL,
  media_cupdated int(11) NOT NULL,
  media_cprefix varchar(10) NOT NULL,
  PRIMARY KEY  (media_cid),
  KEY media_cdisplay (media_cdisplay)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table menus
#

CREATE TABLE menus (
  menu_id int(11) NOT NULL auto_increment,
  menu_pid int(11) default '0',
  menu_type varchar(25) default NULL,
  menu_title varchar(100) default NULL,
  menu_link varchar(255) default NULL,
  menu_image varchar(255) default NULL,
  menu_weight int(3) default '0',
  menu_mid int(3) default '0',
  menu_name varchar(35) NOT NULL,
  menu_sectionid int(3) NOT NULL default '0',
  menu_display tinyint(1) default '0',
  menu_target varchar(20) default NULL,
  menu_class varchar(15) NOT NULL,
  PRIMARY KEY  (menu_id),
  KEY menu_type (menu_type)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table mimetypes
#

CREATE TABLE `mimetypes` (
  mime_id int(11) NOT NULL auto_increment,
  mime_ext varchar(60) NOT NULL default '',
  mime_types text NOT NULL,
  mime_name varchar(255) NOT NULL default '',
  mime_images varchar(160) NOT NULL,
  mime_safe tinyint(1) NOT NULL default '1',
  mime_category smallint(8) NOT NULL,
  mime_display tinyint(1) NOT NULL default '1',
  KEY mime_id (mime_id)
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table newblocks
#

CREATE TABLE newblocks (
  bid mediumint(8) unsigned NOT NULL auto_increment,
  mid smallint(5) unsigned NOT NULL default '0',
  func_num tinyint(3) unsigned NOT NULL default '0',
  options varchar(255) NOT NULL default '',
  name varchar(150) NOT NULL default '',
  title varchar(255) NOT NULL default '',
  content text NOT NULL,
  side tinyint(1) unsigned NOT NULL default '0',
  weight smallint(5) unsigned NOT NULL default '0',
  block_type char(1) NOT NULL default '',
  c_type char(1) NOT NULL default '',
  isactive tinyint(1) unsigned NOT NULL default '0',
  dirname varchar(50) NOT NULL default '',
  func_file varchar(50) NOT NULL default '',
  show_func varchar(50) NOT NULL default '',
  edit_func varchar(50) NOT NULL default '',
  template varchar(50) NOT NULL default '',
  bcachetime int(10) unsigned NOT NULL default '0',
  last_modified int(10) unsigned NOT NULL default '0',
  description text NOT NULL,
  liveupdate int(1) NOT NULL default '0',
  PRIMARY KEY  (bid,mid,title,weight,side)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table online
#

CREATE TABLE online (
  online_sessionid varchar(60) NOT NULL,
  online_uid mediumint(8) unsigned NOT NULL default '0',
  online_uname varchar(25) NOT NULL default '',
  online_updated int(10) unsigned NOT NULL default '0',
  online_addon smallint(5) unsigned NOT NULL default '0',
  online_component varchar(25) NOT NULL,
  online_ip varchar(15) NOT NULL default '',
  online_hidden tinyint(1) NOT NULL default '0',
  KEY online_addon (online_addon)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table profile
#

CREATE TABLE profile (
  profile_id smallint(5) unsigned NOT NULL auto_increment,
  profile_modid smallint(5) unsigned NOT NULL default '0',
  profile_catid smallint(5) unsigned NOT NULL default '0',
  profile_sectid smallint(5) unsigned NOT NULL default '1',
  profile_name varchar(25) NOT NULL default '',
  profile_title varchar(30) NOT NULL default '',
  profile_value text NOT NULL,
  profile_desc text NOT NULL,
  profile_formtype varchar(15) NOT NULL default '',
  profile_valuetype varchar(10) NOT NULL default '',
  profile_order smallint(5) unsigned NOT NULL default '0',
  profile_required tinyint(2) NOT NULL default '0',
  profile_display tinyint(2) NOT NULL default '1',
  PRIMARY KEY  (profile_id),
  KEY profile_mod_cat_id (profile_modid,profile_catid),
  KEY name (profile_catid)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table profilecategory
#

CREATE TABLE profilecategory (
  profilecat_id smallint(5) unsigned NOT NULL auto_increment,
  profilecat_name varchar(25) NOT NULL default '',
  profilecat_order smallint(5) unsigned NOT NULL default '0',
  profilecat_desc text NOT NULL,
  profilecat_display smallint(2) NOT NULL default '1',
  PRIMARY KEY  (profilecat_id)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table profileoption
#

CREATE TABLE profileoption (
  profileop_id mediumint(8) unsigned NOT NULL auto_increment,
  profileop_name varchar(255) NOT NULL default '',
  profileop_value varchar(255) NOT NULL default '',
  profile_id smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (profileop_id),
  KEY profile_id (profile_id)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table ranks
#

CREATE TABLE ranks (
  rank_id smallint(5) unsigned NOT NULL auto_increment,
  rank_title varchar(50) NOT NULL default '',
  rank_min mediumint(8) unsigned NOT NULL default '0',
  rank_max mediumint(8) unsigned NOT NULL default '0',
  rank_special tinyint(1) unsigned NOT NULL default '0',
  rank_image varchar(255) default NULL,
  PRIMARY KEY  (rank_id),
  KEY rankspecial (rank_special)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table rss
#

CREATE TABLE rss (
  rss_id smallint(3) unsigned NOT NULL auto_increment,
  rss_name varchar(255) NOT NULL default '',
  rss_url varchar(255) NOT NULL default '',
  rss_rssurl varchar(255) NOT NULL default '',
  rss_encoding varchar(15) NOT NULL default '',
  rss_cachetime mediumint(8) unsigned NOT NULL default '3600',
  rss_asblock tinyint(1) unsigned NOT NULL default '0',
  rss_display tinyint(1) unsigned NOT NULL default '0',
  rss_weight smallint(3) unsigned NOT NULL default '0',
  rss_mainfull tinyint(1) unsigned NOT NULL default '1',
  rss_mainimg tinyint(1) unsigned NOT NULL default '1',
  rss_mainmax tinyint(2) unsigned NOT NULL default '10',
  rss_blockimg tinyint(1) unsigned NOT NULL default '0',
  rss_blockmax tinyint(2) unsigned NOT NULL default '10',
  rss_xml text NOT NULL,
  rss_updated int(10) NOT NULL default '0',
  PRIMARY KEY  (rss_id)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table section
#

CREATE TABLE section (
  section_id mediumint(8) NOT NULL auto_increment,
  section_title varchar(150) NOT NULL,
  section_image varchar(150) NOT NULL,
  section_weight tinyint(1) NOT NULL default '0',
  section_display enum('0','1') NOT NULL default '1',
  section_published int(11) NOT NULL,
  section_imageside varchar(4) NOT NULL default 'left',
  section_description text NOT NULL,
  section_type varchar(10) NOT NULL,
  section_is tinyint(1) NOT NULL default '1',
  UNIQUE KEY section_id (section_id),
  KEY section_title (section_display,section_published,section_weight)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table security
#
CREATE TABLE security (
  security_id mediumint(8) NOT NULL,
  security_title varchar(60) NOT NULL,
  security_login varchar(10) NOT NULL,
  security_password varchar(10) NOT NULL,
  security_sessionid varchar(32) NOT NULL,
  security_ip varchar(10) NOT NULL,
  security_date int(11) NOT NULL,
  security_user_agent varchar(255) NOT NULL,
  security_remote_addr varchar(20) NOT NULL,
  security_http_referer varchar(255) NOT NULL,
  security_request_uri varchar(255) NOT NULL,
  PRIMARY KEY  (security_id),
  KEY security_title (security_title(3))
) TYPE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table session
#

CREATE TABLE session (
  sess_id varchar(32) NOT NULL default '',
  sess_updated int(10) unsigned NOT NULL default '0',
  sess_ip varchar(15) NOT NULL default '',
  sess_data text NOT NULL,
  PRIMARY KEY  (sess_id),
  KEY updated (sess_updated)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table smiles
#

CREATE TABLE smiles (
  id smallint(5) unsigned NOT NULL auto_increment,
  code varchar(50) NOT NULL default '',
  smile_url varchar(100) NOT NULL default '',
  emotion varchar(75) NOT NULL default '',
  display tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table stats
#

CREATE TABLE streaming (
  streaming_id mediumint(9) NOT NULL,
  streaming_title varchar(150) NOT NULL,
  streaming_file varchar(255) NOT NULL,
  streaming_uid mediumint(8) NOT NULL,
  streaming_description text NOT NULL,
  streaming_image varchar(255) NOT NULL,
  streaming_weight smallint(5) NOT NULL,
  streaming_display tinyint(1) NOT NULL,
  streaming_published tinyint(1) NOT NULL,
  streaming_mimetype varchar(100) NOT NULL,
  streaming_alias varchar(60) NOT NULL,
  PRIMARY KEY  (streaming_id),
  KEY streaming_title (streaming_title),
  KEY streaming_uid (streaming_uid),
  KEY streaming_display (streaming_display),
  KEY streaming_published (streaming_published)
) TYPE=MyISAM;


# --------------------------------------------------------

#
# Table structure for table stats
#

CREATE TABLE stats (
  stat_id mediumint(8) unsigned NOT NULL auto_increment,
  stat_date int(11) unsigned NOT NULL default '1',
  stat_user_agent varchar(255) NOT NULL,
  stat_remote_addr varchar(20) NOT NULL,
  stat_http_referer varchar(255) NOT NULL,
  stat_request_uri varchar(255) NOT NULL,
  stat_request_addon varchar(255) NOT NULL,
  stat_unique tinyint(1) NOT NULL,
  PRIMARY KEY  (stat_id)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table tplfile
#

CREATE TABLE tplfile (
  tpl_id mediumint(7) unsigned NOT NULL auto_increment,
  tpl_refid smallint(5) unsigned NOT NULL default '0',
  tpl_addon varchar(25) NOT NULL default '',
  tpl_tplset varchar(50) NOT NULL default '',
  tpl_file varchar(50) NOT NULL default '',
  tpl_desc varchar(255) NOT NULL default '',
  tpl_lastmodified int(10) unsigned NOT NULL default '0',
  tpl_lastimported int(10) unsigned NOT NULL default '0',
  tpl_type varchar(20) NOT NULL default '',
  PRIMARY KEY  (tpl_id),
  KEY tpl_refid (tpl_refid,tpl_type),
  KEY tpl_tplset (tpl_tplset,tpl_file(10))
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table tplset
#

CREATE TABLE tplset (
  tplset_id int(7) unsigned NOT NULL auto_increment,
  tplset_name varchar(50) NOT NULL default '',
  tplset_desc varchar(255) NOT NULL default '',
  tplset_credits text NOT NULL,
  tplset_created int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (tplset_id)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table tplsource
#

CREATE TABLE tplsource (
  tpl_id mediumint(7) unsigned NOT NULL default '0',
  tpl_source mediumtext NOT NULL,
  KEY tpl_id (tpl_id)
) ENGINE=MyISAM;

# --------------------------------------------------------

#
# Table structure for table trash
#

CREATE TABLE trash (
  trash_id int(11) unsigned NOT NULL auto_increment,
  trash_sid int(11) unsigned NOT NULL default '0',
  trash_cid int(11) unsigned NOT NULL default '0',
  trash_uid int(11) unsigned NOT NULL default '0',
  trash_alias varchar(100) NOT NULL default '',
  trash_created int(11) unsigned NOT NULL default '0',
  trash_published int(11) unsigned NOT NULL default '0',
  trash_updated int(11) unsigned NOT NULL default '0',
  trash_expired int(11) unsigned NOT NULL default '0',
  trash_title varchar(100) NOT NULL default '',
  trash_subtitle varchar(100) NOT NULL default '',
  trash_intro text NOT NULL,
  trash_body mediumtext NOT NULL,
  trash_images text NOT NULL,
  trash_summary text NOT NULL,
  trash_counter int(11) NOT NULL default '0',
  trash_type varchar(10) NOT NULL default 'static',
  trash_hits int(11) NOT NULL default '0',
  trash_version decimal(3,2) NOT NULL default '0.00',
  trash_approved int(1) NOT NULL default '0',
  trash_meta text NOT NULL,
  trash_keywords text NOT NULL,
  trash_weight tinyint(5) NOT NULL default '0',
  trash_display tinyint(1) NOT NULL default '1',
  trash_spotlight tinyint(1) NOT NULL default '0',
  trash_spotlightmain tinyint(1) NOT NULL default '0',
  trash_date int(11) unsigned NOT NULL default '0',
  trash_userid int(11) unsigned NOT NULL default '0',
  trash_mid mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (trash_id),
  KEY idx_sid (trash_sid),
  KEY idx_cid (trash_cid)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table userprofile
#

CREATE TABLE userprofile (
  userprofile_id mediumint(8) unsigned NOT NULL auto_increment,
  userprofile_uid smallint(8) NOT NULL,
  userprofile_cid smallint(5) NOT NULL,
  userprofile_value text NOT NULL,
  userprofile_pid smallint(8) NOT NULL,
  userprofile_name varchar(60) NOT NULL,
  userprofile_weight mediumint(8) NOT NULL,
  PRIMARY KEY  (userprofile_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table users
#

CREATE TABLE users (
  uid mediumint(8) unsigned NOT NULL auto_increment,
  login varchar(25) NOT NULL default '',
  name varchar(60) NOT NULL default '',
  uname varchar(25) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  url varchar(100) NOT NULL default '',
  user_avatar varchar(30) NOT NULL,
  user_regdate int(10) unsigned NOT NULL default '0',
  user_from varchar(100) NOT NULL default '',
  user_sig tinytext NOT NULL,
  user_viewemail tinyint(1) unsigned NOT NULL default '0',
  actkey varchar(8) NOT NULL default '',
  pass varchar(32) NOT NULL default '',
  posts mediumint(8) unsigned NOT NULL default '0',
  attachsig tinyint(1) unsigned NOT NULL default '0',
  rank smallint(5) unsigned NOT NULL default '0',
  level tinyint(3) unsigned NOT NULL default '1',
  theme varchar(100) NOT NULL default '',
  timezone_offset float(3,1) NOT NULL default '0.0',
  last_login int(10) unsigned NOT NULL default '0',
  umode varchar(10) NOT NULL default '',
  uorder tinyint(1) unsigned NOT NULL default '0',
  notify_method tinyint(1) NOT NULL default '1',
  notify_mode tinyint(1) NOT NULL default '0',
  user_mailok tinyint(1) unsigned NOT NULL default '1',
  ipaddress varchar(20) NOT NULL default '',
  user_coppa_dob int(11) NOT NULL,
  user_coppa_agree tinyint(1) NOT NULL default '0',
  user_language varchar(100) NOT NULL,
  editor varchar(60) NOT NULL,
  user_usrlevel smallint(2) NOT NULL default '0',
  user_usrmedpref smallint(2) NOT NULL default '0',
  user_cookie varchar(32) NOT NULL,
  user_anon smallint(1) NOT NULL default '0',
  PRIMARY KEY  (uid),
  KEY uname (uname)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table zariliacomments
#
CREATE TABLE zariliacomments (
  com_id mediumint(8) unsigned NOT NULL auto_increment,
  com_pid mediumint(8) unsigned NOT NULL default '0',
  com_rootid mediumint(8) unsigned NOT NULL default '0',
  com_modid smallint(5) unsigned NOT NULL default '0',
  com_itemid mediumint(8) unsigned NOT NULL default '0',
  com_icon varchar(25) NOT NULL default '',
  com_created int(10) unsigned NOT NULL default '0',
  com_modified int(10) unsigned NOT NULL default '0',
  com_uid mediumint(8) unsigned NOT NULL default '0',
  com_ip varchar(15) NOT NULL default '',
  com_title varchar(255) NOT NULL default '',
  com_text text NOT NULL,
  com_sig tinyint(1) unsigned NOT NULL default '0',
  com_status tinyint(1) unsigned NOT NULL default '0',
  com_exparams varchar(255) NOT NULL default '',
  dohtml tinyint(1) unsigned NOT NULL default '0',
  dosmiley tinyint(1) unsigned NOT NULL default '0',
  doxcode tinyint(1) unsigned NOT NULL default '0',
  doimage tinyint(1) unsigned NOT NULL default '0',
  dobr tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (com_id),
  KEY com_pid (com_pid),
  KEY com_itemid (com_itemid),
  KEY com_uid (com_uid),
  KEY com_title (com_title(40))
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# Table structure for table zarilianotifications
#
CREATE TABLE zarilianotifications (
  not_id mediumint(8) unsigned NOT NULL auto_increment,
  not_modid smallint(5) unsigned NOT NULL default '0',
  not_itemid mediumint(8) unsigned NOT NULL default '0',
  not_category varchar(30) NOT NULL default '',
  not_event varchar(30) NOT NULL default '',
  not_uid mediumint(8) unsigned NOT NULL default '0',
  not_mode tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (not_id),
  KEY not_modid (not_modid),
  KEY not_itemid (not_itemid),
  KEY not_class (not_category),
  KEY not_uid (not_uid),
  KEY not_event (not_event)
) ENGINE=MyISAM;
