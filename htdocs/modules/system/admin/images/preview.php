<?php
include('../../../../mainfile.php');
include("../../../../libraries/wideimage/lib/WideImage.inc.php");
header('Content-type: image/png');
$file = $_GET['file'];
//echo XOOPS_ROOT_PATH.'/uploads/'.$file;
echo wiImage::load(XOOPS_ROOT_PATH.'/uploads/'.$file)->resize(400, 300)->asString('png');
?>