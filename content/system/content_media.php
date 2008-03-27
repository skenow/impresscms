<?php
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
set_magic_quotes_runtime( 0 );
if ( ini_get( 'zlib.output_compression' ) ) {
    @ini_set( 'zlib.output_compression', 'Off' );
}
if ( function_exists( 'mb_http_output' ) ) {
    @mb_http_output( 'pass' );
}

$media_id = zarilia_cleanRequestVars( $_REQUEST, 'media_id', 0 );
$media_direct = zarilia_cleanRequestVars( $_REQUEST, 'media_direct', false );
/**
 */
$media_handler = &zarilia_gethandler( 'media' );
$media = &$media_handler->get( $media_id );
if ( is_object( $media ) ) {
    $_download_filename = ZAR_ROOT_PATH . '/' . $media->getMediaCategory() . '/' . $media->getVar( 'media_name' );
    $_media_filesize = ( !$media->getVar( 'media_filesize' ) ) ? filesize( $_download_filename ) : $media->getVar( 'media_filesize' );
    if ( $media_direct ) {
        $UserBrowser = zariliaCheckBrowser( true );
        $_media_mimetype = ( $UserBrowser == true ) ? 'application/octetstream' : 'application/octet-stream';
    } else {
        $_media_mimetype = ( !$media->getVar( 'media_mimetype' ) ) ? filetype( $_download_filename ) : $media->getVar( 'media_mimetype' );
    }
    $_media_nicename = ( !$media->getVar( 'media_nicename' ) ) ? rawurldecode( $media->getVar( 'media_name' ) ) : rawurldecode( $media->getVar( 'media_nicename' ) . "." . $media->getVar( 'media_ext' ) );
    $_media_created = ( !$media->getVar( 'media_created' ) ) ? rawurldecode( $media->getVar( 'media_created' ) ) : time();

    header( 'Content-type: ' . $_media_mimetype );
    header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
    // header( 'Cache-control: max-age=31536000' );
    header( 'Expires: ' . gmdate( "D, d M Y H:i:s", time() + 31536000 ) . 'GMT' );
    header( 'Content-disposition: inline; filename=' . $_media_nicename );
    header( 'Content-Length: ' . $_media_filesize );
    header( 'Last-Modified: ' . gmdate( "D, d M Y H:i:s", $_media_created ) . 'GMT' );
	$zariliaConfig['downloadtype'] = 0;
	switch ( $zariliaConfig['downloadtype'] ) {
			case '0':
				readfile_chunked( $_download_filename, $retbytes = true );
				break;
			case '1':
			default:
				zarilia_readfile2( $_download_filename );
				break;
	} // switch
}
exit();

function readfile_chunked( $filename, $retbytes = true ) {
    $chunksize = 1 * ( 1024 * 1024 ); // how many bytes per chunk
    $buffer = '';
    $cnt = 0;
    $handle = fopen( $filename, 'rb' );
    if ( $handle === false ) {
        return false;
    } while ( !feof( $handle ) ) {
        $buffer = fread( $handle, $chunksize );
        echo $buffer;
        if ( $retbytes ) {
            $cnt += strlen( $buffer );
        }
    }
    $status = fclose( $handle );
    if ( $retbytes && $status ) {
        return $cnt; // return num. bytes delivered like readfile() does.
    }
    return $status;
}

function get_content( $url ) {
    $ch = curl_init();
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_HEADER, 0 );
    ob_start();
    curl_exec ( $ch );
    curl_close ( $ch );
    $string = ob_get_contents();
    ob_end_clean();
    return $string;
}

function zarilia_readfile( $filename ) {
    readfile( $filename );
}

?>