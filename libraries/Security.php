<?php

/**
* Form Builder & Admin Generator

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2011, marcio
* @version 1.6.5
*/

class Vf_Security
{

	/**
	* Skladowa klasy ktora uwzglednia czy token do csrf ma dzialac lub nie
	* @var bool $csrf
	* @access protected
	*/
	protected $csrf = true;
	
	/**
	* WLaczamy/Wylaczamy csrf
	* @access public
	* @param bool $val
	*/
	public function set_csrf($val)
	{
		$this->csrf = $val;
	}

	/**
	* Zwraca czy wlaczony/wylaczony
	* @access public
	* @return bool
	*/
	public function is_csrf()
	{
		return $this->csrf;
	}
	
	/**
	* Filtruje globalne zmienne
	* @access private 
	* @param bool $post filtracja post
	* @param bool $get filtracja get
	* @param bool $cookie filtracja cookie
	* @param bool $session filtracja session
	*/
	public function filter_vars($post = false, $get = false, $cookie = false, $session = false)
	{
		if ($post) {
			foreach($_POST as $key => $value) {
				$_POST[$key] = htmlspecialchars(trim($value));
			}
		}
		if($get) {
			foreach($_GET as $key => $value) {
				$_GET[$key] = htmlspecialchars(trim($value));
			}
		}
		if($cookie) {
			foreach($_POST as $key => $value) {
				$_POST[$key] = htmlspecialchars(trim($value));
			}
		}
		if($session) {
			foreach($_SESSION as $key => $value) {
				$_SESSION[$key] = htmlspecialchars(trim($value));
			}
		}
	}
	
	
	/**
	* Generuje token dla csrf
	* @access public
	* @return string
	*/
	public function csrf_token_generate()
	{
		if ($this->is_csrf()) {
			$token = substr(md5(uniqid(rand(), true)), 1, 25);
			Vf_Core::getContainer()->request->response->setSession('token', $token);
			return $token;
		}
		return null;
	}
	
	
	/**
	* Sprawdza zgodnosc tokenu z get'a z tym z zapisanym w sesji
	* @access public
	* @return bool
	*/
	public function csrf_check_token($token)
	{
		if ($this->csrf) {		
			return ($token == Vf_Core::getContainer()->request->session('token')) ? true : false; 
		}
		return null;	
	}
}


?>