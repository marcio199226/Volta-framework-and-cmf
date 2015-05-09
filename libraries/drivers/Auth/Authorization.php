<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

require_once(DIR_INTERFACES.'IAuth.php');

class Vf_Auth_Authorization_Driver implements IAuth
{

	/**
	*Czy haslo ma byc poddane funkcji hashowania
	*@access protected
	*@var bool $hashPasswd
	*/	
	protected $hashPasswd = true;

	/**
	*Skladowa klasy ktora przechowywuje sol do hashowania hasla
	*@access protected
	*@var string $salt
	*/	
	protected $salt;
	
	/**
	*Skladowa klasy ktora przechowywuje obiekt typu User
	*@access private
	*@var object|null $user
	*/
	private $user = null;
	
	/**
	*Skladowa klasy ktora przechowywuje obiekt bazy danych
	*@access private
	*@var object|null $db
	*/
	private $db = null;
	

	/**
	*Konstruktor tworzy instacje bazy danych i ustawia obiekt uzytkownika
	*@access public 
	*@param object $user
	*/
	public function __construct(Vf_User $user)
	{
		$auth_config = new Vf_Config('config.Authorization');
		$this -> salt = $auth_config -> salt;
		$this -> user = $user;
		$this -> db = Vf_Db::Factory("MySql");
	}
	
	
	/**
	*Metoda autoryzacji uzytkownika do systemu
	*@access public 
	*@return bool
	*/
	public function login()
	{
		$data = $this -> user -> get($this -> user -> get_login());

		if($this -> user -> get_login() == $this -> user -> login && $this -> __hash_pwd($this -> user -> get_password()) == $this -> user -> haslo)
		{
			$_SESSION['ip'] = $this -> user -> get_ip();
			$_SESSION['browser'] = $this -> user -> get_browser();
			$_SESSION['user'] = $this -> user -> login;
			$_SESSION['hash'] = $this -> user -> hash;
			$_SESSION['role'] = $this -> user -> role;
			$_SESSION['time'] = time();
			return true;
		}	
		return false;
	}
	

	/**
	*Metoda wylogowywania uzytkownika z systemu
	*@access public 
	*/
	public function logout()
	{
		unset($_SESSION['ip']);
		unset($_SESSION['browser']);
		unset($_SESSION['user']);
		unset($_SESSION['hash']);
		unset($_SESSION['role']);
		unset($_SESSION['time']);
		session_destroy();
	}
	

	/**
	*Metoda ktory sprawdza czy uzytkownik jest na pewno zalogowany i nie ingerowal w sesje
	*@access public 
	*@return bool
	*/
	public function is_logged()
	{
		if(isset($_SESSION['user']) && isset($_SESSION['hash']))
		{
			$user = $this -> db -> Select('*', 'users')
								-> Where(array('hash' => $_SESSION['hash']))
								-> Limit(1)
								-> Execute();
		
			$user_data = $this -> db -> FetchAssoc($user);

			if($this -> db -> CountRows($user) > 0 && $user_data['login'] == $_SESSION['user'])
			{
				if($_SESSION['ip'] == $this -> user -> get_ip() && $_SESSION['browser'] == $this -> user -> get_browser() && $_SESSION['role'] == $user_data['role'])
				{
					return true;
				}
			
				else
				{
					$this -> logout();
					throw new Volta_Auth_Exception("Zostales wylogowany.");
				}
			}
			else
			{
				$this -> logout();
				throw new Volta_Auth_Exception("Zostales wylogowany.");
			}
		}
		else
		{
			//throw new Volta_Auth_Exception("Nie jestes zalogowany");
			return false;
		}
	}
	
	
	/**
	*Metoda ktora ustawia czy haslo ma byc hashowane
	*@access public
	*@param bool $hash
	*/
	public function hashPassword($hash) 
	{
		$this -> hashPasswd = $hash;	
	}
	
	
	/**
	*Metoda ktory hashuje haslo razem z sola
	*@access private
	*@param string $password haslo uzytkownika
	*@return string hashowane haslo
	*/
	private function __hash_pwd($password) 
	{
		return ($this -> hashPasswd === true) ? sha1($this -> salt.$password) : $password;	
	}
}

?>