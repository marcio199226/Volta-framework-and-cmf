<?

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT . 'Database.php');
require_once(DIR_INTERFACES . 'ITransactions.php');

class Vf_Mysql_Query_Adapter extends Vf_Database implements ITransactions
{
	protected $connection;
	
	protected $config;
	
	protected $transaction;
	
	
	public function connect() 
	{
		$this->config = new Vf_Config(DIR_CONFIG, 'Xml');
  
		if($this->connection = mysql_connect($this->config->host, $this->config->login, $this->config->pwd)) 
		{
			if(!mysql_select_db($this->config->db))
			{
				throw new Vf_Db_Select_Exception('Wystapil problem podczas wybierania bazy: '.$this->config->db);
			}
		
			if(!empty($this->config->charset)) 
			{
				$this->SetCharset($config->charset);
			}
			return $this->connection;
		}
		else
		{
			throw new Vf_Db_Connection_Exception("Wystapil blad podczas laczenia z baza");
		}
	}
	
	
	/**
	*Ustawia kodowanie
	*@access public 
	*@param string $charset typ kodowania
	*/
	public function SetCharset($charset) 
	{
		$this->SetQuery('SET NAMES '.$this->Escape($charset));
	}
	
	
	protected function Escape($escape)
	{
		switch(gettype($escape))
		{
			case 'string':
						$val = "'".mysql_real_escape_string($escape)."'";
			break;
						
			case 'integer':
						$val = intval($escape);
			break;
					
			case 'double':
						$val = floatval($escape);
			break;
			
			default:
						$val = "'$escape'";
			break;
		}
		return $val;
	}
	
	
	protected function Quote($tblOrColumn)
	{
		$tbl_column = explode('.', $tblOrColumn);
		
		if($tblOrColumn == '*')
		{
			return $tblOrColumn;
		}
		else if(sizeof($tbl_column) == 1)
		{
			return '`'.$tblOrColumn.'`';
		}
		else
		{
			return '`'.$tbl_column[0].'`.`'.$tbl_column[1].'`';
		}
	}
	
	
	public function Limit($limit) 
	{
		$lmt = ' LIMIT ';
		$lmt .= (is_array($limit)) ? implode(',', $limit) : $limit;
		$this->query .= $lmt;
	
		return $this; 
	}
	
	
	/**
	*Wykonuje zapytanie
	*@access public 
	*@param string $query zapytanie sql do wykonania
	*@return mixed handler zapytania
	*/
	public function SetQuery($query) 
	{
		$sql = mysql_query($query);
		self::$queries++;
		
		if($sql === false)
		{
			throw new Vf_Db_Exception(mysql_error(), $query);
		}	
		return $sql;
	}
	
	
	/**
	*Wysyla zapytanie do bazy
	*@access public 
	*@return mixed $result
	*/
	public function Execute() 
	{
		$qid = $this->start();

		if($result = $this->SetQuery($this->query)) 
		{
			$this->reset_query();
			$this->stop($qid);
			$data = ($this->toObject === true) ? new Vf_Db_MySql_Result($result, $this->connection) : $result;
			$this->toObject = false;
			return $data;
		}
		return false;
	}
	
	
	public function getLastQuery()
	{
		return $this->last_query;
	}
	
	
	/**
	*zamyka polaczenie z baza
	*@access public 
	*/
	public function close() 
	{
		if(is_resource($this->connection)) 
		{
			mysql_close($this->connection);
		}
	}
	
	
	public function begin()
	{
		$this->transaction = true;
		return @mysql_query('BEGIN');
	}
	
	
	public function commit()
	{
		if($this->transaction)
		{
			$this->transaction = false;
			return @mysql_query('COMMIT');
		}
	}
	
	
	public function rollback()
	{
		if($this->transaction)
		{
			$this->transaction = false;
			return @mysql_query('ROLLBACK');
		}
	}
	
	
	public function lock($tables)
	{
		return @mysql_query('LOCK TABLES '.implode(',', $tables));
	}
	
	
	public function unlock()
	{
		return @mysql_query('UNLOCK TABLES');
	}
	
	
	/**
	*liczy ilosc rekordow
	*@access public 
	*@param resource $resource
	*@return int
	*/
	public function CountRows($resource)
	{
		$rows = mysql_num_rows($resource);
		return $rows;
	}
	
	
	/**
	*liczy ilosc rekordow
	*@access public 
	*@param $table tabela z ktorej ma policzyc rekordy
	*@return int
	*/
	public function Count($table)
	{
		$data = $this->SetQuery('select COUNT(*) from '.$this->Quote($table));
		$fetch = $this->FetchRow($data);
		return $fetch[0];
	}

	
	/**
	*zwraca rekordy w postaci tablicy
	*@access public 
	*@param resource $resource
	*@return array
	*/
	public function FetchAllAssoc($resource) 
	{
		$data = array();
		while($tab = mysql_fetch_assoc($resource))
		{
			$data[] = $tab;
		}
		return $data;
	}
  

	/**
	*zwraca rekord w postaci tablicy
	*@access public 
	*@param resource $resource
	*@return array
	*/
	public function FetchAssoc($resource) 
	{
		return mysql_fetch_assoc($resource);
	}
	
	
	/**
	*zwraca rekord w postaci objektu
	*@access public 
	*@param resource $resource
	*@return object
	*/
	public function FetchObject($resource) 
	{
		return mysql_fetch_object($resource);
	}
	

	/**
	*zwraca rekord w postaci tablicy z kluczami numerycznymi
	*@access public 
	*@param resource $resource
	*@return array
	*/
	public function FetchRow($resource) 
	{
		return mysql_fetch_row($resource);
	}


	/**
	*zwraca rekordy w postaci tablicy
	*@access public 
	*@param resource $resource
	*@return array
	*/
	public function FetchAllRows($resource) 
	{
		$rows = array();
		while($tab = mysql_fetch_row($resource)) 
		{
			$rows[] = $tab;
		}
		return $rows;
	}
  
  
	/**
	*mysql_insert_id()
	*@access public 
	*@return integer
	*/
	public function InsertId() 
	{
		return mysql_insert_id();
	}
	
	
	public function getQueriesNumber()
	{
		return self::$queries;
	}
}


class Vf_Db_Mysql_Result extends Vf_Db_Result
{		
	public function __construct($resource, $connection, $type = 'FetchAssoc')
	{
		parent::__construct($resource, $type);
		$this->connection = $connection;
		
		if(is_bool($this->result))
		{
			$this->insert_id = mysql_insert_id($connection);
			$this->total_rows = mysql_affected_rows($connection);
		}
		else if(is_resource($this->result))
		{
			$this->total_rows = mysql_num_rows($this->result);
		}
	}
	
	
	public function __destruct()
	{
		if (is_resource($this->result))
		{
			mysql_free_result($this->result);
		}
	}
	
	
	public function FetchAssoc() 
	{
		return mysql_fetch_assoc($this->result);
	}
	
	
	public function FetchObject() 
	{
		return mysql_fetch_object($this->result);
	}
	
	
	public function FetchRow() 
	{
		return mysql_fetch_row($this->result);
	}
	
	
	public function seek($offset)
	{
		if($offset < $this->total_rows)
		{
			return mysql_data_seek($this->result, $offset);
		}
		return false;
	}
}

?>