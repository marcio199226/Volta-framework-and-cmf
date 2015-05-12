<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/


class Vf_Request
{
	const POST = 'POST';
	const GET = 'GET';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
	const HEAD = 'HEAD';
	const OPTIONS = 'OPTIONS';
	const PATCH = 'PATCH';
	const ANY = 'ANY';

	/**
	* Skladowa klasy ktora przechowywuje instancje klasy Vf_Response
	* @access public
	* @var object $response
	*/
	public $response = null;
	
	
	/**
	* Konstruktor klasy ustawia skladawa na objekt Vf_Response
	* @access public 
	*/
	public function __construct($response = null)
	{
		if($response === null) {
			$this->response = new Vf_Response();
		} else {
			$this->response = new Vf_Response($response);
		}
	}
	
	
	/**
	* Zwraca dane z tablicy $_POST o kluczu $key
	* @access public 
	* @param string $key klucz dla tablicy $_POST
	* @param bool $filter czy filtrowac dane
	* @return mixed string|array zwraca wartosc lub tablice z wartoscia o danym kluczu
	*/
	public function post($key = null, $filter = true)
	{
		if ($key === null) {
			return $_POST;
		} elseif (isset($_POST[$key])) {
			if(!is_array($_POST[$key])) {
				return ($filter === true) ? htmlspecialchars(trim($_POST[$key])) : $_POST[$key];
			} else {
				foreach ($_POST[$key] as $k => $v) {
					$post[$k] = ($filter === true) ? htmlspecialchars(trim($v)) : $v;
				}
				return $post;
			}
		}
		return null;
	}
	

	/**
	* Zwraca dane z tablicy $_GET o kluczu $key
	* @access public 
	* @param string $key klucz dla tablicy $_GET
	* @param bool $filter czy filtrowac dane
	* @return string zwraca wartosc o danym kluczu
	*/
	public function get($key = null, $filter = true)
	{
		if ($key === null) {
			return $_GET;
		}
		if (isset($_GET[$key])) {
			return ($filter === true) ? htmlspecialchars(trim($_GET[$key])) : $_GET[$key];
		}
		return null;
	}
	
	
	/**
	* Zwraca dane z tablicy $_SESSION o kluczy $key
	* @access public 
	* @param string $key klucz dla tablicy $_SESSION
	* @param bool $filter czy filtrowac dane
	* @return mixed string|array zwraca wartosc lub tablice z wartoscia o danym kluczu
	*/
	public function session($key = null, $filter = true)
	{
		if ($key === null) {
			return $_SESSION;
		}
		if (isset($_SESSION[$key])) {
			if (!is_array($_SESSION[$key])) {
				return ($filter === true) ? htmlspecialchars(trim($_SESSION[$key])) : $_SESSION[$key];
			} else {
				foreach ($_SESSION[$key] as $k => $v) {
					$session[$k] = ($filter === true) ? htmlspecialchars(trim($v)) : $v;
				}
				return $session;
			}
		}
		return null;
	}
	
	
	/**
	* Zwraca dane z tablicy $_COOKIE o kluczy $key
	* @access public 
	* @param string $key klucz dla tablicy $_COOKIE
	* @param bool $filter czy filtrowac dane
	* @return mixed string|array zwraca wartosc lub tablice z wartoscia o danym kluczu
	*/
	public function cookie($key = null, $filter = true)
	{
		if ($key === null) {
			return $_COOKIE;
		}
		if (isset($_COOKIE[$key])) {
			if (!is_array($_COOKIE[$key])) {
				return ($filter === true) ? htmlspecialchars(trim($_COOKIE[$key])) : $_COOKIE[$key];
			} else {
				foreach ($_COOKIE[$key] as $k => $v) {
					$cookie[$k] = ($filter === true) ? htmlspecialchars(trim($v)) : $v;
				}
				return $cookie;
			}
		}
		return null;
	}
	
	
	/**
	* Pobieramy referer
	* @access public 
	* @return string zwraca referer
	*/
	public function referer()
	{
		return $_SERVER['HTTP_REFERER'];
	}
	
	
	/**
	* Zwraca metode POST/GET/PUT/DELETE
	* @access public 
	* @return string metode
	*/
	public function method()
	{
		return (isset($_SERVER['X_HTTP_METHOD_OVERRIDE'])) ? $_SERVER['X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
	}
	
	
    /**
     * Czy POST?
     * @return bool
     */
	public function isPost()
	{
		return $this->method() === self::POST;
	}
	
	
    /**
     * Czy GET?
     * @return bool
     */
	public function isGet()
	{
		return $this->method() === self::GET;
	}
	
	
    /**
     * Czy PUT?
     * @return bool
     */
	public function isPut()
	{
		return $this->method() === self::PUT;
	}
	
	
    /**
     * Czy DELETE?
     * @return bool
     */
	public function isDelete()
	{
		return $this->method() === self::DELETE;
	}
	
	
    /**
     * Czy HEAD?
     * @return bool
     */
	public function isHead()
	{
		return $this->method() === self::HEAD;
	}
	
	
    /**
     * Czy PATCH?
     * @return bool
     */
	public function isPatch()
	{
		return $this->method() === self::PATCH;
	}
	
	
    /**
     * Czy OPTIONS?
     * @return bool
     */
	public function isOptions()
	{
		return $this->method() === self::OPTIONS;
	}
	
	/**
	* Zwraca ip uzytkownika
	* @access public 
	* @return string ip
	*/
	public function ip()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
	
	
	/**
	* Zwraca user agent uzytkownika
	* @access public 
	* @return string
	*/
	public function userAgent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
	
	/**
	* Zwraca czy otrzymano zadanie ajax
	* @access public 
	* @return bool
	*/
	public function isAjax()
	{
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
			if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
				return true;
			}	
		}
		return false;
	}
	
	
	/**
	* Sprawdza czy wyslano dane json
	* @access public 
	* @return bool
	*/
	public function isJson()
	{
		return isset($_SERVER['HTTP_ACCEPT']) && (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
	}
	
	
	/**
	* Sprawdza czy zadanie jest gospodarowane poprzez restful
	* @access public 
	* @return bool
	*/
	public function isRestful()
	{
		return (Vf_Router::instance()->getFrontController() == 'Rest') ? true : false;
	}
}

?>