<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Model.php');

class Vf_online_Model extends Vf_Model
{
		
	public function deleteInactiveUsers($seconds)
	{
		$expired = (time() - $seconds);
		return $this -> db -> Delete('online_users', array('time' => $expired), '<');
	}
	
	
	public function setOnlineUsers()
	{
		$request = new Vf_Request();
		
		$data = array(
						'id' => null,
						'ip' => $request -> ip(),
						'time' => time(),
						'username' => $request -> session('user')
					);
		
		return $this -> db -> Insert('online_users', $data, true);
	}
	
	
	public function getOnlineUsers()
	{
		//$q = $this -> db -> SetQuery('Select distinct ip, username from online_users');
		$q = $this -> db -> Select(array('ip', 'username') ,'online_users') -> GroupBy(array('ip', 'username')) -> Execute();
		return $this -> db -> FetchAllAssoc($q);
	}
}

?>