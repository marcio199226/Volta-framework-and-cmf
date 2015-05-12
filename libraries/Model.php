<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY . 'Db.php');

class Vf_Model
{
	protected $db;
	
	public function __construct()
	{
		$config = new Vf_Config(DIR_CONFIG, 'Xml');
		$this->db = Vf_Db::Factory($config->DbType);
	}
}
?>