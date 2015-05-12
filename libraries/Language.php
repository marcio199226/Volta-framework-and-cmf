<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Language
{
	protected static $instance = null;

	protected $driver = null;
	
	protected $extensionsAdapter = array(
		'php' => 'Array',
		'xml' => 'Xml'
	);
	
	
	public function __construct($file = null)
	{	
		$adapter = $this->getClassAdapter($file);
		$this->loadDriver($adapter, $file);
	}
	
	
	public static function instance()
	{
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	
	protected function loadDriver($adapter, $file)
	{
		if (Vf_Loader::existsFile(DIR_DRIVERS . 'Language/' . $adapter . '.php')) {
			require_once(DIR_DRIVERS . 'Language/' . $adapter . '.php');
			$className = 'Vf_Language_' . $adapter . '_Adapter';
	
			if (class_exists($className)) {
				$this->driver = new $className();
				if ($file !== null) {
					$this->driver->load($file);
				}
			}
		} else {
			throw new Vf_LanguageDriverNotFound_Exception("Adapter {$adapter} not found");
		}
	}
	
	
	public function get()
	{
		return $this->driver;
	}
	
	
	private function getClassAdapter($file)
	{
		if($file !== null) {
			$extension = end(explode('.', $file));
			return $this->extensionsAdapter[$extension];
		}
		return 'Array';
	}
}

?>