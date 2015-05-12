<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Plugin
{
	protected $settings;
	
	protected $container = null;
	
	public function configure($settings)
	{
		$this->settings = $settings;
		$this->container = Vf_Core::getContainer();
	}
}

?>