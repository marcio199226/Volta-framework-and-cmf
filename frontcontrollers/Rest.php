<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Controller.php');


class Vf_Rest_FrontController extends Vf_Controller
{	

	private $router = null;
	
	private $restful = null;

	public function __construct()
	{
		$this -> router = Vf_Router::instance();
		$this -> restful = new Vf_RestfulServer();
	}
	
	
	public function Index()
	{
		$request = Vf_Core::getContainer() -> request;
		$rest = $this -> _includeRoutes();
		$this -> _registerResource($rest);
		$this -> restful -> handle();
	}
	
	
	private function _registerResource($rest)
	{
		$componentName = $this -> router -> getSegment(1);
		$action = $this -> router -> getSegment(2);
		$restful = $rest[$componentName][$action];
		$className = 'Vf_'.$componentName.'_Rest_Component';
				
		$this -> restful -> setClassPath(DIR_COMPONENTS.$componentName.'/'.DIR_CTRL)
			-> setFileClassName($componentName.'_Rest.php')
			-> setClassName($className);
			
		if(is_string($restful['method']))
		{
			$this -> restful -> $restful['method']($componentName, $action);
		}
		else
		{
			foreach($restful['method'] as $method)
			{
				$this -> restful -> $method($componentName, $action);
			}
		}
		
		if(isset($restful['format']))
		{
			$this -> restful -> setResponseFormat($restful['format']);
		}
		
		if(isset($restful['status']))
		{
			$this -> restful -> setHttpStatus($restful['status']);
		}
		
		if(isset($restful['apiKey']))
		{
			$this -> restful -> setCheckApiKey($restful['apiKey']);
		}
		
		if(isset($restful['checkIp']))
		{
			$this -> restful -> setCheckIp($restful['checkIp']);
		}
		
		if(isset($restful['resource']))
		{
			$this -> restful -> setResource($restful['resource']);
		}
		
		if(isset($restful['roles']))
		{
			$this -> restful -> setRoles($restful['roles']);
		}
	}
	
	
	private function _includeRoutes()
	{
		$routerConfig = new Vf_Config('config.Router');
		$restful = array();
		foreach($routerConfig -> includes as $component)
		{
			if(Vf_Loader::existsFile(DIR_COMPONENTS.$component.'/urls.php'))
			{
				include(DIR_COMPONENTS.$component.'/urls.php');
				$restful = (sizeof($routes['rest']) > 0) ? array_merge($routerConfig -> rest, $routes['rest']) : $this -> routerConfig -> rest;
			}
		}
		return $restful;
	}
	
}

?>