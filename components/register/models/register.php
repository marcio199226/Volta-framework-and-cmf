<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_LIBRARY . 'Orm.php');

class Vf_register_Model extends Vf_Orm
{
	protected $table = 'users';
	
	protected $primaryKey = 'login';
	
	protected $struct = array('id', 'login', 'haslo', 'hash', 'email', 'role');
	
	
	public function addAccountData($data)
	{
		return $this->db->Insert('accounts', $data, true);
	}
	
	
	public function activeAccount($code)
	{
		return $this->db->Update('accounts', array('active' => 1), array('code' => $code));
	}
	
	
	public function isActiveAccount($code)
	{
		$account = $this->db->Select('id', 'accounts')->Where(array('code' => $code, 'active' => 1))->Limit(1)->Execute();
		return ($this->db->CountRows($account) > 0) ? true : false;
	}
	
	
	public function checkHash($hash)
	{
		$account = $this->db->Select('*', 'recovered_password')->Where(array('hash' => $hash))->Limit(1)->Execute();
		return ($this->db->CountRows($account) > 0) ? true : false;
	}
	
	
	public function getUserData($loginOrEmail)
	{
		$user = $this->db->Select('*', 'users')->WhereOr(array('login' => $loginOrEmail, 'email' => $loginOrEmail))->Execute();
		return ($this->db->CountRows($user) > 0) ? $this->db->FetchAssoc($user) : false;
	}
}

?>