<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Loader
{
	static protected $mapped_path = array(
		'GeSHi' => 'modules/geshi/geshi.php'
	);

	public static function autoload($class)
	{
		$suffix = explode('_', $class);
		$suff = $suffix[count($suffix)-1];

		if ($suff == 'Model') {
			if(self::existsFile(DIR_MODELS . $suffix[1] . '.php')) {
				require_once(DIR_MODELS . $suffix[1] . '.php');
			} elseif (self::existsFile(DIR_COMPONENTS . $suffix[1] . '/' . DIR_MODELS . $suffix[1] . '.php')) {
				require_once(DIR_COMPONENTS . $suffix[1] . '/' . DIR_MODELS . $suffix[1] . '.php');
			} elseif (self::existsFile(DIR_PLUGINS . $suffix[1] . '/' . DIR_MODELS . $suffix[1] . '.php')) {
				require_once(DIR_PLUGINS . $suffix[1] . '/' . DIR_MODELS . $suffix[1] . '.php');
			} elseif (self::existsFile(DIR_WIDGETS_MODELS . $suffix[1] . '.php')) {
				require_once(DIR_WIDGETS_MODELS . $suffix[1] . '.php');
			}
		} else {
			if (array_key_exists($class, self::$mapped_path)) {
				require_once(self::$mapped_path[$class]);
			} elseif (self::existsFile(DIR_LIBRARY . $suffix[1] . '.php')) {
				require_once(DIR_LIBRARY . $suffix[1] . '.php');
			}
		}
	}
	
	
	public static function loadFile($path, $rec = false)
	{
		if (!$rec) {
			return require_once($path);
		} else {
			foreach (glob($path) as $file) {
				require_once($file);
			}
			return true;
		}
	}
	
	
	public static function loadModel($model, $component, $create = true)
	{
		if(self::existsFile(DIR_COMPONENTS . $component . '/' . DIR_MODELS . $model . '.php')) {
			require_once(DIR_COMPONENTS . $component . '/' . DIR_MODELS . $model . '.php');
			if ($create) {
				$class = 'Vf_' . $model . '_Model';
				if(class_exists($class)) {
					return new $class();
				}
			}
		}
	}
	
	
	public static function existsFile($file)
	{
		return file_exists($file);
	}
	
	
	public static function existsDir($dir)
	{
		return is_dir($dir);
	}
	

	public static function loadLib($name, $param = null)
	{
		if (self::existsFile(DIR_LIBRARY . $name . '.php')) {
			require_once(DIR_LIBRARY . $name . '.php');
		}
		$class = 'Vf_' . $name;
		
		if (class_exists($class)) {
			if($param === null) {
				$object = new $class();
			} else {
				$object = new $class($param);
			}
		} else {
			throw new Exception("Nie znaleziono pliku biblioteki: " . $name);
		}
		return $object;
	}
	
	
	public static function loadHelper($helper)
	{
		if(self::existsFile(DIR_HELPERS . $helper . '.php')) {
			require_once(DIR_HELPERS . $helper . '.php');
		} else {
			throw new Exception("Nie znaleziono pliku helpera: " . $helper);
		}
	}
	
	
	public static function loadWidget($widget)
	{
		if(self::existsFile(DIR_WIDGETS_CTRL . $widget . '.php')) {
			require_once(DIR_WIDGETS_CTRL . $widget . '.php');
			$widgetClassName = 'Vf_' . $widget . '_Widget';
			$widgetClass = new $widgetClassName();
			return $widgetClass->display();
		} else {
			throw new Exception("Nie znaleziono pliku helpera: " . $helper);
		}
	}
}
?>