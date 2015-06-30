<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


abstract class Vf_Form_Widget
{
	protected $name = null;

	protected $attributes = array();
	
	protected $value = null;
	
	protected $decorators = array();
	
	protected $options = array();
	
	protected $settings = array();
	
	protected $form = null;
	
	//this is for method hasAppendHtml / appendHtml
	protected $appendHtml = false;
	//this is for method hasPrependHtml / prependHtml
	protected $prependHtml = false;
	
	protected $interfaces = array(
		Vf_Form_Decorator::APPEND => 'IFormDecoratorSimple',
		Vf_Form_Decorator::PREPEND => 'IFormDecoratorSimple',
		Vf_Form_Decorator::WRAP => 'IFormDecoratorWrapped',
		Vf_Form_Decorator::WITHIN => 'IFormDecoratorEnhanced'
	);
	
	
	//workaround for php below 5.4 version so Vf_Form_TextBox_Widget::create()->method1()... for php < 5.4.x and for php >= 5.4 (new Vf_Form_TextBox_Widget())->method1()...
	public static function create(array $triggers = array())
	{
		$element = new static;
		if (sizeof($triggers) > 0) {
			$element->setTriggers($triggers);
		}
		return $element;
	}
	
	
	public function setTriggers($options)
	{
		if (is_array($options) && sizeof($options) > 0) {
			foreach ($options as $trigger => $args) {
				if (method_exists($this, $trigger)) {
					$this->{$trigger}($args);
				}
			}
		}
	}
	
	
	public function loadDecorators($decorators)
	{
		if (is_array($decorators)) {
			foreach ($decorators as $decorator) {
				if (file_exists(DIR_DRIVERS . 'Form/decorators/' . $decorator . '.php')) {
					require_once(DIR_DRIVERS . 'Form/decorators/' . $decorator . '.php');
				}
			}
		} elseif (file_exists(DIR_DRIVERS . 'Form/decorators/' . $decorators . '.php')) {
			require_once(DIR_DRIVERS . 'Form/decorators/' . $decorators . '.php');
		}
		return $this;
	}
	
	
	public function setFieldName($name)
	{
		$this->name = $name;
		return $this;
	}
	
	
	public function getFieldName()
	{
		return ($this->name !== null) ? $this->name : $this->attributes['name'];
	}
	
	
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}
	
	
	public function getValue()
	{
		return ($this->value !== null) ? $this->value : $this->attributes['value'];
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
		return false;
	}
	
	
	public function getAttributes($mergeOptions = true)
	{
		if (!isset($this->attributes['name'])) {
			$this->attributes['name'] = $this->name;
		}
		if (!isset($this->attributes['value']) && $this->value !== null) {
			$this->attributes['value'] = $this->value;
		}
		return (sizeof($this->options) > 0 && $mergeOptions) ? array_merge($this->attributes, $this->options) : $this->attributes;
	}
	
	
	public function addDecorator($decorator)
	{
		$this->decorators[] = $decorator;
		return $this;
	}
	
	
	public function addDecorators($decorators)
	{
		if (is_array($decorators)) {
			foreach ($decorators as $decorator) {
				$this->decorators[] = $decorator;
			}
		} else {
			throw new InvalidArgumentException('Vf_Form_Widget::addDecorators - $decorators should be an array');
		}
		return $this;
	}
	
	
	public function getDecorators()
	{
		return $this->decorators;
	}
	
	
	public function setOption($value)
	{
		$this->options[] = $value;
		return $this;
	}
	
	
	public function getOptions()
	{
		return implode(' ', $this->options);
	}
	
	
	public function setForm(Vf_Form $form)
	{
		$this->form = $form;
		return $this;
	}
	
	//tutaj mamy dostep do bledow formularzy wiec mozemy je w jakis sposob bindowac do elementu za pomoca nowego dekoratora np error ktory bedzie zawieral blad i bedzie typu APPEND
	public function getForm()
	{
		return $this->form;
	}
	
	
	public function getSettings()
	{
		return $this->settings;
	}
	
	
	public function getSetting($name)
	{
		if (isset($this->settings[$name])) {
			return $this->settings[$name];
		}
		return false;
	}
	
	
	public function setSetting($name, $value)
	{
		$this->settings[$name] = $value;
		return $this;
	}
	
	
	//if $this->form->hasErrors() to dodajemy dekorator label lub jakis inny do elementu jako APPEND z trescia bledu
	public function display()
	{
		$amountOfDecorators = sizeof($this->decorators);
		$element = $this->render();
		$widget = '';
		$wrapped = $this->getWrappedDecorators();
		if ($amountOfDecorators > 0) {
			foreach ($this->decorators as $decorator) {
				if(!is_array($decorator->getPosition())) {
					if (!$this->hasInterface($decorator)) {
						throw new Vf_Form_Decorator_Exception("Vf_Form_Widget::display decorator {get_class($decorator)} must implement {$this->interfaces[$decorator->getPosition()]} interface");
					}
					if ($decorator->getPosition() == $decorator::WITHIN) {
						$toAppend = $decorator->append();
						if (is_string($toAppend)) {
							$this->setOption($toAppend);
						} elseif (is_array($toAppend)) {
							$this->setAttributes($toAppend);
						}
						$element = $this->render();
						//if there is only WITHIN decorator for current field render it and return
						if ($amountOfDecorators == 1) {
							return $element;
						}
					}
					if ($decorator->getPosition() == $decorator::APPEND) {
						$widget .= $element;
						$widget .= $decorator->render();
					} elseif ($decorator->getPosition() == $decorator::PREPEND) {
						$widget .= $decorator->render();
						$widget .= $element;	
					} elseif ($decorator->getPosition() == $decorator::WRAP) {
						if ($wrapped['count'] > 1) {
							$c = 1;
							foreach ($wrapped['decorators'] as $wrapDecorator) {
								if($wrapDecorator->getPosition() == $wrapDecorator::WRAP) {
									if ($wrapped['count'] == $c) { //poprawic tu i powinno dzialac
										$wrapDecorator->attach($widget);
									} else {
										$wrapDecorator->attach($element);
									}
									$widget = $wrapDecorator->open();
									$widget .= $wrapDecorator->getContent();
									$widget .= $wrapDecorator->close();
								}
								$c++;
							}
						} else {
							 if ($amountOfDecorators <= 1) {
								$decorator->attach($element);
							} else {
								$decorator->attach($widget);
							}
							$widget = $decorator->open();
							$widget .= $decorator->getContent();
							$widget .= $decorator->close();
						}
					}
				} else {
					foreach ($decorator->getPosition() as $position) {
						if (!$this->hasInterface($decorator, $position)) {
							throw new Vf_Form_Decorator_Exception("Vf_Form_Widget::display decorator {get_class($decorator)} must implement {$this->interfaces[$position]} interface");
						}
						if ($position == $decorator::WITHIN) {
							$toAppend = $decorator->append();
							if (is_string($toAppend)) {
								$this->setOption($toAppend);
							} elseif (is_array($toAppend)) {
								$this->setAttributes($toAppend);
							}
							$element = $this->render();
						}
						if ($position == $decorator::APPEND) {
							//$widget .= $element;
							$widget .= $decorator->render();
						} elseif ($position == $decorator::PREPEND) {
							$widget .= $decorator->render();
							//$widget .= $element;							
						} elseif ($position == $decorator::WRAP) {
							$wdt = ($amountOfDecorators <= 1) ? $element : $widget;
							$decorator->attach($wdt);
							$widget = $decorator->open();
							$widget .= $decorator->getContent();
							$widget .= $decorator->close();
						}
					}
				}
			}
			return $widget;
		}
		return $element;
	}
	
	
	protected function hasInterface($decorator, $position = null)
	{
		$pos = ($position === null) ? $decorator->getPosition() : $position;
		return ($decorator instanceof $this->interfaces[$pos]) ? true : false;
	}
	
	protected function getWrappedDecorators()
	{
		$wrapped = 0;
		$decWrapped = array();
		foreach ($this->decorators as $decorator) {
			if($decorator instanceof IFormDecoratorWrapped) {
				if (is_array($decorator->getPosition()) && in_array($decorator::WRAP, $decorator->getPosition())) {
					$wrapped++;
					$decWrapped[] = $decorator;
				} elseif ($decorator->getPosition() == $decorator::WRAP) {
					$wrapped++;
					$decWrapped[] = $decorator;
				} else {
					continue;
				}
			}
		}
		return array('count' => $wrapped, 'decorators' => $decWrapped);
	}
	
	
	//for internal use only for widget when we would implement appendHtml/prependHtml scope
	protected function hasAppendHtml()
	{
		return ($this->appendHtml !== false) ? true : false;
	}
	
	//for internal use only for widget when we would implement appendHtml/prependHtml scope
	protected function hasPrependHtml()
	{
		return ($this->prependHtml !== false) ? true : false;
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