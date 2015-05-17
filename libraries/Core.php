<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012-2015, marcio
*@version 1.0
*/

define('DIR_FRONT' , 'frontcontrollers/');
define('DIR_CTRL' , 'controllers/');
define('DIR_MODELS', 'models/');
define('DIR_VIEWS' , 'views/');
define('DIR_PLUGINS' , 'plugins/');
define('DIR_COMPONENTS' , 'components/');
define('DIR_WIDGETS' , 'widgets/');
define('DIR_WIDGETS_CFG', 'widgets/config/');
define('DIR_WIDGETS_CTRL' , 'widgets/controllers/');
define('DIR_WIDGETS_VIEWS' , 'widgets/views/');
define('DIR_WIDGETS_MODELS' , 'widgets/models/');
define('DIR_WIDGETS_ASSETS' , 'widgets/assets/');
define('DIR_LIBRARY' , 'libraries/');
define('DIR_HELPERS' , 'helpers/');
define('DIR_FUNCTIONS' , 'helpers/functions/');
define('DIR_CONFIG' , 'config/'); 
define('DIR_LANG' , 'i18n/');
define('DIR_ABSTRACT' , 'abstract/');
define('DIR_INTERFACES' , 'interfaces/');
define('DIR_MODULES' , 'modules/');
define('DIR_EVENTS' , 'events/');
define('DIR_EXCEPTIONS' , 'libraries/exceptions/');
define('DIR_LOG' , 'log/');
define('DIR_CACHE' ,'cache/');
define('DIR_DRIVERS' , 'libraries/drivers/');
define('DIR_NOTIFY' , 'notify/');
define('DIR_ASSETS' , 'assets/');
define('DIR_IMG' , 'images/');
define('DIR_CSS' , 'css/');
define('DIR_JS' , 'js/');

//include required classess
require_once(DIR_LIBRARY . 'Loader.php');
require_once(DIR_LIBRARY . 'Event.php');
require_once(DIR_LIBRARY . 'Di.php');
require_once(DIR_LIBRARY . 'Benchmark.php');

//load all events class
Vf_Loader::LoadFile(DIR_EVENTS . '*.php', true);
//load all exceptions class for libraries
Vf_Loader::LoadFile(DIR_EXCEPTIONS . '*.php', true);

//set error handler, exception handler and autoload class
error_reporting(E_ALL);
set_exception_handler(array('Exception_Handler', 'Handler'));
set_error_handler(array(new Error_Handler(), 'Handler'));
spl_autoload_register(array('Vf_Loader', 'autoload'));


final class Vf_Core
{

	/**
	*Skladowa klasy ktora przechowywuje kontener na dane
	*@access private
	*@var Vf_Di_Container
	*/
	private $container;
	
	
	/**
	*Wczytuje plik routera i laduje klasy do skladowych
	*@access public 
	*/
	public function __construct()
	{
		if (Vf_Loader::existsFile(DIR_LIBRARY . 'Router.php')) {
			require_once(DIR_LIBRARY . 'Router.php');
		} else {
			throw new Exception("Nie znaleziono biblioteki routera");
		}
		$this->container = self::getContainer();
		//register system events
		$this->setEvents($this->container->configCore->events);
	}

	
	/**
	*Odpala frontcontroller na podstawie uri z routera
	*@access public 
	*@return string kod html/xml strony
	*/
	public function dispatch()
	{
		try {	
			$appController = $this->container->router->getFrontController();
			$appAction = $this->container->router->getFrontControllerAction();
			
			if (Vf_Loader::existsFile(DIR_FRONT . $appController . '.php')) {
				Vf_Benchmark::start('core');
				require_once(DIR_FRONT . $appController . '.php');
				
				$appControllerClassName = 'Vf_' . $appController . '_FrontController';
				
				Vf_Event::runEvent('system.init');
				
				if (class_exists($appControllerClassName)) {
					Vf_Event::runEvent('system.pre.controller.constructor');
					$app = new $appControllerClassName();
					Vf_Event::runEvent('system.post.controller.constructor');
					
					if (method_exists($app, $appAction)) {
						Vf_Event::runEvent('system.pre.action');
						$output = $app->$appAction();
						Vf_Event::runEvent('system.post.action');
					} elseif ($app instanceof Vf_Controller) {
						$action = $this->container->config->frontAction;
						Vf_Event::runEvent('system.pre.action');
						$output = $app->$action();
						Vf_Event::runEvent('system.post.action');
					}
					
					Vf_Benchmark::stop('core');
					
					if(!$this->container->request->isRestful()) {
						Vf_Event::runEvent('system.display', $output);
					}
					
					Vf_Event::runEvent('system.shutdown');
					
					//if compression is turned off set response becouse Vf_CompressApp_Events didn't do anything
					//moved this part to CompressApp event class
					/*
					if($this->container->config->compression_level == 0)
					{
						$this->container->request->response
							-> setHttpStatus(200)
							-> setResponse($output);
					}
					*/
					print $this->container->request->response;
				}
			} else {
				self::Error(404);
			}
		} catch (Exception $e) {
			self::logAdd($e);
			self::Error(503);
		}
	}
	
	
	/**
	*Ustawia eventy aplikacji na podstawie konfiguracji 
	*@access public 
	*@param array $events tablica z eventami
	*/
	private function setEvents($events)
	{
		if (is_array($events) && sizeof($events) > 0) {
			foreach ($events as $eventsName => $data) {
				foreach ($data as $class) {
					if(!is_string($class[0])) {
						Vf_Event::addEvent($eventsName, $class[0], $class[1]);
					} else {
						Vf_Event::addEvent($eventsName, new $class[0]($this->container), $class[1]);
					}
				}
			}
		}
	}
	
	
	/**
	*Zwraca kontener ze wszystkimi klasami ktore sa potrzebne innym czescia aplikacji
	*@access public 
	*@return Vf_Di_Container
	*/
	public static function getContainer()
	{
		$container = Vf_Di_Container::instance();
		$config = new Vf_Config('config.Core');

		if (sizeof($config->container['objects']) > 0) {
			foreach ($config->container['objects'] as $di) {
				if (!isset($di['closure'])) {
					if (isset($di['method'])) {
						if ($di['is_static']) {
							$container->$di['propertyName'] = call_user_func(array($di['class'], $di['method']));
						} else {
							$object = new $di['class']();
							$container->$di['propertyName'] = call_user_func(array($object, $di['method']));
						}
					} else {
						$container->$di['propertyName'] = new $di['class']();
					}
				} else {
					$container->$di['propertyName'] = function() use($container, $di) {
						$closure = $di['closure'];
						return $closure($container);
					};
				}
			}
		}
		if (sizeof($config->container['shared']) > 0) {
			foreach ($config->container['shared'] as $shared) {
				if (!isset($shared['closure'])) {
					if (!isset($shared['args'])) {
						$container->share($shared['propertyName'], function() use($container, $shared) {
							return new $shared['class']();
						});
					} else {
						try {
							$container->share($shared['propertyName'], function() use($container, $shared) {
								$class = new ReflectionClass($shared['class']);
								return $class->newInstanceArgs($shared['args']);
							});
						} catch (ReflectionException $e) {
							self::logAdd($e);
							self::Error(503);
						}
					}
				} else {
					$container->share($shared['propertyName'], function() use($container, $shared) {
						$closure = $shared['closure'];
						return $closure($container);
					});
				}
			}
		}
		if (sizeof($config->container['proprieties']) > 0) {
			foreach ($config->container['proprieties'] as $key => $property) {
				$container->$key = $property($container);
			}
		}
		return $container;
	}
	
	
	/**
	*Zwraca strone z bledem
	*@access public 
	*@static
	*@param int $error kod bledu
	*@return string strona z bledem razem z naglowkami
	*/
	public static function Error($error) 
	{
		if(file_exists(DIR_VIEWS . $error . '.php')) {
			ob_end_clean();
			$request = new Vf_Request();
			
			print $request->response
				-> setHttpStatus($error)
				-> setResponse(file_get_contents(DIR_VIEWS . $error . '.php'))
				-> getResponse();
					
			$request->response->flushContents();
		}
	}
	
	
	/**
	*Dodajemy info do pliku z logami
	*@access public 
	*@static
	*@param Exception $exception parametr musi byc klasa ktora dziedziczy po Exception
	*/
	public static function logAdd($exception)
	{
		$eName = new ReflectionClass($exception);
		$class = $eName->getName();
		$error = "caught: " . $class . " ";
		$error  .= date('d-m-Y H.i.s');
		$error .= "\n";
		$error  .= "File: " . $exception->getFile();
		$error  .= " Line: " . $exception->getLine() . "\n";
		$error  .= "Message: " . $exception->getMessage() . "\n\n";
		file_put_contents(DIR_LOG . 'logs.php', $error, FILE_APPEND | LOCK_EX);
	}
}


class Error_Exception extends Exception
{
	public function __construct($err, $num, $file, $line)
	{
		$this->file = $file;
		$this->line = $line;
		parent::__construct($err, $num); 
	}
}


class Exception_Handler
{
	public static function Handler(Exception $e)
	{
		$eName = new ReflectionClass($e);
		$class = $eName->getName();
		$exception = "Uncaught: " . $class . " ";
		$exception  .= date('d-m-Y H.i.s');
		$exception .= "\n";
		$exception  .= "File: " . $e->getFile();
		$exception  .= " Line: " . $e->getLine() . "\n";
		$exception  .= "Message: " . $e->getMessage() . "\n\n";
		file_put_contents(DIR_LOG . 'logs.php', $exception, FILE_APPEND | LOCK_EX);
		Vf_Core::Error(503);
	}
}


class Error_Handler
{
	public function Handler($num, $err, $file, $line)
	{
		if($num != E_NOTICE) {
			if (error_reporting() & $num) {
				throw new Error_Exception($err, $num, $file, $line);
			}
		}
		return true;
	}
}
?>