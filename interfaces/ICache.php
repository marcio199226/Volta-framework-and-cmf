<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

interface ICache 
{
    public function getCache($name);
	public function setCache($id, $data, $lifetime = null);
	public function deleteCache($id);
	public function deleteAllCache();
}

?>