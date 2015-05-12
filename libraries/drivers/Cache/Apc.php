<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES . 'ICache.php');

class Vf_Apc_Cache_Driver implements ICache 
{
	/**
	* Skladowa z konfiguracja cache-u
	* @access protected
	* @var object Vf_Cache
	*/
	protected $config = null;
	
	
	/**
	* Sprawdzamy czy rozszerzenie apc jest dostepne
	* @access public 
	*/
	public function __construct() {
		$this->config = new Vf_Config('config.Cache');
		if (!extension_loaded('apc')) {
			throw new Vf_Cache_Exception('Nie znaleziono rozszerzenia PHP APC');
		}
	}
  
  
 	/**
	* Zwraca dane z tablicy $_POST o kluczu $key
	* @access protected
	* @param string $id nazwa cache-u
	* @return boolean
	*/
	protected function cacheExists($id) 
	{
		return (apc_exists(sha1($id))) ? true : false;
	}
  
  
 	/**
	* Pobiera cache o danym id
	* @access public 
	* @param string $name nazwa cache-u
	* @param int $lifetime
	* @return mixed string|boolean zwraca dane lub jesli nie istnieja false
	*/
	public function getCache($name, $lifetime = null) 
	{
		if ($this->cacheExists($name)) {
			$cacheData = apc_fetch(sha1($name), $success);
		}
		return ($success) ? $cacheData : false; 
	}
  
  
 	/**
	* Ustawia cache o danym id
	* @access public 
	* @param string $id nazwa cache-u
	* @param string|array $data zawartosc ktora chcemy zapisac
	* @param int $lifetime czas po ktorym cache wygasa
	* @return boolean
	*/
	public function setCache($id, $data, $lifetime = null) 
	{
		if($lifetime != null) {
			$life = $lifetime;
		} else {
			$life = $this->config->cache_file_lifetime;
		}
		return apc_store(sha1($id), $data, $life);
	}
  
  
 	/**
	* Usuwam cache o danym id
	* @access public 
	* @param string $id nazwa cache-u
	* @return boolean
	*/
	public function deleteCache($id) 
	{
		return ($this->cacheExists($id)) ? apc_delete(sha1($id)) : true;
	}

  
 	/**
	* Usuwa caly cache
	* @access public 
	* @return boolean
	*/
	public function deleteAllCache() 
	{
		@apc_clear_cache('user');
		@apc_clear_cache();
		return true;
	}
}

?>
