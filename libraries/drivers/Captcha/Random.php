<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_INTERFACES . 'ICaptcha.php');

class Vf_Captcha_Random_Adapter implements ICaptcha 
{
	public function getCode($sessionName) 
	{
		$token =  substr(sha1(time() . rand(1, 10000)), 0, 6);
		$_SESSION[$sessionName] = $token;	
		return $token;	  
	}
}

?>