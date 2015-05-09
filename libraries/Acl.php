<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

class Vf_Acl
{

	/**
	*Skladowa klasy ktora uwzglednia sterownik ktory ma zostac uzyty
	*@access protected
	*@var string $acl_cfg
	*/
	protected $acl_cfg = 'ini';
	
	/**
	*Skladowa klasy ktora przechowywuje informacje na temat uzytkownika
	*@access protected
	*@var array $user
	*/
	protected $user = array();
	
	/**
	*Skladowa klasy ktora przechowywuje prawa dostepu danego uzytkownika
	*@access protected
	*@var array $acl_perms
	*/
	protected $acl_perms = array();

	/**
	*Pobiera prawa na podstawie sterownika
	*@access public 
	*@return bool
	*/
	public function load_rules()
	{
		if(file_exists(DIR_DRIVERS.'Acl/' . $this -> acl_cfg.'.php'))
			include_once(DIR_DRIVERS.'Acl/' . $this -> acl_cfg.'.php');
		
		else
			return false;
			
		$class = 'Vf_Acl_'.$this -> acl_cfg.'_Adapter';
		$acl_obj = new $class();
		
		if($acl_obj instanceof IAcl)
		{
			$acl_obj -> set_user_data($this -> user);
			$this -> acl_perms = $acl_obj -> load();
			$this -> acl_perms['user_role'] = $this -> acl_perms[$this -> user['role']];
			$this -> acl_perms['all'] = $this -> acl_perms;
			return true;
		}
		
		else
			throw new Volta_Acl_Exception("Loader ustawien acl musi posiadac interfejs IAcl");
		
	}
	
	
	/**
	*Ustawia dane dotyczace uzytkownika
	*@access public 
	*@param string $role rola uzytkownika user/moderator/admin itp...
	*@param int $id unikalny nr uzytkownika (id)
	*/
	public function set_user_role($role, $id = null)
	{
		$this -> user['role'] = $role;
		$this -> user['id'] = $id;
	}
	
	
	/**
	*Dodaje nowy zasob do ustawien acl
	*@access public 
	*@param string|array $resource zasob/zasoby do dodania
	*/
	public function add_resource($resource)
	{
		if(!is_array($resource))
			$this -> acl_perms['user_role'][$resource] = array();
			
		else
		{
			foreach($resource as $res)
				$this -> acl_perms['user_role'][$res] = array();
		}
	}
	
	
	/**
	*Dodanie roli dla grupy uzytkownika
	*@access public 
	*@param string $gruop grupa
	*@param string $resource zasob
	*@param string $role rola
	*/
	public function add_role($resource, $role)
	{
		$this -> acl_perms['user_role'][$resource][] = $role;
	}
	
	
	/**
	*Usuwanie roli z grupy uzytkownika dla danego zasobu
	*@access public 
	*@param string $resource zasob
	*@param string $role rola
	*@return bool
	*/
	public function remove_role($resource, $role)
	{
		if(in_array($role, $this -> acl_perms['user_role'][$resource]))
		{
			foreach($this -> acl_perms['user_role'][$resource] as $key => $rl)
			{
				if($rl == $role)
				{
					unset($this -> acl_perms['user_role'][$resource][$key]);
					return true;
				}
			}
				return false;	
		}
		return false;
	}
	
	
	/**
	*Usuwa zasob z grupy uzytkownika
	*@access public 
	*@param string $resource zasob
	*@return bool
	*/
	public function remove_resource($resource)
	{
		if(isset($this -> acl_perms['user_role'][$resource]))
		{
			unset($this -> acl_perms['user_role'][$resource]);
			return true;
		}
			
		return false;
	}
	

	/**
	*Usuwa calkowicie grupe
	*@access public 
	*@param string $gruop grupa
	*@return bool
	*/
	public function remove_group($group)
	{
		if(isset($this -> acl_perms['all'][$group]))
		{
			unset($this -> acl_perms['all'][$group]);
			return true;
		}
			
		return false;
	}
	
	
	/**
	*sprawdza czy mamy grupe
	*@access public 
	*@param string $gruop grupa
	*@return bool
	*/
	public function has_group($group)
	{
		if($group == $this -> user['role'])
		{
			return true;
		}	
		return false;
	}
	
	
	/**
	*sprawdza czy mamy prawa do danego komponentu/pluginu
	*@access public 
	*@param string $gruop grupa
	*@return bool
	*/
	public function has_resource($resource)
	{
		if(array_key_exists($resource, $this -> acl_perms['user_role']))
		{
			return true;
		}	
		return false;
	}
	
	
	/**
	*sprawdza czy mamy role w danym zasobie
	*@access public 
	*@param string $gruop grupa
	*@return bool
	*/
	public function has_role($resource, $role)
	{
		if(in_array($role, $this -> acl_perms['user_role'][$resource]))
		{
			return true;
		}	
		return false;
	}
	
	
	/**
	*Rozszerza grupe uzytkownika o prawa innych grup
	*@access public 
	*@param string|array $child
	*/
	public function extend($child)
	{
		if(!is_array($child))
		{
			if(array_key_exists($child, $this -> acl_perms['all']))
				$this -> acl_perms['user_role'] = array_merge($this -> acl_perms['user_role'], $this -> acl_perms['all'][$child]);
		}
		
		else
			foreach($child as $group)
			if(array_key_exists($group, $this -> acl_perms['all']))
				$this -> acl_perms['user_role'] = array_merge($this -> acl_perms['user_role'], $this -> acl_perms['all'][$group]);
		
	}
	
	/**
	*Sprawdzenie czy uzytkownik posiada odpowiednie prawa
	*@access public 
	*@param string $resource zasob
	*@param string $role rola
	*@return bool
	*/
	public function is_allowed($resource, $role)
	{
	
		if(!array_key_exists($resource, $this -> acl_perms['user_role']))
			throw new Volta_Acl_Deny_Exception("Brak uprawnien");
			
		else if(in_array('*', $this -> acl_perms['user_role'][$resource]))
			return true;
			
		else if(in_array($role, $this -> acl_perms['user_role'][$resource]))
			return true;
			
		else
			throw new Volta_Acl_Deny_Exception("Nie posiadasz wystarczajacyh uprawnien");
	}

}

?>