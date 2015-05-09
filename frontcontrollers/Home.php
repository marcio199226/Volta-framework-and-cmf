<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Controller.php');


class Vf_Home_FrontController extends Vf_Controller
{
	public function Index()
	{
		$router = Vf_Core::getContainer() -> router;
		$config = Vf_Core::getContainer() -> config;
		$page = $router -> getFrontController();
		//$module = (method_exists($this, $router -> getFrontControllerAction())) ? $router -> getFrontControllerAction() : $config -> frontAction;
		$module = $router -> getFrontControllerAction();
		$componentModel = new Vf_Component_Model();
		
		try
		{
			$components = $componentModel -> getComponents($page, $module);
		}
		catch(Exception $e)
		{
			$components = $componentModel -> getComponents($page, $config -> frontAction);
		}
		
		$component = new Vf_Component();
		$component -> toLoad($components);
		$rendered = $component -> display();
	
		$view = new Vf_View('templates/Home');
		$view -> component = $rendered;
		return $view -> render();
	}
	
	//dac do klasy Vf_Controller
	public function language()
	{
		$uri = Vf_Core::getContainer() -> router;
		$lang = new Vf_Language();
		$lang -> get() -> setLang($uri -> getSegment(2));
		$this -> redirect(Vf_Core::getContainer() -> request -> referer());
	}
}

?>