<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_LIBRARY . 'Model.php');

class Vf_lastNews_Model extends Vf_Model
{
	public function getLatestNews($amount, $language)
	{
		$news = $this->db->Select('news_translations.id_news, news_translations.title', 'news')
			->Join('news_translations', array('news_translations.id_news' => 'news.id'), 'LEFT')
			->Where(array('news_translations.language' => $language))
			->OrderBy('id_news', 'DESC')
			->Limit($amount)
			->Execute();					 
		return $this->db->FetchAllAssoc($news);
	}
}

?>