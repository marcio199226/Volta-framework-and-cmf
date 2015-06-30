<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_RestfulServer
{
	/**
	* Skladowa ktora trzyma obiekt klasy Vf_Request
	* @access private
	* @var object Vf_Request
	*/
	private $request = null;
	
	/**
	* Skladowa ktora trzyma obiekt klasy Vf_Request
	* @access private
	* @var object Vf_Router
	*/
	private $router = null;
	
	/**
	* Skladowa ktora typ odpowiedzi z server-a
	* @access private
	* @var string
	*/
	private $responseType = 'json';
	
	/**
	* Skladowa ktora trzyma dozwolone metody dla mapowanej akcji
	* @access private
	* @var array
	*/
	private $method = array();
	
	/**
	* Skladowa ktora trzyma parametry wysylane dla zadania
	* @access private
	* @var array
	*/
	protected $parameters = array();
	
	/**
	* Komponent zadania restful
	* @access private
	* @var string
	*/
	private $component;
	
	/**
	* Akcja komponentu
	* @access private
	* @var string
	*/
	private $action;
	
	/**
	* Zasob dla acl-a np news / news_Admin / comment itp...patrz config/acl/config.ini
	* @access private
	* @var string
	*/
	private $resource = null;
	
	/**
	* Role dla acl-a patrz config/acl/config.ini
	* @access private
	* @var array
	*/
	private $roles = array();
	
	/**
	* Status ktory ma zwrocic server
	* @access protected
	* @var int
	*/
	protected $status = null;
	
	/**
	* Czy mamy sprawdzac wyslany api-key z zadania
	* @access protected
	* @var boolean
	*/
	protected $apiKey = false;
	
	/**
	* Czy mamy sprawdzac czy podany ip lub pobrany automatycznie z $_SERVER istnieje w bazie danych
	* @access protected
	* @var boolean
	*/
	protected $checkIp = false;
	
	/**
	* Tablice z naglowkami do wyslania jako response
	* @access protected
	* @var array
	*/
	protected $headers = array(
		'REQUEST_TYPE' => 'REST'
	);
	
	/**
	* Tablice z naglowkami dla danego typu odpowiedzi
	* @access private
	* @var array
	*/
	private $headersForResponse = array(
		'xml'           => 'application/xml',
        'json'          => 'application/json',
        'jsonp'         => 'application/javascript',
        'serialized'    => 'application/vnd.php.serialized',
        'php'           => 'text/plain',
        'html'          => 'text/html',
        'csv'           => 'application/csv'
	);
	
	/**
	* Tresc odpowiedzi
	* @access private
	* @var string
	*/
	private $response = '';
	
	/**
	* Sciekza w ktorej znajduje sie nasz kontroler restful
	* @access private
	* @var string
	*/
	private $path = null;
	
	/**
	* Nazwa pliku kontrolera
	* @access private
	* @var string
	*/
	private $filename = null;
	
	
	/**
	* Ustawia potrzebna nam obiekty
	* @access public 
	*/
	public function __construct()
	{
		$this->router = Vf_Core::getContainer()->router;
		$this->request = Vf_Core::getContainer()->request;
	}
	
	
	/**
	* Przetwarza zadania na podstawie uri uwgledniajac ip / api-key / autentykacja / autoryzacje jesli je wczesniej ustawilismy
	* @access public 
	* @throws Vf_RestfulServer_Exception w przypadku niepoprawnych ip / api-key / autentykacja / autoryzacje jesli wczesniej zostaly ustawione
	* @throws Vf_RestfulServerResourceMethodNotExists_Exception jesli nie ma zadnej akcji dla danej metody GET/POST/PATCH itp...
	* @return string zwraca zawartosc akcji razem ze statusem i wszystkimi naglowaki powodzenia/bledu akcji
	*/
	public function handle()
	{	
		try {
			$this->headers['Content-Type'] = $this->headersForResponse[$this->getResponseFormat()];
			
			if (!$this->isAllowedMethod()) {
				$methods = implode(',', $this->method);
				throw new Vf_RestfulServerResourceMethodNotExists_Exception("No action mapped for method: {$methods}");
			}
			$rest = $this->createRestObject();
				
			if (!$this->checkApiKey()) {
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this->request->response->messages[401], //or default messages
						'Invalid api key'
					), 401
				);
				//check validation by username and password if get credentials in urls.php
			}
			if (!$this->checkIfAllowIp()) {
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this->request->response->messages[401], //or default messages
						'Invalid ip'
					), 401
				);
				//check validation by username and password if get credentials in urls.php
			}
			if ($user = $this->checkAuthentication() === false) {
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this->request->response->messages[401], //or default messages
						'Invalid credentials'
					), 401
				);
			}
			if ($this->isAllowedResourceRole($user) === false) {
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this->request->response->messages[403], //or default messages
						'You are not allowed to access to this resource'
					), 403
				);
			}
			$response = $this->run($rest);
			
			//set status from component's urls.php if not setted in restful controller otherwise set status from controller
			$status = ($rest->getHttpStatus() === null) ? $this->status : $rest->getHttpStatus();
			$this->processResponse($response, $status);
			
		} catch (Vf_RestfulServer_Exception $e) {
			$responseData = array(
				'exception' => $e->getMessage(),
				'errors' => $e->getErrors(),
				'status' => $e->getHttpStatus()
			);
			$this->processResponse($responseData, $e->getHttpStatus());
			
		} catch (Vf_RestfulServerResourceMethodNotExists_Exception $e) {
			$responseData = array(
				'exception' => $e->getMessage(),
				'status' => 405
			);
			$this->processResponse($responseData, 405);
		}
	}
	
	
	/**
	* Wyswietla zwrocona zawartosc akcji razem z naglowaki i statusem
	* @access public 
	* @param mixed $response
	* @param int $status
	* @return string zwraca zawartosc akcji razem ze statusem i wszystkimi naglowaki powodzenia/bledu akcji
	*/
	private function processResponse($response, $status)
	{
		$processed = (is_array($response)) ? $this->response($response) : $response;
		$this->headers['Content-Length'] = strlen($processed);
		
		$this->request->response 
			-> sendHttpHeaders($this->headers, true)
			-> setHttpStatus($status) 
			-> setResponse($processed);
				
		print $this->request->response;
			
		$this->request->response->flushContents();
	}
	
	
	/**
	* Zwraca typ wyslanego zadania przez client-a
	* @access private
	* @return boolean
	*/
	private function getRequestContentType()
	{
		if (isset($_SERVER['CONTENT_TYPE'])) {
            return $_SERVER['CONTENT_TYPE'];
        }
		return false;
	}
	
	
	/**
	* Pobiera dane i je formatuje na podstawie CONTENT_TYPE wyslanego przez client-a i zapisuje do skladowe $this->parameters
	* @access protected
	*/
	protected function retrieveParameters()
	{
		if(sizeof($this->parameters) == 0) {
			$data = file_get_contents("php://input");
			
			switch ($this->getRequestContentType()) {
				case 'application/json':
					$this->parameters = json_decode($data, true);
					break;
				
				case 'application/x-www-form-urlencoded':
					parse_str($data, $parsed);
					$this->parameters = $parsed;
					break;
				
				case 'application/vnd.php.serialized':
					$this->parameters = unserialize($data);
					break;
			}
		}
	}
	
	
	/**
	* Ustawia parametry
	* @access public
	* @param array $params
	*/
	public function setParameters($params)
	{
		$this->parameters = $params;
	}
	
	
	/**
	* Zwraca parametry
	* @access protected
	* @return array
	*/
	protected function getParameters()
	{
		return $this->parameters;
	}
	
	
	/**
	* Sprawdza czy metoda wywolana przez client-a jest dozwolona dla wczesniej zmapowanych ustawien
	* @access private
	* @return boolean
	*/
	private function isAllowedMethod()
	{
		return (in_array($this->request->method(), $this->method) || in_array(Vf_Request::ANY, $this->method)) ? true : false;
	}
	
	
	/**
	* Zwraca odpiwedz server-a
	* @access public 
	* @return string
	*/
	public function __toString()
	{
		return $this->response;
	}
	
	
	/**
	* Sprawdza autentykacje uzytkownika na podstawie login/pwd/group
	* @access private
	* @throws Vf_RestfulServer_Exception jesli brakuje parametrow login/password/group w zadaniu wyslanym przez client-a
	* @return mixed boolean|Vf_User
	*/
	private function checkAuthentication()
	{
		if ($this->resource !== null) {
			$this->retrieveParameters();

			if (!isset($this->parameters['login']) || !isset($this->parameters['password']) || !isset($this->parameters['group'])) {
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this->request->response->messages[400], //or default messages
						'Login, password and group are required'
					), 400
				);
			} else {
				$user = Vf_Core::getContainer()->user;
				$auth_config = new Vf_Config('config.Authorization');
				
				$data = $user->get($this->parameters['login']);
				$pwd = sha1($auth_config->salt . $this->parameters['password']);

				if($user->login == $this->parameters['login'] 
					&& $user->haslo == $pwd 
					&& $user->role == $this->parameters['group'])
				{
					return $user;
				}
				return false;
			}
		}
		return true;
	}
	
	
	/**
	* Sprawdza autoryzacje uzytkownika za pomoca acl-a
	* @access private
	* @param object Vf_User
	* @return boolean
	*/
	private function isAllowedResourceRole($user)
	{
		if ($this->resource !== null && sizeof($this->roles) > 0) {
			$user = Vf_Core::getContainer()->user;
			$acl = Vf_Core::getContainer()->acl;
			$user->get($user->login);
			$acl->set_user_role($user->role, $user->id);
			$acl->load_rules();

			try {
				foreach ($this->roles as $key => $role) {
					if (!$acl->is_allowed($this->resource, $role)) {
						return false;
					}
				}
			} catch (Volta_Acl_Deny_Exception $e) {
				return false;
			}
			return true;
		}
	}
	
	
	/**
	* Sprawdza klucz api-key wyslany w zadaniu client-a
	* @access private
	* @throws Vf_RestfulServer_Exception jesli brakuje parametru api_key w zadaniu client-a
	* @return boolean
	*/
	private function checkApiKey()
	{
		if ($this->request->method() == Vf_Request::GET) {
			if ($api = $this->router->getSegment(3)) {
				$this->parameters['api_key'] = $api;
			}
		} else {
			$this->retrieveParameters();
		}

		if (!isset($this->parameters['api_key']) && $this->apiKey === true) {
			throw new Vf_RestfulServer_Exception(
				array(
					'exception' => $this->request->response->messages[400], //or default messages
					'Api key is missing'
				), 400
			);
		} elseif (isset($this->parameters['api_key']) && $this->apiKey === true) {
			return Vf_Orm::factory('RestfulApiKey') 
				-> find($this->parameters['api_key'])
				-> isLoaded();
		}
		return true;
	}
	
	
	/**
	* Zwraca ip client-a ktory wyslal zadanie
	* @access private
	* @return boolean
	*/
	private function getIp()
	{
		return (isset($this->parameters['ip'])) ? $this->parameters['ip'] : $this->request->ip();
	}
	
	
	/**
	* Sprawdza czy ip client-a ktory wyslal zadanie istnieje na podstawie ip wyslanego w zadaniu lub $_SERVER
	* @access private
	* @return boolean
	*/
	private function checkIfAllowIp()
	{
		if($this->checkIp) {
			$this->retrieveParameters();
			return Vf_Orm::factory('RestfulApiKey') 
				-> setPrimaryKey('ip') 
				-> find($this->getIp())
				-> isLoaded();
		}
		return true;
	}
	
	
	/**
	* Sprawdza autentykacje uzytkownika na podstawie login/pwd/group
	* @access public
	* @throws Vf_RestfulServer_Exception jesli brakuje parametrow login/password/group w zadaniu wyslanym przez client-a
	* @return object this
	*/
	public function setResponse($response)
	{
		$this->response = $response;
		return $this;
	}
	
	
	/**
	* Zwraca zawartosc zadania
	* @access public
	* @return string
	*/
	public function getResponse()
	{
		return $this->response;
	}
	
	
	/**
	* Ustawia w jakim formacie ma byc zwrocana zawartosc zadania
	* @access public
	* @param string $format 
	* @return object this
	*/
	public function setResponseFormat($format)
	{
		if($format == 'uri') {
			$segment = $this->router->getSegment()-1;
			$type = $this->router->getSegment($segment);
			
			//if type is available set it otherwise keep default
			if(array_key_exists($type, $this->headersForResponse)) {
				$this->responseType = $type;
			}
		} else {
			$this->responseType = $format;
		}
		return $this;
	}
	
	
	/**
	* Zwraca typ formatu odpowiedzi
	* @access public
	* @return string
	*/
	public function getResponseFormat()
	{
		return $this->responseType;
	}
	
	
	/**
	* Ustawia sciezke w ktorej znajduje sie kontroler
	* @access public
	* @param string $path sciezka gdzie znajduje sie katalog
	* @return object this
	*/
	public function setClassPath($path)
	{
		$this->path = $path;
		return $this;
	}
	
	
	/**
	* Zwraca sciezke kontrolera
	* @access public
	* @return string
	*/
	public function getClassPath()
	{
		return $this->path;
	}
	
	
	/**
	* Ustawia nazwe pliku kontrolera
	* @access public
	* @param string $filename nazwa pliku
	* @return object this
	*/
	public function setFileClassName($filename)
	{
		$this->filename = $filename;
		return $this;
	}
	
	
	/**
	* Zwraca nazwe pliku kontrolera
	* @access public
	* @return string
	*/
	public function getFileNameClass()
	{
		return $this->filename;
	}
	
	
	/**
	* Ustawia nazwe klasy kontrolera restful
	* @access public
	* @param string $className
	* @return object this
	*/
	public function setClassName($className)
	{
		$this->className = $className;
		return $this;
	}
	
	
	/**
	* Zwraca nazwe klasy kontrolera
	* @access public
	* @return string
	*/
	public function getClassName()
	{
		return $this->className;
	}
	
	
	/**
	* Ustawia status ktory ma byc zwrocony razem zawartoscia akcji
	* @access public
	* @param int $status kod statusu
	* @return object this
	*/
	public function setHttpStatus($status)
	{
		$this->status = $status;
		return $this;
	}
	
	
	/**
	* Zwraca kod statusu
	* @access public
	* @return object this
	*/
	public function getHttpStatus()
	{
		return $this->status;
	}
	
	
	/**
	* Ustawia czy api-key bedzie sprawdzany przy zadaniu client-a
	* @access public
	* @param boolean $state
	*/
	public function setCheckApiKey($state)
	{
		$this->apiKey = (bool)$state;
	}
	
	
	/**
	* Zwraca true/false dla skladowej apiKey
	* @access public
	* @return boolean
	*/
	public function getCheckApiKey()
	{
		return $this->apiKey;
	}
	
	
	/**
	* Ustawia czy ip bedzie sprawdzane przy zadaniu client-a
	* @access public
	* @param boolean $check
	*/
	public function setCheckIp($check)
	{
		$this->checkIp = $check;
	}
	
	
	/**
	* Zwraca true/false dla skladowej checkIp
	* @access public
	* @param string $path sciezka gdzie znajduje sie katalog
	* @return object this
	*/
	public function getCheckIp()
	{
		return $this->checkIp;
	}
	
	
	/**
	* Ustawia nazwe zasobu
	* @access public
	* @param string $resource
	*/
	public function setResource($resource)
	{
		$this->resource = $resource;
	}
	
	
	/**
	* Zwraca nazwe zasobu
	* @access public
	* @return string
	*/
	public function getResource()
	{
		return $this->resource;
	}
	
	
	/**
	* Ustawia role do sprawdzanie dla danego zasoby
	* @access public
	* @param array $roles
	*/
	public function setRoles($roles)
	{
		$this->roles = $roles;
	}
	
	
	/**
	* Zwraca tablice z rolami do sprawdzenia
	* @access public
	* @return array
	*/	
	public function getRoles()
	{
		return $this->roles;
	}
	
	
	/**
	* Tworzy obiekt kontrolera restful
	* @access private
	* @return object
	*/
	private function createRestObject()
	{
		if(Vf_Loader::existsFile($this->getClassPath() . $this->filename)) {
			require_once($this->getClassPath() . $this->filename);
			$className = $this->className;
			return new $className();
		}
	}
	
	
	/**
	* Wywoluje akcje kontrolera i zwraca jej zawartosc
	* @access private
	* @param object obiekt kontrolera
	* @return string odpowiedz json/xml/csv
	*/
	private function run($rest)
	{
		$action = $this->action;
		try {
			$rest->setParameters($this->parameters);
			$response = $rest->$action();
		} catch(Vf_RestfulServer_Exception $e) {
			//handle errors throwed by rest controller with exception and/or validations errors
			$this->status = $e->getHttpStatus();
			$response = array(
				'exception' => $e->getMessage() ,
				'errors' => $e->getErrors(),
				'status' => $this->status
			);
		}
		return $this->response($response);
	}
	
	
	/**
	* Tablice zwrocona przez akcje kontrolera ktora bedzie konwertowana do string odpowiedniego formatu
	* @access private
	* @param array $response
	* @return string
	*/
	private function response($response)
	{
		switch ($this->responseType) {
			case 'json':
				return $this->json($response);
				break;
				
			case 'xml':
				return $this->xml($response);
				break;
			
			case 'csv':
				return $this->csv($response);
				break;
			
			case 'serialized':
				return $this->serialize($response);
				break;
		}
	}
	
	
	/**
	* Formatuje tablice do json
	* @access private
	* @param array $response
	* @return string
	*/
	private function json($response)
	{
		return (is_array($response)) ? json_encode($response) : json_encode(array($response));
	}
	
	
	/**
	* Formatuje tablice do xml
	* @access private
	* @param array $response
	* @return string
	*/
	private function xml($response)
	{
		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><restData></restData>");
		$this->toXml($xml, $response);
		return $xml->asXML();
	}
	
	
	/**
	* Formatuje tablice do tagow xml
	* @access private
	* @param object SimpleXMLElement $xml 
	* @param array $data talice do przekonwertowania
	*/
	private function toXml($xml, $data)
	{
		foreach ($data as $key => $value) {
			if (is_array($value)) {
				$key = is_numeric($key) ? "item$key" : $key;
				$child = $xml->addChild($key);
				$this->toXml($child, $value);
			} else {
				$key = is_numeric($key) ? "item$key" : $key;
				$xml->addChild($key, $value);
			}
		}
	}
	
	
	/**
	* Formatuje tablice do csv
	* @access private
	* @param array $response
	* @return string
	*/
	private function csv($response)
	{
		return $this->toCsv($response);
	}
	
	//try this: https://kernelcurry.com/blog/2014/01/28/array-to-csv-download.html
	/**
	* Formatuje tablice csv
	* @access private
	* @param array $data
	* @return string
	*/
	private function toCsv($data)
	{
		$contents = '';
		if (!empty($data)) {
			// Create a title row. Support 1-dimension arrays.
			$first_row = reset($data);
			if (is_array($first_row)) {
				$titles = array_keys($first_row);
			} else {
				$titles = array_keys($data);
			}
			array_unshift($data, $titles);
			$handle = fopen('php://temp', 'r+');
			foreach ($data as $line) {
				fputcsv($handle, (array) $line, ',', '"');
			}
			rewind($handle);
			while (!feof($handle)) {
				$contents .= fread($handle, 8192);
			}
			fclose($handle);
		}
		return $contents;
	}
	
	
	/**
	* Formatuje tablice do serialize
	* @access private
	* @param array $response
	* @return string
	*/
	private function serialize($response)
	{
		return serialize($response);
	}
	
	
	/**
	* Zwraca metode zadania client-a
	* @access public
	* @return string
	*/
	public function getMethod()
	{
		return $this->method;
	}
	

	/**
	* Ustawia metode zadania na GET jego komponent i akcje
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function get($component, $action)
	{
		$this->method[] = Vf_Request::GET; 
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
	
	
	/**
	* Ustawia metode zadania na POST jego komponent i akcje
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function post($component, $action)
	{
		$this->method[] = Vf_Request::POST;
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
	
	
	/**
	* Ustawia metode zadania na PUT jego komponent i akcje
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function put($component, $action)
	{
		$this->method[] = Vf_Request::PUT;
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
	
	
	/**
	* Ustawia metode zadania na DELETE jego komponent i akcje
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function delete($component, $action)
	{
		$this->method[] = Vf_Request::DELETE;
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
	
	
	/**
	* Ustawia metode zadania na HEAD jego komponent i akcje
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function head($component, $action)
	{
		$this->method[] = Vf_Request::HEAD;
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
	
	
	/**
	* Ustawia metode zadania na PATCH jego komponent i akcje
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function patch($component, $action)
	{
		$this->method[] = Vf_Request::PATCH;
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
	
	
	/**
	* Ustawia metode zadania na OPTIONS jego komponent i akcje
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function options($component, $action)
	{
		$this->method[] = Vf_Request::OPTIONS;
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
	

	/**
	* Ustawia metode zadania na kazda mozliwa get/post/delete itp...potem mozna ja sprawdzac w akcji komponentu
	* @access public
	* @param string $component nazwa komponentu/kontrolera
	* @param string $action nazwa akcji
	* @return object this
	*/
	public function any($component, $action)
	{
		$this->method[] = Vf_Request::ANY;
		$this->component = $component;
		$this->action = $action;
		return $this;
	}
}
?>