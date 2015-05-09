<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Orm.php');

class Vf_adminPages_Model extends Vf_Orm
{

	protected $table = 'users';
	
	protected $primaryKey = 'hash';
	
	protected $struct = array('id', 'login', 'haslo', 'hash', 'email', 'role');
	

	public function getAll()
	{
		$pages = $this -> db -> Select('*', 'components')
							 -> OrderBy('id', 'DESC')
							 -> Execute();
								 
		$fetched = $this -> db -> FetchAllAssoc($pages);
		
		$byPage = array();
		foreach($fetched as $fetch)
			$byPage[$fetch['page']][] = $fetch;
		return $byPage;
		
	}
	
	
	public function deletePageId($id)
	{
		return $this -> db -> Delete('components', array('id' => $id));
	}
	
	
	public function addPage($data)
	{
		return $this -> db -> Insert('components', $data, true);
	}
	
	
	public function getComponentPlugins($page, $module, $component)
	{
		$plugins = $this -> db -> Select('*', 'pm_component_plugins')
							   -> Where(array('p_page' => $page, 'p_module' => $module, 'p_component' => $component))
							   -> Execute();
								 
		return $this -> db -> FetchAllAssoc($plugins);
	}
	
	
	public function turnOffPlugin($id)
	{
		return $this -> db -> Update('pm_component_plugins', array('active' => 0), array('p_id' => $id));
	}
	
	
	public function turnOnPlugin($id)
	{
		return $this -> db -> Update('pm_component_plugins', array('active' => 1), array('p_id' => $id));
	}
	
	
	public function addPlugin($data)
	{
		return $this -> db -> Insert('pm_component_plugins', $data, true);
	}
	
	
	public function deleteAddonTable($table)
	{
		if(!is_array($table))
		{
			return $this -> db -> SetQuery('DROP TABLE IF EXISTS `'.$table.'`');
		}
		else
		{
			$deleted = false;
			foreach($table['table'] as $key => $tbl)
			{
				if($this -> db -> SetQuery('DROP TABLE IF EXISTS `'.$tbl.'`'))
				{
					$deleted = true;
				}
				else
				{
					$deleted = false;
				}
			}
			return $deleted;
		}
	}
	
	
	public function deleteComponentRecords($component)
	{
		return $this -> db -> Delete('components', array('component' => $component));
	}
	
	
	public function deletePluginRecords($plugin)
	{
		return $this -> db -> Delete('pm_component_plugins', array('plugin' => $plugin));
	}
	
	
	public function InsertSchema($schema)
	{
		return $this -> db -> SetQuery($schema);
	}
	
	
	public function countUsers()
	{
		return $this -> db -> Count('users');
	}
	
	
	public function getAllUsers($offset, $perPage)
	{
		//$this -> primaryKey = 'id';
		//return $this -> setSort('DESC') -> findAll($offset, $perPage) -> toArray();
		$users = $this -> db -> Select('usr.*, userBan.*, userAccount.active', 'users as usr')
						     -> Join('ban as userBan', array('userBan.ban_user' => 'usr.login'), 'LEFT')
						     -> Join('accounts as userAccount', array('userAccount.login' => 'usr.login'), 'LEFT')
						     -> Limit(array($offset, $perPage))
						     -> Execute();
		
		return $this -> db -> FetchAllAssoc($users);
	}
	
	
	public function getUserData($id)
	{
		$this -> primaryKey = 'id';
		return $this -> find($id);
	}
	
	
	public function removeUser($id)
	{
		//orm delete
		return $this -> delete(array('id' => (int)$id));
	}
	
	
	public function banUser($data)
	{
		return $this -> db -> Insert('ban', $data, true);
	}
	
	
	public function unbanUser($user)
	{
		return $this -> db -> Delete('ban', array('ban_user' => $user));
	}
	
	
	private function _checkUserAccountActive($id)
	{
		$users = $this -> db -> Select('id', 'accounts')
							 -> Where(array('id' => $id))
						     -> Limit(1)
						     -> Execute();
		return $users;
	}
	
	
	public function activeAccount($data, $userID)
	{
		if($this -> _checkUserAccountActive($userID))
		{
			return $this -> db -> Update('accounts', array('active' => 1), array('login' => $data['login']));
		}
		else
		{
			return $this -> db -> Insert('accounts', $data, true);
		}
	}
	
	
	public function disableAccount($data, $userID)
	{
		if($this -> _checkUserAccountActive($userID))
		{
			return $this -> db -> Update('accounts', array('active' => 0), array('login' => $data['login']));
		}
		else
		{
			return $this -> db -> Insert('accounts', $data, true);
		}
	}
	
	
	public function addAccountData($data)
	{
		return $this -> db -> Insert('accounts', $data, true);
	}
	
	
	public function getLastInsertId()
	{
		$q = $this -> db -> SetQuery('SELECT LAST_INSERT_ID()');
		$id = $this -> db -> FetchRow($q);
		return $id[0];
	}
	
	
	public function getAclGroups()
	{
		$aclCfg = parse_ini_file(DIR_CONFIG.'acl/config.ini', true);
		foreach($aclCfg as $group => $roles)
		{
			if($group == 'guest')
				continue;
			$groups[] = $group;
		}
		return $groups;
	}
	
	
	public function removeLocale($locale)
	{
		return $this -> db -> Delete('locales', array('locale' => $locale));
	}
	
	
	public function addLocale($data)
	{
		return $this -> db -> Insert('locales', $data, true);
	}
}

?>