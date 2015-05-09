<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_User_Helper
{
	public static function is($type)
	{
		$request = new Vf_Request();
		
		if($request -> session('role') == $type)
		{
			return true;
		}
		return false;
	}
	
	
	public static function anonymous()
	{
		$request = new Vf_Request();
		
		if(!$request -> session('user') && !$request -> session('role') && !$request -> session('hash'))
		{
			return true;
		}
		return false;
	}
	
	
	public static function hasRole($resource, $role)
	{
		$acl = Vf_Core::getContainer() -> aclCore;
		
		try
		{
			if($acl -> is_allowed($resource, $role))
			{
				return true;
			}
		}
		catch(Volta_Acl_Deny_Exception $e)
		{
			return false;
		}
		return false;
	}
	
	
	public static function hasRoles($resources)
	{
		$allowed = array();
		$acl = Vf_Core::getContainer() -> aclCore;
		
		foreach($resources as $resource)
		{
			foreach($resource as $role)
			{
				try
				{
					if($acl -> is_allowed($resource, $role))
					{
						$allowed[$resource][$role] = true;
					}
				}
				catch(Volta_Acl_Deny_Exception $e)
				{
					$allowed[$resource][$role] = false;
				}
			}
		}
		return $allowed;
	}
}
?>