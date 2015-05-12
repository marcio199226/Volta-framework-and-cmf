<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_User_Helper
{
	/**
	* Czy uzytkownik jest w danej grupie
	* @static
	* @access public 
	* @param string $type nazwa grupy
	* @return boolean
	*/
	public static function is($type)
	{
		$request = new Vf_Request();
		if ($request->session('role') == $type) {
			return true;
		}
		return false;
	}
	
	
	/**
	* Jestes gosciem
	* @static
	* @access public 
	* @return booleean
	*/
	public static function anonymous()
	{
		$request = new Vf_Request();
		if (!$request->session('user') && !$request->session('role') && !$request->session('hash')) {
			return true;
		}
		return false;
	}
	
	
	/**
	* Czy uzytkownik posiada prawa do zasobu
	* @static
	* @access public 
	* @param string $resource zasob
	* @param string $role nazwa roli
	* @return boolean
	*/
	public static function hasRole($resource, $role)
	{
		$acl = Vf_Core::getContainer()->aclCore;
		try {
			if ($acl->is_allowed($resource, $role)) {
				return true;
			}
		} catch (Volta_Acl_Deny_Exception $e) {
			return false;
		}
		return false;
	}
	
	
	/**
	* Czy uzytkownik posiada wiecej praw do zasobu
	* @static
	* @access public 
	* @param array $resource tablica z prawami
	* @return boolean
	*/
	public static function hasRoles($resources)
	{
		$allowed = array();
		$acl = Vf_Core::getContainer()->aclCore;
		
		foreach ($resources as $resource) {
			foreach ($resource as $role) {
				try {
					if ($acl->is_allowed($resource, $role)) {
						$allowed[$resource][$role] = true;
					}
				} catch(Volta_Acl_Deny_Exception $e) {
					$allowed[$resource][$role] = false;
				}
			}
		}
		return $allowed;
	}
}
?>