<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_ABSTRACT.'Validation.php');
require_once(DIR_INTERFACES.'IValidation.php');

class user extends Vf_Validation implements IValidation
{
	
	/**
	*Konstruktor ustawia konfiguracje walidatora
	*@access public 
	*@param array $cfg
	*/
	public function __construct($cfg)
	{
		parent::__construct();
		$this -> configure($cfg);
	}
	
	
	/**
	*Metoda ktora sprawdza walidacje danych na podstawie wczesniej ustawionej konfiguracji
	*@access public 
	*@param string $object tresc do walidacji
	*@return bool|string
	*/
	public function is_valid($object)
	{	
		if($this -> get_option('check_ban'))
		{
			if(empty($object))
			{
				return true;
			}
			
			$message =  $this -> language -> get() -> Banned;
			$ban = Vf_Orm::factory('Ban');
			
			if($ban -> isBanned($object))
			{
				return $this -> set_error($message);
			}
		}
		
		
		if($this -> get_option('check_is_active'))
		{
			$request = new Vf_Request();
			if(empty($object))
			{
				return true;
			}
			
			$message =  $this -> language -> get() -> AccountDisabled;
			$account = Vf_Orm::factory('UserActive') -> find($request -> post('login'));
			
			if($account -> isLoaded() && $account -> active == 0)
			{
				return $this -> set_error($message);
			}
		}
		
		
		if($this -> get_option('check_user'))
		{
			$message =  $this -> language -> get() -> UserExists;
			$user = Vf_Orm::factory('UserExists', $_POST[$this -> get_option('check_user')]);
			
			if($user -> isLoaded())
			{
				return $this -> set_error($message);
			}
		}
		
		return true;
	}
}

?>