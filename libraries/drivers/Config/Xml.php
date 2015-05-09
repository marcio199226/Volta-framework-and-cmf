<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES.'IConfig.php');

class Vf_Config_Xml_Adapter implements IConfig
{

	public function load($config_path)
	{
		$cache = new Vf_Cache();
		if($cachedConfig = $cache -> getCache($config_path, 86400))
		{
			return $cachedConfig;
		}
		else if(file_exists($config_path))
		{
			$xml = new SimpleXMLElement($config_path, 0, true);
			$xml2php = $this -> _XmlToArray($xml);
			$cache -> setCache($config_path, $xml2php, 86400);
			return $xml2php;
		}
		else
		{
			throw new Vf_Config_Xml_Adapter_Exception("Nie zaladowano konfiguracji: ".$config_path);
		}
	}
	
	
	public function isAcceptSuffix($suffix)
	{
		if($suffix == 'xml')
			return true;
		return false;
	}
	
	
	private function _XmlToArray($xml_object)
	{
		$config = array();
		if(is_object($xml_object) && $vars = get_object_vars($xml_object))
		{
			foreach($vars as $key => $value)
			{
				if($key == 'cached_action')
				{
					$i = 0;
					foreach($value -> children() as $child => $value)
					{
						$config[$key][$i] = (string)$value;
						$i++;
					}
				}
				else
				{
					if(!is_object($value))
					{
						$config[$key] = $value;
					}
					else
					{
						$config[$key] = $this -> _XmlToArray($value);
						$this -> _normalizePluginsArray($config, $key);
					}
				}
			}
			return $config;
		}
		return $xml_object;
	}
	
	
	private function _normalizePluginsArray(&$data, $currentKey)
	{	
		if($currentKey == 'compatible' || $currentKey == 'actions')
		{
			foreach($data[$currentKey] as $childNodeName => $values)
			{
				//foreach($values as $name => $attr)
				$data[$currentKey] = $values;
			}
		}
	}
}
?>