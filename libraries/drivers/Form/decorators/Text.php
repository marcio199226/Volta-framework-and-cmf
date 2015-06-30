<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormDecorator.php');
require_once(DIR_INTERFACES . 'IFormDecorator.php');

class Vf_Form_Text_Decorator extends Vf_Form_Decorator implements IFormDecoratorSimple
{
	protected $position = self::APPEND;
	
	public function render()
	{
		return $this->getOption('text');
	}
	
	
	public function appendHtml($append)
	{
		$this->appendHtml = $append;
		return $this;
	}
	
	
	public function prependHtml($prepend)
	{
		$this->prependHtml = $prepend;
		return $this;
	}
}
?>