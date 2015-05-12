<?php

/**
* Volta framework

 *@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_LIBRARY . 'Orm.php');

class Vf_comment_Model extends Vf_Orm
{
	//ToDO poprawic by pobieralo news-y na podstawie komponentu i id wpisu. Patrz: $this->deleteAllComments
	public function getComments($page, $module, $ref_id)
	{	
		$data = $this->db->Select('*', 'comments')
			->Where(array('page' => $page, 'module' => $module, 'ref_id' => $ref_id))
			->OrderBy('id' , 'ASC')
			->Execute();		
		return $this->db->FetchAllAssoc($data);
	}
	
	
	public function deleteComment($id)
	{
		return $this->db->Delete('comments', array('id' => $id));
	}
	
	
	public function addComment($insert)
	{
		return $this->db->Insert('comments', $insert, true);
	}
	
	
	public function deleteAllComments($component, $ref_id)
	{
		return $this->db->Delete('comments', array('component' => $component, 'ref_id' => $ref_id));
	}
	
	
	public function getAuthorEmail($joinTable, $joinOn, $id)
	{
		$data = $this->db->Select('users.email', 'users')
			-> Join($joinTable, array($joinOn => 'users.login'))
			->Where(array($joinTable . '.id' => $id))
			->Execute();
							
		$email = $this->db->FetchRow($data);
		return $email[0];
	}
}

?>