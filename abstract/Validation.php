<?php 

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

require_once(DIR_LIBRARY.'Language.php');

abstract class Vf_Validation
{

	/**
	* Skladowa klasy ktora przechowywuje ustawienia walidacji
	* @access protected
	* @var array $config
	*/
	protected $config = array();

	/**
	* Skladowa klasy ktora przechowywuje instancje klasy Language
	* @access protected
	* @var object $language
	*/
	protected $language = null;
	
	
	/**
	* Tworzymy instancje klasy do obslugi jezykow dla bledow zwracanych przez walidatory
	* @access public
	* @param string $file
	*/
	public function __construct($file = 'Validation.php')
	{
		$this->language = new Vf_Language($file);
	}

	
	/**
	* Metoda ustawia konfiguracje
	* @access public 
	* @param array $config
	*/
	public function configure($config)
	{
		$this->config = $config;
	}
	
	
	/**
	* Metoda zwraca nam dana wartosc z konfiguracji walidacji
	* @access public 
	* @param string $key
	* @return mixed 
	*/
	public function get_option($key)
	{
		return (isset($this->config[$key])) ? $this->config[$key] : null;
	}
	
	
	/**
	* Metoda zwraca pelna konfiguracje walidacji
	* @access public 
	* @return array
	*/
	public function get_options()
	{
		return $this->config;
	}
	
	
	/**
	* Ustawiamy tresc bledy ustawiajac tez nazwe pola
	* @access protected
	* @param string tresc bledu
	* @param string nazwa pola
	* @return string
	*/
	protected function set_error($errorMsg, $fieldname = '') 
	{
		return str_replace('%field%', $fieldname, $errorMsg);
	}
	
}
?>