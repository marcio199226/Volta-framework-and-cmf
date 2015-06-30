<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormWidget.php');
require_once(DIR_INTERFACES . 'IFormWidget.php');

class Vf_Form_Select_Widget extends Vf_Form_Widget implements IFormWidget
{
	protected $selected = false;
	
	protected $values = array();
	
	protected $multiple = false;
	
	protected $includeKey = false;
	
	protected $separator = ' ';
	
	protected $bootstrap = false;
	
	protected $toArray = false;
	
	protected $appendHtml = '</select>';
	
	protected $prependHtml = '<select %s>';
	
	
	public function setValues($values)
	{
		$this->values = $values;
		return $this;
	}
	
	
	public function getValues()
	{
		return $this->values;
	}
	
	
	public function setSelected($selected)
	{
		$this->selected = $selected;
		return $this;
	}
	
	
	public function isChecked($value)
	{
		if (!$this->checked) {
			return false;
		}
		return (in_array($value, $this->checked)) ? true : false;
	}
	
	
	public function getSelected()
	{
		return $this->selected;
	}
	
	
	public function isMultiple()
	{
		$this->multiple = true;
	}
	
	
	public function getMultiple()
	{
		return $this->multiple;
	}
	
	
	public function setSeparator($separator)
	{
		$this->separator = $separator;
		return $this;
	}
	
	
	public function getSeparator()
	{
		return $this->separator;
	}
	
	
	public function bootstrap()
	{
		$this->bootstrap = true;
		return $this;
	}
	
	
	public function isBootstrap()
	{
		return $this->bootstrap;
	}
	
	
	public function toArray()
	{
		$this->toArray = true;
		return $this;
	}
	
	
	public function includeKey()
	{
		$this->includeKey = true;
		return $this;
	}
	

	public function render()
	{
		if ($this->getMultiple()) {
			$this->setOption('multiple'); //$this->setAttribute('multiple', 'multiple') = multiple="multiple"
		}
		$select .= sprintf($this->prependHtml, implode(' ', array_map(function ($v, $k) { return $k . '=' . $v; }, $this->getAttributes(), array_keys($this->getAttributes()))));
		if (sizeof($this->values > 0)) {
			$options .= Vf_Forms_Helper::option($this->values, $this->getSelected(), $this->includeKey);
		}
		return Vf_Forms_Helper::select($this->getFieldName(), $options, $this->getAttributes());
	}
}
?>