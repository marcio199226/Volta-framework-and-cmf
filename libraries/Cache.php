<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Cache 
{
  
	protected $cache;
  
	public function __construct() 
	{
		$config = new Vf_Config('config.Cache');
		$DriverName = $config -> driver;
		$DriverObj = 'Vf_'.$DriverName.'_Cache_Driver';
	  
		if(file_exists(DIR_DRIVERS.'Cache/'.$DriverName.'.php'))
		{
			require_once(DIR_DRIVERS.'Cache/'.$DriverName.'.php');
		}
		else
		{
			throw new Vf_Cache_Exception('Nie znaleziono klasy adaptera: '.$DriverObj);
		}
	   
		$this -> cache = new $DriverObj();
	}
  
  
	public function getCache($name, $lifetime = null) 
	{
		return $this -> cache -> getCache($name, $lifetime);
	}
  
  
	public function setCache($id, $data, $lifetime = null) 
	{ 
		return $this -> cache -> setCache($id, $data, $lifetime);
	}
  
  
 	public function DeleteCache($id) 
	{
		return $this -> cache -> deleteCache($id);
	}
  
  
	public function DeleteAllCache() 
	{
		return $this -> cache -> deleteAllCache();
	}
}

?>
