<?php
// $Id: media.php,v 1.1 2007/03/16 02:38:02 catzwolf Exp $
// %%%%%% Media Manager %%%%%
/*Media Category*/
define( '_MA_AD_MEDIA_CID', '#' );
define( '_MA_AD_MEDIA_CTITLE', 'Title' );
define( '_MA_AD_MEDIA_CWEIGHT', 'Weight' );
define( '_MA_AD_MEDIA_CDISPLAY', 'Display' );
define( '_MA_AD_MEDIA_CCOUNT', 'Media' );

define( '_MA_AD_MEDIA_NOATTACHMENTS', 'Currently No Media uploaded to display' );


define( '_MA_AD_MEDIA_CREATE', 'Create Media Category' );
define( '_MA_AD_MEDIA_MODIFY', 'Modify %s' );

define( '_MD_AM_LIST_MEDIA', 'List Media' );
define( '_MD_AM_ADD_MEDIA', 'Upload Media' );

define( '_MA_AD_MEDIA_ENAME', 'Media Name:' );
define( '_MA_AD_MEDIA_ENICENAME', 'Media Nice Name:' );
define( '_MA_AD_MEDIA_ECATEGORY', 'Select Media Category:' );
define( '_MA_AD_MEDIA_EEXT', 'Media Extension:' );
define( '_MA_AD_MEDIA_EMIMETYPE', 'Media Mimetype:' );
define( '_MA_AD_MEDIA_EFILESIZE', 'Media Filesize:' );
define( '_MA_AD_MEDIA_ECAPTION', 'Media Caption:' );
define( '_MA_AD_MEDIA_ECREATED', 'Media Created:' );
define( '_MA_AD_MEDIA_ECREATED_DSC', '' );
define( '_MA_AD_MEDIA_EWEIGHT', 'Media Weight:' );
define( '_MA_AD_MEDIA_EWEIGHT_DSC', '' );
define( '_MA_AD_MEDIA_EDISPLAY', 'Media Activate:' );
define( '_MA_AD_MEDIA_EDISPLAY_DSC', '' );
define( '_MA_AD_MEDIA_EDIM', 'Media Dimensions:' );
define( '_MA_AD_MEDIA_EREALNAME', 'Media Display:' );
/**
 */
define( '_MA_AD_MEDIA_ECATTITLE', 'Category Name:' );
define( '_MA_AD_MEDIA_ECDIRNAME', 'Media Path:' );
define( '_MA_AD_MEDIA_ECDESCRIPTION', '' );
define( '_MA_AD_MEDIA_ECATRGRP', 'Select groups for media manager use:' );
define( '_MA_AD_MEDIA_ECATWGRP', 'Select groups allowed to upload images:' );
define( '_MA_AD_MEDIA_ECMAXSIZE', 'Max size allowed (bytes):' );
define( '_MA_AD_MEDIA_ECMAXWIDTH', 'Max size allowed (bytes):' );
define( '_MA_AD_MEDIA_ECMAXHEIGHT', 'Max height allowed (pixels):' );
define( '_MA_AD_MEDIA_ECWEIGHT', 'Category Order:' );
define( '_MA_AD_MEDIA_ECDISPLAY', 'Activate Category?' );

/*uploader*/
define( '_MA_AD_MEDIA_NOCATEGORIES', 'ERROR: No categories created. Please create one and try again.' );
define( '_MA_AD_MEDIA_SINGLEFILE', 'Single File' );
define( '_MA_AD_MEDIA_MULTIFILE', 'Multi Files' );
define( '_MA_AD_MEDIA_ADDFILE', 'Add New Media File' );

define( '_MD_AM_SHOW_MIMETYPE', 'Search Mimetypes' );
define( '_MD_AM_SHOW_EXT', 'Search Extensions' );
define( '_MD_AM_DISPLAY_MEDIACAT', 'Show Mimetypes' );
define( '_MD_AM_SEARCH_MEDIA', 'Search Media' );
define( '_MD_AM_SEARCH_TEXT', 'Search Text' );

// define( '_MA_AD_MEDIA_DIM', 'Media Dimensions: (actual)' );
define( '_MA_AD_MEDIA_DIM_NUM', 'w: %u x h: %u' );
define( '_MA_AD_MEDIA_NEWPREFIX', 'Image Prefix:' );
define( '_MA_AD_MEDIA_NAME', 'Media Name:' );
define( '_MA_AD_MEDIA_FILENAME', 'Media Filename:' );
define( '_MA_AD_MEDIA_FILESIZE', 'Media Filesize:' );
define( '_MA_AD_MEDIA_WIDTH', 'Width' );
define( '_MA_AD_MEDIA_HEIGHT', 'height' );
define( '_MA_AD_MEDIA_MIMETYPE', 'Mimetype:' );
define( '_MA_AD_MEDIA_DIM', 'Dimensions:' );
define( '_MA_AD_MEDIA_SIZE', 'Media File Size:' );
define( '_MA_AD_MEDIA_MAXSIZE', 'Max size allowed (bytes):' );
define( '_MA_AD_MEDIA_MAXWIDTH', 'Max width allowed (pixels):' );
define( '_MA_AD_MEDIA_MAXHEIGHT', 'Max height allowed (pixels):' );
define( '_MA_AD_MEDIA_CAT', 'Media Category:' );
define( '_MA_AD_MEDIA_FILE', 'Upload Media File:' );
define( '_MA_AD_MEDIA_WEIGHT', 'Media Order:' );
define( '_MA_AD_MEDIA_DISPLAY', 'Activate Media?' );
define( '_MA_AD_MEDIA_MIME', 'MIME type:' );

define( '_MA_AD_MEDIA_RESIZE', 'Resize Selected/uploaded Image?' );
define( '_MA_AD_MEDIA_RESIZE_DSC', 'Select this option to resize the selected image or the image you are uploading from your hard drive:' );
define( '_MA_AD_MEDIA_DELETE', 'Delete Orginal Image after resize?' );
define( '_MA_AD_MEDIA_DELETE_DSC', 'Use this option to delete the orginal image after a resize has been performed on the selected/uploaded media:' );
define( '_MA_AD_MEDIA_NEWWIDTH', 'New Image Width:' );
define( '_MA_AD_MEDIA_NEWWIDTH_DSC', 'If resize selected, the image height will be changed to this new value:' );
define( '_MA_AD_MEDIA_NEWHEIGHT', 'New Image Height:' );
define( '_MA_AD_MEDIA_NEWHEIGHT_DSC', 'If resize selected, the image width will be changed to this new value:' );
define( '_MA_AD_MEDIA_NEWQUALITY', 'Image Quality (1 - 100):' );
define( '_MA_AD_MEDIA_NEWQUALITY_DSC', 'If resize selected, the image quality will be changed to this new value: Value form 1 (worst) to 100 (best).' );
define( '_MA_AD_MEDIA_KEEPASPECT', 'Keep Image Aspect?' );
define( '_MA_AD_MEDIA_KEEPASPECT_DSC', 'If resize selected, the new image width will stay in relation to the original image height and width:' );
define( '_MA_AD_MEDIA_RESIZEOPTIONS', 'Media Resizing Options' );
define( '_MA_AD_MEDIA_MAINCATLISTING', 'Category Listing:' );
define( '_MA_AD_MEDIA_SELECT', 'Select media to upload' );
define( '_MA_AD_MEDIA_NAVAILTOUPLOAD', 'Nothing Available To Upload' );
define( '_MA_AD_MEDIA_AUTOWEIGHT', 'Use Auto Weight?' );

define( '_MA_AD_MEDIA_ERRORNOCACHEFOLDER', '<b>ERROR:</b> Please create a \'cache\' folder in the your upload folder!<br /><br />Once you have created this folder and chmodded this to 0777 you will be able to batch upload media.' );
define( '_MA_AD_MEDIA_HOWTOUSE', '<b>How to Use:</b> Upload all the media you wish to batch add to the <b>Upload Path</b> folder and make sure all media media are writeable. chmod: 0777' );
define( '_MA_AD_MEDIA_UPLOADPATH', 'Upload Path' );
define( '_MA_AD_MEDIA_FAILFETCHIMG', 'Failed uploading: <b>%s</b>. Error returned: %s' );
// error defines
define( '_MA_AD_MEDIA_PATHNOTEXIST', 'Media %s does not exist in path %s' );
define( '_MA_AD_MEDIA_FILESIZEEXCEEDS', 'Media size for %s exceeds the %s bytes limit' );
define( '_MA_AD_MEDIA_FILEWIDTHERROR', 'Height or/and width (" . %u . " x " . %u . ") for Media: %s  exceeds the %u x %u limit' );
define( '_MA_AD_MEDIA_UNKNOWNFILETYPE', 'Unknown filetype for Media %s<br />Media has not been copied into the database.' );
define( '_MA_AD_MEDIA_UNKNOWNMIME', 'Unknown mimetype for Media %s<br />Media has not been copied into the database.' );
define( '_MA_AD_MEDIA_ERRORCOPY', 'Error copying %s to path %s.' );
define( '_MA_AD_MEDIA_ERRORPERMISSION', 'Error changing write permission on path %s, please change to 0777.' );
define( '_MA_AD_MEDIA_ERRORDELETE', 'Error deleting Media %s from path %s.' );
define( '_MA_AD_MEDIA_PATHPROBLEM', 'Path %s either does not exist or is not writable!!.' );
define( '_MA_AD_MEDIA_NOFILESSELECTED', 'No Media selected to upload. Please go back and select a file to upload.' );

/*Addons Information*/
define( '_MA_INFO_NAME', 'Media Administration' );
define( '_MA_INFO_DESCRIPTION', '' );
define( '_MA_INFO_AUTHOR', 'Zarilia Project' );
define( '_MA_INFO_LICENSE', 'GPL see LICENSE' );
define( '_MA_INFO_IMAGE', 'media_admin.png' );

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
define( '_MA_INFO_PATH', 'index.php?fct=media' );
define( '_MA_INFO_OFFICIAL', 1 );
define( '_MA_INFO_SYSTEM', 1 );
define( '_MA_INFO_HASADMIN', 1 );

?>