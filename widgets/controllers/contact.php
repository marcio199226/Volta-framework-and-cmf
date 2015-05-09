<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES.'IWidget.php');
require_once(DIR_ABSTRACT.'Widget.php');

class Vf_contact_Widget extends Vf_Widget_Abstract implements IWidget
{	
	private $contactsAsLink = array(
		'facebook',
		'www',
		'github',
		'twitter'
	);

	public function display()
	{
		$model = new Vf_contact_Model();
		$contactData = $model -> getContactsData();
		
		$view = new Vf_View('contact', 'widget');
		$view -> loadHelper('User');
		$view -> loadHelper('Form');
		$view -> loadHelper('Uri');
		$view -> contacts = $contactData;
		$view -> contactsAsLink = $this -> contactsAsLink;
		
		if($this -> container -> request -> isAjax() && $this -> container -> aclCore -> is_allowed('general', 'edit'))
		{
			if($this -> container -> request -> method() == 'POST')
			{
				if($model -> saveContacts($this -> container -> request -> post()))
				{
					$this -> container -> request -> response 
						-> sendHttpHeaders(array(
							'Content-Type'  => 'text/octet-stream',
							'Cache-Control' => 'no-cache',
							'Content-Type'  => 'application/json'
						))
						-> setJson(array('msg' => 'Zapisano poprawnie'))
						-> getJson();
						
					$this -> container -> request -> response -> flushContents();
				}
				else
				{
					$this -> container -> request -> response 
						-> setJson(array('msg' => 'Blad podczas zapisywania'))
						-> getJson();
						
					$this -> container -> request -> response -> flushContents();
				}
			}
		}
		
		return $view -> render();
	}
}

?>