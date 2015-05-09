<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES.'ICaptcha.php');

class Vf_Captcha_Math_Adapter implements ICaptcha 
{
	public function getCode($sessionName) 
	{
		$number[0] = rand(1, 50);
		$number[1] = rand(1, 50);
		$_SESSION[$sessionName] = array_sum($number);	
		return $number[0].' + '.$number[1].' = ';	  
	}
}

?>