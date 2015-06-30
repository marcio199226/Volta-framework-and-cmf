<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormDecorator.php');
require_once(DIR_INTERFACES . 'IFormDecorator.php');

class Vf_Form_Label_Decorator extends Vf_Form_Decorator implements IFormDecoratorSimple, IFormDecoratorWrapped
{
	protected $position = self::PREPEND;
	
	public function render()
	{
		return Vf_Html_Helper::tag('label', true, $this->getAttributes(), $this->getOption('title'));
	}
	
	
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
		return Vf_Html_Helper::tag('label', true, $this->getAttributes());
		return ($this->hasPrependHtml()) ? Vf_Html_Helper::close('label') . $this->prependHtml : Vf_Html_Helper::close('label');
	}
	
	
	public function close()
	{
		return ($this->hasAppendHtml()) ? Vf_Html_Helper::close('label') . $this->appendHtml : Vf_Html_Helper::close('label');
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