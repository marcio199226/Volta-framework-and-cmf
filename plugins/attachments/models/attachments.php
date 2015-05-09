<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Model.php');

class Vf_attachments_Model extends Vf_Model
{
	public function saveAttachmentsFileNames($filenames, $table)
	{
		//myslq_insert_id() i LAST_INSERT_ID() nie dzialaja
		$query = $this -> db -> SetQuery('select max(id) from '.$table);
		$last_id = $this -> db -> FetchRow($query);
		$filenames['ref_id'] = $last_id[0];
		return $this -> db -> Insert('attachments', $filenames, true);
	}
	
	
	public function getAttachments($component, $ref_id)
	{
		$data = $this -> db -> Select('*', 'attachments')
					  -> Where(array('component' => $component, 'ref_id' => $ref_id))
					  -> OrderBy('id' , 'ASC')
					  -> Execute();
					
		$attachments = $this -> db -> FetchAssoc($data);

		if(!is_array($attachments))
		{
			return array();
		}
		else
		{
			return explode('|', $attachments['filename']);
		}
		
	}
	
	
	public function deleteAttachmentsFilesAndEntry($component, $ref_id)
	{
		return $this -> db -> Delete('attachments', array('component' => $component, 'ref_id' => $ref_id));
	}
	
	
	public function deleteFileFromEntry($component, $ref_id, $attachments)
	{
		if($attachments !== null)
		{
			$attachments = implode('|', $attachments);
			$data = array('filename' => $attachments);
			return $this -> db -> Update('attachments', $data, array('component' => $component, 'ref_id' => $ref_id));
		}
		else
		{
			return $this -> deleteAttachmentsFilesAndEntry($component, $ref_id);
		}
	}
}

?>