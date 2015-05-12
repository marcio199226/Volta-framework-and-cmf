<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Event 
{

	protected static $hooks = array();
  
	protected static $runs = array();
	
  
	/**
	* Pobiera zdarzenia pod danym hookiem
	* @access public 
	* @static
	* @param string $event nazwa zdarzenia
	* @return array
	*/
	public static function getEvent($event) 
	{
		return (isset(self::$hooks[$event])) ? self::$hooks[$event] : array();
	}
  
  
	/**
	* Dodaje zdarzenie
	* @access public 
	* @static
	* @param string $event nazwa zdarzenia
	* @param object $object object ktory chcemy wykorzystac
	* @param string $method nazwa metody do wykonania
	*/
	public static function addEvent($event, $object, $method) 
	{
		self::$hooks[$event][] = array($object, $method);
	}
  
  
	/**
	* Wywoluje wszystkie zdarzenia o danym prefiksie
	* @access public 
	* @static
	* @param string $event nazwa zdarzenia
	* @param mixed argumenty ktore chcemy przeslac do zdarzenia przez referencje
	*/
	public static function runEvent($event, &$args = null) 
	{
		if (isset(self::$hooks[$event]))  {	  
			foreach (self::$hooks[$event] as $evt) {
				self::$runs[$event] = $event; 
				if ($args) {
					call_user_func(array($evt[0], $evt[1]), &$args);
				} else {
					call_user_func(array($evt[0], $evt[1]));
				}
			}	  
		} 
	}
  

	/**
	* zwraca true jesli zdarzenie istnieje
	* @access public 
	* @static
	* @return boolean
	*/
	public static function hasRunEvent($event) 
	{
		return isset(self::$runs[$event]);
	}
}

?>