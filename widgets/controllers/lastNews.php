<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_INTERFACES . 'IWidget.php');

class Vf_lastNews_Widget implements IWidget
{	
	public function display()
	{
		$model = Vf_Orm::factory('lastNews');
		$config = new Vf_Config(DIR_WIDGETS_CFG.'lastNews.xml', 'Xml');
		
		$translate = Vf_Language::instance();
		$translate->get()->load('widgets/lastNews.php');
		
		$view = new Vf_View('lastNews', 'widget');
		$view->importFunctions('common');
		$view->loadHelper('Uri');
		$view->news = $model->getLatestNews($config->number_of_news, $translate->get()->getLang());
		
		return $view->render();
	}
}

?>