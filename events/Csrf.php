<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

class Vf_Csrf_Events
{
	/**
	* Generuje token csrf dla aplikacji
	* @access public 
	*/
	public function generateToken()
	{
		Vf_Core::getContainer()->csrf->csrf_token_generate();
	}
}

?>