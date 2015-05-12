<?php 

/**
* Form Builder & Admin Generator

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2011, marcio
* @version 1.6.5
*/

class Vf_Validator
{
	
	/**
	* Skladowa klasy ktora przechowywuje nazwe pola i konfiguracje walidacji
	* @access protected
	* @var array $validator
	*/
	protected $validator = array();
	
	/**
	* Dane ktora waliduje klasa
	* @access protected
	* @var array $data
	*/
	protected $data = array();
	
	/**
	* Zwrocone bledy przez validator
	* @access protected
	* @var array $errors
	*/
	protected $errors = array();
	
	
	/**
	* Metoda laduje poszeczegolne walidadotory
	* @access public 
	* @param array|string $validators
	*/
	public function load($validators)
	{
		if (is_array($validators)) {
			foreach ($validators as $validator) {
				if (file_exists(DIR_DRIVERS . 'Validator/' . $validator . '.php')) {
					require_once(DIR_DRIVERS . 'Validator/' . $validator . '.php');
				}
			}
		} elseif (file_exists(DIR_DRIVERS . 'Validator/' . $validators . '.php')) {
			require_once(DIR_DRIVERS . 'Validator/' . $validators . '.php');
		}	
	}
	
	
	/**
	* Sprawdzamy czy submitowano formularz do walidacji
	* @access public 
	* @param string $post_key
	* @return bool
	*/
	public function submitted($post_key)
	{
		return (isset($_POST[$post_key])) ? true : false;
	}
	
	
	/**
	* Ustawiamy dane do walidacji ($_POST)
	* @access public 
	* @param array $data
	*/
	public function add_data()
	{
		$args = func_num_args();
		if ($args == 1) {
			$this->data = func_get_arg(0);
		} elseif ($args == 2) {
			$argv = func_get_args();
			$this->data = array_merge($argv[0], $argv[1]);
		}
	}
	
	
	/**
	* Dodajemy reguly walidacji
	* @access public 
	* @param string $key nazwa pola
	* @param object instacja klasy walidatora wraz z konfiguracja
	*/
	public function add_rule($key, IValidation $validator)
	{
		$this->validator[$key] = $validator;
	}	
	
	
	/**
	* Walidujemy dane
	* @access public 
	*/
	public function validation()
	{
		foreach ($this->validator as $key => $object) {
			$data = $this->validator[$key]->is_valid($this->data[$key]);
			if ($data !== true) {
				$this->errors[$key] = $data;
			}
		}
	}
	
	
	/**
	* Zwracamy bledy otrzymane po walidacji
	* @access public 
	* @param string $err klucz bledu
	* @return string|array
	*/
	public function get_errors($err = null)
	{
		if (!is_null($err)) {
			return $this->errors[$err];
		}
		return $this->errors;
	}
}

?>