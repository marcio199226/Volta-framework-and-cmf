<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


abstract class Vf_Form_Decorator
{
	const APPEND = 1;
	const PREPEND = 2;
	const WRAP = 3;
	const WITHIN = 4;
	
	protected $position = null;
	
	protected $tag = '';
	
	protected $attributes = array();
	
	protected $options = array();
	
	//this is for abstract method hasAppendHtml / appendHtml
	protected $appendHtml = false;
	//this is for abstract method hasPrependHtml / prependHtml
	protected $prependHtml = false;
	
	
	//workaround for php below 5.4 version so Vf_Form_TextBox_Widget::create()->method1()... for php < 5.4.x and for php >= 5.4 (new Vf_Form_TextBox_Widget())->method1()...
	public static function create()
	{
		return new static;
	}
	
	
	public function setAttribute($name, $value)
	{
		$this->attributes[$name] = $value;
		return $this;
	}
	
	
	public function setAttributes(array $attributes)
	{
		if (sizeof($attributes) > 0) {
			foreach ($attributes as $name => $value) {
				$this->attributes[$name] = $value;
			}
		}
		return $this;
	}
	
	
	public function getAttribute($name)
	{
		if (array_key_exists($name, $this->attributes)) {
			return $this->attributes[$name];
		}
		return null;
	}
	
	
	public function getAttributes()
	{
		return $this->attributes;
	}
	
	
	public function setTag($tag)
	{
		$this->tag = $tag;
		return $this;
	}
	
	
	public function getTag()
	{
		return $this->tag;
	}
	
	
	public function setPosition($position)
	{
		if (!is_integer($position)) {
			throw new InvalidArgumentException('Vf_Form_Decorator::setPosition - Position must be an integer value');
		}
		$this->position = $position;
		return $this;
	}
	
	
	public function getPosition()
	{
		return $this->position;
	}
	
	
	public function setOption($key, $value)
	{
		$this->options[$key] = $value;
		return $this;
	}
	
	
	public function setOptions($options)
	{
		if (!is_array($options)) {
			throw new InvalidArgumentException('Vf_Form_Decorator::setOptions - Options should be an array');
		}
		foreach ($options as $key => $value) {
			$this->options[$key] = $value;
		}
		return $this;
	}
	
	
	public function getOptions()
	{
		return $this->options;
	}
	
	
	public function getOption($name)
	{
		if(isset($this->options[$name])) {
			return $this->options[$name];
		}
		return false;
	}
	
	//for internal use only for decorators when we would implement appendHtml/prependHtml scope
	protected function hasAppendHtml()
	{
		return ($this->appendHtml !== false) ? true : false;
	}
	
	//for internal use only for decorators when we would implement appendHtml/prependHtml scope
	protected function hasPrependHtml()
	{
		return ($this->prependHtml !== false) ? true : false;
	}
	
	
	abstract public function appendHtml($append);
	abstract public function prependHtml($prepend);
}
?>