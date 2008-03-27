<?php
// $Id: template.php,v 1.5 2007/05/05 11:11:26 catzwolf Exp $
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
if ( !defined( 'SMARTY_DIR' ) ) {
    exit();
}
/**
 * Base class: Smarty template engine
 */
require_once SMARTY_DIR . 'Smarty.class.php';

/**
 * Template engine
 *
 * @package kernel
 * @subpackage core
 * @author Kazumi Ono
 * @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
class ZariliaTpl extends Smarty {
    /**
     * Allow update of template files from the themes/ directory?
     * This should be set to false on an active site to increase performance
     */
    var $_canUpdateFromFile = false;
    var $_HeaderTags = array();
    var $_BeforeOutputTags = "";
    var $_title = "";

    var $_metatags = array();
    var $_link = array();
    var $_script = array();
    var $_css = array();
    var $_body = '<body>';
    var $_icon = '';

    /**
     * Constructor
     */
    function ZariliaTpl( $display = true ) {
        global $zariliaConfig;

        $this->Smarty();
        $this->compile_id = null;
        if ( $zariliaConfig['theme_fromfile'] == 1 ) {
            $this->_canUpdateFromFile = true;
            $this->compile_check = true;
        } else {
            $this->_canUpdateFromFile = false;
            $this->compile_check = false;
        }
        $this->left_delimiter = '<{';
        $this->right_delimiter = '}>';
        $this->template_dir = ZAR_THEME_PATH;
        $this->cache_dir = ZAR_CACHE_PATH;
        $this->compile_dir = ZAR_COMPILE_PATH;
        $this->plugins_dir = array( ZAR_ROOT_PATH . '/class/smarty/plugins' );
        $this->default_template_handler_func = 'zarilia_template_create';
        $this->use_sub_dirs = false;
//		var_dump($_SERVER);
		if (isset($_SERVER['REQUEST_URI'])) {
	        if ( eregi( 'index.php', $_SERVER['REQUEST_URI'] ) ) {
		        if ( $_SERVER['QUERY_STRING'] == '' ) {
			        unset( $_SERVER['REQUEST_URI'] );
				}
	        }
		}
        if ( $display ) {
			if (!defined('_LANGCODE')) define('_LANGCODE', 'en');
			if (!defined('_CHARSET')) define('_CHARSET', 'utf-8');
			require_once ZAR_ROOT_PATH .'/include/version.php';
            $this->assign(
                array( 'zarilia_url' => ZAR_URL,
                    'zarilia_rootpath' => ZAR_ROOT_PATH,
                    'zarilia_langcode' => _LANGCODE,
                    'zarilia_charset' => _CHARSET,
                    'zarilia_version' => ZARILIA_VERSION,
                    'zarilia_upload_url' => ZAR_UPLOAD_URL,
                    'zarilia_theme' => $zariliaConfig['theme_set'],
                    'zarilia_image' => ZAR_IMAGE_URL,
                    'zarilia_imageurl' => ZAR_IMAGE_URL,
                    'zarilia_theme_image' => ZAR_THEME_URL . "/" . $zariliaConfig['theme_set'] . "/images",
                    'zarilia_theme_url' => ZAR_THEME_URL . "/" . $zariliaConfig['theme_set'],
                    'zarilia_theme_path' => ZAR_THEME_PATH . "/" . $zariliaConfig['theme_set'],
                    'zarilia_themecss' => ZAR_URL . '/themes/' . $zariliaConfig['theme_set'] . '/css/style.css',
                    'zarilia_requesturi' => isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '',
                    'zarilia_sitename' => $zariliaConfig['sitename'],
                    'zarilia_slogan' => $zariliaConfig['slogan'],
                    'zarilia_footer' => isset($zariliaConfig['footer']) ? $zariliaConfig['footer'] : '',
                    )
                );
        }
    }

    /**
     * Set the directory for templates
     *
     * @param string $dirname Directory path without a trailing slash
     */
    function zarilia_setTemplateDir( $dirname ) {
        $this->template_dir = $dirname;
    }

    /**
     * Get the active template directory
     *
     * @return string
     */
    function zarilia_getTemplateDir() {
        return $this->template_dir;
    }

    /**
     * Set debugging mode
     *
     * @param boolean $flag
     */
    function zarilia_setDebugging( $flag = false ) {
        $this->debugging = is_bool( $flag ) ? $flag : false;
    }

    /**
     * Set caching
     *
     * @param integer $num
     */
    function zarilia_setCaching( $num = 0 ) {
        $this->caching = ( int )$num;
    }

    /**
     * Set cache lifetime
     *
     * @param integer $num Cache lifetime
     */
    function zarilia_setCacheTime( $num = 0 ) {
        $num = ( int )$num;
        if ( $num <= 0 ) {
            $this->caching = 0;
        } else {
            $this->cache_lifetime = $num;
        }
    }

    /**
     * Set directory for compiled template files
     *
     * @param string $dirname Full directory path without a trailing slash
     */
    function zarilia_setCompileDir( $dirname ) {
        $this->compile_dir = $dirname;
    }

    /**
     * Set the directory for cached template files
     *
     * @param string $dirname Full directory path without a trailing slash
     */
    function zarilia_setCacheDir( $dirname ) {
        $this->cache_dir = $dirname;
    }

    /**
     * Render output from template data
     *
     * @param string $data
     * @return string Rendered output
     */
    function zarilia_fetchFromData( &$data ) {
        $dummyfile = ZAR_CACHE_PATH . '/dummy_' . time();
        $fp = fopen( $dummyfile, 'w' );
        fwrite( $fp, $data );
        fclose( $fp );
        $fetched = $this->fetch( 'file:' . $dummyfile );
        unlink( $dummyfile );
        $this->clear_compiled_tpl( 'file:' . $dummyfile );
        return $fetched;
    }

    /**
     */
    function zarilia_canUpdateFromFile() {
        return $this->_canUpdateFromFile;
    }

    /**
     * * Displays template
     */
    function display( $resource_name, $cache_id = null, $compile_id = null, $showheaders = true ) {
        eval( $this->_BeforeOutputTags );
        if ( $showheaders ) {
            global $zariliaConfig;
/*            $zarilia_header = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
            // doc type
            $zarilia_header = "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">\n";
            $zarilia_header .= "<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"{$zariliaConfig['language']}\" lang=\"{$zariliaConfig['language']}\" dir=\"ltr\" >\n";
            $zarilia_header .= "<head>\n";*/
            $zarilia_header = $this->_title;
            // $zarilia_header .= "  <base href=\"" . ZAR_URL . "\" />\n";
            $zarilia_header .= implode("",$this->_metatags);
            $zarilia_header .= implode("",$this->_css);
            $zarilia_header .= implode("",$this->_script);
            $zarilia_header .= $this->_icon;
            foreach( $this->_css as $k => $v ) {
                $zarilia_header .= $v;
            }
            foreach( $this->_script as $k => $v ) {
                $zarilia_header .= $v;
            }
       //     $zarilia_header .= "</head>\n";
//            $zarilia_header .= $this->_body;
            $this->assign( array( 'zarilia_header' => $zarilia_header ) );
        }
        $this->fetch( $resource_name, $cache_id, $compile_id, true );
    }

    function addTitle( $title ) {
        $this->_title = "  <title>$title</title>\n";
    }
    // Deprec function | John
    function headerAdd( $code = "" ) {
        $this->_script[] = $code;
    }

    function addMeta( $name, $content, $httpequiv = false ) {
        if ( $httpequiv ) {
            $this->_metatags[$name] = "  <meta http-equiv=\"$name\" content=\"$content\" />\n";
        } else {
            $this->_metatags[$name] = "  <meta name=\"$name\" content=\"$content\" />\n";
        }
    }

    function addScriptSource( $code = "" ) {
        if ( !empty( $code ) ) {
            $tmp1 = "  <script type=\"text/javascript\">\n";
            $tmp1 .= "  $code\n";
            $tmp1 .= "  </script>\n";
            $this->_script[] = $tmp1;
        }
    }

    function addScript( $code = '', $withtags = true ) {
        if ( !empty( $code ) ) {
            if ( $withtags == true ) {
                $this->_script[] = "  <script type=\"text/javascript\" src=\"" . $code . "\"></script>\n";
            } else {
                //echo $code;
				$this->_script[] = $code; //"  <script type=\"text/javascript\" src=\"" . $script . "\"></script>\n";
            }
        }
    }

    function addCSS( $code, $media = '' ) {
        if ( !empty( $code ) ) {
            $this->_css[] = "  <link rel=\"stylesheet\" href=\"" . $code . "\" type=\"text/css\" ".(($media=='')?'':'media="'.$media.'" ')."/>\n";
        }
    }

    function addIcon( $code ) {
        if ( !empty( $code ) ) {
            $this->_icon = "  <link href=\"" . $code . "\" rel=\"shortcut icon\" type=\"image/x-icon\" />\n";
        }
    }

    function addBody( $id = '', $extra = '' ) {
        $this->_body = '<body ';
        if ( $id != '' ) {
            $this->_body .= 'id="' . $id . '"';
        }
        if ( $extra != '' ) {
            $this->_body .= ' ' . $extra . '';
        }
        $this->_body .= ' >';
    }

    function addExecBeforeOutput( $code ) {
        $this->_BeforeOutputTags .= "$code\n";
    }
}

/**
 * Smarty default template handler function
 *
 * @param  $resource_type
 * @param  $resource_name
 * @param  $template_source
 * @param  $template_timestamp
 * @param  $smarty_obj
 * @return bool
 */
function zarilia_template_create ( $resource_type, $resource_name, &$template_source, &$template_timestamp, &$smarty_obj ) {
    if ( $resource_type == 'db' ) {
        $file_handler = &zarilia_gethandler( 'tplfile' );
        $tpl = &$file_handler->find( 'default', null, null, null, $resource_name, true );
        if ( count( $tpl ) > 0 && is_object( $tpl[0] ) ) {
            $template_source = $tpl[0]->getSource();
            $template_timestamp = $tpl[0]->getLastModified();
            return true;
        }
    } else {
    }
    return false;
}

/**
 * function to update compiled template file in templates_c folder
 *
 * @param string $tpl_id
 * @param boolean $clear_old
 * @return boolean
 */
function zarilia_template_touch( $tpl_id, $clear_old = true ) {
    $tpl = new ZariliaTpl();
    $tpl->force_compile = true;
    $tplfile_handler = &zarilia_gethandler( 'tplfile' );
    $tplfile = &$tplfile_handler->get( $tpl_id );
    if ( is_object( $tplfile ) ) {
        $file = $tplfile->getVar( 'tpl_file' );
        if ( $clear_old ) {
            $tpl->clear_cache( 'db:' . $file );
            $tpl->clear_compiled_tpl( 'db:' . $file );
        }
        $tpl->fetch( 'db:' . $file );
        return true;
    }
    return false;
}

/**
 * Clear the addon cache
 *
 * @param int $mid Addons ID
 * @return
 */
function zarilia_template_clear_addon_cache( $mid ) {
    $block_arr = &ZariliaBlock::getByAddon( $mid );
    $count = count( $block_arr );
    if ( $count > 0 ) {
        $zariliaTpl = new ZariliaTpl();
        $zariliaTpl->zarilia_setCaching( 2 );
        for ( $i = 0; $i < $count; $i++ ) {
            if ( $block_arr[$i]->getVar( 'template' ) != '' ) {
                $zariliaTpl->clear_cache( 'db:' . $block_arr[$i]->getVar( 'template' ), 'blk_' . $block_arr[$i]->getVar( 'bid' ) );
            }
        }
    }
}

?>