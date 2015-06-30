<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormDecorator.php');
require_once(DIR_INTERFACES . 'IFormDecorator.php');

class Vf_Form_CustomTag_Decorator extends Vf_Form_Decorator implements IFormDecoratorSimple
{
	protected $position = self::PREPEND;
	
	protected $tagName = 'p';
	
	public function render()
	{
		return Vf_Html_Helper::tag($this->tagName, true, $this->getAttributes(), $this->getOption('text'));
	}
	
	
	public function setTagName($tag)
	{
		$this->tagName = $tag;
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