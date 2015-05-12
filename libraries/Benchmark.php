<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Db.php');

class Vf_Benchmark
{
	protected static $benchmark = array();
	
	public static function start($id)
	{
		self::$benchmark[$id]['start'] = microtime(true);
		self::$benchmark[$id]['mem_start'] = self::memory_usage();
	}
	
	
	public static function stop($id)
	{
		self::$benchmark[$id]['end'] = microtime(true);
		self::$benchmark[$id]['mem_end'] = self::memory_usage();
	}
	
	
	public static function get($id, $decimals = 4)
	{
		$db = Vf_Db::Factory("MySql");
		$queries = $db -> getQueriesNumber();
		
		$time_elapse = self::$benchmark[$id]['end'] - self::$benchmark[$id]['start'];
		$time = number_format($time_elapse, $decimals);
		
		$memory = self::$benchmark[$id]['mem_end'] - self::$benchmark[$id]['mem_start'];
		$mem = number_format($memory, 2);
		
		return array(
			'time' => $time,
			'memory' => $mem.'MB',
			'sql' => $queries
		);
	}
	
	
	private static function memory_usage()
	{
		return (function_exists('memory_get_usage')) ? (memory_get_usage() / 1024 / 1024) : 0;
	}
}

?>