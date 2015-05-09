<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

abstract class Vf_Widget_Abstract
{
	protected $container = null;
	
	public function setCoreContainer()
	{
		$this -> container = Vf_Core::getContainer();
	}
}

?>