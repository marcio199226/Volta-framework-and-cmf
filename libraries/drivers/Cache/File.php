<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES . 'ICache.php');

class Vf_File_Cache_Driver implements ICache 
{
	protected $config = null;
	
	/**
	* Tworzy skladowa z zawartoscia konfiguracji dla cache
	* @access public 
	*/
	public function __construct()
	{
		$this->config = new Vf_Config('config.Cache');
	}
  
  
 	/**
	* Sprawdza czy czas pliku wygasl
	* @access protected
	* @param string $file nazwa pliku z cache
	* @param int $time czas po ktorym ma wygasnac
	* @return boolean
	*/
	protected function CacheExpired($file, $time = null) 
	{
		$life = ($time === null) ? $this->config->cache_file_lifetime : $time;
		return ((time() - filemtime($file)) > $life) ? true : false;
	}
  
  
 	/**
	* Sprawdza czy plik cache-u istnieje
	* @access protected
	* @param string $file
	* @return boolean
	*/
	protected function CacheExists($file) 
	{
		return (file_exists($file)) ? true : false;
	}
  
  
 	/**
	* Pobiera cache z pliku o podanej nazwie
	* @access public 
	* @param string $file nazwa pliku
	* @param int $lifetime czas po ktorym plik jest do usuniecia
	* @return mixed string|array|boolean
	*/
	public function getCache($name, $lifetime = null) 
	{
		if ($this->CacheExists($this->config->cache_file_directory . md5($name) . '.cache')) {
			if (!$this->CacheExpired($this->config->cache_file_directory . md5($name) . '.cache', $lifetime)) {
				$cacheFile = $this->config->cache_file_directory . md5($name) . '.cache';
				$cacheData = unserialize(file_get_contents($cacheFile));
			} else {
				$this->DeleteCache($name);
				$cacheData = '';
			}
		}
		return (!empty($cacheData)) ? $cacheData : false; 
	}
  
  
 	/**
	* Zapisuje cache do pliku 
	* @access public 
	* @param string $id nazwa identyfikacyjna dla cache
	* @param mixed $data zawartosc
	*/
	public function setCache($id, $data, $lifetime = null) 
	{
		if ($lifetime != null) {
			$life = $lifetime;
		} else {
			$life = $this->config->cache_file_lifetime;
		}
		if (file_put_contents($this->config->cache_file_directory . md5($id) . '.cache', serialize($data), LOCK_EX)) {
			touch($this->config->cache_file_directory . md5($id) . '.cache', time() + $life);
		}
	}
  
  
	/**
	* Usuwa cache o danym id
	* @access public 
	* @param string $id nazwa identyfikacyjna
	* @return boolean
	*/
	public function deleteCache($id) 
	{
		if (file_exists($this->config->cache_file_directory . md5($id) . '.cache')) {                              
			return (@unlink($this->config->cache_file_directory . md5($id) . '.cache')) ? true : false; 
		} 
		return false;
	}
  
  
	/**
	* Usuwa caly cache z katalogu /cache
	* @access public 
	* @return boolean
	*/
	public function deleteAllCache() 
	{
		$files = glob($this->config->cache_file_directory . '*.cache');
		foreach ($files as $file)  {
			if (unlink($file) === false) {
				$unlinkFiles = false;
				break;
			}
		}
		return ($unlinkFiles != false) ? true : false;
	}
}

?>
