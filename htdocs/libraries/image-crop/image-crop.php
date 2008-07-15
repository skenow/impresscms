<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Image crop - DHTML user interface</title>
	<link rel="stylesheet" href="css/xp-info-pane.css">
	<link rel="stylesheet" href="css/image-crop.css">
	<script type="text/javascript" src="js/xp-info-pane.js"></script>
	<script type="text/javascript" src="js/ajax.js"></script>
	<script type="text/javascript">
	/************************************************************************************************************
	(C) www.dhtmlgoodies.com, April 2006
	
	This is a script from www.dhtmlgoodies.com. You will find this and a lot of other scripts at our website.	
	
	Terms of use:
	You are free to use this script as long as the copyright message is kept intact. However, you may not
	redistribute, sell or repost it without our permission.
	
	Thank you!
	
	www.dhtmlgoodies.com
	Alf Magne Kalleland
	
	************************************************************************************************************/	

	
	/* Variables you could modify */
	
	var crop_script_server_file = '<?=$_GET['crop_script'];?>';
	
	var cropToolBorderWidth = 1;	// Width of dotted border around crop rectangle
	var smallSquareWidth = 7;	// Size of small squares used to resize crop rectangle
	
	// Size of image shown in crop tool
	var crop_imageWidth = <?=$_GET['imageWidth'];?>;
	var crop_imageHeight = <?=$_GET['imageHeight'];?>;
	
	// Size of original image
	var crop_originalImageWidth = <?=$_GET['imageWidth'];?>;
	var crop_originalImageHeight = <?=$_GET['imageHeight'];?>;
	
	var crop_minimumPercent = 10;	// Minimum percent - resize
	var crop_maximumPercent = 200;	// Maximum percent -resize
	
	
	var crop_minimumWidthHeight = 15;	// Minimum width and height of crop area
	
	var updateFormValuesAsYouDrag = true;	// This variable indicates if form values should be updated as we drag. This process could make the script work a little bit slow. That's why this option is set as a variable.
	if(!document.all)updateFormValuesAsYouDrag = true;	// Enable this feature only in IE
	
	/* End of variables you could modify */
	</script>
	<script type="text/javascript" src="js/image-crop.js"></script>
</head>
<body>
<div id="pageContent">
<div id="dhtmlgoodies_xpPane">  
	<div class="dhtmlgoodies_panel">
		<div>
			<!-- Start content of pane -->
			<form>
			<input type="hidden" id="input_image_ref" value="<?=$_GET['image'];?>">
			<input type="hidden" id="image_id" value="<?=$_GET['image_id'];?>">
			<input type="hidden" id="uniq" value="<?=$_GET['uniq'];?>">
			<input type="hidden" id="image_name" value="<?=$_GET['image_name'];?>">
			<table width="100%">
				<tr>
					<td align="right">X:</td><td><input type="text" class="textInput" name="crop_x" id="input_crop_x"></td>
				</tr>
				<tr>
					<td align="right">Y:</td><td><input type="text" class="textInput" name="crop_y" id="input_crop_y"></td>
				</tr>
				<tr>
					<td align="right">Width:</td><td><input type="text" class="textInput" name="crop_width" id="input_crop_width"></td>
				</tr>
				<tr>
					<td align="right">Height:</td><td><input type="text" class="textInput" name="crop_height" id="input_crop_height"></td>
				</tr>
				<tr>
					<td id="cropButtonCell" colspan="2" align="center">
					<input type="button" onclick="cropScript_executeCrop(this)" value="Preview">
					<input type="button" onclick="cropScript_saveCrop(this)" value="Save" />
					<input type="button" onclick="cropScript_cancelCrop(this)" value="Cancel" />
					</td>
				</tr>
			</table>
			<input type="hidden" id="input_convert_to" value="<?=substr($_GET['image_name'],strlen($_GET['image_name'])-3,3);?>">
			<input type="hidden" class="textInput" name="crop_percent_size" id="crop_percent_size" value="100">
			<div id="crop_progressBar">
			
			</div>		
			</form>
			<!-- End content -->
		</div>	
	</div>
	<div class="dhtmlgoodies_panel">
		<div>
			<!-- Start content of pane -->
			<table>
				<tr>
					<td><b><?=$_GET['image_title'];?></b></td>
				</tr>
				<tr>
					<td>Dimension: <span id="label_dimension"></span></td>
				</tr>
			</table>
			<!-- End content -->
		</div>		
	</div>
	<div class="dhtmlgoodies_panel">
		<div>
			<!-- Start content of pane -->
			
			To select crop area, drag and move the dotted rectangle or type in values directly into the form.
			
			<!-- End of content -->
		</div>		
	</div>
</div>

<div class="crop_content">
<div id="imageContainer">
<img src="<?=$_GET['image'];?>">
</div>
</div>
</div>

<script type="text/javascript">
initDhtmlgoodies_xpPane(Array('Crop inspector','Image details','Instructions'),Array(true,true,true),Array('pane1','pane2','pane3'));
init_imageCrop();
</script>
 
</body>
</html>