<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Flash
{

    /**
     * @const string type of message and function name for box helper that render those messages
     */
	const SUCCESS = 'success';
	const INFO = 'info';
	const ERROR = 'error';

	/**
	* Skladowa klasy ktora przechowywuje wszystkie wiadomosci
	* @access private
	* @var array $messages
	*/
	private $messages = array(
		'now' => array(),
		'prev' => array(),
		'next' => array()
	);
	
	
	/**
	* Skladowa klasy ktora uwzglednia z ktorego requestu zwrocic dane now/next = current Request prev = previous Request
	* @access private
	* @var string $from
	*/
	private $from = 'now';
	
	
	/**
	* Konstruktor ktory laduje helper Box do obrobki informacni
	* @access public 
	*/
	public function __construct()
	{
		Vf_Loader::loadHelper('Box');
	}
	
	
	/**
	* Magiczny getter zwraca nam informacje pod danym kluczem, uzywac jesli klucz posiada tylko jedna wartosc w przeciwnym razie $this->getMessages()
	* @access public 
	* @param string $key klucz gdzie zapisalismy wczesniej nasza wiadomosc
	* @return string|null
	*/
	public function __get($key)
	{
		return (isset($this->messages[$this->from][$key])) ? html_entity_decode($this->messages[$this->from][$key]) : null;
	}
	
	
	/**
	* Ustawiamy gdzie ma czytac dane
	* @access public 
	* @param string $from now/prev/next
	* @return string $this
	*/
	public function from($from)
	{
		$this->from = $from;
		return $this;
	}
	
	
	/**
	* Zwraca wszystkie wiadomosci przefiltrowane
	* @access public 
	* @return array
	*/
	public function getMessages()
	{	
		$messages = array_merge($this->messages['now'], $this->messages['prev']);
		$filtered = array_map(function($arr) {
			foreach($arr as $k => $v)
				$html[$k] = html_entity_decode($v);
			return $html;
		}, $messages);
		return $filtered;
	}
	

	/**
	* Dodajemy nowa wiadomosc dla nastepnego zadania
	* @access public 
	* @param string $key klucz tablicy
	* @param string $value wartosc danego klucza
	* @param string $type patrz const klasy
	*/
	public function add($key, $value, $type = self::SUCCESS)
	{
		if (!is_array($value)) {
			$this->messages['next'][$key] = htmlentities(call_user_func_array(array('Vf_Box_Helper', $type), array($value)));
		} else {
			foreach ($value as $msg) {
				$this->messages['next'][$key][] = htmlentities(call_user_func_array(array('Vf_Box_Helper', $type), array($msg)));
			}
		}
	}
	
	
	/**
	* Dodajemy nowa wiadomosc dla aktualnego zadania
	* @access public 
	* @param string $key klucz tablicy
	* @param string $value wartosc danego klucza
	* @param string $type patrz const klasy
	*/
	public function now($key, $value, $type = self::SUCCESS)
	{
		if (!is_array($value)) {
			$this->messages['now'][$key] = htmlentities(call_user_func_array(array('Vf_Box_Helper', $type), array($value)));
		} else {
			foreach ($value as $msg) {
				$this->messages['now'][$key][] = htmlentities(call_user_func_array(array('Vf_Box_Helper', $type), array($msg)));
			}
		}
	}
	
	
	/**
	* Zachowaj wiadomości bezpośrednie z poprzedniego zadania dla nastepnego
	* @access public 
	*/
	public function keep()
	{
		if (sizeof($this->messages['prev']) > 0) {
			foreach ($this->messages['prev'] as $key => $value) {
				$this->messages['next'][$key] = $value;
			}
		}
	}
	

	/**
	* Sprawdza czy dana wiadomosc istnieje
	* @access public 
	* @param string $key klucz ktorego szukamy
	* @return bool
	*/
	public function hasFlash($key)
	{
		$flashMessages = Vf_Core::getContainer()->request->session('flashMessages', false);
		return (isset($flashMessages[$key])) ? true : false;
	}
	

	/**
	* Laduje wiadomosc z poprzedniego zadania jesli istnieja do nastepnego uzyte w: events/FlashMessages.php i zarejestrowane w Core/config.php
	* @access public 
	*/
    public function loadFromPreviousRequest()
    {
		$flashMessages = Vf_Core::getContainer()->request->session('flashMessages', false);
        if (isset($flashMessages)) {
            $this->messages['prev'] = $flashMessages;
        }
    }
	
	
	/**
	* Zapisuje wszystkie wiadomosci do sesji by byly dostepne globalnie
	* @access public 
	*/
	public function save()
	{
		Vf_Core::getContainer()->request->response->setSession('flashMessages', $this->messages['next'], true);
	}

}

?>