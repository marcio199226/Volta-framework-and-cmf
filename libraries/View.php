<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/


class Vf_View
{
	/**
	* Skladowa klasy ktora przechowywuje wszystkie zmienne dla widoku
	* @access protected
	* @var array $vars
	*/
	protected $vars = array();
	
	/**
	* Sciezka do widoku
	* @access protected
	* @var string $viewPath
	*/
	protected $viewPath = null;

	
	/**
	* Konstruktor ktory include-uje plik widoku
	* @access public 
	* @param string $view nazwa widoku z ewentualna sciezka jesli jest to sub folder
	* @param string $type typ widoku component/widget/plugin/front
	* @param string $cmpName nazwa komponentu 
	*/
	public function __construct($view, $type = 'front', $cmpName = null)
	{
		$viewPath = $this->getViewPath($type, $cmpName, $view);
		if(Vf_Loader::existsFile($viewPath)) {
			$this->viewPath = $viewPath;
		} else {
			throw new Vf_ViewNotFound_Exception('View: ' . $view . ' doesn\'t exists');
		}
	}
	
	
	/**
	* Setter ustawia zmienne dla widoku
	* @access public 
	* @param string $key nazwa zmienne dla widoku
	* @param string $value wartosc zmiennej
	*/
	public function __set($key, $value)
	{
		$this->vars[$key] = $value;
	}
	
	
	/**
	* Getter zwraca wartosc zmiennej
	* @access public 
	* @param string $key nazwa zmiennej
	* @return mixed string|array
	*/
	public function __get($key)
	{
		return $this->vars[$key];
	}
	
	
	/**
	* Ustawia zmienne tak samo jak __set z tym ze mozemy podac tablice z klucz => wartosc jako 2 argument
	* @access public 
	* @param string|null $key
	* @param string|array $values
	*/
	public function assign($key = null, $values)
	{
		if (is_array($values)) {
			foreach ($values as $k => $val) {
				$this->vars[$k] = $val;
			}
		} else {
			$this->vars[$key] = $values;
		}
	}

	
	/**
	* Zwraca obiekt helper-a do zmiennej. Do uzyca jesli helper nie jest statyczny
	* @access public 
	* @param string $helper nazwa helper-a
	*/
	public function setHelper($helper)
	{
		if(Vf_Loader::existsFile(DIR_HELPERS . $helper . '.php')) {
			require_once(DIR_HELPERS . $helper . '.php');
			$helperName = 'Vf_' . $helper . '_Helper';
			$this->vars[$helper] = new $helperName();
		} else {
			throw new Vf_ViewHelperNotFound_Exception("Helper $helperName not found");
		}
	}
	
	
	/**
	* Dolacza plik helper-a zeby byly widocznie wszystkie metody. Do uzycia gdy helper nie ma metod statycznych
	* @access public 
	* @param string $helper nazwa helper-a
	*/
	public function loadHelper($helper)
	{
		if (Vf_Loader::existsFile(DIR_HELPERS . $helper . '.php')) {
			require_once(DIR_HELPERS . $helper . '.php');
		} else {
			throw new Vf_ViewHelperNotFound_Exception("Helper $helper not found");
		}
	}
	
	
	/**
	* Importuje funkcje do widoku
	* @access public 
	* @param string $name nazwa przestrzeni funkcji
	*/
	public function importFunctions($name)
	{
		if (Vf_Loader::existsFile(DIR_FUNCTIONS . $name . '.php')) {
			require_once(DIR_FUNCTIONS . $name . '.php');
		} else {
			throw new Vf_ViewFileFunctionsNotFound_Exception("File functions $name not found");
		}
	}
	
	
	/**
	* Dodaje do zmiennej flashMessages obiekt klasy Vf_Flash
	* @access public 
	*/
	public function addFlash()
	{
		$this->vars['flashMessages'] = Vf_Core::getContainer()->request->response->flash;
	}
	
	
	/**
	* Zwraca zawartosc widoku
	* @access public 
	* @return string
	*/
	public function render()
	{
		ob_start();
		extract($this->vars, EXTR_SKIP);
			
		include($this->viewPath);
		
		$display = ob_get_contents();
		ob_end_clean();
		
		return $display;
	}
	
	
	/**
	* Ustawia sciezke do widoku na podstawie jego typu
	* @access public 
	* @param string $type typ widoku
	* @param string $component nazwa komponentu
	* @param string $view nazwa widoku
	* @return string 
	*/
	protected function getViewPath($type, $component, $view)
	{
		switch ($type) {
			case 'front':
				return DIR_VIEWS . $view . '.php';
				break;
						
			case 'component':
				return DIR_COMPONENTS . $component . '/' . DIR_VIEWS . $view . '.php';
				break;

			case 'plugin':
				return DIR_PLUGINS . $component . '/' . DIR_VIEWS . $view . '.php';
				break;
							
			case 'widget':
				return DIR_WIDGETS_VIEWS . $view . '.php';
				break;
		}
	}
}
?>