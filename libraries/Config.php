<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/


class Vf_Config
{

	protected $defaultConfigLoaderAdapter = 'Array'; //Array/Xml/Json

	protected $config = array();
	
	protected $accepted = array(
		'Array' => 'php',
		'Ini' => 'ini',
		'Csv' => 'csv',
		'Xml' => 'xml',
		'Json' => 'json'
	);
								
	protected static $loaded_config = array();
	
	protected static $instances = array();

								
	public function __construct($config, $driver = null)
	{
		$driver = $this->getDefaultAdapter($driver);
		if (isset(self::$loaded_config[$config])) {
			$this->config =  self::$loaded_config[$config];
			return;
		} else {
			if (Vf_Loader::existsFile(DIR_DRIVERS . 'Config/' . $driver . '.php')) {
				if ($this->isAcceptAdapter($driver)) {
					require_once(DIR_DRIVERS . 'Config/' . $driver . '.php');
					$class = $this->setAdapter($driver);
					$instance = new $class();
					$suffix = end(explode('.', $config));
				
					if ($this->accepted[$driver] == $suffix) {
						$suff = $suffix;
					} else {
						$suff = $this->accepted[$driver];
					}
				
					if ($instance->isAcceptSuffix($suff)) {
						if ($instance instanceof IConfig) {
							if (is_file($config)) {
								$configFile = $config;
							} else {
								$configRe = str_replace('.', '/', $config);
								$configFile = $configRe . '/config.' . $suff;
							}
							$this->config = $instance->load($configFile);
							self::$loaded_config[$config] = $this->config;
						} else {
							throw new Vf_Config_Exception("Adapter konfiguracji musi implementowac interfejs IConfig.");
						}
					} else {
						throw new Vf_Config_Exception("Niedozwolone rozszerzenie pliku konfiguracyjnego.");
					}
				} else {
					throw new Vf_Config_Exception("Niedozwolony typ adaptera");
				}
			} else {
				throw new Vf_Config_Exception("Nie znaleziono pliku adaptera: " . $driver);
			}
		}
	}

	
  	/**
	* Ustawia obiekt jako singleton
	* @access public
	* @static
	* @param string $config nazwa konfiguracji
	* @param string $driver typ adaptera do wczytania konfiguracji
	* @return Vf_Config
	*/
	public static function &instance($config, $driver = null)
	{
		if (!array_key_exists($config, self::$instances)) {
			self::$instances[$config] = new self($config, $driver);
		}
		return self::$instances[$config];
	}
	
	public function get()
	{
		return $this->config;
	}
	
	
	public function __get($key)
	{
		if (isset($this->config[$key])) {
			return $this->config[$key];
		}
	}
	
	
	protected function setAdapter($driver)
	{
		switch ($driver) {
			case 'Array':
				$class = 'Vf_Config_Array_Adapter';
				break;
								
			case 'Ini':
				$class = 'Vf_Config_Ini_Adapter';
				break;
								
			case 'Csv':
				$class = 'Vf_Config_Csv_Adapter';
				break;
						
			case 'Xml':
				$class = 'Vf_Config_Xml_Adapter';
				break;
						
			case 'Json':
				$class = 'Vf_Config_Json_Adapter';
				break;
		}
		return $class;
	}
	
	
	private function getDefaultAdapter($adapter)
	{
		if ($adapter === null) {
			$adapter = $this->defaultConfigLoaderAdapter;
			return $adapter;
		}
		return $adapter;
	}
	
	
	public function addItem($key, $value)
	{
		if (array_key_exists($key, $this->config)) {
			$this->deleteItem($key);
		}
		$this->config[$key] = $value;
	}
	
	
	public function deleteItem($key)
	{
		if (array_key_exists($key, $this->config)) {
			unset($this->config[$key]);
			return true;
		}
		return false;
	}
	
	
	public function editItem($key, $value)
	{
		if(array_key_exists($key, $this->config)) {
			$this->config[$key] = $value;
		}
	}
	
	
	protected function isAcceptAdapter($adapter)
	{
		if(array_key_exists($adapter, $this->accepted))
			return true;
		return false;
	}
}
?>