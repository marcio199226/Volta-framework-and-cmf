<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

require_once(DIR_INTERFACES . 'IUser.php');

class Vf_User_Db_Driver implements IUser
{
	/**
	*Skladowa klasy ktora przechowywuje instancje bazy danych
	*@access private
	*@var object|null $db
	*/	
	private $db = null;
	

	/**
	*Konstruktor tworzy instacje bazy danych
	*@access public 
	*/
	public function __construct()
	{
		$this->db = Vf_Db::Factory("MySql");
	}
	
	
	/**
	*Metoda pobiera informacje z bazy na podstawie id uzytkownika
	*@access public 
	*@param integer $id
	*@return array tablica z info
	*/
	public function get_by_id($id)
	{
		if (!empty($id)) {
			$user = $this->db->Select('*', 'users')
				->Where(array('id' => $id))
				->Limit(1)
				->Execute();
							
			return $this->db->FetchAssoc($user);
		}
		return array('id' => null, 'role' => 'guest');
	}
	
	
	/**
	*Metoda pobiera informacje z bazy na podstawie login-u uzytkownika
	*@access public 
	*@param string $login
	*@return array tablica z info
	*/
	public function get_by_login($login)
	{
		if (!empty($login)) {
			$user = $this->db->Select('*', 'users')
				->Where(array('login' => $login))
				->Limit(1)
				->Execute();
							
			return $this->db->FetchAssoc($user);
		}
		return array('id' => null, 'role' => 'guest');
	}	
}

?>