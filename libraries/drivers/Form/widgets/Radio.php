<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormWidget.php');
require_once(DIR_INTERFACES . 'IFormWidget.php');

class Vf_Form_Radio_Widget extends Vf_Form_Widget implements IFormWidget
{
	protected $checked = false;
	
	protected $values = array();
	
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
		return ($value == $this->checked) ? true : false;
	}
	
	
	public function getChecked()
	{
		return $this->checked;
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
	
	
	public function render()
	{
		//look at checkbox widget
		if (sizeof($this->values) == 0) {
			$label = ($this->getSetting('label')) ? $this->getSetting('label') : $this->getValue();
			return Vf_Forms_Helper::radio($this->getFieldName(), $this->getValue(), $this->isChecked($this->getValue()), $this->getAttributes()) . $label;
		} else if (sizeof($this->values) == 1) {
			$this->setAttribute('value', $this->values[0]['value']);
			$checkbox = Vf_Forms_Helper::radio($this->getFieldName(), $this->getAttribute('value'), $this->isChecked($this->values[0]['value']), $this->getAttributes());
			$label = (isset($this->values[0]['label'])) ? $this->values[0]['label'] : $this->values[0]['value'];
			if ($this->getUseDecorators() && !$this->isBootstrap()) {
				return $checkbox;
			} elseif ($this->getUseDecorators() && $this->isBootstrap()) {
				return $checkbox . $label;
			} else {
				return $checkbox . $label;
			}
		} elseif (sizeof($this->values) > 1) {
			if ($this->getUseDecorators()) {
				$this->createCollection($this);
				foreach ($this->collections as $collection) {
					$label = ($collection->getSetting('label')) ? $collection->getSetting('label') : $collection->getValue();
					$collection->loadDecorators(array('Label', 'BootstrapFormGroup'));
					if (!$this->isBootstrap()) {
						$collection->addDecorator(
							Vf_Form_Label_Decorator::create()
								->setPosition(Vf_Form_Decorator::APPEND)
								->setOption('title', $label)
						);
						$collection->addDecorator(Vf_Form_BootstrapFormGroup_Decorator::create()->setAttribute('class', 'form-group'));
					} else {
						$collection->addDecorator(Vf_Form_Label_Decorator::create()->setPosition(Vf_Form_Label_Decorator::WRAP));
						$collection->addDecorator(Vf_Form_BootstrapFormGroup_Decorator::create()->setAttribute('class', 'radio'));
					}
					$checkboxes .= $collection->display();
				}
			} else {
				foreach ($this->values as $k => $v) {
					//$this->setValue($v['value']);
					$this->setAttribute('value', $v['value']);
					$label = (isset($v['label']) ? $v['label'] : $v['value']);
					$checkboxes[] = Vf_Forms_Helper::radio(null, null, $this->isChecked($v['value']), $this->getAttributes()) . $label;
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