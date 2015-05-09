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
	
  
	public static function getEvent($event) 
	{
		return (isset(self::$hooks[$event])) ? self::$hooks[$event] : array();
	}
  
  
	public static function addEvent($event, $object, $method) 
	{
		self::$hooks[$event][] = array($object, $method);
	}
  
  
	public static function runEvent($event, &$args = null) 
	{
		if(isset(self::$hooks[$event])) 
		{	  
			foreach(self::$hooks[$event] as $evt) 
			{
				self::$runs[$event] = $event; 
				if($args)
				{
					call_user_func(array($evt[0], $evt[1]), &$args);
				}
				else
				{
					call_user_func(array($evt[0], $evt[1]));
				}
			}	  
		} 
	}
  
  
	public static function hasRunEvent($event) 
	{
		return isset(self::$runs[$event]);
	}
}

?>