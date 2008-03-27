<?php
// $Id: class.thumbnail.php,v 1.3 2007/04/24 09:33:14 catzwolf Exp $
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
defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );

/**
 * ZariliaThumbs
 *
 * @package
 * @author Catzwolf
 * @copyright Copyright (c) 2006
 * @version $Id: class.thumbnail.php,v 1.3 2007/04/24 09:33:14 catzwolf Exp $
 * @access public
 */
class ZariliaThumbs {
    var $_image_name;
    var $_image_path;

    var $_image_save_name;
    var $_image_save_path;

    var $_zarilia_path = ZAR_ROOT_PATH;
    var $_zarilia_url = ZAR_URL;
    var $_zarilia_thumb_path = ZAR_THUMBS_PATH;

    var $_use_thumbNails;
    var $_use_lib_type;
    var $_use_cache;
    var $_use_prefix = 0;

    var $_img_info = array();
    var $_img_width = 100;
    var $_img_height = 100;
    var $_img_quality = 100;
    var $_img_aspect = 1;

    var $_img_prefix = '';

    var $_img_error = array();
    var $_img_reporterror = 0;

    /**
     * ZariliaThumbs::ZariliaThumbs()
     *
     * @param  $image_name
     * @param  $image_path
     * @param  $thumbnail_path
     * @return
     */
    function ZariliaThumbs( $image_name, $image_path ) {
        $this->_image_name = zarilia_trim( $image_name );
        $this->_image_path = zarilia_trim( $image_path );
    }

    /*
	* Do Set Stuff
	*/
    function setImageSaveName( $value ) {
        $this->_image_save_name = zarilia_trim( $value );
    }

    /**
     * ZariliaThumbs::setImageSavePath()
     *
     * @param  $value
     * @return
     */
    function setImageSavePath( $value ) {
        $this->$_image_save_path = zarilia_trim( $value );
    }

    /**
     * ZariliaThumbs::setThumbnailPath()
     *
     * @param string $value
     * @return
     */
    function setThumbnailPath( $value = 'thumbs' ) {
        $this->_image_thumb_path = zarilia_trim( $value );
    }

    /**
     * ZariliaThumbs::setImagePrefix()
     *
     * @param string $value
     * @return
     */
    function setImagePrefix( $value = '' ) {
        $this->_img_prefix = zarilia_trim( $value );
    }

    /**
     * ZariliaThumbs::setUseThumbs()
     *
     * @param integer $value
     * @return
     */
    function setUseThumbs( $value = 1 ) {
        $this->_use_thumbNails = ( isset( $value ) ) ? 1 : 0;
    }

    /**
     * ZariliaThumbs::setImagePath()
     *
     * @param string $value
     * @return
     */
    function setImagePath( $value = '' ) {
        $this->_zarilia_path = $value;
    }

    /**
     * ZariliaThumbs::setImageUrl()
     *
     * @param string $value
     * @return
     */
    function setImageUrl( $value = '' ) {
        $this->_zarilia_url = $value;
    }

    /**
     * ZariliaThumbs::setLibType()
     *
     * @param string $value
     * @return
     */
    function setLibType( $value = "gd2" ) {
        switch ( $value ) {
            case 0:
                $value = 'gd1';
                break;
            case 1:
            default:
                $value = 'gd2';
                break;
            case 2:
                $value = 'im';
                break;
        } // switch
        $this->_use_lib_type = $value;
    }

    /*
	* match image path
	*/
    function get_image_path( $type = 0 ) {
        if ( $type == 0 ) {
            // if ( substr( $this->_zarilia_path, -1 ) ) {
            return $this->_zarilia_path . DIRECTORY_SEPARATOR . $this->_image_path;
        } else {
            return $this->_zarilia_url . "/" . $this->_image_path;
        }
    }

    /**
     * ZariliaThumbs::get_image_savepath()
     *
     * @param integer $type
     * @return
     */
    function get_image_savepath( $type = 0 ) {
        $this->_image_save_path = ( $this->_image_save_path ) ? $this->_image_save_path : $this->_image_path;
        if ( $type == 0 ) {
            // if ( substr( $this->_zarilia_path, -1 ) ) {
            return $this->_zarilia_path . DIRECTORY_SEPARATOR . $this->_image_save_path;
        } else {
            return $this->_zarilia_url . "/" . $this->_image_save_path;
        }
    }

    /**
     * ZariliaThumbs::get_image_thumb_path()
     *
     * @param integer $type
     * @return
     */
    function get_image_thumb_path( $type = 0 ) {
        if ( $type == 0 ) {
            // if ( substr( $this->_zarilia_path, -1 ) ) {
            return ZAR_THUMBS_PATH;
        } else {
            return ZAR_THUMBS_URL;
        }
    }

    /**
     * ZariliaThumbs::getSavedfilename()
     *
     * @param integer $type
     * @return
     */
    function getSavedfilename( $type = 0 ) {
        $temp_pathName = ( $this->_image_save_path ) ? 1 : 0;
        $DIRECTORY_SEPARATOR = ( $type == 0 ) ? DIRECTORY_SEPARATOR : "/";

        $path = '';
        if ( $this->_img_thumbs == true ) {
            $path .= $this->get_image_thumb_path( $type ) . $DIRECTORY_SEPARATOR;
            $path .= $this->_img_thumbprefix . ".";
        } else {
            $path .= $this->get_image_savepath( $type ) . $DIRECTORY_SEPARATOR;
        }

        if ( $this->_image_save_name ) {
            $path .= $this->_image_save_name;
        } else {
            $path .= $this->_image_name;
        }
        return $path;
    }

    /*
	* public details
	*/
    function do_thumb( $img_width = 0, $img_height = 0, $img_quality = 0, $img_aspect = 0, $use_cache = true, $img_thumbs = true ) {
        global $_image_file_size;

        $this->_img_width = intval( $img_width );
        $this->_img_height = intval( $img_height );
        $this->_img_quality = intval( $img_quality );
        $this->_img_aspect = intval( $img_aspect );
        $this->_img_thumbs = ( $img_thumbs ) ? true : false;
        $this->_use_cache = ( $use_cache ) ? true : false;
        if ( ( $this->_use_lib_type == 'gd1' || $this->_use_lib_type == 'gd2' ) && !$this->gd_lib_check() ) {
            $this->_img_error[] = "No GD library found";
            return false;
        }
        if ( $this->check_paths() != true ) {
            $this->_img_error[] = $this->check_paths();
            return false;
        }
        if ( !$this->image_check() ) {
            $this->_img_error[] = "No GD library found";
            return false;
        }
        $_image_file_size = filesize( $this->get_image_path( 0 ) . "/" . $this->_image_name );
        if ( ( $this->_img_info[0] < $this->_img_width ) && ( $this->_img_info[1] < $this->_img_height ) ) {
            $_thumb_nail['imgTitle'] = $this->get_image_path( 1 ) . "/" . $this->_image_name;
            $_thumb_nail['imgWidth'] = $this->_img_info[0];
            $_thumb_nail['imgHeight'] = $this->_img_info[1];
            $_thumb_nail['imgFilesize'] = $_image_file_size;
            return $_thumb_nail;
        }

        $image = $this->do_resize();
        if ( false === $image ) {
            $this->_img_error[] = "Error: Could not create thumbnail image";
            return false;
        } else {
            return $image;
        }
    }

    /**
     * ZariliaThumbs::do_resize()
     *
     * @return
     */
    function do_resize() {
        $_thumb_nail = array();

        global $_image_file_size;
        /**
         * Get image size and scale ratio
         */
        $scale = min( $this->_img_width / $this->_img_info[0], $this->_img_height / $this->_img_info[1] );
        /**
         * If the image is larger than the max shrink it
         */
        $newWidth = $newHeight = 0;
        $newWidth = ( $newWidth > $this->_img_info[0] ) ? $this->_img_info[0] : $this->_img_width;
        $newHeight = ( $newHeight > $this->_img_info[1] ) ? $this->_img_info[1] : $this->_img_height;

        if ( $scale < 1 && $this->_img_aspect == true ) {
            $newWidth = floor( $scale * $this->_img_info[0] );
            $newHeight = floor( $scale * $this->_img_info[1] );
        }

        if ( $this->_img_thumbs ) {
            $this->_img_thumbprefix = $newWidth . "x" . $newHeight;
        }

        $temp_savefile = $this->getSavedfilename( 0 );
        $temp_sourcefile = $this->get_image_path( 0 ) . DIRECTORY_SEPARATOR . $this->_image_name;

        $_cache_img_info = @getimagesize( $temp_savefile );
        $has_changed = ( file_exists( $temp_savefile ) && ( $newWidth != $_cache_img_info[0] || $newHeight != $_cache_img_info[0] ) ) ? 1 : 0;
        if ( $this->_use_cache && $has_changed == 1 ) {
            $_thumb_nail['imgTitle'] = $this->getSavedfilename( 1 );
            $_thumb_nail['imgWidth'] = $_cache_img_info[0];
            $_thumb_nail['imgHeight'] = $_cache_img_info[1];
            $_thumb_nail['imgFilesize'] = $_image_file_size;
            return $_thumb_nail;
        }
        /**
         * if we are not updating the image and the image exists then return the thumbnail already there
         */

        switch ( $this->_use_lib_type ) {
            case "im":
                if ( !empty( $zariliaAddonConfig['path_magick'] ) && is_dir( $zariliaAddonConfig['path_magick'] ) ) {
                    if ( preg_match( "#[A-Z]:|\\\\#Ai", __FILE__ ) ) {
                        $cur_dir = dirname( __FILE__ );
                        $src_file_im = '"' . $cur_dir . '\\' . strtr( $temp_sourcefile, '/', '\\' ) . '"';
                        $new_file_im = '"' . $cur_dir . '\\' . strtr( $temp_savefile, '/', '\\' ) . '"';
                    } else {
                        $src_file_im = escapeshellarg( $temp_sourcefile );
                        $new_file_im = escapeshellarg( $temp_savefile );
                    }
                    $magick_command = $zariliaAddonConfig['path_magick'] . '/convert -quality {$zariliaAddonConfig["imagequality"]} -antialias -sample {$newWidth}x{$newHeight} {$src_file_im} +profile "*" ' . str_replace( '\\', '/', $new_file_im ) . '';
                    passthru( $magick_command );
                    $_thumb_nail['imgTitle'] = $this->getSavedfilename( 1 );
                    $_thumb_nail['imgWidth'] = $newWidth;
                    $_thumb_nail['imgHeight'] = $newHeight;
                    $_thumb_nail['imgFilesize'] = $_image_file_size;
                    return $_thumb_nail;
                }
                return false;
                break;

            case "gd1":
            case "gd2":
            default :
                $imageCreateFunction = ( function_exists( 'imagecreatetruecolor' ) && $this->_use_lib_type == "gd2" ) ? "imagecreatetruecolor" : "imagecreate";
                $imageCopyfunction = ( function_exists( 'ImageCopyResampled' ) && $this->_use_lib_type == "gd2" ) ? "imagecopyresampled" : "imagecopyresized";
                switch ( $this->_img_info[2] ) {
                    case 1:
                        // GIF image
                        $img = ( function_exists( 'imagecreatefromgif' ) ) ? imagecreatefromgif( $temp_sourcefile ) : imageCreateFromPNG( $temp_sourcefile );
                        $tmp_img = $imageCreateFunction( $newWidth, $newHeight );
                        $imageCopyfunction( $tmp_img, $img, 0, 0, 0, 0, $newWidth, $newHeight, $this->_img_info[0], $this->_img_info[1] );
                        if ( function_exists( 'imagegif' ) ) {
                            imageGIF( $tmp_img, $temp_savefile );
                        } else {
                            imagePNG( $tmp_img, $temp_savefile );
                        }
                        imagedestroy( $tmp_img );
                        break;

                    case 2:
                        $img = ( function_exists( 'imagecreatefromjpeg' ) ) ? imageCreateFromJPEG( $temp_sourcefile ) : imageCreateFromPNG( $temp_sourcefile );
                        $tmp_img = $imageCreateFunction( $newWidth, $newHeight );
                        $imageCopyfunction( $tmp_img, $img, 0, 0, 0, 0, $newWidth, $newHeight, $this->_img_info[0], $this->_img_info[1] );
                        if ( function_exists( 'imagejpeg' ) ) {
                            imageJPEG( $tmp_img, $temp_savefile, $this->_img_quality );
                        } else {
                            imagePNG( $tmp_img, $temp_savefile, $this->_img_quality );
                        }
                        imagedestroy( $tmp_img );
                        break;

                    case 3:
                        $img = imageCreateFromPNG( $temp_sourcefile );
                        $tmp_img = $imageCreateFunction( $newWidth, $newHeight );
                        $imageCopyfunction( $tmp_img, $img, 0, 0, 0, 0, $newWidth, $newHeight, $this->_img_info[0], $this->_img_info[1] );
                        imagePNG( $tmp_img, $temp_savefile );
                        imagedestroy( $tmp_img );
                        break;

                    case 6:
                    default:
                        $_thumb_nail['imgTitle'] = $this->get_image_path( 1 ) . "/" . $this->_image_name;
                        $_thumb_nail['imgWidth'] = $newWidth;
                        $_thumb_nail['imgHeight'] = $newHeight;
                        $_thumb_nail['imgFilesize'] = $_image_file_size;
                        return $_thumb_nail;
                }
                /**
                 * return image info
                 */
                $_thumb_nail['imgTitle'] = $this->getSavedfilename( 1 );
                $_thumb_nail['imgWidth'] = $newWidth;
                $_thumb_nail['imgHeight'] = $newHeight;
                $_thumb_nail['imgFilesize'] = $_image_file_size;
                return $_thumb_nail;
        }
    }
    /**
     * ZariliaThumbs::check_paths()
     *
     * @return
     */
    function check_paths() {
        /*
		* check source image
		*/
        $_temp_image_Path = $this->get_image_path();
        if ( !is_string( $_temp_image_Path ) || !is_dir( $_temp_image_Path ) || !is_writable( $_temp_image_Path ) ) {
            return 'Error: Source path does not exist or is not readable';
        }

        $_temp_savePath = $this->get_image_savepath();
        if ( !is_string( $_temp_image_Path ) || !is_dir( $_temp_savePath ) || !is_writable( $_temp_savePath ) ) {
            return 'Error: Destination path does not exist or is not readable';
        }
        return true;
    }

    /**
     * ZariliaThumbs::image_check()
     *
     * @param string $imageinfo
     * @return
     */
    function image_check() {
        $_temp_image_Path = $this->get_image_path() . DIRECTORY_SEPARATOR . $this->_image_name;
        echo $_temp_image_Path;
		if ( !file_exists( $_temp_image_Path ) || !is_readable( $_temp_image_Path ) ) {
            $this->_img_error[] = 'Error: Source file does not exist or is not readable';
            return false;
        }

        $this->_img_info = @getimagesize( $_temp_image_Path );
        if ( !is_array( $this->_img_info ) ) {
            $this->_img_error[] = "Error: Failed getting image information";
            return false;
        }
        return true;
    }

    /**
     * ZariliaThumbs::gd_lib_check()
     *
     * @return
     */
    function gd_lib_check() {
        if ( !extension_loaded( 'gd' ) ) {
            return false;
        }
        $gdlib = ( function_exists( 'gd_info' ) );
        $ret = ( false == $gdlib = gd_info() ) ? false : true;
        return true;
    }

    /**
     * ZariliaThumbs::use_thumbs()
     *
     * @return
     */
    function use_thumbs() {
        if ( $this->_use_thumbNails ) {
            return true;
        }
        return false;
    }

    function imageCacheCheck( $_cache_img_info = array(), $has_changed = 0 ) {
        if ( !$this->_use_cache ) {
            return true;
        }
        return true;
    }

    /**
     * ZariliaThumbs::getErrors()
     *
     * @return
     */
    function getErrors() {
        if ( count( $this->_img_error ) > 0 && $this->_img_reporterror ) {
            foreach ( $this->_img_error as $error ) {
                echo "<h4>System Errors</h4>";
                echo "<div>$error</div>";
            }
        }
    }
}

?>