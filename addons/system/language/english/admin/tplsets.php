<?php
// $Id: tplsets.php,v 1.1 2007/03/16 02:38:03 catzwolf Exp $
//%%%%%% Template Manager %%%%%
define('_MA_AD_TPLSET_ID','#');
define('_MA_AD_TPLSET_NAME','Template Name');
define('_MA_AD_TPLSET_CREATED','Creation Date');
define('_MA_AD_TPLSET_INSTALLED','Template Installed');
define('_MA_AD_TPLSET_TEMPLATES','Addons');
define('_MA_AD_TPLSET_COUNT','Count');
define('_MA_AD_TPLSET_DEFAULT','Default');
define('_MA_AD_TPLSET_ACTION','Action');

define('_MA_AD_EMENU_CREATE','Create Template');


define('_MD_INSTALL','Install');
define('_MD_EDITTEMPLATE','Edit template file');
define('_MD_FILENAME','File name');
define('_MD_FILEDESC','Description');
define('_MD_LASTMOD','Last modified');
define('_MD_FILEMOD','Last modified (file)');
define('_MD_FILECSS','CSS');
define('_MD_FILEHTML','HTML');
define('_MD_AM_BTOTADMIN', 'Back to template set manager');
define('_MD_RUSUREDELTH', 'Are you sure that you want to delete this template set and all its template data?');
define('_MD_RUSUREDELTPL', 'Are you sure that you want to delete this template data?');
define('_MD_PLZINSTALL', 'Press the button below to start installation');
define('_MD_PLZGENERATE', 'Press the button below to generate file(s)');
define('_MD_CLONETHEME','Clone a template set');
define('_MD_THEMENAME','Base template set');
define('_MD_NEWNAME','Enter new template set name');
define('_MD_IMPORT','Import');
define('_MD_RUSUREIMPT', 'Importing template data from the templates directory will overwrite your changes in database.<br />Click "Import" to proceed.');
define('_MD_THMSETNAME','Name');
define('_MD_CREATED','Created');
define('_MD_SKIN','Skin');
define('_MD_TEMPLATES','Templates');
define('_MD_EDITSKIN','Edit skin');
define('_MD_NOFILE','No File');
define('_MD_VIEW','View');
define('_MD_COPYDEFAULT','Copy default file');
define('_MD_DLDEFAULT','Download default file');
define('_MD_VIEWDEFAULT','View default template');
define('_MD_DOWNLOAD','Download');
define('_MD_UPLOAD','Upload');
define('_MD_GENERATE','Generate');
define('_MD_CHOOSEFILE', 'Choose file to upload');
define('_MD_UPWILLREPLACE', 'Uploading this file will overwrite the data in database!');
define('_MD_UPLOADTAR', 'Upload a template set');
define('_MD_CHOOSETAR', 'Choose a template set package to upload');
define('_MD_ONLYTAR', 'Must be a tar.gz/.tar file with a valid ZARILIA template set structure');
define('_MD_NTHEMENAME', 'New template set name');
define('_MD_ENTERTH', 'Enter a template set name for this package. Leave it blank for automatic detection.');
define('_MD_TITLE','Title');
define('_MD_CONTENT','Content');
define('_MD_DEFAULTTHEME','Your site uses this template set as default');
define('_MD_AM_ERRTHEME', 'The following template sets have no valid skin files data. Press delete to remove data related to the template set.');
define('_MD_SKINIMGS','Skin image files');
define('_MD_EDITSKINIMG','Edit skin image files');
define('_MD_IMGFILE','File name');
define('_MD_IMGNEWFILE','Upload new file');
define('_MD_IMGDELETE','Delete');
define('_MD_ADDSKINIMG','Add skin image file');
define('_MD_BLOCKHTML', 'Block HTML');
define('_MD_IMAGES', 'Images');
define('_MD_NOZLIB', 'Zlib support must be enabled on your server');
define('_MD_LASTIMP', 'Last Imported');
define('_MD_FILENEWER', 'A newer file that has not been imported yet exists under the <b>templates</b> directory.');
define('_MD_FILEIMPORT', 'An older file that has not been imported yet exists under the <b>templates</b> directory.');
define('_MD_FILEGENER', 'Template file does not eixst. It can be generated (copied from the <b>default</b> template), uploaded, or imported from the <b>templates</b> directory.');

define( '_MA_INFO_NAME', 'Templates Administration' );
define( '_MA_INFO_DESCRIPTION', '' );
define( '_MA_INFO_AUTHOR', 'Zarilia Project' );
define( '_MA_INFO_LICENSE', 'GPL see LICENSE' );
define( '_MA_INFO_IMAGE', 'age_admin.png' );

define( '_MA_INFO_LEAD', 'John Neill, Raimondas Rimkevicius' );
define( '_MA_INFO_CONTRIBUTORS', '' );
define( '_MA_INFO_CREDITS', '' );
define( '_MA_INFO_WEBSITE_URL', 'http://zarilia.com' );
define( '_MA_INFO_WEBSITE_NAME', 'Zarilia Project' );
define( '_MA_INFO_EMAIL', 'webmaster@zarilia.com' );
define( '_MA_INFO_VERSION', '1.3' );
define( '_MA_INFO_STATUS', 'Alpha' );
define( '_MA_INFO_RELEASEDATE', 'Not Yet' );
define( '_MA_INFO_DISCLAIMER', 'This is a alpha product and not to be used on a production website' );

define( '_MA_INFO_DEMO_SITE_URL', 'http://zarilia.com' );
define( '_MA_INFO_DEMO_SITE_NAME', 'Zarilia Demo' );
define( '_MA_INFO_SUPPORT_SITE_URL', 'http://zarilia.com' );
define( '_MA_INFO_SUPPORT_SITE_NAME', 'Zarilia Support Site' );
define( '_MA_INFO_SUBMIT_BUG_URL', 'http://sourceforge.net/tracker/?group_id=140225&atid=745776' );
define( '_MA_INFO_SUBMIT_BUG_NAME', 'Zarilia Bug Tracker' );
define( '_MA_INFO_SUBMIT_FEATURE_URL', 'http://sourceforge.net/tracker/?group_id=140225&atid=745779' );
define( '_MA_INFO_SUBMIT_FEATURE_NAME', 'Zarilia Feature Tracker' );
define( '_MA_INFO_PATH', 'index.php?fct=tplsets' );
define( '_MA_INFO_OFFICIAL', 1 );
define( '_MA_INFO_SYSTEM', 1 );
define( '_MA_INFO_HASADMIN', 1 );
?>