<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Controller.php');

class Vf_news_Admin_Component extends Vf_Controller
{

	public function Index()
	{
		$news = Vf_Orm::factory('news') -> getAllNews(Vf_language::instance() -> get() -> getLang(), 0, 20);
		$view = new Vf_View('admin/newsTable', 'component', 'news');
		$view -> loadHelper('Box');
		$view -> news = $news;
		return $view -> render();
	}
	
	
	public function addNews()
	{
		$request = Vf_Core::getContainer() -> request;
		$translate = Vf_Language::instance();
		$translate -> get() -> load('components/news/add.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$newsModel = Vf_Orm::factory('news');
		$languagesModel = new Vf_Language_Model();
		$locales = $languagesModel -> getLocalesAsLanguages();
		
		$view = new Vf_View('admin/add', 'component', 'news');
		$view -> loadHelper('Form');
		$view -> loadHelper('Box');
		$view -> importFunctions('common');
		$view -> addFlash();
		$view -> locales = $locales;
		$view -> currentLocale = $translate -> get() -> getLang();
		
		if($request -> post('submit_add_news') || $request -> isAjax())
		{
			$validation = new Vf_Validator();
			$validation -> load('str');
			$validation -> add_data($_POST);
			$validation -> add_rule('title', new str(array('field' => Vf_Translate_Helper::__('newsFormAddNewEntryTitle'), 'required' => true, 'alphadigit' => true, 'between' => array(5, 40))));
			$validation -> add_rule('content', new str(array('field' => Vf_Translate_Helper::__('newsFormAddNewEntryContent'), 'required' => true)));
			$validation -> validation();
			
			$news = array(
				'id' => null,
				'autor' => $request -> session('user'),
				'data' => date('d/m/Y')
			);
			
			if(sizeof($validation -> get_errors()) == 0)
			{
				if($newsModel -> addNews($news))
				{
					$title = $request -> post('title');
					$content = $request -> post('content');
					$lastID = $newsModel -> getLastInsertId();
					
					foreach($locales as $locale => $language)
					{
						if(array_key_exists($locale, $title))
						{
							$newsTranslations = array(
								'id' => null,
								'id_news' => $lastID,
								'title' => $title[$locale],
								'content' => $content[$locale],
								'language' => $locale
							);
							$newsModel -> addNewsTranslations($newsTranslations);
						}
					}
					
					if(!$request -> isAjax())
					{
						$this -> redirect('./');
					}
				}
				else
				{
					$view -> error_add_news = Vf_Translate_Helper::__('Blad podczas dodawania wpisu.');
				}
			}
			else
			{
				//these flash errors are for plugins that elaborate request from ajax
				$request -> response -> flash -> add('validationErrors', $validation -> get_errors(), Vf_Flash::ERROR);
				$view -> errors = $validation -> get_errors();
			}
		}
		
		return $view -> render();
	}
	
	
	public function editNews()
	{
		$request = Vf_Core::getContainer() -> request;
		$uri =  Vf_Core::getContainer() -> router;
		
		$translate = Vf_Language::instance();
		$translate -> get() -> load('components/news/edit.php');
		
		Vf_Loader::loadHelper('Translate');
		
		$model = Vf_Orm::factory('news');
		$newsEdit = $model -> getNews($uri -> getSegment(3), $translate -> get() -> getLang());
		$front = $uri -> getFrontController();
		$module = $uri -> getFrontControllerAction();
		
		if($newsEdit !== null)
		{
			$viewEdit = new Vf_View('admin/edit', 'component', 'news');
			$viewEdit -> loadHelper('Form');
			$viewEdit -> loadHelper('Box');
			$viewEdit -> importFunctions('common');
			$viewEdit -> news = $newsEdit;
			
			if($request -> post('submit_edit_news'))
			{
				$validation = new Vf_Validator();
				$validation -> load('str');
				$validation -> add_data($_POST);
				$validation -> add_rule('title', new str(array('field' => 'tytul', 'required' => true, 'alphadigit' => true, 'between' => array(5, 40))));
				$validation -> add_rule('content', new str(array('field' => 'tresc', 'required' => true)));
				$validation -> validation();

				if(sizeof($validation -> get_errors()) == 0)
				{
					$newsTranslations = array(
						'title' => $request -> post('title'),
						'content' => $request -> post('content'),
					);
				
					if($model -> editTranslationsNews($newsTranslations, $uri -> getSegment(3), $translate -> get() -> getLang()))
					{
						$viewEdit -> msg_edit_news = Vf_Translate_Helper::__('Edytowano wpis');
					}
					else
					{
						$viewEdit -> error_edit_news = Vf_Translate_Helper::__('Blad podczas edycji wpisu');
					}
				}
				else
				{
					$viewEdit -> errors = $validation -> get_errors();
				}
			}
			return $viewEdit -> render();
		}
		else
		{
			throw new Vf_Component_Exception(Vf_Translate_Helper::__('Nie ma news-a o taki id'));
		}
	}
}

?>