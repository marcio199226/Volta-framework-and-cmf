<?

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_Db_Exception extends Exception 
{ 
	public function __construct($error, $sql = null)
	{
		$msg = $error.'=>'.$sql;
		parent::__construct($msg, mysql_errno());
	}
}

class Vf_Db_Connection_Exception extends Exception { }

class Vf_Db_Select_Exception extends Exception { }


abstract class Vf_Database
{
	protected $query;
	
	protected $benchmark = true;
	
	protected $toObject = false;
	
	protected static $queries = 0;
	
	protected static $benchmarks = array();
	
	
	/**
	*Czy ma zwrocic obiekt
	*@access public 
	*@param boolean $object
	*/
	public function objectResult($object = true)
	{
		$this -> toObject = $object;
		return $this;
	}
	
	
	/**
	*Tworzy funkcje dla aliasow
	*@access protected
	*@param string $func funkcja dla aliasu (max, min, count)
	*@param string $column kolumna ktora ma byc wykorzystana do aliasu
	*@param string|bool nazwa aliasu
	*@return string kawalek zapytania sql 
	*/
	protected function CallBackFunc($func = '', $column, $as = false) 
	{
		if(!empty($func)) 
		{
			if($as !== false) 
			{
				$callback = "$func($column) AS $as"; 
			}
			else 
			{
				$callback = "$func($column)";  
			}
		}
		return $callback;
	}
	
	
	/**
	*Tworzy czesc zapytania dla select
	*@access public 
	*@param string $rows kolumny
	*@param string $table tabela z ktorej pobrac dane
	*@param string $callbackFunc funkcja dla aliasu
	*@param bool $callbackAs czy ma tworzy alias lub nie
	*@return object $this
	*/
	public function Select($rows, $table, $callbackFunc = '', $callbackAs = true) 
	{
		$select = 'SELECT ';
	
		if(is_string($rows) && isset($callbackFunc)) 
		{
			if(!empty($callbackFunc)) 
			{
				if($callbackAs !== false) 
				{
					$selectCall = $this -> CallBackFunc($callbackFunc, $rows, $callbackAs); 
				}
				else
				{
					$selectCall = $this -> CallBackFunc($callbackFunc, $rows, false);  
				}
			}
			else
			{
				$selectCall = $rows;
			}
			$select .= $selectCall;
		}
		else if(is_array($rows) && count($rows) > 1)
		{
			$select .= implode(',', $rows);
		}
		else if(is_string($rows))
		{
			$select .= $rows;
		}
	
		$select .= ' FROM ';
		$select .= (is_array($table)) ? implode(',', $table) : $table;
		$this -> query .= $select;
		
		return $this;
	}
	
	
	/**
	*Tworzy czesc zapytania dla where +and+
	*@access public 
	*@param string $where warunki where
	*@return object $this
	*/
	public function Where($where = array()) 
	{
		$whr = ' WHERE ';
  
		if(is_array($where) && count($where) > 0) 
		{
			foreach($where as $name => $value) 
			{
				if(!strstr($name, '='))
				{
					$data[] = $name.'='.$this -> Escape($value);
				}
				else if(strstr($name, '='))
				{
					$data[] = $name.$this -> Escape($value);
				}
			}	
		}
		$whr .= implode(' and ', $data);
		$this -> query .= $whr;
	
		return $this;
	}
	
	
	/**
	*Tworzy czesc zapytania dla where +or+
	*@access public 
	*@param string $where warunki
	*@return object $this
	*/
	public function WhereOr($where = array()) 
	{
		$whr = ' WHERE ';
  
		if(is_array($where) && count($where) > 0) 
		{
			foreach($where as $name => $value) 
			{
				if(!strstr($name, '='))
				{
					$data[] = $name.'='.$this -> Escape($value);
				}
				else if(strstr($name, '='))
				{
					$data[] = $name.$this -> Escape($value);
				}
			}
		}
		$whr .= implode(' or ', $data);
		$this -> query .= $whr;
	
		return $this;
	}
	
	
	/**
	*Tworzy czesc zapytania dla like
	*@access public 
	*@param array $like warunek like
	*@param bool $not NOT LIKE / LIKE
	*@param string $expression laczenie +and+ / +or+
	*@param string $contain typ szukania *% / %* / %*%
	*@return object $this
	*/
	public function Like($like = array(), $not = false, $expression = 'and', $contain = '%%') 
	{
  
		$likeSql = ' WHERE ';
		$ex = ($expression == 'and') ? ' and ': ' or ';
		$exNot = ($not === true) ? ' NOT LIKE ': ' LIKE ';
		
		if($contain == '%%')
		{
			$search_contain = '%'.$this -> Escape($value).'%';
		}
		else if($contain == '%*')
		{
			$search_contain = '%'.$this -> Escape($value);
		}
		else if($contain == '*%')
		{
			$search_contain = $this -> Escape($value).'%';
		}
			
	
		if(is_array($like) && count($like) > 0) 
		{
			foreach($like as $key => $value) 
			{
				$likes[] = $key.$exNot.$search_contain;
			}
		}
		$likeSql .= implode($ex, $likes);
		$this -> query .= $likeSql;
	
		return $this;
	}
	
	
	/**
	*Tworzy czesc zapytania dla join
	*@access public 
	*@param string $table tabela
	*@param string $on warunki dla join'a
	*@param string $cond typ join'a
	*@return object $this
	*/
	public function Join($table, $on, $cond = 'INNER') 
	{
		if(in_array($cond, array('JOIN', 'INNER', 'LEFT', 'RIGHT', 'OUTER', 'LEFT OUTER', 'RIGHT OUTER', 'FULL OUTER')))
		{
			$join = ($cond == 'JOIN') ? ' '.$cond.' ': ' '.$cond.' JOIN ';
		}
		else
		{
			$join = ' INNER JOIN '.$table;
		}
		$join .= (is_array($table)) ? implode(',', $table): $table;
	
		if(is_array($on)) 
		{
			$join .= ' ON(';
			foreach($on as $column => $equalColumn) 
			{
				$onClause[] = $column.'='.$equalColumn;
			}
			$join .= implode(' ', $onClause);
			$join .= ')';
		}
		else if(is_string($on)) 
		{
			$join .= ' USING('.$on.')';
		}
		$this -> query .= $join;
		
		return $this;
	}
	
	
	public function andClause()
	{
		$this -> query = 'AND';
		return $this;
	}
	
	
	public function orClause()
	{
		$this -> query = 'OR';
		return $this;
	}
	
	
	/**
	*Tworzy czesc zapytania dla in()
	*@access public 
	*@param string $column kolumna
	*@param array $in wartosci dla warunku in - in(1,2,3)
	*@param bool $clause NOT IN / IN
	*@param array $selectRows kolumna dla warunku in
	*@param array $conf konfiguracja [0]tabela [1]kolumna [2]funkcja dla aliasu [3]alias wlaczony/wylaczony
	*@param array $where warunek where
	*@return object $this
	*/
	public function In($column = '', $in = array(), $clause = false, $selectRows = array(), $conf = array(), $where = array()) 
	{
		$inClause = ($clause == true) ? ' NOT IN' : ' IN';
	
		if(count($selectRows) > 0 && count($conf) > 0)	
		{
			$this -> query .= ' WHERE '.$conf[1].$inClause.'(';
	  
			if(isset($where) && count($where) > 0) 
			{
				$this -> Select($selectRows, $conf[0], $conf[2], $conf[3]);
				$this -> Where($where);			  
			}
			else 
			{
				$this -> Select($selectRows, $conf[0], $conf[2], $conf[3]);
			}
	
			$this -> query .= ')';	
		}
		else
		{
			foreach($in as $escIn)
			{
				$escapeIn[] = $this -> Escape($escIn);
			}	
			$this -> query .= ' WHERE '.$column.$inClause.'('.implode(',', $escapeIn).')';
		}
  
		return $this; 
	}
	
	
	/**
	*Tworzy czesc zapytania dla sortowania
	*@access public 
	*@return object $this
	*/
	public function OrderBy() 
	{
		if(func_num_args() == 0)
		{
			$orderBy = ' ORDER BY RAND()';
		}
		else if(func_num_args() == 1) 
		{
			$orderBy = ' ORDER BY ';
			$argv = func_get_args();
			$argv = $argv[0];
	  
			if(is_array($argv))
			{
				foreach($argv as $column => $ord) 
				{
					$ord = strtoupper($ord);
		
					if(!in_array($ord, array('ASC', 'DESC', 'NULL')))
					{
						$ord = 'ASC';
					}
					$ordColumn[] = $column.' '.$ord;
				}
				$orderBy .= implode(',', $ordColumn);
			}  
		}
		else if(func_num_args() == 2) 
		{
			$argv = func_get_args();
			$orderBy = ' ORDER BY '.$argv[0].' '.strtoupper($argv[1]);
		}
		$this -> query .= $orderBy; 
		return $this;
	}
	
	
	/**
	*Tworzy czesc zapytania dla groupby
	*@access public 
	*@param string|array $group warunki
	*@return object $this
	*/
	public function GroupBy($group) 
	{
		$groupSql = ' GROUP BY ';
  
		if(is_array($group)) 
		{
			foreach($group as $groupBy)
			{
				$groupClause[] = $groupBy;
			}
			$groupSql .= implode(',', $groupClause);
		}
		else
		{
			$groupSql .= $group;
		}

		$this -> query .= $groupSql;
		return $this;
	}
	
	
	/**
	*Zapytanie insert
	*@access public 
	*@param string $table tabela do ktorej zapisac dane
	*@param array $records rekordy w postaci array('pole' => 'wartosc')
	*@param bool $into czy ma byc czesc dla into (wtedy trzeba uwazac na taka sama kolejnosc zmiennych jak w bazie
	*@return mixed
	*/
	public function Insert($table, $records = array(), $into = false)
	{
		$insert = 'INSERT INTO '.$table;
	
		if(is_array($records) && count($records >= 0)) 
		{
			if($into)
			{
				$insert .= '(';
	
				foreach($records as $column => $record)
				{
					$insCol[] = "`$column`";
				}
				$insert .= implode(',', $insCol);	 
				$insert .= ')';
			}
	 
			$insert .= ' values(';
	  
			foreach($records as $column => $record)
			{
				$insRec[] = $this -> Escape($record);
			}
			$insert .= implode(',', $insRec);
			$insert .= ')';
		}
		return $this -> SetQuery($insert);
	}
	
	
	/**
	*Zapytanie insert dla wielu rekordow
	*@access public 
	*@param string $table tabela do ktorej zapisac dane
	*@param array $records rekordy w postaci array(0 => array('pole' => 'wartosc'))
	*@param bool $into czy ma byc czesc dla into (wtedy trzeba uwazac na taka sama kolejnosc zmiennych jak w bazie
	*@return mixed
	*/
	public function MultiInsert($table, $records = array(), $into = false)
	{
		$insert = 'INSERT INTO '.$table;
		
		if($into)
		{
			$insert .= ' ('.implode(',', array_keys($records[0])).')';
		}
		if(is_array($records) && count($records >= 0)) 
		{
			$insert .= ' values ';
	  
			foreach($records as $record)
			{
				$values = array();
				foreach($record as $column => $value)
				{
					$values[] = $this -> Escape($value);
				}
				$inserts[] = '( '.implode(',', $values).' )';
			}
			$insert .= implode(',', $inserts);
		}
		return $this -> SetQuery($insert);
	}
  
  
	/**
	*zapytanie typu delete
	*@access public 
	*@param string $table tabela z ktorej usunac
	*@param array $where warunek where dla ktorego trzeba usunac rekord
	*@return mixed
	*/
	public function Delete($table, $where = array(), $condition = '=') 
	{
		$delete = 'DELETE FROM '.$table.' WHERE ';
	
		if(is_array($where) && count($where) >= 1) 
		{
			foreach($where as $key => $value)
			{
				$del[] = $key.$condition.$this -> Escape($value);
			}
		}
		$delete .= implode(' and ', $del);
	
		return $this -> SetQuery($delete);
	}
  
  
	/**
	*zapytanie typu update
	*@access public 
	*@param string $table tabela
	*@param array $set wartosci rekordow array('kolumna' => 'wartosc')
	*@param array $where warunek dla zapytania
	*@return mixed
	*/
	public function Update($table, $set = array(), $where = array()) 
	{
		if(is_array($set) && count($set) >= 1) 
		{
			foreach($set as $key => $val)
			{
				$setValue[] = $key.'='.$this -> Escape($val);
			}
		}
		
		if(is_array($where) && count($where) >= 1) 
		{
			foreach($where as $key => $value)
			{
				$up[] = $key.'='.$this -> Escape($value);
			}
		}
			
		$update = 'UPDATE '.$table.' SET '.implode(',', $setValue).' WHERE '.implode(' and ', $up);
		return $this -> SetQuery($update);
	}
	
	
	/**
	*czysci skladowa z zapytaniem
	*@access protected 
	*/
	protected function reset_query()
	{
		$this -> query = '';
	}
	
	
 	/**
	*profiler uwzglednia poczatkowy czas zapytania i jego "tresc", zwieksza licznkin lacznych zapytan
	*@access protected
	*@return string $qid id zapytania
	*/
	protected function start()
	{
		if($this -> benchmark === true) 
		{
			$qid = uniqid();
			Vf_Database::$benchmarks[$qid]['query'] = $this -> query;
			Vf_Database::$benchmarks[$qid]['init_time'] = microtime();
			return $qid; 
		}
	}
	
	
	/**	
	*zatrzymuje profilowanie dla danego zapytania
	*@access public 
	*@param string $qid id zapytania
	*/
	protected function stop($qid)
	{
		if($this -> benchmark === true) 
		{
			Vf_Database::$benchmarks[$qid]['time'] = number_format(microtime() - Vf_Database::$benchmarks[$qid]['init_time'], 6);
			Vf_Database::$benchmarks['all_time'] += Vf_Database::$benchmarks[$qid]['time'];
		}
	}
	
	abstract public function connect();
	abstract public function close();
	abstract public function SetCharset($charset);
	abstract protected function Escape($escape);
	abstract protected function Quote($escape);
	abstract public function SetQuery($query);
	abstract public function Execute();
	abstract public function Limit($limit);
	abstract public function CountRows($resource);
	abstract public function Count($table);
	abstract public function FetchAllAssoc($resource);
	abstract public function FetchAssoc($resource);
	abstract public function FetchObject($resource);
	abstract public function FetchRow($resource);
	abstract public function FetchAllRows($resource);
	abstract public function InsertId();
}


abstract class Vf_Db_Result implements ArrayAccess, Iterator, Countable
{
	protected $result;
	
	protected $insert_id;
	
	protected $total_rows  = 0;
	
	protected $current = 0;
	
	protected $fetch;
	
	
	public function __construct($resource, $type = 'FetchAssoc')
	{
		$this -> result = $resource;
		$this -> fetch = $type;
	}
	
	
	public function setFetchMethod($fetch)
	{
		$this -> fetch = $fetch;
	}
	
	
	abstract public function FetchAssoc();
	abstract public function FetchObject();
	abstract public function FetchRow();
	abstract public function seek($offset);

	
	public function FetchField($field)
	{
		$row = $this -> current();
		return $row[$field];
	}
	
	
	public function insert_id()
	{
		return $this -> insert_id;
	}
	
	
	//Countable
	public function count()
	{
		return $this -> total_rows;
	}
	
	
	//ArrayAccess
	public function offsetExists($offset)
	{
		return $this -> seek($offset);
	}
	
	
	public function offsetUnset($offset)
	{
		throw new Exception("Pola sa tylko do odczytu");
	}
	
	
	public function offsetGet($offset)
	{
		if(!$this -> seek($offset))
		{
			return false;
		}
		return call_user_func(array(&$this, $this -> fetch));
	}
	
	
	public function offsetSet($offset, $value)
	{
		throw new Exception("Pola sa tylko do odczytu");
	}
	
	
	//Iterator
	public function key()
	{
		return $this -> current;
	}
	
	
	public function current()
	{
		return $this -> offsetGet($this -> current);
	}
	
	
	public function rewind()
	{
		$this -> current = 0;
		return $this;
	}
	
	
	public function prev()
	{
		$this -> seek(--$this -> current);
		return $this;
	}
	
	
	public function next()
	{
		$this -> seek(++$this -> current);
		return $this;
	}
	
	
	public function valid()
	{
		return $this -> offsetExists($this -> current);
	}
}

?>