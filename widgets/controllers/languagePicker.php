<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES.'IWidget.php');

class Vf_languagePicker_Widget implements IWidget
{	
	public function display()
	{
		$locales = new Vf_Language_Model();
		
		$view = new Vf_View('languagePicker', 'widget');
		$view -> loadHelper('Uri');
		$view -> locales = $locales -> getLocales();
		return $view -> render();
	}
}

?>