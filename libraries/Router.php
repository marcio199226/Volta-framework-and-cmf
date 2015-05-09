<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_Router
{

	protected $frontController = null;
	
	protected $frontControllerAction = null;
	
	protected $componentAction = null;
	
	protected $params = array();
	
	protected $currentRoute = null;
	
	protected $route = false;
	
	private $routerConfig = null;
	
	private $config = null;
	
	private static $instance = null;
	
	
	public static function &instance()
	{
		if(self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	public function __construct()
	{
		$cache = new Vf_Cache();
		
		$this -> config = new Vf_Config(DIR_CONFIG, 'Xml');
		$this -> routerConfig = new Vf_Config('config.Router');
		
		if($urls = $cache -> getCache('routes', 86400))
		{
			$this -> routerConfig -> static_routes = $urls['static'];
			$this -> routerConfig -> dynamic_routes = $urls['dynamic'];
		}
		else
		{
			$this -> includeRoutes();
			$toCacheRoutes['static'] = $this -> routerConfig -> static_routes;
			$toCacheRoutes['dynamic'] = $this -> routerConfig -> dynamic_routes;
			$cache -> setCache('routes', $toCacheRoutes, 86400);
		}
		
		$static = array_keys($this -> routerConfig -> static_routes);
		
		if(strpos($_SERVER['REQUEST_URI'], 'index.php') === false)
			$_SERVER['REQUEST_URI'] = 'index.php';
		if(substr($_SERVER['REQUEST_URI'], -1) == '/')
			$_SERVER['REQUEST_URI'] = 'index.php';
			
		$path = explode($this -> routerConfig -> self, $_SERVER['REQUEST_URI']);
		$this -> currentRoute = $path[count($path)-1];

		if($this -> currentRoute == 'index.php')
		{
			$this -> setFrontController($this -> config -> frontController);
			$this -> setFrontControllerAction($this -> config -> frontAction);
			$this -> setComponentAction($this -> config -> componentAction);
		}
		else if($this -> dynamicRoutes() != false)
		{
			return;
		}
		else
		{
			if(in_array($path[count($path)-1], $static))
			{
				$route = explode($this -> routerConfig -> delimiter, $this -> routerConfig -> static_routes[$this -> currentRoute]);
			}
			else
			{
				$route = explode($this -> routerConfig -> delimiter, $this -> currentRoute);
			}
			$front = (isset($route[0])) ? $route[0] : $this -> config -> frontController;
			$frontAction = (isset($route[1])) ? $route[1] : $this -> config -> frontAction;
			$componentAct = (isset($route[2])) ? $route[2] : $this -> config -> componentAction;
			$route = array_slice($route, 3);

			$this -> setFrontController($front);
			$this -> setFrontControllerAction($frontAction);
			$this -> setComponentAction($componentAct);
			$this -> setParams($route);
		}
	}
	
	
	protected function dynamicRoutes()
	{
		$dynamicRoutes = $this -> routerConfig -> dynamic_routes;
		$route = $this -> currentRoute;
		
		foreach($dynamicRoutes as $routeName => $routing)
		{
			if(preg_match($routing['pattern'], $route))
			{
				$front = (array_key_exists('frontcontroller', $routing)) ? $routing['frontcontroller'] : $this -> config -> frontController;
				$frontAction = (array_key_exists('frontaction', $routing)) ? $routing['frontaction'] : $this -> config -> frontAction;
				$componentAct = (array_key_exists('cmpaction', $routing)) ? $routing['cmpaction'] : $this -> config -> componentAction;

				$this -> setFrontController($front);
				$this -> setFrontControllerAction($frontAction);
				$this -> setComponentAction($componentAct);
				$this -> setParams($route);
				$this -> setRoute($routing);
				return true;
			}
			else
			{
				continue;
			}
		}
	}
	
	
	protected function includeRoutes()
	{
		foreach($this -> routerConfig -> includes as $component)
		{
			if(Vf_Loader::existsFile(DIR_COMPONENTS.$component.'/urls.php'))
			{
				include(DIR_COMPONENTS.$component.'/urls.php');
				$static = (sizeof($routes['static']) > 0) ? array_merge($this -> routerConfig -> static_routes, $routes['static']) : $this -> routerConfig -> static_routes;
				$dynamic = (sizeof($routes['dynamic']) > 0) ? array_merge($this -> routerConfig -> dynamic_routes, $routes['dynamic']) : $this -> routerConfig -> dynamic_routes;
			}
		}
		$this -> routerConfig -> static_routes = $static;
		$this -> routerConfig -> dynamic_routes = $dynamic;
	}
	
	
	
	public function setFrontController($front)
	{
		$this -> frontController = $front;
	}
	
	
	public function setFrontControllerAction($frontAction)
	{
		$this -> frontControllerAction = $frontAction;
	}
	
	
	public function setComponentAction($action)
	{
		$this -> componentAction = $action;
	}
	
	
	public function setParams($params)
	{
		$this -> params = $params;
	}
	
	
	public function setRoute($current)
	{
		$this -> route = $current;
	}
	
	
	public function getFrontController()
	{
		return $this -> frontController;
	}
	
	
	public function getFrontControllerAction()
	{
		return $this -> frontControllerAction;
	}
	
	
	public function getComponentAction()
	{
		return $this -> componentAction;
	}
	

	public function getParams($key = null)
	{
		if($key != null)
		{
			if(isset($this -> params[$key]))
			{
				return $this -> params[$key];
			}
			else
			{
				return false;
			}
		}
		return $this -> params;
	}
	
	
	public function getSegment($key = false)
	{
		$segments = explode($this -> routerConfig -> delimiter, $this -> currentRoute);
		
		if($key === false)
		{
			return sizeof($segments);
		}
		else if(isset($segments[$key]))
		{
			return $segments[$key];
		}
		else
		{
			return false;
		}
	}
	
	
	public function getDelimiter()
	{
		return $this -> routerConfig -> delimiter;
	}
	
	
	public function getRoute()
	{
		return $this -> route;
	}
	
	public function getDynamicRoutes()
	{
		return $this -> routerConfig -> dynamic_routes;
	}
}
?>