<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Controller_Exception extends Exception { }

abstract class Vf_Controller
{
	
	public function forward($controller, $action, $component = null)
	{
		if(Loader::existsFile(DIR_FRONT.$controller.'.php'))
			require_once(DIR_FRONT.$controller.'.php');
		else if(Loader::existsFile(DIR_COMPONENTS.$component.'/'.DIR_CTRL.$controller.'.php'))
			require_once(DIR_COMPONENTS.$component.'/'.DIR_CTRL.$controller.'.php');
		else
			throw new Vf_Controller_Exception("Nie znaleziono odpowiedniego pliku kontrolera: ".$controller);
			
		if(class_exists($controller))
		{
			$ctrl = new $controller();
			
			if($ctrl instanceof Vf_Controller)
			{
				if(method_exists($ctrl, $action))
				{
					return $ctrl -> $action();
				}
				else
				{
					return false;
				}
			}
		}	
		else
			throw new Vf_Controller_Exception("Nie znaleziono klasy kontrolera: ".$controller);
	}
	
	
	public function redirect($redirect)
	{
		Vf_Event::runEvent('system.redirect');
		header("Location: ".$redirect);
		exit;
	}
	
	
	public function refresh($url, $time = 1)
	{
		Vf_Event::runEvent('system.redirect');
		header('Refresh: '.$time.'; url='.$url);
		exit;
	}
	

	abstract public function Index();
}

?>