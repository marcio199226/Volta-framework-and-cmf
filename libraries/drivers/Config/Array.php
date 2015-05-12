<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_INTERFACES . 'IConfig.php');

class Vf_Config_Array_Adapter implements IConfig
{
	public function load($config_path)
	{
		$configs = array();
		if (file_exists($config_path)) {
			require($config_path);
			return $configs;
		} else {
			throw new Vf_Config_Array_Adapter_Exception("Nie zaladowano konfiguracji: " . $config_path);
		}
	}
	
	
	public function isAcceptSuffix($suffix)
	{
		if ($suffix == 'php') {
			return true;
		}
		return false;
	}

}
?>