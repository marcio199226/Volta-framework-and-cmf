<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Plugin.php');
require_once(DIR_PLUGINS.'comment/'.DIR_LIBRARY.DIR_NOTIFY.'add.php');

class Vf_comment_Plugin extends Vf_Plugin
{
	
	public function submit()
	{
		$segment = $this -> settings['segment_ref'];
	
		$csrf = new Vf_Security();
		$captcha = new Vf_Captcha();
		$captcha -> setSize(155, 35);
		
		$comments = Vf_Orm::factory('comment');
		
		//load files with translations
		$translate = Vf_Language::instance();
		$translate -> get() -> load('plugins/comment/comment.php');
		//load helper translate for controllers translations
		Vf_Loader::loadHelper('Translate');
		
		$view = new Vf_View('comment', 'plugin', 'comment');
		$view -> loadHelper('Form');
		$view -> loadHelper('User');
		$view -> loadHelper('BBCode');
		$view -> importFunctions('common');
		$view -> comments = $comments -> getComments($this -> container -> router -> getFrontController(), $this -> container -> router -> getFrontControllerAction(), $this -> container -> router -> getSegment($segment));
		
		if(!$this -> container -> request -> post('submit_padd_comment'))
		{
			$view -> captcha = $captcha -> getCaptcha();
		}
		
			if($this -> container -> request -> post('submit_padd_comment'))
			{
				if($csrf -> csrf_check_token($this -> container -> request -> post('csrf_token')))
				{
					if($captcha -> checkCaptcha($this -> container -> request -> post('pcaptcha')))
					{
				
						$validation = new Vf_Validator();
						$validation -> load('str');
						$validation -> add_data($_POST);
						
						if(Vf_User_Helper::anonymous() === true)
						{
							$validation -> add_rule('puser', new str(array('field' => 'nick', 'required' => true, 'alphadigit' => true, 'max' => 20)));
						}
						
						$validation -> add_rule('pcomment', new str(array('field' => 'komentarz', 'required' => true, 'alphadigit' => true, 'max' => 300)));
						$validation -> validation();
						
						$author = (Vf_User_Helper::anonymous() === true) ? $this -> container -> request -> post('puser') : $this -> container -> request -> session('user');
				
						$comment = array(
							'id' => null,
							'page' => $this -> container -> router -> getFrontController(),
							'module' => $this -> container -> router -> getFrontControllerAction(),
							'component' => $this -> settings['component'],
							'ref_id' => $this -> container -> router -> getSegment($segment),
							'author' => $author,
							'content' => $this -> container -> request -> post('pcomment'),
							'data' => date('d/m/Y')
						);
					
						if(sizeof($validation -> get_errors()) == 0)
						{
							if($comments -> addComment($comment))
							{
								if(isset($this -> settings['notify']['on']) && $this -> settings['notify']['on'] == 1)
								{
									$notify = new Vf_Notify_Comment_Add();
									$notify -> setRecipients($comments -> getAuthorEmail($this -> settings['notify']['join_t'], $this -> settings['notify']['join_on'], $this -> container -> router -> getSegment($segment)));
									$notify -> setSender('admin@marcio.ekmll.com');
									$notify -> setSubject('Powiadomienie o nowym komentarzu');
									$notify -> setUrl('<a target="_blank" href="www.marcio.ekmll.com/Vf/'.$this -> container -> router -> getSegment($segment).'">Wpisu</a>'); 
									$notify -> setMessage('Zostal dodany nowy komentarz do '.$notify -> getUrl());
									$notify -> notify();
								}
								
								Vf_Loader::loadHelper('Uri');
								Vf_Uri_Helper::redirect($this -> container -> request -> referer());
							}
							else
							{
								$view -> error_add_comment = Vf_Translate_Helper::__('Blad podczas dodawania komentarza.');
							}
						}
						else
						{
							$view -> errors = $validation -> get_errors();
						}
					}
					else
					{
						$view -> captcha = $captcha -> getCaptcha();
						$view -> msg = Vf_Translate_Helper::__('Zla captcha');
					}
				}
				else
				{
					$view -> captcha = $captcha -> getCaptcha();
					$view -> msg = Vf_Translate_Helper::__('Zly token');
				}
			}
			return $view -> render();
	}
	
	
	public function delete()
	{
		$csrf = new Vf_Security();
		Vf_Loader::loadHelper('Uri');
		
		if($this -> container -> request -> post('del_comment'))
		{
			$comment = Vf_Orm::factory('comment');
			if($comment -> deleteComment($this -> container -> request -> post('del_comment')) && $csrf -> csrf_check_token($this -> container -> request -> post('csrf')))
			{
				Vf_Uri_Helper::redirect($this -> container -> request -> referer());
			}
			else
			{
				Vf_Uri_Helper::redirect($this -> container -> request -> referer());
			}
		}
	}
	
	
	public function deleteAllRecordComments()
	{
		$csrf = new Vf_Security();
		
		//load files with translations
		$translate = Vf_Language::instance();
		$translate -> get() -> load('plugins/comment/deleteAll.php');
		//load helper translate for controllers translations
		Vf_Loader::loadHelper('Translate');
		
		$view = new Vf_View('deleteAll', 'plugin', 'comment');
		$view -> loadHelper('Form');
		$view -> loadHelper('Box');
		$view -> importFunctions('common');
		
		if($this -> container -> request -> post('submit_pdel_comment'))
		{
			$comment = Vf_Orm::factory('comment');
			$ref_id = $this -> container -> router -> getSegment($this -> settings['segment_ref']);
			
			if($comment -> deleteAllComments($this -> settings['component'], $ref_id) && $csrf -> csrf_check_token($this -> container -> request -> post('csrf_token')))
			{
				$view -> msg = Vf_Translate_Helper::__('Powiazane komentarze zostaly usuniete');
			}
			else
			{
				$view -> error_del = Vf_Translate_Helper::__('Blad podczas usuwania komentarzy');
			}
		}
		return $view -> render();
	}
}

?>