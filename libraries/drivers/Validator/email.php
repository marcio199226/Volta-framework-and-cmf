<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_ABSTRACT . 'Validation.php');
require_once(DIR_INTERFACES . 'IValidation.php');

class email extends Vf_Validation implements IValidation
{
	/**
	* Konstruktor ustawia konfiguracje walidatora
	* @access public 
	* @param array $cfg
	*/
	public function __construct($cfg)
	{
		parent::__construct();
		$this->configure($cfg);
	}
	
	
	/**
	* Validator sprawdza czy istnieje juz taki email w bazie danych
	* @access public 
	* @param string $object tresc do walidacji
	* @return bool|string
	*/
	public function is_valid($object)
	{	
		if ($this->get_option('check_email')) {
			$message =  $this->language->get()->EmailExists;
			$user = Vf_Orm::factory('EmailExists', $_POST[$this->get_option('check_email')]);
			
			if ($user->isLoaded()) {
				return $this->set_error($message);
			}
		}
		return true;
	}
}

?>