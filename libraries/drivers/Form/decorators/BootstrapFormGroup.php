<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormDecorator.php');
require_once(DIR_INTERFACES . 'IFormDecorator.php');

class Vf_Form_BootstrapFormGroup_Decorator extends Vf_Form_Decorator implements IFormDecoratorWrapped
{
	protected $position = self::WRAP;
	
	protected $content = null;
	
	
	public function attach($content)
	{
		$this->content = $content;
	}
	
	
	public function getContent()
	{
		return $this->content;
	}
	
	
	public function open()
	{
		return Vf_Html_Helper::tag('div', true, $this->getAttributes());
	}
	
	public function close()
	{
		return Vf_Html_Helper::close('div');
	}
	
	//for implement if we would append / prepend any html or text to this decorator look at label decorator
	public function appendHtml($append) {}
	
	public function prependHtml($prepend) {}
}
?>