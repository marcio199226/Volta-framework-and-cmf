<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_INTERFACES . 'IWidget.php');
require_once(DIR_ABSTRACT . 'Widget.php');


class Vf_about_Widget extends Vf_Widget_Abstract implements IWidget
{	
	public function display()
	{
		$translate = Vf_Language::instance();
		$translate->get()->load('widgets/about.php');
		$locale = $translate->get()->getLang();
		
		Vf_Loader::loadHelper('Translate');
		
		$model = Vf_Orm::factory('about');
		
		$view = new Vf_View('about', 'widget');
		$view->loadHelper('Form');
		$view->loadHelper('User');
		$view->loadHelper('BBCode');
		$view->loadHelper('Uri');
		$view->importFunctions('common');
		$view->contents = $model->getAboutMeContents($locale);
		$view->locale = $locale;
		
		if ($this->container->request->isAjax() && $this->container->aclCore->is_allowed('general', 'edit')) {
			if ($this->container->request->post('aboutMeContents')) {
				if ($model->saveContents($this->container->request->post('aboutMeContents'), $this->container->request->post('locale'))) {

					$this->container->request->response 
						-> sendHttpHeaders(array(
							'Cache-Control' => 'no-cache',
							'Content-Type'  => 'application/json'
						)) 
						-> setJson(array('msg' => Vf_Translate_Helper::__('aboutMeWidgetSaved')))
						-> getJson();
						
					$this->container->request->response->flushContents();
				} else {
					$this->container->request->response 
						-> sendHttpHeaders(array(
							'Cache-Control' => 'no-cache',
							'Content-Type'  => 'application/json'
						)) 
						-> setJson(array('msg' => Vf_Translate_Helper::__('aboutMeWidgetNotSaved')))
						-> getJson();
						
					$this->container->request->response->flushContents();
				}
			}
		}
		return $view->render();
	}
}

?>