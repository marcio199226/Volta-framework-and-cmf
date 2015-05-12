<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY . 'Plugin.php');
require_once(DIR_LIBRARY . 'Orm.php');

class Vf_attachments_Plugin extends Vf_Plugin
{
	public function attachmentsAdd() 
	{
		$translate = Vf_Language::instance();
		$translate->get()->load('plugins/attachments/add.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$view = new Vf_View('admin/attachmentsAddForm', 'plugin', 'attachments');
		$view->loadHelper('Form');
		$view->importFunctions('common');
		$view->addFlash();
		$view->idFormSubmitButton = $this->settings['addNewsFormSumbitId'];
		$view->parentIdForm = $this->settings['addNewsFormId'];
		$view->locale = $translate->get()->getLang();
		$view->flashErrorsKey = $this->settings['flashErrors'];
		
		if (isset($_FILES)) {
			if (!Vf_Core::getContainer()->request->response->flash->hasFlash($this->settings['flashErrors'])) {
				try {
					$validation = new Vf_Validator();
					$validation->load('upload');
					$validation->add_data($_FILES);
					$validation->add_rule('files', new upload(array('size' => 6000000, 'invalid_extensions' => array('php', 'php3', 'phtml', 'html', 'js', 'css'))));
					$validation->validation();
					
					if(sizeof($validation->get_errors()) == 0) {
						$upload = new Vf_Upload();
						$upload->setPath($this->settings['attachmentsDir']);
						$upload->setOverwriteFile(true);
						
						if($upload->multiSend('files')) {
							$data = array(
								'id' => null,
								'page' => $this->container->router->getFrontController(),
								'module' => $this->container->router->getFrontControllerAction(),
								'component' => $this->settings['component'],
								'ref_id' => null,
								//'filename' => implode('|', $upload->getRenamed())
								'filename' => implode('|', $this->deleteEmptyFiles($_FILES['files']['name']))
							);
										 
							$model = new Vf_attachments_Model();
							$model->saveAttachmentsFileNames($data, $this->settings['table_last_insert_id']);
							$view->msg = Vf_Translate_Helper::__('Zalaczniki zostaly wyslane');
						}
					} else {
						$view->errors = $validation->get_errors();
					}
				} catch (Vf_Upload_Path_Exception $e) {
					$view->exception = $e->getMessage();
				} catch (Vf_Upload_File_Exception $e) {
					$view->exception = $e->getMessage();
				}
			} else {
				//get messages direct from view
				//$view->messages = Vf_Core::getContainer()->request->response->flash->getMessages();
			}
		}
		return $view->render();
	}
	
	
	public function attachmentsShow() 
	{
		$translate = Vf_Language::instance();
		$translate->get()->load('plugins/attachments/show.php');
		
		$model = new Vf_attachments_Model();
		$segment = $this->container->router->getSegment($this->settings['segment_ref']);
		
		$view = new Vf_View('attachments', 'plugin', 'attachments');
		$view->loadHelper('Uri');
		$view->importFunctions('common');
		$view->dir = $this->settings['attachmentsDir'];
		$view->attachments = $model->getAttachments($this->settings['component'], $segment);
		return $view->render();
	}

	
	public function deleteAttachments()
	{
		$model = new Vf_attachments_Model();
		$segment = $this->container->router->getSegment($this->settings['segment_ref']);
		
		if ((sizeof($_POST) > 0 && !$this->container->request->post('submit_no')) || $this->container->request->isAjax()) {
			$attachments = $model->getAttachments($this->settings['component'], $segment);
			if ($model->deleteAttachmentsFilesAndEntry($this->settings['component'], $segment)) {
				foreach ($attachments as $file) {
					@unlink($this->settings['attachmentsDir'] . $file);
				}
			}
		}
	}
	
	
	public function deleteAttachmentFile()
	{
		$translate = Vf_Language::instance();
		$translate->get()->load('plugins/attachments/delete.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$model = new Vf_attachments_Model();
		$ref_id = $this->container->router->getSegment($this->settings['segment_ref']);
		
		$view = new Vf_View('admin/attachmentsDelete', 'plugin', 'attachments');
		$view->loadHelper('Uri');
		$view->importFunctions('common');
		$view->dir = $this->settings['attachmentsDir'];
		$view->attachments = $model->getAttachments($this->settings['component'], $ref_id);
		$view->id = $ref_id;
		
		if ($this->container->request->isAjax()) {
			$attachments = $model->getAttachments($this->settings['component'], $ref_id);
			$filename = $this->container->request->post('filename');
			$dir = $this->container->request->post('path');

			foreach ($attachments as $key => $file) {
				if ($file == $filename) {
					unset($attachments[$key]);
				}
			}

			$remained = (sizeof($attachments) == 0) ? null : $attachments;
			if ($model->deleteFileFromEntry($this->settings['component'], $ref_id, $remained)) {
				if (@unlink($dir . $filename)) {
					$this->container->request->response 
						-> sendHttpHeaders(array(
							'Content-Type'  => 'text/octet-stream',
							'Cache-Control' => 'no-cache',
							'Content-Type'  => 'application/json'
						))
						-> setJson(array('msg' => Vf_Translate_Helper::__('Zalacznik zostal usuniety')))
						-> getJson();
						
					$this->container->request->response->flushContents();
				} else {
					$this->container->request->response 
						-> sendHttpHeaders(array(
							'Content-Type'  => 'text/octet-stream',
							'Cache-Control' => 'no-cache',
							'Content-Type'  => 'application/json'
						))
						-> setJson(array('msg' => Vf_Translate_Helper::__('Wystapil blad podczas usuwania zalacznika')))
						-> getJson();
						
					$this->container->request->response->flushContents();
				}
			} else {
				$this->container->request->response 
					-> sendHttpHeaders(array(
						'Content-Type'  => 'text/octet-stream',
						'Cache-Control' => 'no-cache',
						'Content-Type'  => 'application/json'
					))
					-> setJson(array('msg' => Vf_Translate_Helper::__('Blad podczas usuwania zalacznika z bazy')))
					-> getJson();
						
				$this->container->request->response->flushContents();
			}
		}
		return $view->render();
	}
	
	
	private function deleteEmptyFiles($files)
	{
		foreach($files as $key => $file) {
			if(empty($file)) {
				unset($files[$key]);
			}
		}
		return $files;
	}
}

?>