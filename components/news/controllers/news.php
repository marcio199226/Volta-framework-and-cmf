<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Controller.php');

class Vf_news_Component extends Vf_Controller
{
	protected $uri = null;
	
	protected $request = null;
	
	public function __construct()
	{
		$this->uri = Vf_Core::getContainer()->router;
		$this->request = Vf_Core::getContainer()->request;
	}
	
	
	public function Index()
	{
		$translate = Vf_Language::instance();
		$translate->get()->load('components/news/list.php');
		Vf_Loader::loadHelper('Uri');
		$model = Vf_Orm::factory('news');
		
		$pager = new Vf_Pagination();
		$pager->setView('bootstrapSimple');
		$pager->setTotal($model->countNews($translate->get()->getLang()));
		$pager->setPerPage(5);
		$pager->setUriSegment($this->uri->getSegment());
		$pager->setBaseUrl(Vf_Uri_Helper::site(true, '', false));
		
		$newsModel = $model->getAllNews($translate->get()->getLang(), $pager->getOffset(), $pager->getPerPage());
		
		$view = new Vf_View('listAll', 'component', 'news');
		$view->loadHelper('User');
		$view->loadHelper('Box');
		$view->loadHelper('BBCode');
		$view->importFunctions('common');
		$view->news = $newsModel;
		$view->pager = $pager->display(true);
		$response = $view->render();
		Vf_Core::getContainer()->request->response->cache('public', 86400); //1 day
		Vf_Core::getContainer()->request->response->etag($response, true);
		Vf_Core::getContainer()->request->response->expires(time() + 86400);
		//Vf_Core::getContainer()->request->response->lastModified();
		//Vf_Core::getContainer()->request->response->setHttpStatus(200);
		return $response;
	}
	
	
	public function readNews()
	{	
		$translate = Vf_Language::instance();
		$translate->get()->load('components/news/list.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$newsModel = Vf_Orm::factory('news');
		$news = $newsModel->getNews($this->uri->getSegment(0), $translate->get()->getLang());
		
		if ($news !== null) {
			$view = new Vf_View('read', 'component', 'news');
			$view->loadHelper('User');
			$view->loadHelper('Box');
			$view->loadHelper('BBCode');
			$view->importFunctions('common');
			$view->news = $news;
			return $view->render();
		} else {	
			throw new Vf_Component_Exception(Vf_Translate_Helper::__('Nie ma news-a o taki id'));
		}
	}
	
	
	public function delete()
	{
		$csrf = new Vf_Security();
		
		$translate = Vf_Language::instance();
		$translate->get()->load('components/news/list.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$view = new Vf_View('delete', 'component', 'news');
		$view->loadHelper('Box');
		$view->message = Vf_Translate_Helper::__('Czy na pewno chcesz usunac wpis?');
		
		if ($this->request->post('submit_no')) {
			$this->redirect('./');
		} elseif ($this->request->post('submit_yes')) {
			if (Vf_Orm::factory('news')->removeNews($this->uri->getSegment(3)) && $csrf->csrf_check_token($this->uri->getSegment(4))) {
				$this->redirect('./');
			} else {
				$this->redirect('./');
			}
		}
		return $view->render();
	}
	
	
	public function rss()
	{
		$news = Vf_Orm::factory('news')->getAllNews(Vf_Language::instance()->get()->getLang(), 0, 30);
		Vf_Loader::loadHelper('Uri');
		
		$feed = new Vf_Feed();
		$feed->addItems($news);
		$feed->setTitle('My news feed');
		$feed->setLink(Vf_Uri_Helper::base(true));
		$feed->setDescription('Ostatnie wpisy na moim blogu');
		$feed->setItemTag('description', 'content');
		$feed->setItemTag('title', 'title');
		$feed->setItemTag('link', array(Vf_Uri_Helper::base(true), 'id'));
		$feed->setItemTag('pubDate', 'data');
		$feed->saveAsXml();
		$this->redirect('../' . $feed->getFeedFile());
	}
}

?>