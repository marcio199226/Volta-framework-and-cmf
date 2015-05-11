<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Orm.php');

class Vf_news_Model extends Vf_Orm
{
	protected $table = 'news';
	
	protected $primaryKey = 'id';
	
	protected $struct = array('id', 'autor', 'data');
	
	protected $with = 'news_translations';
	
	protected $join = array(
		'news_translations' => array(
			'rows' => array('news_translations.title', 'news_translations.content'),
			'type' => 'INNER',
			'on' => array(
				'news_translations.id_news' => 'news.id'
			),
			'where' => array(
				'news_translations.language' => 'pl'
			)
		)
	);
	
	
	public function getAllNews($language, $offset, $perPage)
	{
		$news = $this -> db -> Select('news.*, news_translations.title, news_translations.content', 'news')
			-> Join('news_translations', array('news_translations.id_news' => 'news.id'), 'LEFT')
			-> Where(array('news_translations.language' => $language))
			-> OrderBy('id', 'DESC')
			-> Limit(array($offset, $perPage))
			-> Execute();
		
		return $this -> db -> FetchAllAssoc($news);
	}
	
	
	public function getNews($id, $language)
	{
		$news = $this -> db -> Select('news.*, news_translations.title, news_translations.content', 'news')
			-> Join('news_translations', array('news_translations.id_news' => 'news.id'), 'LEFT')
			-> Where(array('news.id' => $id, 'news_translations.language' => $language))
			-> Execute();
		
		return ($this -> db -> CountRows($news) > 0) ? $this -> db -> FetchAssoc($news): null;
	}
	
	
	public function getNewsByID($id)
	{
		$news = $this -> db -> Select('id', 'news_translations')
			-> Where(array('id_news' => $id))
			-> Execute();
		
		return ($this -> db -> CountRows($news) > 0) ? $this -> db -> FetchAssoc($news): null;
	}
	
	
	public function addNews($data)
	{
		return $this -> db -> Insert('news', $data, true);
	}
	
	
	public function addNewsTranslations($data)
	{
			$this -> db -> Insert('news_translations', $data, true);
	}
	
	
	public function removeNews($id)
	{
		$language = Vf_Language::instance() -> get() -> getLang();
		$this -> db -> Delete('news_translations', array('id_news' => $id, 'language' => $language));
		//if all translations was removed so remove entry from news table too
		if($this -> getNewsByID($id) === null)
		{
			$this -> db -> Delete('news', array('id' => $id));
		}
	}
	
	
	public function editNews($data, $id)
	{
		return $this -> db -> Update('news', $data, array('id' => $id));
	}
	
	
	public function editTranslationsNews($data, $id, $lang)
	{
		return $this -> db -> Update('news_translations', $data, array('id_news' => $id, 'language' => $lang));
	}
	
	
	public function countNews($language)
	{
		$data = $this -> db -> SetQuery("select COUNT(*) from news_translations where news_translations.language = '{$language}'");
		$fetch = $this -> db -> FetchRow($data);
		return $fetch[0];
	}
	
	public function getLastInsertId()
	{
		$q = $this -> db -> SetQuery('SELECT LAST_INSERT_ID()');
		$id = $this -> db -> FetchRow($q);
		return $id[0];
	}
}

?>