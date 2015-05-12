<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Region_Helper
{
	public static function cacheStart()
	{
		ob_start();
	}
	
	
	public static  function cacheSave($id, $lifetime = 900)
	{
		$region = ob_get_contents();
		ob_end_clean();
		
		$user = new Vf_User();
		$regionCacheName = $id . $user->user();
		$cache = new Vf_Cache();
		$cache->setCache($regionCacheName, $region, $lifetime);
		return $region;
	}
	
	
	public static function getCache($id, $lifetime = 900)
	{
		$user = new Vf_User();
		$regionCacheName = $id . $user->user();
		$cache = new Vf_Cache();
		return $cache->getCache($regionCacheName, $lifetime);
	}
}
?>