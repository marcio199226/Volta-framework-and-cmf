<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_FlashMessages_Events
{
	/**
	* Skladowa klasy ktora przechowywuje kontener klas
	* @access private
	* @var Vf_Di_Container $di
	*/
	private $di;
	
	
	/**
	* Dajemy dostep do kontenera
	* @access public 
	* @param Vf_Di_Container
	*/
	public function __construct(Vf_Di_Container $di)
	{
		$this->di = $di;
	}

	/**
	* Metoda ktora zapisuje wszystkie wiadomosci do sesji
	* @access public 
	*/
	public function register()
	{
		$this->di->request->response->flash->save();
	}
	
	/**
	* Metoda ktora zapisuje wszystkie wiadomosci do sesji z poprzedniego zadania
	* @access public 
	*/
	public function loadFromPreviousRequest()
	{	
		$this->di->request->response->flash->loadFromPreviousRequest();
	}
}
?>