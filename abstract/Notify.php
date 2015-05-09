<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_INTERFACES.'INotify.php');

abstract class Vf_Notify_Abstract implements INotify
{
	protected $recipients = array();
	
	protected $sender;
	
	protected $subject;
	
	protected $url;
	
	protected $msg;
	
	protected $isEmail;
	
	
	public function setRecipients($recipients)
	{
		if(!is_array($recipients))
		{
			$this -> recipients = array($recipients);
		}
		else
		{
			$this -> recipients = $recipients;
		}
	}
	
	
	public function addRecipient($recipient)
	{
		$this -> recipients[] = $recipient;
	}
	
	
	public function setSender($sender)
	{
		$this -> sender = $sender;
	}
	
	
	public function setSubject($subject)
	{
		$this -> subject = $subject;
	}
	
	
	public function setUrl($url)
	{
		$this -> url = $url;
	}
	
	
	public function setMessage($msg)
	{
		$this -> msg = $msg;
	}
	
	
	public function getRecipients()
	{
		return $this -> recipients;
	}
	
	
	public function getSender()
	{
		return $this -> sender;
	}
	
	
	public function getSubject()
	{
		return $this -> subject;
	}
	
	
	public function getUrl()
	{
		return $this -> url;
	}
	
	
	public function getMessage()
	{
		return $this -> msg;
	}
	
	
	public function isEmail($email)
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	
	public function notify()
	{
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$this -> getSender() . "\r\n";
		
		if(sizeof($this -> getRecipients()) > 0)
		{
			foreach($this -> getRecipients() as $key => $email)
			{
				if($this -> isEmail($email))
				{
					mail($email, $this -> getSubject(), $this -> getMessage(), $headers);
				}
			}
		}
	}
}

?>