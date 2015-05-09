<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_RestfulServer
{
	private $request = null;
	
	private $router = null;
	
	private $responseType = 'json';
	
	private $method = array();
	
	protected $parameters = array();
	
	private $component;
	
	private $action;
	
	private $resource = null;
	
	private $roles = array();
	
	protected $status = null;
	
	protected $apiKey = false;
	
	protected $checkIp = false;
	
	protected $headers = array(
		'REQUEST_TYPE' => 'REST'
	);
	
	private $headersForResponse = array(
		'xml'           => 'application/xml',
        'json'          => 'application/json',
        'jsonp'         => 'application/javascript',
        'serialized'    => 'application/vnd.php.serialized',
        'php'           => 'text/plain',
        'html'          => 'text/html',
        'csv'           => 'application/csv'
	);
	
	private $response = '';
	
	private $path = null;
	
	private $filename = null;
	
	
	public function __construct()
	{
		$this -> router = Vf_Core::getContainer() -> router;
		$this -> request = Vf_Core::getContainer() -> request;
	}
	
	
	public function handle()
	{	
		try
		{
			$this -> headers['Content-Type'] = $this -> headersForResponse[$this -> getResponseFormat()];
			
			if(!$this -> _isAllowedMethod())
			{
				$methods = implode(',', $this -> method);
				throw new Vf_RestfulServerResourceMethodNotExists_Exception("No action mapped for method: {$methods}");
			}
			
			$rest = $this -> _createRestObject();
				
			if(!$this -> _checkApiKey())
			{
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this -> request -> response -> messages[401], //or default messages
						'Invalid api key'
					), 401
				);
				//check validation by username and password if get credentials in urls.php
			}
			if(!$this -> _checkIfAllowIp())
			{
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this -> request -> response -> messages[401], //or default messages
						'Invalid ip'
					), 401
				);
				//check validation by username and password if get credentials in urls.php
			}
			if($user = $this -> _checkAuthentication() === false)
			{
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this -> request -> response -> messages[401], //or default messages
						'Invalid credentials'
					), 401
				);
			}
			if($this -> _isAllowedResourceRole($user) === false)
			{
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this -> request -> response -> messages[403], //or default messages
						'You are not allowed to access to this resource'
					), 403
				);
			}
			
			$response = $this -> _run($rest);
			
			//set status from component's urls.php if not setted in restful controller otherwise set status from controller
			$status = ($rest -> getHttpStatus() === null) ? $this -> status : $rest -> getHttpStatus();
			
			$this -> _processResponse($response, $status);
		}
		catch(Vf_RestfulServer_Exception $e)
		{
			$responseData = array(
				'exception' => $e -> getMessage(),
				'errors' => $e -> getErrors(),
				'status' => $e -> getHttpStatus()
			);
			$this -> _processResponse($responseData, $e -> getHttpStatus());
		}
		catch(Vf_RestfulServerResourceMethodNotExists_Exception $e)
		{
			$responseData = array(
				'exception' => $e -> getMessage(),
				'status' => 405
			);
			$this -> _processResponse($responseData, 405);
		}
	}
	
	
	private function _processResponse($response, $status)
	{
		$processed = (is_array($response)) ? $this -> _response($response) : $response;
		$this -> headers['Content-Length'] = strlen($processed);
		
		$this -> request -> response 
			-> sendHttpHeaders($this -> headers, true)
			-> setHttpStatus($status) 
			-> setResponse($processed);
				
		print $this -> request -> response;
			
		$this -> request -> response -> flushContents();
	}
	
	
	private function _getRequestContentType()
	{
		if(isset($_SERVER['CONTENT_TYPE'])) 
		{
            return $_SERVER['CONTENT_TYPE'];
        }
		return false;
	}
	
	
	protected function _retrieveParameters()
	{
		if(sizeof($this -> parameters) == 0)
		{
			$data = file_get_contents("php://input");
			
			switch($this -> _getRequestContentType())
			{
				case 'application/json':
					$this -> parameters = json_decode($data, true);
				break;
				
				case 'application/x-www-form-urlencoded':
					parse_str($data, $parsed);
					$this -> parameters = $parsed;
				break;
				
				case 'application/vnd.php.serialized':
					$this -> parameters = unserialize($data);
				break;
			}
		}
	}
	
	
	public function setParameters($params)
	{
		$this -> parameters = $params;
	}
	
	
	protected function _getParameters()
	{
		return $this -> parameters;
	}
	
	
	private function _isAllowedMethod()
	{
		return (in_array($this -> request -> method(), $this -> method) || in_array(Vf_Request::ANY, $this -> method)) ? true : false;
	}
	
	
	public function __toString()
	{
		return $this -> response;
	}
	
	
	private function _checkAuthentication()
	{
		if($this -> resource !== null)
		{
			$this -> _retrieveParameters();

			if(!isset($this -> parameters['login']) || !isset($this -> parameters['password']) || !isset($this -> parameters['group']))
			{
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $this -> request -> response -> messages[400], //or default messages
						'Login, password and group are required'
					), 400
				);
			}
			else
			{
				$user = Vf_Core::getContainer() -> user;
				$auth_config = new Vf_Config('config.Authorization');
				
				$data = $user -> get($this -> parameters['login']);
				$pwd = sha1($auth_config -> salt.$this -> parameters['password']);

				if($user -> login == $this -> parameters['login'] 
					&& $user -> haslo == $pwd 
					&& $user -> role == $this -> parameters['group'])
				{
					return $user;
				}
				return false;
			}
		}
		return true;
	}
	
	
	private function _isAllowedResourceRole($user)
	{
		if($this -> resource !== null && sizeof($this -> roles) > 0)
		{
			$user = Vf_Core::getContainer() -> user;
			$acl = Vf_Core::getContainer() -> acl;
			$user -> get($user -> login);
			$acl -> set_user_role($user -> role, $user -> id);
			$acl -> load_rules();

			try
			{
				foreach($this -> roles as $key => $role)
				{
					if(!$acl -> is_allowed($this -> resource, $role))
					{
						return false;
					}
				}
			}
			catch(Volta_Acl_Deny_Exception $e)
			{
				return false;
			}
			return true;
		}
	}
	
	
	private function _checkApiKey()
	{
		if($this -> request -> method() == Vf_Request::GET)
		{
			if($api = $this -> router -> getSegment(3))
			{
				$this -> parameters['api_key'] = $api;
			}
		}
		else
		{
			$this -> _retrieveParameters();
		}
		
		if(!isset($this -> parameters['api_key']) && $this -> apiKey === true)
		{
			throw new Vf_RestfulServer_Exception(
				array(
					'exception' => $this -> request -> response -> messages[400], //or default messages
					'Api key is missing'
				), 400
			);
		}
		else if(isset($this -> parameters['api_key']) && $this -> apiKey === true)
		{
			return Vf_Orm::factory('RestfulApiKey') 
				-> find($this -> parameters['api_key'])
				-> isLoaded();
		}
		return true;
	}
	
	
	private function _getIp()
	{
		return (isset($this -> parameters['ip'])) ? $this -> parameters['ip'] : $this -> request -> ip();
	}
	
	
	private function _checkIfAllowIp()
	{
		if($this -> checkIp)
		{
			$this -> _retrieveParameters();
			
			return Vf_Orm::factory('RestfulApiKey') 
				-> setPrimaryKey('ip') 
				-> find($this -> _getIp())
				-> isLoaded();
		}
	}
	
	
	public function setResponse($response)
	{
		$this -> response = $response;
		return $this;
	}
	
	
	public function getResponse()
	{
		return $this -> response;
	}
	
	
	public function setResponseFormat($format)
	{
		if($format == 'uri')
		{
			$segment = $this -> router -> getSegment()-1;
			$type = $this -> router -> getSegment($segment);
			
			//if type is available set it otherwise keep default
			if(array_key_exists($type, $this -> headersForResponse))
			{
				$this -> responseType = $type;
			}
		}
		else
		{
			$this -> responseType = $format;
		}
		return $this;
	}
	
	
	public function getResponseFormat()
	{
		return $this -> responseType;
	}
	
	
	public function setClassPath($path)
	{
		$this -> path = $path;
		return $this;
	}
	
	
	public function getClassPath()
	{
		return $this -> path;
	}
	
	
	public function setFileClassName($filename)
	{
		$this -> filename = $filename;
		return $this;
	}
	
	
	public function getFileNameClass()
	{
		return $this -> filename;
	}
	
	
	public function setClassName($className)
	{
		$this -> className = $className;
		return $this;
	}
	
	
	public function getClassName()
	{
		return $this -> className;
	}
	
	
	public function setHttpStatus($status)
	{
		$this -> status = $status;
		return $this;
	}
	
	
	public function getHttpStatus()
	{
		return $this -> status;
	}
	
	
	public function setCheckApiKey($state)
	{
		$this -> apiKey = (bool)$state;
	}
	
	
	public function getCheckApiKey()
	{
		return $this -> apiKey;
	}
	
	
	public function setCheckIp($check)
	{
		$this -> checkIp = $check;
	}
	
	
	public function getCheckIp()
	{
		return $this -> checkIp;
	}
	
	
	public function setResource($resource)
	{
		$this -> resource = $resource;
	}
	
	
	public function getResource()
	{
		return $this -> resource;
	}
	
	
	public function setRoles($roles)
	{
		$this -> roles = $roles;
	}
	
	
	public function getRoles()
	{
		return $this -> roles;
	}
	
	
	
	private function _createRestObject()
	{
		if(Vf_Loader::existsFile($this -> getClassPath().$this -> filename))
		{
			require_once($this -> getClassPath().$this -> filename);
			$className = $this -> className;
			return new $className();
		}
	}
	
	
	private function _run($rest)
	{
		$action = $this -> action;
		try
		{
			$rest -> setParameters($this -> parameters);
			$response = $rest -> $action();
		}
		//handle errors throwed by rest controller with exception and/or validations errors
		catch(Vf_RestfulServer_Exception $e)
		{
			$this -> status = $e -> getHttpStatus();
			$response = array(
				'exception' => $e -> getMessage() ,
				'errors' => $e -> getErrors(),
				'status' => $this -> status
			);
		}
		return $this -> _response($response);
	}
	
	
	private function _response($response)
	{
		switch($this -> responseType)
		{
			case 'json':
				return $this -> _json($response);
			break;
				
			case 'xml':
				return $this -> _xml($response);
			break;
			
			case 'csv':
				return $this -> _csv($response);
			break;
			
			case 'serialized':
				return $this -> _serialize($response);
			break;
		}
	}
	
	
	private function _json($response)
	{
		return (is_array($response)) ? json_encode($response) : json_encode(array($response));
	}
	
	
	private function _xml($response)
	{
		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><restData></restData>");
		$this -> _toXml($xml, $response);
		return $xml -> asXML();
	}
	
	
	private function _toXml($xml, $data)
	{
		foreach($data as $key => $value)
		{
			if(is_array($value))
			{
				$key = is_numeric($key) ? "item$key" : $key;
				$child = $xml -> addChild($key);
				$this -> _toXml($child, $value);
			}
			else
			{
				$key = is_numeric($key) ? "item$key" : $key;
				$xml -> addChild($key, $value);
			}
		}
	}
	
	
	private function _csv($response)
	{
		return $this -> _toCsv($response);
	}
	
	//try this: https://kernelcurry.com/blog/2014/01/28/array-to-csv-download.html
	private function _toCsv($data)
	{
		$contents = '';
			if (!empty($data))
			{
				// Create a title row. Support 1-dimension arrays.
				$first_row = reset($data);
				if (is_array($first_row))
				{
					$titles = array_keys($first_row);
				}
				else
				{
					$titles = array_keys($data);
				}
				array_unshift($data, $titles);
				$handle = fopen('php://temp', 'r+');
				foreach ($data as $line)
				{
					fputcsv($handle, (array) $line, ',', '"');
				}
				rewind($handle);
				while (!feof($handle))
				{
					$contents .= fread($handle, 8192);
				}
				fclose($handle);
			}
			return $contents;
	}
	
	
	private function _serialize($response)
	{
		return serialize($response);
	}
	
	
	public function getMethod()
	{
		return $this -> method;
	}
	

	public function get($component, $action)
	{
		$this -> method[] = Vf_Request::GET; 
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
	
	
	public function post($component, $action)
	{
		$this -> method[] = Vf_Request::POST;
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
	
	
	public function put($component, $action)
	{
		$this -> method[] = Vf_Request::PUT;
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
	
	
	public function delete($component, $action)
	{
		$this -> method[] = Vf_Request::DELETE;
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
	
	
	public function head($component, $action)
	{
		$this -> method[] = Vf_Request::HEAD;
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
	
	
	public function patch($component, $action)
	{
		$this -> method[] = Vf_Request::PATCH;
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
	
	
	public function options($component, $action)
	{
		$this -> method[] = Vf_Request::OPTIONS;
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
	
	
	public function any($component, $action)
	{
		$this -> method[] = Vf_Request::ANY;
		$this -> component = $component;
		$this -> action = $action;
		return $this;
	}
}
?>