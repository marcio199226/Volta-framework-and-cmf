<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Response
{
	/**
	* Skladowa klasy ktora przechowywuje kody statusow http
	* @access public
	* @var array $messages
	*/
	public $messages = array(
		// Informational 1xx
		100 => 'Continue',
		101 => 'Switching Protocols',

		// Success 2xx
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',

		// Redirection 3xx
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found', // 1.1
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		// 306 is deprecated but reserved
		307 => 'Temporary Redirect',

		// Client Error 4xx
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		422 => 'Unprocessable Entity',
		423 => 'Locked',
		424 => 'Failed Dependency',

		// Server Error 5xx
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		507 => 'Insufficient Storage',
		509 => 'Bandwidth Limit Exceeded'
	);

	/**
	* Skladowa klasy ktora przechowywuje wszystkie naglowki zapisujemy jako $responseInstance ->headers['Pragma'] = 'no-cache'
	* @access public
	* @var array $headers
	*/
	public $headers = array();
	
	
	/**
	* Skladowa klasy ktora przechowywuje instancje klasy Vf_Flash
	* @access public
	* @var object $flash
	*/
	public $flash = null;
	
	
	/**
	* Skladowa klasy ktora przechowywuje kod ktory ma byc zwrocony do przegladarki
	* @access protected
	* @var string $response
	*/
	protected $response = null;
	
	/**
	* Skladowa klasy ktora przechowywuje kod json ktory ma byc zwrocony do przegladarki
	* @access protected
	* @var array $json
	*/
	protected $json = array();

	
	/**
	* Konstruktor ktory moze dzialac jako setter dla skladowej response
	* @access public 
	* @param string $response data for response
	*/
	public function __construct($response = null)
	{
		if ($response !== null) {
			$this->response = $response;
		}
		$this->flash = new Vf_Flash();
		return $this;
	}
	
	
	/**
	* Magiczna metoda ktora zwroci nam response jesli wyswietlimy obiekt jako zmienna
	* @access public 
	* @return string response
	*/
	public function __toString()
	{
		return $this->response;
	}
	
	
	/**
	* Wysylanie naglowkow http
	* @access public 
	* @param array $headers tablica naglowkow klucz => wartosc
	*/
	public function sendHttpHeaders($headers = null, $replace = true)
	{
		ob_get_clean();
		if(sizeof($headers) > 0 && sizeof($this->headers) > 0) {
			$heads = array_merge($headers, $this->headers);
			foreach($heads as $header => $value) {
				header($header . ': ' . $value, $replace);
			}
		} elseif (is_array($headers)) {
			foreach($headers as $header => $value) {
				header($header . ': ' . $value, $replace);
			}
		} elseif ($headers === null && sizeof($this->headers) > 0) {
			foreach($this->headers as $header => $value) {
				header($header . ': ' . $value, $replace);
			}
		} else {
			throw new InvalidArgumentException('Vf_Response::sendHttpHeaders only accepts array value');
		}
		return $this;
	}

	/**
	* Ustawianie statusu http
	* @access public 
	* @param int $status kod statusu
	*/
	public function setHttpStatus($status = null)
	{
		if(is_numeric($status)) {
			header('HTTP/1.1 ' . $status . ' ' . $this->messages[$status], true, $status);
		} else {
			throw new InvalidArgumentException('Vf_Response::setHttpStatus only accepts an integer status value');
		}
		return $this;
	}
	

	/**
	* Ustawianie cache control
	* @access public 
	* @param string $type public/private
	* @param int $age maksymalny czas
	*/
	public function cache($type, $age = null)
	{
		$cache = $type;
		if($age !== null && is_integer($age)) {
			$cache .= ', max-age: ' . $age;
		} else {
			throw new InvalidArgumentException('Vf_Response::cache only accepts an integer UNIX timestamp value');
		}	
		$this->headers['Cache-Control'] = $cache;
	}
	
	
	/**
	* Ustawianie expires dla strony
	* @access public 
	* @param string|int $time
	*/
    public function expires($time)
    {
        if (is_string($time)) {
            $time = strtotime($time);
        }
        $this->headers['Expires'] = gmdate('D, d M Y H:i:s T', $time);
    }
	
	
	/**
	* Ustawia last modified jesli strona ma nie zmieniona date ustawia status 304 Not Modified do uzycia razem z expires
	* @access public 
	* @throws InvalidArgumentException jesli $time nie jest typu int
	*/
    public function lastModified()
    {
		$time = filemtime(__FILE__);
        if (is_integer($time)) {
            $this->headers['Last-Modified'] = gmdate('D, d M Y H:i:s T', $time);
            if ($time === strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                $this->setHttpStatus(304);
				exit;
            }
        } else {
            throw new InvalidArgumentException('Vf_Response::lastModified invalid file: ' . __FILE__);
        }
    }

	
	/**
	* Ustawia etag z ustalona wartoscia do uzycia razem z expires
	* @access public 
	* @param string $value
	*/
    public function etag($value, $md5 = false)
    {
		if($md5) {
			$value = md5($value);
		}
        $this->headers['ETag'] = $value;
		
        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if (trim($_SERVER['HTTP_IF_NONE_MATCH']) == $value) {
                $this->setHttpStatus(304);
				exit;
            }
        }
    }
	
	
	/**
	* Flush by zwrocic caly kontent odrazu do przegladarki - do uzycia w przypadku zwracania json przez zapytania ajax
	* @access public 
	*/
	public function flushContents()
	{
		flush();
		ob_flush();
		die();
	}
	
	
	/**
	* Ustawianie wartosci response
	* @access public 
	* @param string $response html lub inna wartosc ktory chcemy zapisac do response
	*/
	public function setResponse($response)
	{
		if (strlen($response) > 0) {
			$this->response = $response;
		}
		return $this;
	}
	

	/**
	* Ustawianie json
	* @access public 
	* @param string $json zawartosc json
	*/
	public function setJson($json)
	{
		if (!is_array($json)) {
			$json = array('msg' => $json);
		}
		$this->json = json_encode($json);
		return $this;
	}
	
	
	/**
	* Zwraca lub wyswietla zawartosc skladowej json
	* @access public 
	* @return string
	*/
	public function getJson($print = true)
	{
		if ($print) {
			print $this->json;
			return;
		}
		return $this->json;
	}
	
	
	/**
	* Zwraca skladowa response z kodem html lyb inna zawartoscia
	* @access public 
	* @return string
	*/
	public function getResponse()
	{
		return $this->response;
	}
	
	
	/**
	 * Ustawia cookie
	 * @param string $name Nazwa ciastka (jezeli w konfiguracji zostal ustawiony prefiks, nazwa zostanie nim poprzedzona)
	 * @param string $value Wartosc zapisana w ciastku
	 * @param int $expired Data wygasniecia ciastka (unix timestamp)
	 * @param string $path Sciezka cookie
	 * @param string $host Opcjonalnie, host z jakim zostanie zapisane cookie
	 * @param bool $secure Opcjonalny parametr oznaczajacy, czy ciastko bedzie ustawione na https://
	 * @param bool $httponly Opcjonalny parametr httponly zabezpieczajacy przed odczytaniem cookie przez js
	 */
	public function setCookie($name, $value, $expired = 0, $path = '/', $host = '', $secure = false, $httponly = true)
	{
		$expired = ($expired > 0) ? time() + $expired : 0;
		return setcookie($name, $value, $expired, $path, $host, $secure, $httponly);
	}
	
	
	/**
	* Ustawia sesje
	* @access public 
	* @param string $name klucz sesji
	* @param mixed $value wartosc sesji
	* @param bool $overwrite czy nadpisac sesje jesli istnieje
	*/
	public function setSession($name, $value, $overwrite = false)
	{
		if (isset($_SESSION[$name])) {
			if ($overwrite) {
				$_SESSION[$name] = $value;
			}
		} else {
			$_SESSION[$name] = $value;
		}
	}
	
	
	/**
	* Ustawia tablice $_POST
	* @access public 
	* @param string $name klucz spost
	* @param mixed $value wartosc post
	* @param bool $overwrite czy nadpisac tablice $_POST jesli istnieje
	*/
	public function setPost($name, $value, $overwrite = false)
	{
		if( isset($_POST[$name])) {
			if ($overwrite) {
				$_POST[$name] = $value;
			}
		} else {
			$_POST[$name] = $value;
		}	
	}
	
	
	/**
	* Ustawia tablice $_GET
	* @access public 
	* @param string $name klucz get
	* @param mixed $value wartosc get
	* @param bool $overwrite czy nadpisac tablice $_GET jesli istnieje
	*/
	public function setGet($name, $value, $overwrite = false)
	{
		if (isset($_GET[$name])) {
			if ($overwrite) {
				$_GET[$name] = $value;
			}
		} else {
			$_GET[$name] = $value;
		}
	}
}

?>