<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

//wyjatek dla klasy restful server jesli nie ma ustawionej zadnej akcji dla zasobu o danym typie zadania put/get/post itp..
class Vf_RestfulServerResourceMethodNotExists_Exception extends Exception { }

class Vf_RestfulServer_Exception extends Exception 
{
	private $httpStatus;
	
	private $errors = array();

	public function __construct($error, $httpStatus)
	{
		if(!is_array($error))
		{
			parent::__construct($error);
		}
		else
		{
			if(isset($error['exception']))
			{
				parent::__construct($error['exception']);
				unset($error['exception']);
			}
			$this -> errors = $error;
		}
		$this -> httpStatus = $httpStatus;
	}
	
	
	public function getHttpStatus()
	{
		return $this -> httpStatus;
	}
	
	
	public function getErrors()
	{
		return $this -> errors;
	}
}

?>