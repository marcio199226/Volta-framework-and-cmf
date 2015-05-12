<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Uri_Helper
{
	public static function createUrl() 
	{
		$argv = func_get_args();
		$httpRequest = implode(',', $argv);
		return $_SERVER['SCRIPT_URI'] . '/' . $httpRequest;
	}
	
	
	public static function getParam($param = null)
	{
		$router = new Vf_Router();
		return $router->getParams($param);
	}
	
	
	public static function redirect($url)
	{
		header("Location: ".$url);
	}
	

	public static function refresh($url, $time = 1)
	{
		header('Refresh: ' . $time.'; url=' . $url);
		exit;
	}
	
	
	public static function segment($segment = 0)
	{
		$router = new Vf_Router();
		return $router->getSegment($segment);
	}
	
	
	public static function self()
	{
		return $_SERVER['PHP_SELF'];
	}
	
	
	public static function base($index = true, $default = false, $protocol = 'http')
	{
		$config = new Vf_Config(DIR_CONFIG, 'Xml');
		$http = ($protocol === false) ? '' : $protocol.'://' . $_SERVER['HTTP_HOST'];
		
		if ($index) {
			$url_site = $http . $config->site_domain . $config->home . '/';
		} else {
			$url_site = $http . $config->site_domain;
		}
		if ($default) {
			$router = Vf_Router::instance();
			$url_site .= $router->getFrontController() . $router->getDelimiter() . $router->getFrontControllerAction() . $router->getDelimiter() . $router->getComponentAction();
		}
		return $url_site;
	}
	
	
	public static function site($query = true, $uri = null, $default = true, $protocol = false)
	{
		if (!empty($_SERVER['QUERY_STRING']) && $query === true) {
			$query = '?'.$_SERVER['QUERY_STRING'];
		} else {
			$query = '';
		}
		
		$url = Vf_Uri_Helper::base(true, $default, $protocol);
		//$url .= ($uri !== null && substr($uri, 0, 1) != ',') ? $uri.$query : $query;
		$url .= ($uri !== null) ? $uri . $query : $query;
		return $url;
	}
	
	
	public static function url($routeName, $params, $uri = null)
	{
		$router = Vf_Router::instance();
		$uriSettings = $router->getRoute();
		$routes = $router->getDynamicRoutes();
		
		if (array_key_exists($routeName, $routes) && $uri === null) {
			$currentUri = implode('', $routes[$routeName]['uri']);
		} else {
			$currentUri = ($uriSettings === false && $uri !== null) ? $uri : $uriSettings;
		}

		preg_match_all('#:(?P<args>\w+):#', $currentUri, $matches);
		if (sizeof($matches[0]) == 0) {
			foreach ($params as $key => $value) {
				$url[] = $value;
			}
			$url_site = $currentUri.implode($router->getDelimiter(), $url);
			return Vf_Uri_Helper::site(true, $url_site, true);
			//dodac default-owe wartosci dla parametrow routingu ktore sa ustalowne w urls.php w params
		} else if(sizeof($matches[0]) > 0) {
			foreach ($matches[0] as $count => $key) {
				$cUri[] = str_replace($key, $params[$key], $matches[0][$count]);
			}
			return Vf_Uri_Helper::site(true, implode($router->getDelimiter(), $cUri), false);
		}
	}
	
	
	public static function anchor($routeName, $params, $uri = null, $title, $attributes = null)
	{
		$attr = (is_array($attributes)) ? implode(" ", $attributes) : $attributes;
		return '<a href="' . Vf_Uri_Helper::url($routeName, $params, $uri) . ' ' . $attr . '">' . $title . '></a>';
	}
}
?>