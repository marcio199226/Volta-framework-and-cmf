<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Model.php');


class Vf_Orm_Object implements Iterator
{
	private $key = 0;
	
	private $arr = array();
	
	public function __get($key)
	{
		if(isset($this -> arr[$this -> key()][$key]))
		{
			return $this -> arr[$this -> key()][$key];
		}
	}
	
	public function set($data)
	{
		$this -> arr = $data;
	}
	
	public function rewind()
	{
		$this -> key = 0;
		return $this;
	}
	
	public function key()
	{
		return $this -> key;
	}
	
	public function next()
	{
		++$this -> key;
		return $this;
	}
	
	public function current()
	{
		return $this -> arr[$this -> key];
	}
	
	public function valid()
	{
		return isset($this -> arr[$this -> key]);
	}
	
	public function isLoaded()
	{
		if(is_array($this -> arr))
		{
			return true;
		}
		return false;
	}
	
	public function count()
	{
		return sizeof($this -> arr);
	}
}


class Vf_Orm extends Vf_Model
{
	protected $data = null;
	
	protected $table = '';
	
	protected $primaryKey = '';
	
	protected $join = array(); //join structure array
							
	protected $with = null;
	
	protected $sort = null;
	
	protected $currentPrimaryKeyVal = null;
	
	protected $struct = array();

	protected $loaded = false;
	
	protected $saved = false;
	
	
	public function factory($model, $id = null)
	{
		$modelFullName = 'Vf_'.$model.'_Model';
		$modelName = explode('_', $modelFullName);
		$modelName = $modelName[1];
		
		if(Vf_Loader::existsFile(DIR_MODELS.$modelName.'.php'))
			require_once(DIR_MODELS.$modelName.'.php');
		else if(Vf_Loader::existsFile(DIR_COMPONENTS.$modelName.'/'.DIR_MODELS.$modelName.'.php'))
			require_once(DIR_COMPONENTS.$modelName.'/'.DIR_MODELS.$modelName.'.php');
		else if(Vf_Loader::existsFile(DIR_PLUGINS.$modelName.'/'.DIR_MODELS.$modelName.'.php'))
			require_once(DIR_PLUGINS.$modelName.'/'.DIR_MODELS.$modelName.'.php');
		else if(Vf_Loader::existsFile(DIR_WIDGETS_MODELS.$modelName.'.php'))
			require_once(DIR_WIDGETS_MODELS.$modelName.'.php');
		
		$instance = new $modelFullName();
		
		if($id != null)
		{
			return $instance -> find($id);
		}
		return $instance;
	}
	
	
	public function __get($key)
	{
		if($this -> data != null)
		{
			if(is_array($this -> data))
			{
				if(isset($this -> data[$key]))
				{
					return $this -> data[$key];
				}
			}
			else
			{
				return $this -> data -> {$key};
			}
		}
		return $this -> data[$key];
	}
	
	
	public function  __set($key, $value)
	{
		if($this -> data != null)
		{
			if(is_array($this -> data))
			{
				$this -> data[$key] = $value;
			}
			else
			{
				$this -> data -> {$key} = $value;
			}
		}
		else
		{	
			$this -> data[$key] = $value;
		}
	}
	
	
	public function setPrimaryKey($key)
	{
		$this -> primaryKey = $key;
		return $this;
	}
	
	
	public function find($id = null)
	{
		if($this -> currentPrimaryKeyVal == null)
			$this -> currentPrimaryKeyVal = $id;
		
		if($this -> with != null)
		{
			$this -> data = $this -> join($id);
		}
		else
		{
			$resource = $this -> db -> Select('*', $this -> table) -> Where(array($this -> primaryKey => $id)) -> Execute();
			
			if($this -> db -> CountRows($resource) == 0)
			{
				return $this;
			}
			else if($this -> db -> CountRows($resource) == 1)
			{
				$this -> data = $this -> db -> FetchAssoc($resource);
			}
			else
			{
				$this -> data = $this -> db -> FetchAllAssoc($resource);
			}
		}

		if(is_array($this -> data))
		{
			$this -> loaded = true;
		}		
		return $this;
	}
	
	
	public function findAll($from = 0, $to = 10)
	{
		if($this -> sort === null)
		{
			$resource = $this -> db -> Select('*', $this -> table) -> Limit(array($from, $to)) -> Execute();
		}
		else if($this -> with !== null)
		{
			$data = $this -> join();
		}
		else
		{
			$resource = $this -> db -> Select('*', $this -> table) -> OrderBy($this -> primaryKey, $this -> sort) -> Limit(array($from, $to)) -> Execute();
		}
		
		$this -> data = ($this -> with !== null) ? $data : $this -> db -> FetchAllAssoc($resource);

		if(is_array($this -> data))
		{
			$this -> loaded = true;
		}	
		return $this;
	}
	
	
	public function save($where = array())
	{
		if(sizeof($where) == 0)
		{
			$where = array($this -> primaryKey => $this -> currentPrimaryKeyVal);
		}

		if(sizeof($this -> data) > 0)
		{
			$data = array();
			foreach($this -> data as $column => $value)
			{
				if(in_array($column, $this -> struct))
				{
					$data[$column] = $value;
				}
			}
			if(isset($data[$this -> primaryKey]))
			{
				if($this -> isLoaded())
				{
					if($this -> db -> Update($this -> table, $data, $where))
					{
							$this -> saved = true;
					}
				}
				else
				{
					if($this -> db -> Insert($this -> table, $data, true))
					{
						$this -> saved = true;
					}
				}
			}
		}
		$this -> clear();
		return $this;
	}
	
	
	public function with($table)
	{
		$this -> with = $table;
		return $this;
	}
	
	//zrobic zeby dzialalo dla where i join one to one i one to many
	protected function join($id = null)
	{
		$with = $this -> join[$this -> with];
		$where_primary = $this -> table.'.'.$this -> primaryKey;
		$from = (isset($with['select_from'])) ? $with['select_from'] : $this -> table;
		$order = (isset($with['order'])) ? $with['order'] : null;
		
		foreach($this -> struct as $column)
			$rows[] = $this -> table.'.'.$column.' AS p_'.$column;
		$rows[] = (isset($with['rows'])) ? $with['rows'] : null;
		//$rows = (isset($with['select_form'])) ? $with['select_form'] : '*';
		
		if(isset($with['where']))
			$where = ($id === null) ? $with['where'] : array_merge($with['where'], array($where_primary => $id));
		else
			$where = array($where_primary => $id);
		
		$join = (isset($with['on'])) ? $with['on'] : false;
		
		$this -> db -> Select($rows, $from);
		
		if(is_array($join))
		{
			$this -> db -> Join($this -> with, $join, $with['type']);
		}
		
		if($id != null || isset($with['where']))
		{
			$this -> db -> Where($where);
		}
		
		if($order != null)
		{
			$this -> db -> OrderBy($order);
		}
		
		
		$data = $this -> db -> Execute();
		return ($this -> db -> CountRows($data) == 1) ?  $this -> db -> FetchAssoc($data) : $this -> db -> FetchAllAssoc($data);
	}
	
	
	public function count()
	{
		if(empty($this -> data))
		{
			return 0;
		}
		else if(array_key_exists($this -> primaryKey, $this -> data))
		{
			return 1;
		}
		else
		{
			return sizeof($this -> data);
		}
	}
	
	
	public function isSaved()
	{
		return $this -> saved;
	}
	
	
	public function isLoaded()
	{
		return $this -> loaded;
	}
	
	
	public function delete($where = array())
	{
		if(sizeof($where) == 0)
		{
			$where = array($this -> primaryKey => $this -> currentPrimaryKeyVal);
		}
		
		if($del = $this -> db -> Delete($this -> table, $where))
		{
			$this -> clear();
			return true;
		}
		return false;
	}
	
	
	public function update($values, $where = array())
	{
		if(sizeof($where) == 0)
		{
			$where = array($this -> primaryKey => $this -> currentPrimaryKeyVal);
		}
		
		if($update = $this -> db -> Update($this -> table, $values, $where))
		{
			$this -> clear();
			return true;
		}
		return false;
	}
	
	
	public function insert($values)
	{
		if($insert = $this -> db -> Insert($this -> table, $values, true))
		{
			$this -> clear();
			return true;
		}
		return false;
	}
	
	
	protected function clear()
	{
		if(!empty($this -> currentPrimaryKeyVal))
		{
			$this -> currentPrimaryKeyVal = null;
		}
	}
	
	
	public function setSort($sort = 'asc')
	{
		$this -> sort = $sort;
		return $this;
	}
	
	
	public function toArray()
	{
		if(sizeof($this -> data) > 0)
		{
			return new ArrayObject($this -> data);
		}
		return array();
	}
	
	
	public function toObject()
	{
		$object = new Vf_Orm_Object();
		
		if(is_array($this -> data) && sizeof($this -> data) > 0)
		{
			$object -> set($this -> data);
		}
		return $object;
	}
}
?>