<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES.'ICache.php');


class Vf_File_Cache_Driver implements ICache 
{
	
	protected $config = null;
	
	public function __construct()
	{
		$this -> config = new Vf_Config('config.Cache');
	}
  
  
	protected function CacheExpired($file, $time = null) 
	{
		$life = ($time === null) ? $this -> config -> cache_file_lifetime : $time;
		return ((time() - filemtime($file)) > $life) ? true : false;
	}
  
  
	protected function CacheExists($file) 
	{
		return (file_exists($file)) ? true : false;
	}
  
  
	public function getCache($name, $lifetime = null) 
	{
		if($this -> CacheExists($this -> config -> cache_file_directory.md5($name).'.cache')) 
		{
			if(!$this -> CacheExpired($this -> config -> cache_file_directory.md5($name).'.cache', $lifetime))
			{
				$cacheFile = $this -> config -> cache_file_directory.md5($name).'.cache';
				$cacheData = unserialize(file_get_contents($cacheFile));
			}
			else
			{
				$this -> DeleteCache($name);
				$cacheData = '';
			}
		}
		return (!empty($cacheData)) ? $cacheData : false; 
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
		if(file_put_contents($this -> config -> cache_file_directory.md5($id).'.cache', serialize($data), LOCK_EX))
		{
			touch($this -> config -> cache_file_directory.md5($id).'.cache', time() + $life);
		}
	}
  
  
	public function deleteCache($id) 
	{
		if(file_exists($this -> config -> cache_file_directory.md5($id).'.cache'))
		{                              
			return (@unlink($this -> config -> cache_file_directory.md5($id).'.cache')) ? true : false; 
		} 
		return false;
	}
  
  
    //delete all cache files from cache/ directory
	public function deleteAllCache() 
	{
		$files = glob($this -> config -> cache_file_directory.'*.cache');
	  
		foreach($files as $file) 
		{
			if(unlink($file) === false) 
			{
				$unlinkFiles = false;
				break;
			}
		}
		return ($unlinkFiles != false) ? true : false;
	}
}

?>
