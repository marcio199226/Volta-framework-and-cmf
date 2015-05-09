<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

interface IConfig
{
	public function load($config_path);
	public function isAcceptSuffix($suffix);
}
?>