<?php
include('../../../../mainfile.php');
include("../../../../libraries/wideimage/lib/WideImage.inc.php");
header('Content-type: image/png');
$file = $_GET['file'];
//echo XOOPS_ROOT_PATH.'/uploads/'.$file;
$img = wiImage::load(XOOPS_ROOT_PATH.'/uploads/'.$file);
$width = $img->getWidth();
$height = $img->getHeight();
if ($width > 400 || $height > 300){
    echo $img->resize(400, 300)->asString('png');	
}else{
	echo $img->asString('png');
}

?>