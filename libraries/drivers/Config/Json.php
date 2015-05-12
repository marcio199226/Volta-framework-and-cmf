<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_INTERFACES . 'IConfig.php');

class Vf_Config_Json_Adapter implements IConfig
{
	public function load($config_path)
	{
		if (file_exists($config_path)) { 
			return json_decode(file_get_contents($config_path), true)
		} else {
			throw new Vf_Config_Json_Adapter_Exception("Nie zaladowano konfiguracji: " . $config_path);
		}
	}
	
	
	public function isAcceptSuffix($suffix)
	{
		if ($suffix == 'json') {
			return true;
		}
		return false;
	}
}
?>