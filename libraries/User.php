<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

class Vf_User
{

	/**
	*Skladowa klasy ktora przechowywuje informacje dotyczace uzytkownika pobrane z bazy
	*@access protected
	*@var array $user
	*/
	protected $user = array();
	
	/**
	*Skladowa klasy ktora przechowywuje informacje uzytkownika przez nas ustawione
	*@access protected
	*@var array $data
	*/
	public $data = array();
	
	/**
	*Skladowa klasy ktora przechowywuje nazwe sterownika klasy uzytkownika
	*@access private
	*@var string $driver
	*/
	private $driver;
	
	
	/**
	*Konstruktor ktory tworzy instancje klasy sterownika
	*@access public 
	*@param string $driver nazwa sterownika
	*/
	public function __construct($driver = 'Db')
	{
		if(file_exists(DIR_DRIVERS.'User/'.$driver.'.php'))
			require_once(DIR_DRIVERS.'User/'.$driver.'.php');
			
		$driverName = 'Vf_User_'.$driver.'_Driver';
		$this -> driver = new $driverName();
		
	}
	

	/**
	*Magiczna metoda __call() do ustawiania/pobierania informacji dotyczacyh uzytkownika
	*@access public 
	*@param string nazwa metody
	*@param array $args argumenty metody
	*/
	public function __call($name, $args)
	{
	
		$explode = explode('_', $name);
		$type = $explode[0];
		$field = $explode[1];

		if($type == 'set')		
			$this -> user[$field] = $args[0];
				
		else if($type == 'get')
			return $this -> user[$field];

	}
	
	
	/**
	*Magiczny getter ktory sluzy do pobierania informacji pobranych z bazy na temat uzytkownika
	*@access public 
	*@param string $var nazwa kolumny z bazy
	*/
	public function __get($var)
	{
		return $this -> data[$var];
	}
	
	
	/**
	*Metoda pobiera informacje na temat user-a na podstawie login-u/id
	*@access public 
	*@param string|integer $data login lub id uzytkownika
	*/
	public function get($data)
	{
		if(is_string($data))
			$this -> data = $this -> driver -> get_by_login($data);
		else
			$this -> data = $this -> driver -> get_by_id($data);
	}
	
	
	/**
	*Zwraca nr ip uzytkownika
	*@access public 
	*@return string nr ip
	*/
	public function get_ip()
	{
		return $_SERVER['SERVER_ADDR'];
	}
	
	
	/**
	*Zwraca typ przegladarki uzywanej przez uzytkownika
	*@access public 
	*@return string przegladarka
	*/
	public function get_browser()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
	
	/**
	*Zwraca czas uniksowy
	*@access public 
	*@return integer czas
	*/
	public function get_time()
	{
		return time();
	}
	
	
	/**
	*Zwraca typ uzytkownika
	*@access public 
	*@return string
	*/
	public function user()
	{
		if(isset($_SESSION['role']) && isset($_SESSION['hash']))
		{
			return $_SESSION['role'];
		}
		return 'guest';
	}
	
	
	/**
	*Zwraca czy uzytkownik jest zalogowany lub jest gosciem
	*@access public 
	*@return bool
	*/
	public function is_guest()
	{
		if(!isset($_SESSION['user']) && !isset($_SESSION['hash']))
		{
			return true;
		}
		return false;
	}
	
}

?>