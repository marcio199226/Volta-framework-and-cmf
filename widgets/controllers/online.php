<?php

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

require_once(DIR_INTERFACES . 'IWidget.php');

class Vf_online_Widget implements IWidget
{	
	protected $seconds = 180;
	
	public function display()
	{
		$model = Vf_Orm::factory('online');
		
		$model->setOnlineUsers();
		$model->deleteInactiveUsers($this->seconds);
		
		$translate = Vf_Language::instance();
		$translate->get()->load('widgets/online.php');
		
		$view = new Vf_View('online', 'widget');
		$view->importFunctions('common');
		$data = $this->_whoIsOnline($model->getOnlineUsers());
		$view->users = $data['users'];
		$view->online = $data['online'];
		return $view->render();
	}
	
	
	private function _whoIsOnline($users)
	{
		$online = 0;
		$onlineUsers = array();
		if (sizeof($users) > 0) {
			foreach ($users as $user) {
				if ($user['username'] != '') {
					$onlineUsers[] = $user['username'];
				}
				$online++;
			}
		}
		$names = implode(',', $onlineUsers);
		return array('online' => $online, 'users' => $names);
	}
}

?>