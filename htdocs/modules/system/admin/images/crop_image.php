<?
include '../../../../mainfile.php';
include_once ICMS_LIBRARIES_PATH.'/wideimage/lib/WideImage.inc.php';

if(isset($_GET['image_ref']) && isset($_GET['x']) && isset($_GET['y']) && isset($_GET['x']) && isset($_GET['width']) && isset($_GET['convertTo'])){
	$x = $_GET['x'];
	$y = $_GET['y'];
	$width = $_GET['width'];
	$height = $_GET['height'];
	$image_ref = $_GET['image_ref'];
	$image_id = $_GET['image_id'];
	$percentSize = $_GET['percentSize'];
	$convertTo = $_GET['convertTo'];
	$uniq = $_GET['uniq'];
	$image_name = $_GET['image_name'];
	$save = $_GET['save'];
	$cancel = $_GET['cancel'];
	$overwrite = $_GET['overwrite'];
	$image_nicename = $_GET['image_nicename'];
	$image_weight = $_GET['image_weight'];
	$image_display = $_GET['image_display'];
	
	$x = preg_replace("/[^0-9]/si","",$x);
	$y = preg_replace("/[^0-9]/si","",$y);
	$width = preg_replace("/[^0-9]/si","",$width);
	$height = preg_replace("/[^0-9]/si","",$height);
	$percentSize = preg_replace("/[^0-9]/si","",$percentSize);
	
	if($percentSize>200)$percentSize = 200;
	
	if(strlen($x) && strlen($y) && $width && $height && $percentSize){
	
		if($percentSize!="100"){
			$x = $x * ($percentSize/100);	
			$y = $y * ($percentSize/100);	
			$width = $width * ($percentSize/100);	
			$height = $height * ($percentSize/100);	
		}
		
		$image_handler = xoops_gethandler('image');
		$imgcat_handler = xoops_gethandler('imagecategory');
	    
		$image =& $image_handler->get($image_id);		
		if (!is_object($image)){
			echo "alert('"._ERROR."');";
			exit;
		}
		
		$imagecategory =& $imgcat_handler->get($image->getVar('imgcat_id'));
		$categ_path = $imgcat_handler->getCategFolder($imagecategory);
		$categ_url  = $imgcat_handler->getCategFolder($imagecategory,1,'url');
		$curl = (substr($categ_url,-1) != '/')?$categ_url.'/':$categ_url;
		$cpath = (substr($categ_path,-1) != '/')?$categ_path.'/':$categ_path;
		if (!is_object($imagecategory)){
			echo "alert('"._ERROR."');";
			exit;
		}
		
		$imgname = substr($image->getVar('image_name'),0,strlen($image->getVar('image_name'))-4);
		$destinationFile = "crop_".$uniq."_$imgname.$convertTo";
		
		if ($imagecategory->getVar('imgcat_storetype') == 'db') {
			$img = wiImage::loadFromString($image->getVar('image_body'));
		}else{
			$img = wiImage::load($cpath.$image_name);
		}
		$img->crop($x,$y,$width,$height)->saveToFile($cpath.$destinationFile);
		
		if ($cancel){
			@unlink($cpath.$destinationFile);
			echo 'window.close();';
			exit;
		}
		if ($save){
			echo 'var windowLoc = unescape(window.opener.document.location);';
			$params  = '&oldimg='.$image_name;
			$params .= '&newimg='.$imgname.'.'.$convertTo;
			$params .= '&overwrite='.$overwrite;
			$params .= '&image_nicename='.$image_nicename;
			$params .= '&image_weight='.$image_weight;
			$params .= '&image_display='.$image_display;
			$params .= '&uniq='.$uniq;
			echo 'window.opener.location.href="../../modules/system/admin.php?fct=images&op=cropimg'.$params.'";';
			echo 'window.close();';
		}else{
		    echo "var w = window.open('".$curl.$destinationFile."','imageWin1','width=".($width+5).",height=".($height+5).",resizable=yes');";
		}
	}else{
		echo "alert('"._ERROR."');";
	}	
}
?>