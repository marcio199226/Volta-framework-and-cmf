<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Orm.php');

class Vf_polling_Model extends Vf_Orm
{
	public function getPollAnswers($page, $module, $component, $ref_id)
	{
		$data = $this -> db -> Select(
								'poll_answers.*, poll_questions.title, poll_questions.date_add, poll_questions.date_start, poll_questions.date_expire, (select sum(votes) from poll_answers where poll_answers.poll_id=poll_questions.id) as sum', 
								'poll_questions'
									)
							-> Join('poll_answers', array('poll_answers.poll_id' => 'poll_questions.id'))
							-> Where(array('page' => $page, 'module' => $module, 'component' => $component, 'ref_id' => $ref_id))
							-> Execute();
							
		return $this -> db -> FetchAllAssoc($data);
	}
	
	public function addPollQuestion($data)
	{
		return $this -> db -> Insert('poll_questions', $data, true);
	}
	
	
	public function addPollAnswers($data)
	{
		return $this -> db -> MultiInsert('poll_answers', $data, true);
	}
	
	
	public function addPollAnswerVote($answer_id)
	{
		return $this -> db -> SetQuery('UPDATE poll_answers SET votes=votes+1 WHERE id_answer='.(int)$answer_id);
	}
	
	
	public function getPollId()
	{
		return $this -> db -> InsertId();
	}
	
	
	public function deletePoll($id)
	{
		if($this -> db -> Delete('poll_questions', array('id' => $id)) && $this -> db -> Delete('poll_answers', array('poll_id' => $id)))
		{
			return true;
		}
		return false;
	}
	
	
	public function deleteAnswer($id)
	{
		return $this -> db -> Delete('poll_answers', array('id_answer' => $id));
	}
	
	
	public function hasPoll($page, $module, $component, $ref_id)
	{
		$poll = $this -> db -> Select('id', 'poll_questions')
							-> Where(array('page' => $page, 'module' => $module, 'component' => $component, 'ref_id' => $ref_id))
							-> Limit(1)
							-> Execute();
							
		return $this -> db -> CountRows($poll);
	}
	
}

?>