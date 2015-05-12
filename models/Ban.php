<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY . 'Orm.php');

class Vf_Ban_Model extends Vf_Orm
{
	protected $table = 'ban';
	
	protected $primaryKey = 'ban_user';
	
	protected $struct = array('ban_id', 'ban_user', 'ban_expire');
	
	
	public function isBanned($user)
	{
		$banned = false;
		$query = $this->db->Select('ban_expire', $this->table)
			->Where(array('ban_user' => $user))
			->Limit(1)
			->Execute();
							 
		if ($this->db->CountRows($query) == 1) {
			$bannedData = $this->db->FetchAssoc($query);

			if($bannedData['ban_expire'] == NULL || $bannedData['ban_expire'] == 0) {
				$banned = true;
			} elseif ($bannedData['ban_expire'] != null && (time() > time() - $bannedData['ban_expire'])) {
				if($this->db->Delete($this->table, array('ban_user' => $user))) {
					$banned = false;
				}
			} else {
				$banned = true;
			}
		} else {
			$banned = false;
		}
		return $banned;
	}
}

?>