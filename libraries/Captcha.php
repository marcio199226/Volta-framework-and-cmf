<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/


class Vf_Captcha 
{
	protected $config = null;
  
	protected $adapter = null;
	
	protected $height = 0;
	
	protected $width = 0;
  
	private $img;
  
  
	public function __construct() 
	{
		$this->config = new Vf_Config('config.Captcha');
		$this->width = $this->config->width;
		$this->height = $this->config->height;
		$adapterName = $this->config->driver;
		$adapterClass = 'Vf_Captcha_' . $adapterName . '_Adapter';
	  
		if (file_exists(DIR_DRIVERS . 'Captcha/' . $adapterName . '.php')) {
			require_once(DIR_DRIVERS . 'Captcha/' . $adapterName . '.php');
	    }
		$this->adapter = new $adapterClass();
	}
  
  
	protected function imageType($filename) 
	{
		$type = explode('.', $filename);
		$type = $type[count($type)-1];
	 
		switch ($type) {
			case 'png':
				return 'png';
				break;
	 
			case 'gif':
				return 'gif';
				break;

			case 'jpg':
				return 'jpeg';
				break;	

			case 'jpeg':
				return 'jpeg';
				break;				  				  
		}
	}
  
  
	public function setSize($width, $height) 
	{
		$this->width = $width;
		$this->height = $height;
	}
  
  
	protected function CreateCaptcha($code) 	
	{
	  
		$this->img = @imagecreate($this->width, $this->height);
		$white = imagecolorallocate($this->img, 255, 255, 255); 
		$black = imagecolorallocate($this->img, 180, 180, 180);
		imagefill($this->img, 0, 0, $white); 
		$x = 20;

		for ($y = 0; $y < 5; $y++) {
			imageline($this->img, rand(0, $this->width), rand(0, $this->width), rand(0, $this->height), rand(0, $this->height), rand(0, 200));  
	    }
		for($z = 0; $z < strlen($code); $z++) {
			imagestring($this->img, rand(3,5), $x,rand(5,15), $code[$z], imagecolorallocate($this->img, rand(0, 200), rand(0, 200), rand(0, 200)));
			$x += 5;
		}
	}
  
  
	protected function RenderCaptcha() 
	{
		$func = 'image'.$this->config->captchaType;
		$func($this->img, $this->config->imgCreate);
		imagedestroy($this->img); 
	}
  
  
	public function render() 
	{
		if (Vf_Loader::existsFile($this->config->imgCreate)) {
			unlink($this->config->imgCreate);  
		}
		if ($this->adapter instanceof ICaptcha) {	
			$code = $this->adapter->getCode($this->config->session_name);
			$this->CreateCaptcha($code);
			$this->RenderCaptcha();
		}
	}
  
  
	public function getCaptcha() 
	{  
		$this->render(); 
		return '<img src="' . $this->config->imgTagPath . '" height="' . $this->height . '" width="' . $this->width . '" alt="captcha" />';
	}
  
  
	public function checkCaptcha($post) 
	{
		return (Vf_Core::getContainer()->request->session($this->config->session_name) == $post) ? true : false;
	}
}

?>
