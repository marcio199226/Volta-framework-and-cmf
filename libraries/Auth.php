<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

class Vf_Auth
{

	/**
	*Skladowa klasy ktora przechowywuje sterownik autoryzacji
	*@access private
	*@var object|null $auth
	*/
	private $auth = null;
	
	
	/**
	*Konstruktor ktory ustawia nam obiekt uzytkownika i nazwe sterownika do uzycia
	*@access public 
	*@param object $user obiekt klasu typu User
	*@param string $driver nazwa sterownika
	*/
	public function __construct(Vf_User $user, $driver = 'Authorization')
	{
		if(file_exists(DIR_DRIVERS.'Auth/'.$driver.'.php'))
			require_once(DIR_DRIVERS.'Auth/'.$driver.'.php');
		
		$driverName = 'Vf_Auth_'.$driver.'_Driver';
		$this -> auth = new $driverName($user);
	}
	
	
	/**
	*Adapter metody login() ze sterownika
	*@access public 
	*@return bool
	*/
	public function login()
	{
		return $this -> auth -> login();
	}
	

	/**
	*Adapter metody logout() ze sterownika
	*@access public 
	*/
	public function logout()
	{
		$this -> auth -> logout();
	}
	

	/**
	*Adapter metody is_logged() ze sterownika
	*@access public 
	*@return bool
	*/
	public function is_logged()
	{
		return $this -> auth -> is_logged();
	}
	
	
	/**
	*Zwraca obiekt naszego adaptera
	*@access public 
	*@return bool
	*/
	public function getAdapter()
	{
		return $this -> auth;
	}
}

?>