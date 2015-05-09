<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Model.php');

class Vf_contact_Model extends Vf_Model
{
	public function getContactsData()
	{
		$q = $this -> db -> SetQuery('Select * from contact_me');
		$fetch = $this -> db -> FetchAssoc($q);
		unset($fetch['id']);
		return $fetch;
	}
	
	
	public function saveContacts($data)
	{
		$contact = array();
		foreach($data as $key => $value)
		{
			if(empty($value))
			{
				$contact[$key] = ''; //NULL/0
			}
			else
			{
				$contact[$key] = htmlspecialchars($value);
			}
		}
		return $this -> db -> Update('contact_me', $contact, array('id' => 0));
	}
}

?>