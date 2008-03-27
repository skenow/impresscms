<?php
$rand_id = htmlspecialchars( $_GET['rand'] );
if (!preg_match('/^[_A-Za-z0-9]+$/', $rand_id) )
{	
	$rand_id = '';
}

$bgNum_id = htmlspecialchars( $_GET['bgNum'] );
if (!preg_match('/^[_A-Za-z0-9]+$/', $bgNum_id) )
{	
	$bgNum_id = '';
}

$image = ( $bgNum_id == 0 ) ? imagecreate( 60, 30 ) : imagecreatefromjpeg( "images/background$bgNum_id.jpg" );
/*
* use white as the background image
*/
$bgColor = imagecolorallocate ( $image, 255, 255, 255 );

/*
*  the text color is black
*/
$textColor = imagecolorallocate ( $image, 0, 0, 0 );

/*
*  write the random number
*/
imagestring ( $image, 5, 5, 8, $rand_id, $textColor );

/*
*  send several headers to make sure the image is not cached
*  taken directly from the PHP Manual
*  Date in the past
*/
if ( !headers_sent() ) {
    header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
    header( "Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . " GMT" );
    header( "Cache-Control: no-store, no-cache, must-revalidate" );
    header( "Cache-Control: post-check=0, pre-check=0", false );
    header( "Pragma: no-cache" );
    header( "Cache-control: private" );
    header( 'Content-type: image/jpeg' );
} 
imagejpeg( $image );
imagedestroy( $image );

?>