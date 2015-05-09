<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Orm.php');

class Vf_UserExists_Model extends Vf_Orm
{
	protected $table = 'users';
	
	protected $primaryKey = 'login';
	
	protected $struct = array('id', 'login', 'haslo', 'hash', 'email', 'role');
}

?>