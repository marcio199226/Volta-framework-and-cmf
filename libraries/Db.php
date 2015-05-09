<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_Db
{
	protected static $instance;
	
	protected $db;
	
	protected $adapter;

	
	public static function Factory($db) 
    {
		if(!isset(self::$instance[$db]))
		{
			self::$instance[$db] = new self();
		}
		return self::$instance[$db];
	}
	
	
	public function __construct()
	{
		$cfg = new Vf_Config(DIR_CONFIG);
		if(file_exists(DIR_DRIVERS.'Db/'.$cfg -> DbType.'.php'))
		{
			$this -> db = $cfg -> DbType;
			require_once(DIR_DRIVERS.'Db/'.$cfg -> DbType.'.php');
			$className = 'Vf_'.$cfg -> DbType.'_Query_Adapter';
			
			if(class_exists($className))
			{
				$this -> adapter = new $className();
				
				if($this -> adapter instanceof Vf_Database)
				{
					 $this -> adapter -> connect();
				}
			}
		}
	}
	
	
	public static function &getInstance($db)
	{
		if(!isset(self::$instance[$db]))
		{
			self::$instance[$db] = new self();
		}
		return self::$instance[$db];
	}
	
	
	public function Select($rows, $table, $callbackFunc = '', $callbackAs = true)
	{
		return $this -> adapter -> Select($rows, $table, $callbackFunc, $callbackAs);
	}
	
		
	public function FetchAllAssoc($resource)
	{
		return $this -> adapter -> FetchAllAssoc($resource);
	}
	
	
	public function FetchAssoc($resource)
	{
		return $this -> adapter -> FetchAssoc($resource);
	}
	
	
	public function FetchRow($resource)
	{
		return $this -> adapter -> FetchRow($resource);
	}
	
	
	public function FetchAllRows($resource)
	{
		return $this -> adapter -> FetchAllRows($resource);
	}
	
	
	public function FetchObject($resource)
	{
		return $this -> adapter -> FetchObject($resource);
	}
	
	
	public function Count($resource)
	{
		return $this -> adapter -> Count($resource);
	}
	
	
	public function getQueriesNumber()
	{
		return $this -> adapter -> getQueriesNumber();
	}
	
	
	public function CountRows($resource)
	{
		return $this -> adapter -> CountRows($resource);
	}
	
	
	public function Insert($table, $records = array(), $into = false)
	{
		return $this -> adapter -> Insert($table, $records, $into);
	}
	
	
	public function MultiInsert($table, $records = array(), $into = false)
	{
		return $this -> adapter -> MultiInsert($table, $records, $into);
	}
	
	
	public function Delete($table, $where = array(), $condition = '=')
	{
		return $this -> adapter -> Delete($table, $where, $condition);
	}
	
	
	public function Update($table, $set = array(), $where = array())
	{
		return $this -> adapter -> Update($table, $set, $where);
	}
	
	
	public function InsertId()
	{
		return $this -> adapter -> InsertId();
	}
	
	
	public function SetQuery($query)
	{
		return $this -> adapter -> SetQuery($query);
	}
	
	
	public function __call($method, $args)
	{
		if (!method_exists(self::$instance[$this -> db], $method))
		{
			throw new Exception("Brak metody {$method} w adapterze {$this -> db}");
		}
		return call_user_func_array(array(self::$instance[$this -> db], $method), $args);
	}
}

?>