<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormDecorator.php');
require_once(DIR_INTERFACES . 'IFormDecorator.php');

class Vf_Form_Html5JqueryValidation_Decorator extends Vf_Form_Decorator implements IFormDecoratorSimple, IFormDecoratorEnhanced
{
	protected $position = array(self::WITHIN, self::APPEND);
	
	protected $rules = array();
	
	public function append()
	{
		$append = '';
		if($this->getOption('html5')) {
			$attr = $this->getAttributes();
			if (sizeof($attr) > 0) {
				foreach ($attr as $name => $value) {
					$append .= ' ' . $name . '="' . $value . '"';
				}
			}
		}
		return (!empty($append)) ? $append : null;
	}
	
	
	public function render()
	{
		$validation = '';
		if ($this->getOption('jquery')) {
			//load jquery validate plugin and generate options
			$validation .= '
				{@ js_inline @}
					$(document).ready(function() {
						$(\'#' . $this->getOption('formID') . '\').validate();
					});
				{@ end @}
			';
		}
		return $validation;
	}
	
	
	public function setRules(array $rules)
	{
		
	}
	
	
	public function setMessages(array $messages)
	{
	}
	
	
	public function evalJs($js)
	{
	
	}
	
	
	//for implement if we would append / prepend any html or text to this decorator look at label decorator
	public function appendHtml($append) {}
	
	public function prependHtml($prepend) {}
}
?>