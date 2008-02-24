<?php

/**
 * Simple to use php5 class for image manipulation
 * 
 * @author Arjan Topolovec
 * @version 1.2
 * @license LGPL
 */

/*
 * changelog:
 * 
 * 1.3:
 * - use image resources with open and use them as arguments (example: watermark image)
 * 
 * 1.2:
 * - as parameter to resize function you can set max width or max height
 * - add simple strings to images
 * - merge two images
 * 
 * 1.1:
 * - added support for editing jpeg and gif images
 * - images can now be saved in jpeg and gif (based on file extension)
 * - added image resizing based on image ratio
 */

class Image{
	
	private function __construct(){}
	
	/**
	 * Open given image for editing
	 * Passed $image string must be a vailid image on disk
	 *
	 * @param string $image
	 * @static 
	 * @return object
	 */
	public static function open($image){

			return new CreateImage($image);

		
	}
	
}

class CreateImage{
	
	/**
	 * name of opened image
	 *
	 * @var string
	 */
	public $image_name;
	
	/**
	 * Opened image resource
	 *
	 * @var resource
	 */
	private $image;
	
	/**
	 * Info of opened image
	 * (width, height, bits, mime)
	 *
	 * @var array
	 */
	private $image_info;
	
	public function __construct($image){
		
		//get image info
		$get_size = getimagesize($image);
		$this->image_info = array(
			'width' => $get_size[0],
			'height' => $get_size[1],
			'bits' => $get_size['bits'],
			'mime' => $get_size['mime']
		);
		
		$this->image_name = $image;
		$this->image = $this->imcreate($image);
		
	}
	
	public function __destruct(){
		
		if(isset($this->image)){
			imagedestroy($this->image);
		}
		
	}
	
	public function getInfo(){
		return $this->image_info;
	}
	
	/**
	 * Resize opened image to given $width and $height
	 *
	 * @param int $width
	 * @param int $height
	 * @return object
	 */
	public function resize($width = 0, $height = 0, $filter = array()){
		
		//check if only one lenght given
		if(is_int($width) && !$height){
			$height = (int)($width/($this->image_info['width']/$this->image_info['height']));
		} else if(is_int($height) && !$width){
			$width = (int)($height/($this->image_info['height']/$this->image_info['width']));
		}
		
		//check for filters
		if(isset($filter['max_height']) && $height > $filter['max_height']){
			$height = $filter['max_height'];
			$width = (int)($height/($this->image_info['height']/$this->image_info['width']));
		} else if(isset($filter['max_width']) && $width > $filter['max_width']){
			$width = $filter['max_width'];
			$height = (int)($width/($this->image_info['width']/$this->image_info['height']));
		}

		if(is_int($width) && is_int($height)){
			$image_old = $this->image;
			$this->image = imagecreatetruecolor($width,$height);			
			$otc = imagecolortransparent( $image_old );
			if($otc >= 0 && $otc < imagecolorstotal( $image_old )){
				$tc = imagecolorsforindex( $image_old, $otc );
				$ntc = imagecolorallocate($this->image,$tc['red'],$tc['green'],$tc['blue']);
				imagefill( $this->image, 0, 0, $ntc );
				imagecolortransparent( $this->image, $ntc );
			}
        	imagealphablending($this->image, false);
        	imagesavealpha($this->image, true);
			imagecopyresampled($this->image, $image_old, 0, 0, 0, 0, $width, $height, $this->image_info['width'], $this->image_info['height']);
			imagedestroy($image_old);
			
			$this->image_info['width'] = $width;
			$this->image_info['height'] = $height;
			
			return $this;
			
		}
		
	}
	
	/**
	 * Add given watermark picture to opened image
	 *
	 * @param string $file
	 * @param string $position default is bottomright
	 * @return object
	 */
	public function watermark($file, $position = "bottomright"){
		
		$watermark = $this->imcreate($file, false);
		
		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);
		
		switch($position){
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->image_info['width']-$watermark_width;
				$watermark_pos_y = 0;
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->image_info['height']-$watermark_height;
				break;
			case 'bottomright':
				$watermark_pos_x = $this->image_info['width']-$watermark_width;
				$watermark_pos_y = $this->image_info['height']-$watermark_height;
				break;
		}
		
		$otc = imagecolortransparent( $image_old );
		if($otc >= 0 && $otc < imagecolorstotal( $image_old )){
			$tc = imagecolorsforindex( $image_old, $otc );
			$ntc = imagecolorallocate($this->image,$tc['red'],$tc['green'],$tc['blue']);
			imagefill( $this->image, 0, 0, $ntc );
			imagecolortransparent( $this->image, $ntc );
		}
		imagealphablending($this->image, false);
		imagesavealpha($this->image, true);
		
		imagecopy($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, $watermark_width, $watermark_height);
		
		imagedestroy($watermark);
		
		return $this;
	
	}
	
	/**
	 * Crop opened image
	 *
	 * @param int $top_x
	 * @param int $top_y
	 * @param int $bottom_x
	 * @param int $bottom_y
	 * @return object
	 */
	public function crop($top_x, $top_y, $bottom_x, $bottom_y){
		
		$a = '$top_x:'.$top_x.'<br>';//Esq
		$a .= '$top_y:'.$top_y.'<br>';//Top
		$a .= '$bottom_x:'.$bottom_x.'<br>';//right
		$a .= '$bottom_y:'.$bottom_y.'<br>';//bottom

		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x-$top_x, $bottom_y-$top_y);
		
		$otc = imagecolortransparent( $image_old );
		if($otc >= 0 && $otc < imagecolorstotal( $image_old )){
			$tc = imagecolorsforindex( $image_old, $otc );
			$ntc = imagecolorallocate($this->image,$tc['red'],$tc['green'],$tc['blue']);
			imagefill( $this->image, 0, 0, $ntc );
			imagecolortransparent( $this->image, $ntc );
		}
		imagealphablending($this->image, false);
		imagesavealpha($this->image, true);
		
		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->image_info['width'], $this->image_info['height']);
		imagedestroy($image_old);
		
		$this->image_info['width'] = $bottom_x-$top_x;
		$this->image_info['height'] = $bottom_y-$top_y;
		
		return $this;
		
	}
	
	/**
	 * Rotate opened image for given degree angle
	 *
	 * @param int $degree Valid values -90,90,180
	 * @return object
	 */
	public function rotate($degree){
		
		$black = imagecolorallocate($this->image,0,0,0);
	
		$this->image = imagerotate($this->image, $degree, $black);
		
		imagecolortransparent( $this->image, $black );
		
		$this->image_info['width'] = imagesx($this->image);
		$this->image_info['height'] = imagesy($this->image);
		
		return $this;
		
	}
	
	/**
	 * Apply filter to image
	 * 
	 * @param int $filter filter for function imagefilter http://si2.php.net/manual/en/function.imagefilter.php
	 */
	public function filter($filter,$args=array()){
		if ($filter == 'IMG_FILTER_SEPIA' && !is_int($filter)){
			imagefilter($this->image, IMG_FILTER_GRAYSCALE); 
			imagefilter($this->image, IMG_FILTER_COLORIZE, 90, 60, 40);
		}else{		
			if (isset($args[0]) && isset($args[1]) && isset($args[2])){
				imagefilter($this->image, $filter, $args[0], $args[1], $args[2]);
			}elseif (isset($args[0]) && isset($args[1])){
				imagefilter($this->image, $filter, $args[0], $args[1]);
			}elseif (isset($args[0])){
				imagefilter($this->image, $filter, $args[0]);
			}else{
				imagefilter($this->image, $filter);
			}
		}
		return $this;
	}
	
	/**
	 * Show opened image
	 *
	 */
	public function show(){
		header("Content-type: image/png");
		imagepng($this->image);
		imagedestroy($this->image);
	}
	
	/**
	 * Save opened image
	 *
	 * @param string $filename name to save on hard drive
	 * @param int $quality compress ratio, from 100 (best) to 0 (worst), default 100
	 */
	public function save($filename, $quality = 100){
		return $this->imsave($this->image, $filename, $quality);
		//imagedestroy($this->image);
	}
	
	/**
	 * Create image resource based on mime type of the image
	 * 
	 * @param string $image bath to image
	 * @param bool $main_picture false if not main image like watermark (dafault true)
	 * @return resource
	 */
	private function imcreate($image, $main_picture = true){
		
		if(is_string($image)){
			$image_mime;
			
			if($main_picture == false){
				$info = getimagesize($image);
				$image_mime = $info['mime'];
			} else {
				$image_mime = $this->image_info['mime'];
			}
			
			if($image_mime == ('image/gif')){
				$image_old = imagecreatefromgif($image);
			} else if($image_mime == ('image/png')){
				$image_old = imagecreatefrompng($image);
			} else if($image_mime == ('image/jpeg')){
				$image_old = imagecreatefromjpeg($image);
			}
			
			$otc = imagecolortransparent( $image_old );
			if($otc >= 0 && $otc < imagecolorstotal( $image_old )){
				$tc = imagecolorsforindex( $image_old, $otc );
				$ntc = imagecolorallocate($image_old,$tc['red'],$tc['green'],$tc['blue']);
				imagefill( $this->image, 0, 0, $ntc );
				imagecolortransparent( $image_old, $ntc );
			}
			imagealphablending($image_old, false);
			imagesavealpha($image_old, true);
			
			return $image_old;
		} else if(is_resource($image) && get_resource_type($image) == "gd"){
			return $image;
		} else if(is_object($image)){
			return $image->getResource();
		}
		
	}
	
	/**
	 * Save image in format chosen by extension
	 * 
	 * @param resource $image
	 * @param string $filename
	 * @param int $quality
	 */
	private function imsave($image, $filename = NULL, $quality = NULL){
		
		$info = pathinfo($filename);
		$image_extension = $info['extension'];
		
		if($image_extension == "jpeg" || $image_extension == "jpg"){
			imagejpeg($image, $filename, $quality);
		} else if($image_extension == ("png")){
			imagepng($image, $filename, 0);
		} else if($image_extension == ("gif")){
			imagegif($image, $filename);
		}
		
		return file_exists($filename);
	}
	
	/**
	 * Write simple strings to image
	 * 
	 * @param string $text text to write on image
	 * @param int $x x-position of the text
	 * @param int $y y-position of the text
	 * @param int $size text size
	 * @param array $color text color
	 */
	public function text($text, $x=0, $y=0, $size = 5, $color = array(255, 255, 255)){
		
		imagestring($this->image, $size, $x, $y, $text, imagecolorallocate($this->image, $color[0], $color[1], $color[2]));
		
		return $this;
		
	}
	
	/**
	 * Merge two images
	 * 
	 * @param mixed $image
	 * @param int $x
	 * @param int $y
	 * @param int $opacity values from 0 to 100 
	 */
	public function merge($image, $x=0, $y=0, $opacity=100){
		
		$image_merge = $this->imcreate($image, false);
		
		$imInfo = $this->imsize($image_merge);
			
		imagecopymerge($this->image, $image_merge, $x, $y, 0, 0, $imInfo['width'], $imInfo['height'], $opacity);
		
		return $this;
		
	}
	
	/**
	 * Get image info
	 * 
	 * @param mixed $image
	 */
	private function imsize($image){
		
		if(is_string($image)){
			$get_info = getimagesize($image);
			$info = array('width' => $get_info[0], 'height' => $get_info[1]);
		} else if(is_resource($image)){
			if(get_resource_type($image) == 'gd'){
				$info = array('width' => imagesx($image), 'height' => imagesy($image));
			}
		}
		
		return $info;
		
	}
	
	/**
	 * Get image resource
	 */
	public function getResource(){
		
		return $this->image;
		
	}
	
}

?>