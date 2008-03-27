<?php
/**
 * @version $Id: defines.php,v 1.2 2007/05/09 14:14:28 catzwolf Exp $
 * @copyright 2007
 */
define( "ZAR_SIDEBLOCK_LEFT", 0 );
define( "ZAR_SIDEBLOCK_RIGHT", 1 );
define( "ZAR_SIDEBLOCK_BOTH", 2 );
define( "ZAR_CENTERBLOCK_LEFT", 3 );
define( "ZAR_CENTERBLOCK_RIGHT", 4 );
define( "ZAR_CENTERBLOCK_CENTER", 5 );
define( "ZAR_CENTERBLOCKDOWN_LEFT", 6 );
define( "ZAR_CENTERBLOCKDOWN_RIGHT", 7 );
define( "ZAR_CENTERBLOCKDOWN_CENTER", 8 );
define( "ZAR_BLOCK_INVISIBLE_EDIT", 9 );
define( "ZAR_BLOCK_INVISIBLE", 9 );
define( "ZAR_CENTERBLOCK_ALL", 6 );
define( "ZAR_BLOCK_VISIBLE", 1 );
define( "ZAR_MATCH_START", 0 );
define( "ZAR_MATCH_END", 1 );
define( "ZAR_MATCH_EQUAL", 2 );
define( "ZAR_MATCH_CONTAIN", 3 );
define( "SMARTY_DIR", ZAR_ROOT_PATH . "/class/smarty/" );
define( "ZAR_CACHE_PATH", ZAR_ROOT_PATH . "/data/cache" );
define( "ZAR_CACHE_URL", ZAR_URL . "/data/cache" );
define( "ZAR_UPLOAD_PATH", ZAR_ROOT_PATH . "/data/uploads" );
define( "ZAR_CUPLOAD_PATH", ZAR_ROOT_PATH . "/data/uploads/cache" );
define( "ZAR_THUMBS_PATH", ZAR_ROOT_PATH . "/data/uploads/thumbs" );
define( "ZAR_THUMBS_URL", ZAR_URL . "/data/uploads/thumbs" );
define( "ZAR_COMPILE_PATH", ZAR_ROOT_PATH . "/data/smarty" );

define( "ZAR_UPLOAD_URL", ZAR_URL . "/data/uploads" );
define( "ZAR_IMAGE_URL", ZAR_URL . "/images" );
define( "ZAR_BANNER_URL", ZAR_URL . "/images/banners" );
define( "ZAR_BANNER_PATH", ZAR_ROOT_PATH . "/images/banners" );
define( "ZAR_ADDONS_PATH", ZAR_ROOT_PATH . "/addons" );


define( "ZAR_FRAMEWORK_PATH", ZAR_ROOT_PATH . "/class" );
define( "ZAR_FRAMEWORK_URL", ZAR_URL . "/class" );

define( "ZAR_CONTROLS_URL", ZAR_URL . "/class/controls" );
define( "ZAR_CONTROLS_PATH", ZAR_ROOT_PATH . "/class/controls" );


if ( !defined( 'ZAR_XMLRPC' ) ) {
    define( 'ZAR_DB_CHKREF', 1 );
} else {
    define( 'ZAR_DB_CHKREF', 0 );
}

define( 'ZAR_THEME_URL', ZAR_URL . '/themes' );
define( 'ZAR_THEME_PATH', ZAR_ROOT_PATH . '/themes' );
?>