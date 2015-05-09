<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

require_once(DIR_INTERFACES.'IAcl.php');

class Vf_Acl_ini_Adapter implements IAcl
{

	/**
	*Skladowa klasy ktora przechowywuje informacje dotyczace uzytkownika login i id
	*@access private
	*@var array $user
	*/
	private $user = array();
	
	
	/**
	*Setter ktory ustawia dane uzytkownika
	*@access public 
	*@param array $user
	*/
	public function set_user_data($user)
	{
		$this -> user = $user;
	}
	
	
	/**
	*Laduje konfiguracje acl i zwraca ja do glownej klasy
	*@access public 
	*@return array 
	*/
	public function load()
	{
		return parse_ini_file(DIR_CONFIG.'acl/config.ini', true);
		//return $acl[$this -> user['role']];
	}

}

?>