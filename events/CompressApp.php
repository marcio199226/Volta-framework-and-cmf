<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_CompressApp_Events
{

	/**
	*Skladowa klasy ktora przechowywuje klase Vf_Request
	*@access private
	*@var Vf_Request $request
	*/
	private $request = null;

	/**
	*Skladowa klasy ktora przechowywuje level kompresji
	*@access private
	*@var int $compressionLevel
	*/
	private $compressionLevel = 0;
	
	/**
	*Skladowa klasy ktora przechowywuje kontener klas
	*@access private
	*@var Vf_Di_Container $di
	*/
	private $di;
	
	
	/**
	*Konstruktor ktory ustawia level kompresji
	*@access public 
	*@param int $compressionLvl level kompresji z konfiguracji
	*@param Vf_Request
	*/
	public function __construct(Vf_Di_Container $di)
	{
		$this -> di = $di;
	}

	/**
	*Metoda ktora kompresuje kod html strony
	*@access public 
	*@param string $output przekazywany przez referencje wiec nie potrzeba return itp...
	*/
	public function compress($output)
	{
		if($this -> di -> compressionLevel > 0)
		{
			if(stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false)
			{
				$compression = 'gzip';
			}
			else if(stripos(@$_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate') !== false)
			{
				$compression = 'deflate';
			}
						
			$output = ($compression == 'gzip') ? gzencode($output, $this -> di -> compressionLevel) : gzdeflate($output, $this -> di -> compressionLevel);
			
			$this -> di -> request -> response 
				-> sendHttpHeaders(array(
					'Vary' => 'Accept-Encoding',
					'Content-Encoding' => $compression,
					'Content-Length' => strlen($output)
				))
				-> setHttpStatus(200)
				-> setResponse($output);
		}
		else
		{
			$this -> di -> request -> response
				-> setHttpStatus(200)
				-> setResponse($output);
		}
	}
}
?>