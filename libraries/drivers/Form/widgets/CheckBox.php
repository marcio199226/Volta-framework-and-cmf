<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormWidget.php');
require_once(DIR_INTERFACES . 'IFormWidget.php');

class Vf_Form_CheckBox_Widget extends Vf_Form_Widget implements IFormWidget
{
	protected $checked = false;
	
	protected $values = array();
	
	protected $multiple = false;
	
	protected $separator = ' ';
	
	protected $useDecorators = false;
	
	protected $collections = false;
	
	protected $bootstrap = false;
	
	protected $toArray = false;
	
	
	public function setValues($values)
	{
		$this->values = $values;
		return $this;
	}
	
	
	public function getValues()
	{
		return $this->values;
	}
	
	
	public function setChecked($checked)
	{
		$this->checked = $checked;
		return $this;
	}
	
	
	public function isChecked($value)
	{
		if (!$this->checked) {
			return false;
		}
		return (in_array($value, $this->checked)) ? true : false;
	}
	
	
	public function getChecked()
	{
		return $this->checked;
	}
	
	
	public function isMultiple($multi)
	{
		$this->multiple = $multi;
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
	
	
	public function applyDecorators()
	{
		$this->useDecorators = true;
		return $this;
	}
	
	
	public function getUseDecorators()
	{
		return $this->useDecorators;
	}
	
	
	public function isCollection()
	{
		return ($this->collections !== false) ? true : false;
	}
	
	
	public function setCollection()
	{
		$this->collections = new SplObjectStorage();
		return $this;
	}
	
	
	public function getCollections()
	{
		return $this->collections;
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
	

	//make decorators for inline too with new method inline()
	public function render()
	{
		//without collection, without decorators and without populate widgets through triggers
		if (sizeof($this->values) == 0) {
			$label = ($this->getSetting('label')) ? $this->getSetting('label') : $this->getValue();
			return Vf_Forms_Helper::checkbox($this->getFieldName(), $this->getValue(), $this->isChecked($this->getValue()), $this->getAttributes()) . $label;
		} else if (sizeof($this->values) == 1) { //step1 with collection and populate widget with triggers
			$this->setAttribute('value', $this->values[0]['value']);
			$checkbox = Vf_Forms_Helper::checkbox($this->getFieldName(), $this->getAttribute('value'), $this->isChecked($this->values[0]['value']), $this->getAttributes());
			$label = (isset($this->values[0]['label'])) ? $this->values[0]['label'] : $this->values[0]['value'];
			if ($this->getUseDecorators() && !$this->isBootstrap()) {
				return $checkbox;
			} elseif ($this->getUseDecorators() && $this->isBootstrap()) {
				return $checkbox . $label;
			} else {
				return $checkbox . $label;
			}
		} elseif (sizeof($this->values) > 1) { //step2 if we use collections and triggers and we have more than 1 element
			if ($this->getMultiple()) {
				$fName = $this->getFieldName();
				$this->setFieldName($fName . '[]');
			}
			if ($this->getUseDecorators()) {
				$this->createCollection($this); //create collection of checkbox objects
				foreach ($this->collections as $collection) {
					$label = ($collection->getSetting('label')) ? $collection->getSetting('label') : $collection->getValue();
					$collection->loadDecorators(array('Label', 'BootstrapFormGroup', 'Text'));
					if (!$this->isBootstrap()) {
						$collection->addDecorator(
							Vf_Form_Label_Decorator::create()
								->setPosition(Vf_Form_Decorator::APPEND)
								->setOption('title', $label)
						);
						$collection->addDecorator(Vf_Form_BootstrapFormGroup_Decorator::create()->setAttribute('class', 'form-group'));
					} else {
						$collection->addDecorator(Vf_Form_Label_Decorator::create()->setPosition(Vf_Form_Label_Decorator::WRAP));
						$collection->addDecorator(Vf_Form_BootstrapFormGroup_Decorator::create()->setAttribute('class', 'checkbox'));
						//$collection->addDecorator(Vf_Form_Text_Decorator::create()->setOption('text', $label));
					}
					$checkboxes .= $collection->display(); //when we call this method for each element we recall render metohd for each checkbox element and go to step1
				}
			} else {
				foreach ($this->values as $k => $v) {
					$this->setAttribute('value', $v['value']);
					$label = (isset($v['label']) ? $v['label'] : $v['value']);
					$checkboxes[] = Vf_Forms_Helper::checkbox(null, null, $this->isChecked($v['value']), $this->getAttributes()) . $label;
				}
			}
			if ($this->getUseDecorators()) {
				return $checkboxes;
			} else {
				return ($this->toArray) ? $checkboxes : implode($this->getSeparator(), $checkboxes);
			}
		}
	}
	
	
	private function createCollection($self)
	{
		foreach ($this->values as $k => $v) {
			$wdt = new $self;
			$wdt->setFieldName($this->getFieldName())
				->setValues(array($v))
				->setValue($v['value'])
				->setSetting('label', (isset($v['label']) ? $v['label'] : $v['value']))
				->setChecked($this->getChecked())
				->applyDecorators();
				
			if ($this->isBootstrap()) {
				$wdt->bootstrap();
			}
			$this->collections->attach($wdt);
		}
	}
}
?>