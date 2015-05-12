<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/
require_once(DIR_ABSTRACT . 'Validation.php');
require_once(DIR_INTERFACES . 'IValidation.php');

class str extends Vf_Validation implements IValidation
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
	* Metoda ktora sprawdza walidacje danych na podstawie wczesniej ustawionej konfiguracji
	* @access public 
	* @param string $object tresc do walidacji
	* @return bool|string
	*/
	public function is_valid($object)
	{	
		if ($this->get_option('required')){
			$message = $this->language->get()->required;
			if (is_array($object)) {
				foreach ($object as $key => $value) {
					if (empty($value)) {
						return $this->set_error($message, $this->get_option('field'));
					}
				}
			} else {
				if (empty($object)) {
					return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if ($this->get_option('min')) {
			$message = $this->language->get()->MinLenght;
			if (is_array($object)) {
				foreach ($object as $key => $value) {
					if (strlen($value) < $this->get_option('min')) {
						return $this->set_error($message, $this->get_option('field'));
					}
				}
			} else {
				if (strlen($object) < $this->get_option('min')) {
					return $this->set_error($message, $this->get_option('field'));
				}
			}
		}

		if ($this->get_option('max')) {
			$message = $this->language->get()->MaxLenght;
			if (is_array($object)) {
				foreach ($object as $key => $value) {
					if (strlen($value) > $this->get_option('max')) {
						return $this->set_error($message, $this->get_option('field'));
					}
				}
			} else {
				if (strlen($object) > $this->get_option('max')) {
					return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if ($this->get_option('between')) {
			$between = $this->get_option('between');
			$message = $this->language->get()->Between;
			if (is_array($object)) {
				foreach ($object as $key => $value) {
					if ((strlen($value) < $between[0]) || (strlen($value) > $between[1])) {
						return $this->set_error($message, $this->get_option('field'));
					}
				}
			} else {
				if ((strlen($object) < $between[0]) || (strlen($object) > $between[1])) {
					return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if($this->get_option('digit')) {
			$message = $this->language->get()->Digit;
			if (is_array($object)) {
				foreach ($object as $key => $value) {
					if (!preg_match('/^[0-9]/', $value)) {
						return $this->set_error($message, $this->get_option('field'));
					}
				}
			}
			else {
				if (!preg_match('/^[0-9]/', $object)) {
					return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if ($this->get_option('alpha')) {
			$message = $this->language->get()->Alpha;
			if (is_array($object)) {
				foreach ($object as $key => $value) {
					if (!preg_match('/^[a-zA-Z\-\_\@]/', $value)) {
						return $this->set_error($message, $this->get_option('field'));
					}
				}
			} else {
				if (!preg_match('/^[a-zA-Z\-\_\@]/', $object)) {
					return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if ($this->get_option('alphadigit')) {
			$message = $this->language->get()->AlphaDigit;
			if (is_array($object)) {
				foreach ($object as $key => $value) {
					if (!preg_match('/^[a-zA-Z\-\_\@0-9]/', $value)) {
						return $this->set_error($message, $this->get_option('field'));
					}
				}
			} else {
				if (!preg_match('/^[a-zA-Z\-\_\@0-9]/', $object)) {
					return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if($this->get_option('email')) {
			if(!preg_match('/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i', $object)) {
				$message = $this->language->get()->Email;
				return $this->set_error($message, $this->get_option('field'));
			}
		}
		
		if($this->get_option('compare_pwd')) {
			if($object != $_POST[$this->get_option('compare_pwd')]) {
				$message = $this->language->get()->ComparePwd;
				return $this->set_error($message);
			}
		}
		return true;
	}
}

?>