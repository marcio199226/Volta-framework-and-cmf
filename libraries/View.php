<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_View
{

	protected $vars = array();
	
	protected $viewPath = null;

	
	public function __construct($view, $type = 'front', $cmpName = null)
	{
		$viewPath = $this -> setViewPath($type, $cmpName, $view);
		
		if(Vf_Loader::existsFile($viewPath))
		{
			$this -> viewPath = $viewPath;
		}
		else
		{
			throw new Vf_ViewNotFound_Exception('View: '.$view.' doesn\'t exists');
		}
	}
	
	
	public function __set($key, $value)
	{
		$this -> vars[$key] = $value;
	}
	
	
	public function __get($key)
	{
		return $this -> vars[$key];
	}
	
	
	public function assign($key = null, $values)
	{
		if(is_array($values))
		{
			foreach($values as $key => $val)
			{
				$this -> vars[$key] = $val;
			}
		}
		else
		{
			$this -> vars[$key] = $values;
		}
	}

	
	public function setHelper($helper)
	{
		if(Vf_Loader::existsFile(DIR_HELPERS.$helper.'.php'))
		{
			require_once(DIR_HELPERS.$helper.'.php');
			$helperName = 'Vf_'.$helper.'_Helper';
			$this -> vars[$helper] = new $helperName();
		}
		else
		{
			throw new Vf_ViewHelperNotFound_Exception("Helper $helperName not found");
		}
	}
	
	
	public function loadHelper($helper)
	{
		if(Vf_Loader::existsFile(DIR_HELPERS.$helper.'.php'))
		{
			require_once(DIR_HELPERS.$helper.'.php');
		}
		else
		{
			throw new Vf_ViewHelperNotFound_Exception("Helper $helper not found");
		}
	}
	
	
	public function importFunctions($name)
	{
		if(Vf_Loader::existsFile(DIR_FUNCTIONS.$name.'.php'))
		{
			require_once(DIR_FUNCTIONS.$name.'.php');
		}
		else
		{
			throw new Vf_ViewFileFunctionsNotFound_Exception("File functions $name not found");
		}
	}
	
	
	public function addFlash()
	{
		$this -> vars['flashMessages'] = Vf_Core::getContainer() -> request -> response -> flash;
	}
	
	
	public function render()
	{
		ob_start();
		extract($this -> vars, EXTR_SKIP);
			
		include($this -> viewPath);
		
		$end = ob_get_contents();
		ob_end_clean();
		
		return $end;
		
	}
	
	
	protected function setViewPath($type, $component, $view)
	{
		switch($type)
		{
			case 'front':
				return DIR_VIEWS.$view.'.php';
			break;
						
			case 'component':
				return DIR_COMPONENTS.$component.'/'.DIR_VIEWS.$view.'.php';
			break;

			case 'plugin':
				return DIR_PLUGINS.$component.'/'.DIR_VIEWS.$view.'.php';
			break;
							
			case 'widget':
				return DIR_WIDGETS_VIEWS.$view.'.php';
			break;
		}
	}
}
?>