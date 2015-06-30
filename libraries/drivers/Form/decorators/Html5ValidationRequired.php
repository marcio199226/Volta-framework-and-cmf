<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormDecorator.php');
require_once(DIR_INTERFACES . 'IFormDecorator.php');

class Vf_Form_Html5ValidationRequired_Decorator extends Vf_Form_Decorator implements IFormDecoratorEnhanced
{
	protected $position = self::WITHIN;
	
	public function append()
	{
		return 'required';
	}
	
	
	//for implement if we would append / prepend any html or text to this decorator look at label decorator
	public function appendHtml($append) {}
	
	public function prependHtml($prepend) {}
}
?>