<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_LIBRARY . 'Plugin.php');

class Vf_polling_Plugin extends Vf_Plugin
{
	public function createPoll()
	{
		Vf_Loader::loadHelper('Translate');
		
		$this->container->language->get()->load('plugins/poll/create.php');
	
		$pollingModel = Vf_Orm::factory('polling');
		$page = $this->container->router->getFrontController();
		$module = $this->container->router->getFrontControllerAction();
		$component = $this->settings['component'];
		$ref_id = $this->container->router->getSegment($this->settings['segment_ref']);
		
		if ($pollingModel->hasPoll($page, $module, $component, $ref_id) == 0) {
			$view = new Vf_View('admin/create', 'plugin', 'polling');
			$view->loadHelper('Form');
			$view->loadHelper('Box');
			$view->importFunctions('common');
			$view->expiresValues = array(
				'0' => Vf_Translate_Helper::__('pollFormCreateExpiresNever'),
				'86400' => '24h',
				'259200' => Vf_Translate_Helper::__('pollFormCreateExpires3Days'),
				'604800' => Vf_Translate_Helper::__('pollFormCreateExpiresWeek'),
				'2592000' => Vf_Translate_Helper::__('pollFormCreateExpiresMonth')
			);

			if ($this->container->request->post('submit_padd_poll')) {
				$validation = new Vf_Validator();
				$validation->load('str');
				$validation->add_data($_POST);				
				$validation->add_rule('ptitle_poll', new str(array('field' => Vf_Translate_Helper::__('pollFormCreateLabelQuestion'), 'required' => true, 'alphadigit' => true, 'max' => 200)));
				$validation->add_rule('panswers_poll', new str(array('field' => Vf_Translate_Helper::__('pollFormCreateLabelAnswers'), 'required' => true, 'max' => 200)));
				$validation->validation();
				
				if (sizeof($validation->get_errors()) == 0) {
					$poll = array(
						'id' => null,
						'page' => $page,
						'module' => $module,
						'component' => $this->settings['component'],
						'ref_id' => $ref_id,
						'title' => $this->container->request->post('ptitle_poll'),
						'date_add' => date('d/m/Y'),
						'date_start' => time(),
						'date_expire' => $this->container->request->post('expire'),
						'lang' => $this->container->language->get()->getLang()
					);
							
					$stripNewLinesAnswers = str_replace(array("\n", "\r\n", "\r"), "", $this->clearEndDots($this->container->request->post('panswers_poll')));
					$answers = explode(';', $stripNewLinesAnswers);
						
					if ($this->hasAnswers($answers)) {
						if ($pollingModel->addPollQuestion($poll)) {
							foreach ($answers as $answer) {
								$poll_answer[] = array(
									'id_answer' => null,
									'poll_id' => $pollingModel->getPollId(),
									'answer' => $answer,
									'lang' => Vf_Core::getContainer()->language->get()->getLang()
								);
							}
										
							if ($pollingModel->addPollAnswers($poll_answer)) {
								$view->success = Vf_Translate_Helper::__('pollControllerCreateSuccess');
							} else {
								$view->error_add_poll = Vf_Translate_Helper::__('pollControllerAddAnswerError');
							}
						} else {
							$view->error_add_poll = Vf_Translate_Helper::__('pollControllerAddQuestionError');
						}
					} else {
						$view->error_add_poll = Vf_Translate_Helper::__('pollControllerInvalidAnswers');
					}
				} else {
					$view->errors = $validation->get_errors();
				}
			}	
			return $view->render();
		}
	}
	
	
	public function vote()
	{		
		Vf_Loader::loadHelper('Translate');
	
		$pollingModel = Vf_Orm::factory('polling');
		$page = $this->container->router->getFrontController();
		$module = $this->container->router->getFrontControllerAction();
		$component = $this->settings['component'];
		$ref_id = $this->container->router->getSegment($this->settings['segment_ref']);
		
		if ($pollingModel->hasPoll($page, $module, $component, $ref_id)) {
			
			$acl = $this->container->aclCore;
			$pollData = $pollingModel->getPollAnswers($page, $module, $component, $ref_id);
			$cookieData = unserialize($this->container->request->cookie('poll_voted'));
		
			if(($acl->has_role('polling', 'deletePoll') && $acl->has_role('polling', 'deletePollAnswer')) || $acl->has_role('polling', '*')) {
				$this->container->language->get()->load('plugins/poll/voteAdmin.php');
				$view = new Vf_View('admin/pollAdmin', 'plugin', 'polling');
			} else {
				$this->container->language->get()->load('plugins/poll/vote.php');
				$view = new Vf_View('poll', 'plugin', 'polling');
			}
			
			$view->loadHelper('Box');
			$view->importFunctions('common');
			$view->poll = $pollData;
			$view->voteMode = $this->settings['vote'];
		
			if ($cookieData[$pollData[0]['poll_id']]) {
				$view->hasVoted = true;
			}
			if ($pollData[0]['date_expire'] == 0) {
				$view->hasExpired = false;
			} elseif (time() > ($pollData[0]['date_start'] + $pollData[0]['date_expire'])) {
				$view->hasExpired = true;
			}
		
			if ($this->container->request->post('submit_padd_vote')) {
				if (!$cookieData[$pollData[0]['poll_id']]) {
					if ($this->settings['vote'] == 'single') {
						if ($pollingModel->addPollAnswerVote($this->container->request->post('pvote'))) {
							$cookieData[$pollData[0]['poll_id']] = true;
							setcookie("poll_voted", serialize($cookieData), time()+2592000);
							$view->success = Vf_Translate_Helper::__('Twoj glos zostal oddany');
						} else {
							$view->error_answer = Vf_Translate_Helper::__('Wystapil blad podczas glosowania');
						}
					} else {
						if (sizeof($this->container->request->post('pvote')) > 0) {
							foreach ($this->container->request->post('pvote') as $id_answer) {
								if ($pollingModel->addPollAnswerVote($id_answer)) {
									$voteIsAdded = true;
								} else {
									$voteIsAdded = false;
								}
							}
							if($voteIsAdded) {
								$cookieData[$pollData[0]['poll_id']] = true;
								setcookie("poll_voted", serialize($cookieData), time()+2592000);
								$view->success = Vf_Translate_Helper::__('Twoje glosy zostaly oddane');
							} else {
								$view->error_answer = Vf_Translate_Helper::__('Wystapil blad podczas glosowania');
							}
						} else {
							$view->error_answer = Vf_Translate_Helper::__('Prosze zaznaczyc przynajmniej jedna odpowiedz');
						}
					}
				}
			}
			return $view->render();
		}
	}
	
	
	public function deletePoll()
	{
		if($this->container->request->post('submit_pdel_poll')) {
			$pollAnswer = Vf_Orm::factory('polling');
			if($pollAnswer->deletePoll($this->container->request->post('submit_pdel_poll'))) {
				Vf_Loader::loadHelper('Uri');
				Vf_Uri_Helper::redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	
	public function deletePollAnswer()
	{
		if($this->container->request->post('submit_pdel_poll_answer')) {
			$pollAnswer = Vf_Orm::factory('polling');
			if($pollAnswer->deleteAnswer($this->container->request->post('submit_pdel_poll_answer'))) {
				Vf_Loader::loadHelper('Uri');
				Vf_Uri_Helper::redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	
	private function hasAnswers($answers)
	{
		return (sizeof($answers) > 1) ? true : false;
	}
	
	
	private function clearEndDots($answers)
	{
		if(substr($answers, -1, 1) == ';') {
			return substr($answers, 0, -1);
		}
	}
}

?>