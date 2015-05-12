<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

abstract class Vf_Controller
{
	/**
	* Wykonuje akcje danego kontrolera bez przekierowania
	* @access public 
	* @param string $controller nazwa kontrolera
	* @param string $action nazwa akcji
	* @param string $component nazwa komponentu
	* @return mixed string|boolean
	*/
	public function forward($controller, $action, $component = null)
	{
		if (Loader::existsFile(DIR_FRONT . $controller . '.php')) {
			require_once(DIR_FRONT . $controller . '.php');
			$ctrl = 'Vf_' . $controller . '_FrontController';
		} elseif (Loader::existsFile(DIR_COMPONENTS . $component . '/' . DIR_CTRL . $controller . '.php')) {
			require_once(DIR_COMPONENTS . $component . '/' . DIR_CTRL . $controller . '.php');
			$ctrl = 'Vf_' . $controller . '_Component';
		} else {
			throw new Vf_Controller_Exception("Nie znaleziono odpowiedniego pliku kontrolera: " . $controller);
		}
			
		if (class_exists($ctrl)) {
			$ctrlInstance = new $ctrl();
			
			if ($ctrlInstance instanceof Vf_Controller) {
				if (method_exists($ctrlInstance, $action)) {
					return $ctrlInstance->$action();
				} else {
					return false;
				}
			}
		} else {
			throw new Vf_Controller_Exception("Nie znaleziono klasy kontrolera: " . $ctrl);
		}
	}
	
	
	/**
	* Wykonuje przekierowanie
	* @access public 
	* @param string $redirect
	*/
	public function redirect($redirect)
	{
		Vf_Event::runEvent('system.redirect');
		header("Location: " . $redirect);
		exit;
	}
	
	
	/**
	* Wykonuje przekierowanie z opoznieniem
	* @access public 
	* @param string $url
	* @param int $time sekundy
	*/
	public function refresh($url, $time = 1)
	{
		Vf_Event::runEvent('system.redirect');
		header('Refresh: ' . $time . '; url=' . $url);
		exit;
	}
	

	abstract public function Index();
}

?>