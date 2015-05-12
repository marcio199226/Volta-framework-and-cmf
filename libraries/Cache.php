<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Cache 
{
 	/**
	* @access protected
	* @var $cache objekt adaptera
	*/
	protected $cache;
  
  
 	/**
	* Tworzymy instancje adaptera dla cache
	* @access public 
	*/
	public function __construct() 
	{
		$config = new Vf_Config('config.Cache');
		$DriverName = $config->driver;
		$DriverObj = 'Vf_' . $DriverName . '_Cache_Driver';
	  
		if (file_exists(DIR_DRIVERS . 'Cache/' . $DriverName . '.php')) {
			require_once(DIR_DRIVERS . 'Cache/' . $DriverName . '.php');
		} else {
			throw new Vf_Cache_Exception('Nie znaleziono klasy adaptera: ' . $DriverObj);
		}
		$this->cache = new $DriverObj();
	}
  
  
 	/**
	* Pobieramy cache i danej nazwie
	* @access public 
	* @param string $name nazwa cache
	* @param int $lifetime czas wygasniecia cache
	* @return array|string
	*/
	public function getCache($name, $lifetime = null) 
	{
		return $this->cache->getCache($name, $lifetime);
	}
  

  	/**
	* Ustawiamy cache o danej nazwie
	* @access public 
	* @param string $id nazwa cache
	* @param mixed $data
	* @param int $lifetime czas wygasniecia cache
	*/
	public function setCache($id, $data, $lifetime = null) 
	{ 
		return $this->cache->setCache($id, $data, $lifetime);
	}
  

 	/**
	* Usuwa cache o danym id
	* @access public 
	* @param string $id nazwa cache
	* @return boolean
	*/
 	public function DeleteCache($id) 
	{
		return $this->cache->deleteCache($id);
	}
  
  
 	/**
	* Usuwamy caly cache
	* @access public 
	* @return boolean
	*/
	public function DeleteAllCache() 
	{
		return $this->cache->deleteAllCache();
	}
}

?>
