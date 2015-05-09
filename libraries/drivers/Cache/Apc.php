<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES.'ICache.php');


class Vf_Apc_Cache_Driver implements ICache 
{
	
	protected $config = null;
	
	public function __construct()
	{
		$this -> config = new Vf_Config('config.Cache');
		
		if(!extension_loaded('apc'))
		{
			throw new Vf_Cache_Exception('Nie znaleziono rozszerzenia PHP APC');
		}
	}
  
  
	protected function cacheExists($id) 
	{
		return (apc_exists(sha1($id))) ? true : false;
	}
  
  
	public function getCache($name, $lifetime = null) 
	{
		if($this -> cacheExists($name)) 
		{
			$cacheData = apc_fetch(sha1($name), $success);
		}
		return ($success) ? $cacheData : false; 
	}
  
  
	public function setCache($id, $data, $lifetime = null) 
	{
		if($lifetime != null)
		{
			$life = $lifetime;
		}
		else
		{
			$life = $this -> config -> cache_file_lifetime;
		}
		return apc_store(sha1($id), $data, $life);
	}
  
  
	public function deleteCache($id) 
	{
		return ($this -> cacheExists($id)) ? apc_delete(sha1($id)) : true;
	}

  
	public function deleteAllCache() 
	{
		@apc_clear_cache('user');
		@apc_clear_cache();
		return true;
	}
}

?>
