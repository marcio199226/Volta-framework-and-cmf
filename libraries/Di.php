<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Di_Container
{

	const CONTAINER = 'containerCore';

	/**
	*Skladowa klasy ktora przechowywuje obiekt jako singleton
	*@access private
	*@static
	*@var array $instance
	*/
	private static $instance = null;

	/**
	*Skladowa klasy ktora przechowywuje obiekty i parametry
	*@access private
	*@var array $container
	*/
	private $container = array();
	
	/**
	*Skladowa klasy ktora przechowywuje obiekty po jednej instancji
	*@access private
	*@static
	*@var array $sharedContainer
	*/
	private static $sharedContainer = array();
 
 	/**
	*Skladowa klasy ktora przechowywuje wstrzykniete obiekty
	*@access private
	*@static
	*@var mixed $di
	*/
	private static $di;
 
 
 	/**
	*Magiczny setter
	*@access public 
	*@param string $key klucz na podstawie ktorego zapisujemy dane
	*@param mixed $value wartosc jako Closure lub string
	*/
	public function __set($key, $value) 
	{
		$this -> container[$key] = $value; 
	}
 
 
  	/**
	*Magiczny getter
	*@access public 
	*@param string $key klucz na podstawie ktorego pobieramy dane
	*@return mixed zawartosc kontenera o danym kluczu
	*/
	public function __get($key) 
	{
		if(array_key_exists($key, $this -> container))
		{
			return (is_callable($this -> container[$key])) ? $this -> container[$key]($this) : $this -> container[$key];
		}
		else if(array_key_exists($key, self::$sharedContainer))
		{
			return self::$sharedContainer[$key];
		}
		else
		{
			throw new Vf_Di_Container_Exception('Container: missing property: '.$key);
		}
	}
    
	
  	/**
	*Ustawia obiekt jako singleton
	*@access public 
	*@param string $key klucz pod ktorym bedzie obiekt
	*@param Closure $closure funkcja anonimowa ktora zwraca obiekt by go nie tworzy niepotrzebnie
	*/
	public function share($key, Closure $closure)
	{
		if(!array_key_exists($key, self::$sharedContainer))
		{
			self::$sharedContainer[$key] = $closure -> __invoke();
		}
	}
	
	
  	/**
	*Ustawia obiekt jako singleton
	*@access public
	*@static
	*@return Vf_Di_Container zwraca obiekt kontenera
	*/
	public static function &instance()
	{
		if(self::$instance === null)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
  	/**
	*Ustawia obiekt jako singleton
	*@access public
	*@static
	*@param string $className nazwa klasa
	*@param string|array $arguments argumenty konstruktora dla danej klasy 
	*@return object zwraca obiekt ktory chcemy wczytac
	*/
    public static function get($className, $arguments = null) 
	{
		static $configDi = null;
		static $configCore = null;
		
		if($configDi === null)
		{
			$configDi = new Vf_Config('config.Di');
		}
		
		if($configCore === null)
		{
			$configCore = new Vf_Config('config.Core');
		}
		
        // checking if the class exists
        if(!class_exists($className)) 
		{
			if($configDi -> dependency_include_files)
			{
				self::_includeRequiredFileClass($className, $configCore -> di);
			}
			else
			{
				throw new Vf_Di_Container_Exception('DI: missing class '.$className);
			}
        }
            
        // initialized the ReflectionClass
        $reflection = new ReflectionClass($className);
            
        // creating an instance of the class
        if($arguments === null || count($arguments) == 0) 
		{
            $obj = new $className;
        } 
		else 
		{
            if(!is_array($arguments)) 
			{
                $arguments = array($arguments);
            }
            $obj = $reflection -> newInstanceArgs($arguments);
        }


        // get keys from inject
		if($doc = $reflection -> getDocComment()) 
		{
			preg_match_all("/@Inject [A-Za-z0-9]{1,}/", $doc, $matches);
			$keys = array();
			foreach($matches[0] as $k => $v)
			{
				$q = explode(' ', $v);
				$keys[] = $q[1];
			}
		}
		
		//if dependency_config is turned on set class and values according to the configuration array present in config/Core/config.php
		if($configDi -> dependency_config)
		{
			self::_setDependencyInjectionData($configCore);
		}
		
		foreach($keys as $key)
		{
            if(isset(self::$di -> $key)) 
			{
                switch(self::$di -> $key -> type) 
				{
                    case 'value':
                            $obj -> $key = self::$di -> $key -> value;
                    break;
									
                    case 'class':
                            $obj -> $key = self::get(self::$di -> $key -> value, self::$di -> $key -> arguments);
                    break;
									
                    case 'classSingleton':
                        if(self::$di -> $key -> instance === null) 
						{
                            $obj -> $key = self::$di -> $key -> instance = self::get(self::$di -> $key -> value, self::$di -> $key -> arguments);
                        } 
						else 
						{
                            $obj -> $key = self::$di -> $key -> instance;
                        }
                    break;
                }
            }
        }
        return $obj;
    }
	
	
  	/**
	*wczytujemy automatycznie pliki danej klasy jesli nie istnieje na podstawie konfiguracji z config/Core/config.php
	*@access private
	*@static
	*@param string $className nazwa klasa
	*@param array $coreConfig konfiguracje z config/Core/config.php $configs['di']
	*/
	private static function _includeRequiredFileClass($className, $coreConfig)
	{
		$workDir = getcwd();
		$includedFiles = get_included_files();
		$classFile = $workDir.'/'.$coreConfig['class'][$className]['requiredFileClass'];
		
		if(in_array($classFile, $includedFiles))
		{
			return true;
		}
		else
		{
			if(Vf_Loader::existsFile($classFile))
			{
				require_once($classFile);
				return true;
			}
			else
			{
				throw new Vf_Di_Container_Exception('Missing class file: '.$classFile);
			}
		}
	}
	

  	/**
	*ustawiamy aliasy automatycznie na podstawie konfiguracji w config/Core/config.php
	*@access private
	*@static
	*@param Vf_Config $coreConfig klasa ktora wczytuje konfiguracje z config/Core/config.php
	*/
	private static function _setDependencyInjectionData($coreConfig)
	{
		if(is_array($coreConfig -> di))
		{
			//add class and values to inject automitacally
			foreach($coreConfig -> di as $key => $di)
			{
				if($key == 'class')
				{
					if(sizeof($coreConfig -> di['class']) > 0)
					{
						foreach($di as $key => $class)
						{
							if(!isset($class['args']))
							{
								self::addClass($class['key'], $class['class']);
							}
							else
							{
								self::addClass($class['key'], $class['class'], $class['args']);
							}
						}
					}
				}
				else if($key == 'classSingleton')
				{
					if(sizeof($coreConfig -> di['classSingleton']) > 0)
					{
						foreach($di as $key => $singleton)
						{
							if(!isset($class['args']))
							{
								self::addClassAsSingleton($class['key'], $class['class']);
							}
							else
							{
								self::addClassAsSingleton($class['key'], $class['class'], $class['args']);
							}
						}
					}
				}
				else if($key == 'value')
				{
					if(sizeof($coreConfig -> di['value']) > 0)
					{
						foreach($di as $value)
						{
							self::addValue($value['key'], $value['value']);
						}
					}
				}
			}
		}
		else
		{
			throw new Vf_Di_Container_Exception('Dependecies must be passed as array');
		}
	}

	
  	/**
	*dodajemy skladowa klasy
	*@access public 
	*@param string $key nazwa skladowej
	*@param mixed zawartosc skladowej
	*/
	public static function addValue($key, $value) 
	{
		self::_addToDi($key, (object) array(
			'value' => $value,
			'type' => 'value'
		));
	}
		
		
  	/**
	*Dodaje klase pod dany klucz
	*@access public 
	*@param string $key klucz pod ktorym mamy klase ktora potem bedzie wstrzykiwana w zaleznosci
	*@param string $value Nazwa klasy do wstrzykniecia
	*@param mixed $arguments argumenty klasy dla konstruktora
	*/
	public static function addClass($key, $value, $arguments = null) 
	{
		self::_addToDi($key, (object) array(
			'value' => $value,
			'type' => 'class',
			'arguments' => $arguments
		));
	}
		
		
  	/**
	*Dodaje klase jako singleton pod dany klucz
	*@access public 
	*@param string $key klucz pod ktorym mamy klase ktora potem bedzie wstrzykiwana w zaleznosci
	*@param string $value Nazwa klasy do wstrzykniecia
	*@param mixed $arguments argumenty klasy dla konstruktora
	*/
	public static function addClassAsSingleton($key, $value, $arguments = null) 
	{
		self::_addToDi($key, (object) array(
			'value' => $value,
			'type' => 'classSingleton',
			'instance' => null,
			'arguments' => $arguments
		));
	}
		
		
  	/**
	*Dodaje dane do kontenera
	*@access private
	*@param string $key klucz pod ktorym mamy klase ktora potem bedzie wstrzykiwana w zaleznosci
	*@param object $obj stdclass ktora definiuje nasze zaleznosci
	*/
	private static function _addToDi($key, $obj) 
	{
		if(self::$di === null) 
		{
			self::$di = (object) array();
		}
		self::$di -> $key = $obj;
	}
}

?>