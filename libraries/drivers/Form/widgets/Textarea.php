<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'FormWidget.php');
require_once(DIR_INTERFACES . 'IFormWidget.php');

class Vf_Form_Textarea_Widget extends Vf_Form_Widget implements IFormWidget
{
	public function render()
	{
		return Vf_Forms_Helper::textarea($this->getFieldName(), $this->getValue(), $this->getAttributes());
	}
}
?>