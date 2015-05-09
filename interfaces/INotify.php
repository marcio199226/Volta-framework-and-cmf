<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

interface INotify
{
	public function setRecipients($recipients);
	public function addRecipient($recipient);
	public function getRecipients();
	public function setSender($sender);
	public function getSender();
	public function setSubject($subject);
	public function getSubject();
	public function setUrl($url);
	public function getUrl();
	public function setMessage($msg);
	public function getMessage();
	public function isEmail($email);
	public function notify();
}

?>