<?php
/**
 * xos_logos_PageBuilder component class file
 *
 * @copyright	The XOOPS project http://www.xoops.org/
 * @license      http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package      xos_logos
 * @subpackage   xos_logos_PageBuilder
 * @version		$Id$
 * @author       Skalpa Keo <skalpa@xoops.org>
 * @since        2.3.0
 */
/**
 * This file cannot be requested directly
 */
if ( !defined( 'XOOPS_ROOT_PATH' ) )	exit();

include_once XOOPS_ROOT_PATH . '/class/xoopsblock.php';
include_once XOOPS_ROOT_PATH . '/class/template.php';

/**
 * xos_logos_PageBuilder main class
 *
 * @package     xos_logos
 * @subpackage  xos_logos_PageBuilder
 * @author 		Skalpa Keo
 * @since       2.3.0
 */
class xos_logos_PageBuilder {

	var $theme = false;

	var $blocks = array();

	function xoInit( $options = array() ) {
	    $this->retrieveBlocks();
	    if ( $this->theme ) {
			$this->theme->template->assign_by_ref( 'xoBlocks', $this->blocks );
	    }
	    return true;
	}

	/**
	 * Called before a specific zone is rendered
	 */
	function preRender( $zone = '' ) {
	}
	/**
	 * Called after a specific zone is rendered
	 */
	function postRender( $zone = '' ) {
	}

	function retrieveBlocks() {
		global $xoopsUser, $xoopsModule, $xoopsConfig;

		$startMod = ( $xoopsConfig['startpage'] == '--' ) ? 'system' : $xoopsConfig['startpage'];
		if ( @is_object( $xoopsModule ) ) {
			list( $mid, $dirname ) = array( $xoopsModule->getVar('mid'), $xoopsModule->getVar('dirname') );
			/**
			 * Hack by marcan <INBOX>
			 * Adding a condition to decide if we are on the start page
			 * The Query String needs to be empty as well, or else, we are not directly on the home page of the module
			 */
			//$isStart = ( substr( $_SERVER['PHP_SELF'], -9 ) == 'index.php' && $xoopsConfig['startpage'] == $dirname );
			$isStart = ( substr( $_SERVER['PHP_SELF'], -9 ) == 'index.php' && $xoopsConfig['startpage'] == $dirname ) && $_SERVER['QUERY_STRING'] == '';
			/**
			 * Hack by marcan <INBOX>
			 * Adding a condition to decide if we are on the start page
			 * The Query String needs to be empty as well, or else, we are not directly on the home page of the module
			 */
		} else {
			list( $mid, $dirname ) = array( 0, 'system' );
			$isStart = !@empty( $GLOBALS['xoopsOption']['show_cblock'] );
		}

		$groups = @is_object( $xoopsUser ) ? $xoopsUser->getGroups() : array( XOOPS_GROUP_ANONYMOUS );

		$oldzones = array(
        	XOOPS_SIDEBLOCK_LEFT				=> 'canvas_left',
        	XOOPS_SIDEBLOCK_RIGHT				=> 'canvas_right',
        	XOOPS_CENTERBLOCK_LEFT				=> 'page_topleft',
        	XOOPS_CENTERBLOCK_CENTER			=> 'page_topcenter',
        	XOOPS_CENTERBLOCK_RIGHT				=> 'page_topright',
        	XOOPS_CENTERBLOCK_BOTTOMLEFT		=> 'page_bottomleft',
        	XOOPS_CENTERBLOCK_BOTTOM			=> 'page_bottomcenter',
        	XOOPS_CENTERBLOCK_BOTTOMRIGHT		=> 'page_bottomright',
		);
		foreach ( $oldzones as $zone ) {
			$this->blocks[$zone] = array();
		}
		if ( $this->theme ) {
			$template =& $this->theme->template;
			$backup = array( $template->caching, $template->cache_lifetime );
		} else {
			$template =& new XoopsTpl();
		}
		$xoopsblock = new XoopsBlock();
    	$block_arr = array();
	    $block_arr = $xoopsblock->getAllByGroupModule( $groups, $mid, $isStart, XOOPS_BLOCK_VISIBLE);
	    foreach ( $block_arr as $block ) {
	    	$side = $oldzones[ $block->getVar('side') ];
	    	if ( $var = $this->buildBlock( $block, $template ) ) {
	    		$this->blocks[$side][] = $var;
	    	}
	    }
		if ( $this->theme ) {
			list( $template->caching, $template->cache_lifetime ) = $backup;
		}
	}

	function buildBlock( $xobject, &$template ) {
		// The lame type workaround will change
		$block = array(
			'module'	=> $xobject->getVar( 'dirname' ),
			'title'		=> $xobject->getVar( 'title' ),
			//'name'		=> strtolower( preg_replace( '/[^0-9a-zA-Z_]/', '', str_replace( ' ', '_', $xobject->getVar( 'name' ) ) ) ),
			'weight'	=> $xobject->getVar( 'weight' ),
			'lastmod'	=> $xobject->getVar( 'last_modified' ),
		);

		//global $xoopsLogger;

		$xoopsLogger =& XoopsLogger::instance();

		$bcachetime = intval( $xobject->getVar('bcachetime') );
		//$template =& new XoopsTpl();
        if (empty($bcachetime)) {
            $template->caching = 0;
        } else {
            $template->caching = 2;
            $template->cache_lifetime = $bcachetime;
        }
		$tplName = ( $tplName = $xobject->getVar('template') ) ? "db:$tplName" : "db:system_block_dummy.html";
		$cacheid = 'blk_' . $xobject->getVar('bid');

        if ( !$bcachetime || !$template->is_cached( $tplName, $cacheid ) ) {
            $xoopsLogger->addBlock( $xobject->getVar('name') );
            if ( ! ( $bresult = $xobject->buildBlock() ) ) {
                return false;
            }
			$template->assign( 'block', $bresult );
            $block['content'] = $template->fetch( $tplName, $cacheid );
        } else {
            $xoopsLogger->addBlock( $xobject->getVar('name'), true, $bcachetime );
            $block['content'] = $template->fetch( $tplName, $cacheid );
        }
        return $block;
	}


}