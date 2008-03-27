<?php
// $Id: class.uploader.php,v 1.1 2007/03/16 02:38:58 catzwolf Exp $
// ------------------------------------------------------------------------ //
// Zarilia - PHP Content Management System                      			//
// Copyright (c) 2007 Zarilia                           				//
// 																			//
// Authors: 																//
// John Neill ( AKA Catzwolf )                                     			//
// Raimondas Rimkevicius ( AKA Mekdrop )									//
// 							 												//
// URL: http:www.zarilia.com 												//
// Project: Zarilia Project                                               //
// -------------------------------------------------------------------------//

defined( 'ZAR_ROOT_PATH' ) or die( 'You do not have permission to access this file!' );
/**
 * !
 * Example
 *
 * include_once 'uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $uploader = new ZariliaMediaUploader('/home/zarilia/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
 * if ($uploader->fetchMedia($_POST['uploade_file_name'])) {
 * if (!$uploader->upload()) {
 * echo $uploader->getErrors();
 * } else {
 * echo '<h4>File uploaded successfully!</h4>'
 * echo 'Saved as: ' . $uploader->getSavedFileName() . '<br />';
 * echo 'Full path: ' . $uploader->getSavedDestination();
 * }
 * } else {
 * echo $uploader->getErrors();
 * }
 */

/**
 * Upload Media files
 *
 * Example of usage:
 * <code>
 * include_once 'uploader.php';
 * $allowed_mimetypes = array('image/gif', 'image/jpeg', 'image/pjpeg', 'image/x-png');
 * $maxfilesize = 50000;
 * $maxfilewidth = 120;
 * $maxfileheight = 120;
 * $uploader = new ZariliaMediaUploader('/home/zarilia/uploads', $allowed_mimetypes, $maxfilesize, $maxfilewidth, $maxfileheight);
 * if ($uploader->fetchMedia($_POST['uploade_file_name'])) {
 *                  if (!$uploader->upload()) {
 *                     echo $uploader->getErrors();
 *                  } else {
 *                     echo '<h4>File uploaded successfully!</h4>'
 *                     echo 'Saved as: ' . $uploader->getSavedFileName() . '<br />';
 *                     echo 'Full path: ' . $uploader->getSavedDestination();
 *                  }
 * } else {
 *                  echo $uploader->getErrors();
 *
 * }
 * </code>
 *
 * @package kernel
 * @subpackage core
 * @author Kazumi Ono
 *  @copyright copyright (c) 2007 Zarilia Project - http.www.zarilia.com
 */
mt_srand( ( double ) microtime() * 1000000 );

class ZariliaMediaUploader {
    var $mediaName;
    var $mediaType;
    var $mediaSize;
    var $mediaTmpName;
    var $mediaError;
    var $uploadDir = '';
    var $allowedMimeTypes = array();
    var $maxFileSize = 0;
    var $maxWidth;
    var $maxHeight;
    var $targetFileName;
    var $prefix;
    var $use_prefix;
    var $ext = "";
    var $dimension;
    var $errors = array();

    var $savedDestination;
    var $savedFileName;

    var $noImageSizeCheck = true;
    var $noFileSizeCheck = true;
    /**
     * No admin check for uploads
     */
    var $noadmin_sizecheck;
    var $upload_random;
    /**
     * Constructor
     *
     * @param string $uploadDir
     * @param array $allowedMimeTypes
     * @param int $maxFileSize
     * @param int $maxWidth
     * @param int $maxHeight
     * @param int $cmodvalue
     */
    function ZariliaMediaUploader( $uploadDir, $allowedMimeTypes = 0, $maxFileSize, $maxWidth = 0, $maxHeight = 0 ) {
        if ( is_array( $allowedMimeTypes ) ) {
            $this->allowedMimeTypes = &$allowedMimeTypes;
        }
        $this->uploadDir = ( !empty( $uploadDir ) ) ? $uploadDir : ZAR_UPLOAD_PATH;
        $this->maxFileSize = intval( $maxFileSize );
        if ( isset( $maxWidth ) ) {
            $this->maxWidth = intval( $maxWidth );
        }
        if ( isset( $maxHeight ) ) {
            $this->maxHeight = intval( $maxHeight );
        }
    }

    /**
     * Fetch the uploaded file
     *
     * @param string $media_name Name of the file field
     * @param int $index Index of the file (if more than one uploaded under that name)
     * @global $HTTP_POST_FILES
     * @return bool
     */
    function fetchMedia( $media_name, $index = null ) {
        global $_FILES;

        if ( !isset( $_FILES[$media_name] ) ) {
            $this->setErrors( 'You either did not choose a file to upload or the server has insufficient read/writes to upload this file.!' );
            return false;
        } elseif ( is_array( $_FILES[$media_name]['name'] ) && isset( $index ) ) {
            $index = intval( $index );
            $this->mediaName = ( get_magic_quotes_gpc() ) ? stripslashes( $_FILES[$media_name]['name'][$index] ) : $_FILES[$media_name]['name'][$index];
            $this->mediaType = $_FILES[$media_name]['type'][$index];
            $this->mediaSize = $_FILES[$media_name]['size'][$index];
            $this->mediaTmpName = $_FILES[$media_name]['tmp_name'][$index];
            $this->mediaError = !empty( $_FILES[$media_name]['error'][$index] ) ? $_FILES[$media_name]['errir'][$index] : 0;
        } else {
            $media_name = @$_FILES[$media_name];
            $this->mediaName = ( get_magic_quotes_gpc() ) ? stripslashes( $media_name['name'] ) : $media_name['name'];
            $this->mediaName = $media_name['name'];
            $this->mediaType = $media_name['type'];
            $this->mediaSize = $media_name['size'];
            $this->mediaTmpName = $media_name['tmp_name'];
            $this->mediaError = !empty( $media_name['error'] ) ? $media_name['error'] : 0;
        }

        $this->dimension = getimagesize( $this->mediaTmpName );
        $this->errors = array();
        if ( intval( $this->mediaSize ) < 0 ) {
            $this->setErrors( 'Invalid File Size' );
            return false;
        }
        if ( $this->mediaName == '' ) {
            $this->setErrors( 'Filename Is Empty' );
            return false;
        }
        if ( $this->mediaTmpName == 'none' ) {
            $this->setErrors( 'No file uploaded, this is a error' );
            return false;
        }

        if ( !is_uploaded_file( $this->mediaTmpName ) ) {
            switch ( $this->mediaError ) {
                case 0: // no error; possible file attack!
                    $this->setErrors( 'There was a problem with your upload. Error: 0' );
                    break;
                case 1: // uploaded file exceeds the upload_max_filesize directive in php.ini
                    $this->setErrors( 'The file you are trying to upload is too big. Error: 1' );
                    break;
                case 2: // uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the html form
                    $this->setErrors( 'The file you are trying to upload is too big. Error: 2' );
                    break;
                case 3: // uploaded file was only partially uploaded
                    $this->setErrors( 'The file you are trying upload was only partially uploaded. Error: 3' );
                    break;
                case 4: // no file was uploaded
                    $this->setErrors( 'No file selected for upload. Error: 4' );
                    break;
                default: // a default error, just in case!  :)
                    $this->setErrors( 'No file selected for upload. Error: 5' );
                    break;
            }
            return false;
        }
		return true;
    }

    /**
     * Set the target filename
     *
     * @param string $value
     */
    function setTargetFileName( $value = '' ) {
        $this->targetFileName = strval( trim( $value ) );
    }

    /**
     * Set the prefix
     *
     * @param string $value
     */
    function setPrefix( $value = '' ) {
        $this->prefix = strval( trim( $value ) );
    }

    /**
     * Mask Image with randon data rather than using orginal file name.
     */
    function setRandom( $value = 0 ) {
		$this->upload_random = intval( $value );
    }

    /**
     * Mask Image with randon data rather than using orginal file name.
     */
    function setUsePrefix( $value = 0 ) {
		$this->use_prefix = intval( $value );
    }

    /**
     * Set the imageSizeCheck
     *
     * @param string $value
     */
    function setNoImageSizeCheck( $value = 0 ) {
        $this->noImageSizeCheck = $value;
    }

    /**
     * Set the fileSizeCheck
     *
     * @param string $value
     */
    function setNoFileSizeCheck( $value = 0 ) {
        $this->noFileSizeCheck = $value;
    }

    /**
     * Get the uploaded filename
     *
     * @return string
     */
    function getMediaName() {
        return $this->mediaName;
    }

    /**
     * Get the type of the uploaded file
     *
     * @return string
     */
    function getMediaType() {
        return $this->mediaType;
    }

    /**
     * Get the size of the uploaded file
     *
     * @return int
     */
    function getMediaSize() {
        return $this->mediaSize;
    }

    /**
     * Get the temporary name that the uploaded file was stored under
     *
     * @return string
     */
    function getMediaTmpName() {
        return $this->mediaTmpName;
    }

    /**
     * Get the saved filename
     *
     * @return string
     */
    function getSavedFileName() {
        return $this->savedFileName;
    }

    /**
     * Get the destination the file is saved to
     *
     * @return string
     */
    function getSavedDestination() {
        return $this->savedDestination;
    }

    /**
     * Get the file extension
     *
     * @return string
     */
    function getExt() {
        $this->ext = ltrim( strrchr( $this->mediaName, '.' ), '.' );
        return $this->ext;
    }

    /**
     * Get the file extension
     *
     * @return string
     */
    function getPrefix() {
        $ext = ltrim( strrchr( $this->mediaName, '.' ), '.' );
        $this->prefix = ( $this->prefix != '' ) ? $this->prefix : $ext;
		return $this->prefix;
    }

    /**
     * Check the file and copy it to the destination
     *
     * @return bool
     */
    function upload( $chmod = 0644 ) {
        if ( $this->uploadDir == '' ) {
            $this->setErrors( 'Upload directory not set' );
            return false;
        }

        if ( !is_dir( $this->uploadDir ) ) {
            $this->setErrors( 'Failed opening directory: ' . $this->uploadDir );
        }

        if ( !is_writeable( $this->uploadDir ) ) {
            $this->setErrors( 'Failed opening directory with write permission: ' . $this->uploadDir );
        }

        if ( !$this->checkMaxFileSize() ) {
            $this->setErrors( sprintf( 'File Size: %u. Maximum Size Allowed: %u' , $this->mediaSize, $this->maxFileSize ) );
        }

        if ( is_array( $this->dimension ) ) {
            if ( !$this->checkMaxWidth( $this->dimension[0] ) ) {
                $this->setErrors( sprintf( 'File width: %u. Maximum width allowed: %u', $this->dimension[0], $this->maxWidth ) );
            }
            if ( !$this->checkMaxHeight( $this->dimension[1] ) ) {
                $this->setErrors( sprintf( 'File height: %u. Maximum height allowed: %u', $this->dimension[1], $this->maxHeight ) );
            }
        }

        if ( !$this->checkMimeType() ) {
            $this->setErrors( 'MIME type not allowed: ' . $this->mediaType );
        }

        if ( !$this->_copyFile( $chmod ) ) {
            $this->setErrors( 'Failed uploading file: ' . $this->mediaName );
        }

        if ( count( $this->errors ) > 0 ) {
            return false;
        }
        return true;
    }

    /**
     * Copy the file to its destination
     *
     * @return bool
     */
    function _copyFile( $chmod ) {

		$matched = array();
        if ( !preg_match( "/\.([a-zA-Z0-9]+)$/", $this->mediaName, $matched ) ) {
            $this->setErrors( 'File ' . $this->mediaName . ' has illegal charactors.' );
            return false;
        }

		if ( $this->upload_random == 1) {
			$this->savedFileName = $this->doUploadToRandumFile( 1 );
        } else {
            $this->savedFileName = $this->mediaName;
        }
        if ( $this->use_prefix ) {
            $this->savedFileName = $this->getPrefix() . "_" . $this->savedFileName;
        }
        $this->savedFileName = preg_replace( '!\s+!', '_', $this->savedFileName );
        $this->savedDestination = $this->uploadDir . DIRECTORY_SEPARATOR . $this->savedFileName;

		if ( file_exists( $this->savedDestination ) ) {
            $this->setErrors( 'File ' . $this->savedFileName . ' already exists on the server. Please rename this file and try again.<br />' );
			return false;
        }

        /**
         * if ( false != extension_loaded( 'zlib' ) )
         * {
         * if (
         *
         * @function _exists( 'gzcompress' ) )
         * {
         * require_once( ZAR_ROOT_PATH . '/class/zipdownloader.php' );
         * $downloader = new ZariliaZipDownloader();
         * $downloader -> addFileData( $this -> savedFileName, $this -> uploadDir, time() );
         * $this -> mediaTmpName = $downloader -> download( $this -> savedFileName, true );
         * }
         * }
         * //
         */
        if ( !move_uploaded_file( $this->mediaTmpName, $this->savedDestination ) ) {
            return false;
        }
        @chmod( $this->savedDestination, $chmod );
        return true;
    }

    /**
     * Is the file the right size?
     *
     * @return bool
     */
    function checkMaxFileSize() {
        if ( $this->noFileSizeCheck ) {
            return true;
        }
        if ( $this->mediaSize > $this->maxFileSize ) {
            return false;
        }
        return true;
    }

    /**
     * Is the picture the right width?
     *
     * @return bool
     */
    function checkMaxWidth( $dimension ) {
        if ( !isset( $this->maxWidth ) || $this->noImageSizeCheck ) {
            return true;
        }
        if ( $dimension > $this->maxWidth ) {
            return false;
        }
        return true;
    }

    /**
     * Is the picture the right height?
     *
     * @return bool
     */
    function checkMaxHeight( $dimension ) {
        if ( !isset( $this->maxHeight ) || $this->noImageSizeCheck ) {
            return true;
        }
        if ( $dimension > $this->maxWidth ) {
            return false;
        }
        return true;
    }

    /**
     * Is the file the right Mime type
     *
     * (is there a right type of mime? ;-)
     *
     * @return bool
     */
    function checkMimeType() {
        if ( count( $this->allowedMimeTypes ) > 0 && !in_array( $this->mediaType, $this->allowedMimeTypes ) ) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Add an error
     *
     * @param string $error
     */
    function setErrors( $error ) {
        $this->errors[] = trim( $error );
    }

    /**
     * Get generated errors
     *
     * @param bool $ashtml Format using HTML?
     * @return array |string    Array of array messages OR HTML string
     */
    function &getErrors( $ashtml = true ) {
        if ( !$ashtml ) {
            return $this->errors;
        } else {
            $ret = '<br /><br />';
            if ( count( $this->errors ) > 0 ) {
                //$ret = '<h4>Errors Returned While Uploading</h4>';
                foreach ( $this->errors as $error ) {
                    $ret .= $error . '<br />';
                }
            }
            return $ret;
        }
    }
    /**
     * UploadFile::doUploadToRandumFile()
     *
     * @param  $distpath
     * @param string $prefix
     * @return
     */
    function &doUploadToRandumFile( $addext = 1 ) {
        //$this -> prefix = ( $this -> prefix ) ? $this -> prefix : 'media';
		$temp_filename = ( $addext == 1 ) ? uniqid( $this -> prefix ) . '.' . $this->getExt() : $temp_filename;
        return $temp_filename;
    }
}

?>