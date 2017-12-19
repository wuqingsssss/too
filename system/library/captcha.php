<?php
class Captcha {
	protected $code;
	protected $width = 109;
	protected $height = 30;

	function __construct() { 
		$this->code =  (string)rand(1000,9999);//substr(sha1(mt_rand()), mt_rand(1,30), 6); 
	}

	function getCode(){
		return $this->code;
	}

	function showImage() {
        $image = imagecreatetruecolor($this->width,$this->height);

        $width = imagesx($image); 
        $height = imagesy($image);
		
        $black = imagecolorallocate($image, 0, 0, 0); 
        $white = imagecolorallocate($image, 255, 255, 255); 
        $red = imagecolorallocatealpha($image, 255, 0, 0, 75); 
        $green = imagecolorallocatealpha($image, 0, 255, 0, 75); 
        $blue = imagecolorallocatealpha($image, 0, 0, 255, 75); 
         
        imagefilledrectangle($image, 0, 0, $width, $height, $white); 
         
        imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $red); 
        imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $green); 
        imagefilledellipse($image, ceil(rand(5, 145)), ceil(rand(0, 35)), 30, 30, $blue); 

        imagefilledrectangle($image, 0, 0, $width, 0, $black); 
        imagefilledrectangle($image, $width - 1, 0, $width - 1, $height - 1, $black); 
        imagefilledrectangle($image, 0, 0, 0, $height - 1, $black); 
        imagefilledrectangle($image, 0, $height - 1, $width, $height - 1, $black); 
         
        imagestring($image, 10, intval(($width - (strlen($this->code) * 9)) / 2),  intval(($height - 15) / 2), $this->code, $black);
        
        for($i=0; $i<3; $i++)
        {
        	//  $t = imagecolorallocate($img, rand(0, 255),rand(0, 255),rand(0, 255));
        	// 画线
        	imageline($image, 0, rand(0, $this->height*1.2), rand(70,$this->width*1.2), rand(0, 20), $blue);
        }
	
        $t = imagecolorallocate($image, rand(0, 255),rand(0, 255),rand(0, 255));
        // 为图片添加噪点
        for($i=0; $i<200; $i++)
        {
            imagesetpixel($image, rand(1, 100), rand(1, 30), $red);
        }
        
		header('Content-type: image/jpeg');
		
		imagejpeg($image);
		
		imagedestroy($image);		
	}
}
?>