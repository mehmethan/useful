<?php 

class Image{

	private static $instance;
	private $image = '';
	private $newImage = '';

	const RESIZE_EXACT = 1;
	const RESIZE_PORTRAIT = 2;
	const RESIZE_LANDSCAPE = 3;
	const RESIZE_AUTO = 4;
	

	protected function __construct($imgPath){
		$this->image = $imgPath;	
	}
	
	public static function get($imgFilePath){
		self::$instance = new self($imgFilePath);
		return self::$instance;
	}

	public static function download($imgUrl, $destination = ''){
		$img = file_get_contents($imgUrl);
		$imgName = basename($imgUrl);
		if(!empty($destination)){
			if(!is_dir(dirname($destination))){
				mkdir(dirname($destination), 0755, true);
			}
			$imgName = $destination;
		}
		file_put_contents($imgName, $img);

		self::$instance = new self($imgName);
		return self::$instance;
	}

	private function readImage() {
		switch ($this->type()) {
			case "jpg" :
				$original = imagecreatefromjpeg($this->image);
				return $original;
				break;

			case "png" :
				$original = imagecreatefrompng($this->image);
				return $original;
				break;

			case "gif" :
				$original = imagecreatefromgif($this->image);
				return $original;
				break;

			default :
				return false;
				break;
		}
	}

	public function type(){
		$size = getimagesize($this->image);
		$imageType = $size [2];

		if ($imageType != "1" and $imageType != "2" and $imageType != "3") {
			return false;
		} else {
			switch ($imageType) {
				case 1 :
					return "gif";
					break;

				case 2 :
					return "jpg";
					break;

				case 3 :
					return "png";
					break;

				default :
					return false;
					break;
			}
		}
	}

	public function size(){
		$size = getimagesize($this->image);
		return [$size[0], $size[1]];
	}

	public function resize($w, $h, $option = self::RESIZE_EXACT){
		$original = $this->readImage();
		$original_size = getimagesize($this->image);
		$ratio = $original_size[0] / $original_size[1];

		switch ($option) {
			case 1:
				$width = $w;
				$height = $h;
				break;
			case 2:
				$width = $h * $ratio;
				$height = $h;
				break;
			case 3:
				$width = $w;
				$height = $w * $ratio;
				break;
			case 4:
				if($original_size[1] < $original_size[0]){
					$width = $w;
					$height = $w * $ratio;
				}else if($original_size[1] > $original_size[0]) {
					$width = $h * $ratio;
					$height = $h;
				}else{
					if ($h < $w){
					    $width = $w;
					    $height= $w * $ratio;
					}else if($h > $w){
					    $width = $h * $ratio;
					    $height= $h;
					}else{
					    $width = $w;
					    $height = $h;
					}
				}
				break;
			default:
				break;
		}
		settype($width, 'integer');
		settype($height, 'integer');

		$this->newImage = imagecreatetruecolor($width, $height);
		imagecopyresampled($this->newImage, $original, 0, 0, 0, 0, $width, $height, $original_size[0], $original_size[1]);
		print_r($this->newImage);
		return self::$instance;
	}

	public function crop(){

	}

	public function save($destination = '', $quality = 100){
		if(!empty($destination)){
			if(!is_dir(dirname($destination))){
				mkdir(dirname($destination), 0755, true);
			}
		}else{
			$destination = $this->image;
		}

		switch ($this->type()) {
			case 'jpg':
				 if (imagetypes() & IMG_JPG) {
				    if (imagejpeg($this->newImage, $destination, $quality) === false) {
						trigger_error("File creation problem: " . $destination);
						return false;
					} else {
						return true;
					}
				}
				break;
			case 'png':
				$scaleQuality = round(($quality/100) * 9);
				$invertScaleQuality = 9 - $scaleQuality;
				if (imagetypes() & IMG_PNG) {
				    if (imagepng($this->newImage, $destination, $invertScaleQuality) === false) {
						trigger_error("File creation problem: " . $destination);
						return false;
					} else {
						return true;
					}
				}
				break;
			case 'gif':
				if (imagetypes() & IMG_GIF) {
				   if (imagegif($this->newImage, $destination) === false) {
						trigger_error("File creation problem: " . $destination);
						return false;
					} else {
						return true;
					}
				}
				break;
			default:
				return false;
				break;
		}
	}

}