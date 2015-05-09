<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'RestfulServer.php');

class Vf_news_Rest_Component extends Vf_RestfulServer
{
	public function test()
	{
		return array(0 => array('a', 'b'), 1 => array('c', 'd'));
	}
	
	
	public function test2()
	{
		return array('user' => array('login', 'password', 'email'));
	}
	
	
	public function test3()
	{
		$req = new Vf_Request();

		if($req -> method() == Vf_Request::PUT)
		{
			//try without this method if apikey is set to false and check if parameters exists
			//$this -> _retrieveParameters();
			
			if(sizeof($this -> _getParameters()) > 0)
			{
				//validate data and throw possible errors
				$validation = new Vf_Validator();
				$validation -> load('str');
				$validation -> add_data($this -> _getParameters());
				$validation -> add_rule('title', new str(array('field' => 'title', 'required' => true, 'alphadigit' => true, 'between' => array(5, 40))));
				$validation -> add_rule('content', new str(array('field' => 'content', 'required' => true)));
				$validation -> validation();
				
				if(sizeof($validation -> get_errors()) == 0)
				{
					$this -> status = 201;
					return array('msg' => 'News added');
				}
				else
				{
					throw new Vf_RestfulServer_Exception(
						array(
							'exception' => $req -> response -> messages[400], //or default messages
							$validation -> get_errors()
						), 400
					);
					//return $validation -> get_errors();
				}
			}
			else
				throw new Vf_RestfulServer_Exception(
					array(
						'exception' => $req -> response -> messages[400], //or default messages
						'error' => 'There is no post data'
					), 400
				);
		}
		else if($req -> method() == Vf_Request::POST)
		{
			return array('login' => 'marcio', 'password' => 'secret', 'email' => 'opi14@op.pl');
		}
		else if($req -> method() == Vf_Request::GET)
		{
			return array(
				0 => array('id' => 1, 'login' => 'marcio', 'password' => 'pwd', 'email' => 'dasdds@gmail.com'),
				1 => array('id' => 1, 'login' => 'qwerty', 'password' => 'pwd1234', 'email' => 'dasdds@gmail.com'),
				2 => array('id' => 1, 'login' => 'zxc', 'password' => 'pwd9876', 'email' => 'dasdds@gmail.com')
			);
		}
	}
}

?>