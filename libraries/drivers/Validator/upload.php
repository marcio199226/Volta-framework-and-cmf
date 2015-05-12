<?php 

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

require_once(DIR_ABSTRACT . 'Validation.php');
require_once(DIR_INTERFACES . 'IValidation.php');

class upload extends Vf_Validation implements IValidation
{	
	/**
	* Konstruktor ustawia konfiguracje walidatora
	* @access public 
	* @param array $cfg
	*/
	public function __construct($cfg)
	{
		parent::__construct();
		$this->configure($cfg);
	}
	
	
	/**
	* Metoda ktora sprawdza walidacje danych na podstawie wczesniej ustawionej konfiguracji
	* @access public 
	* @param string $object tresc do walidacji
	* @return bool|string
	*/
	public function is_valid($object)
	{	
		if ($this->get_option('extensions')) {
			$valid_extensions = $this->get_option('extensions');
			$message = $this->language->get()->FileExtension;
			
			if(!is_array($object['name'])) {
				$file_extension = explode('.', $object['name']);
				$file_extension = $file_extension[count($file_extension)-1];
			
				if(!in_array($file_extension, $valid_extensions))
					return $this->set_error($message, $this->get_option('field'));
			}
			else
			{
				foreach($object['name'] as $name)
				{
					$file_extension = explode('.', $name);
					$file_extension = $file_extension[count($file_extension)-1];
			
					if(!in_array($file_extension, $valid_extensions))
						return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if($this->get_option('invalid_extensions'))
		{
			$invalid_extensions = $this->get_option('invalid_extensions');
			$message = $this->language->get()->FileExtension;
			
			if(!is_array($object['name']))
			{
				$file_extension = explode('.', $object['name']);
				$file_extension = $file_extension[count($file_extension)-1];
			
				if(in_array($file_extension, $invalid_extensions))
					return $this->set_error($message, $this->get_option('field'));
			}
			else
			{
				foreach($object['name'] as $name)
				{
					$file_extension = explode('.', $name);
					$file_extension = $file_extension[count($file_extension)-1];
			
					if(in_array($file_extension, $invalid_extensions))
						return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
		
		if($this->get_option('size'))
		{
			$message = $this->language->get()->SizeOfFile;
			
			if(is_array($object['size']))
			{	
				foreach($object['size'] as $size)
				{
					if($size > $this->get_option('size'))
						return $this->set_error($message);
				}
			}
			else
			{
				if($object['size'] > $this->get_option('size'))
					return $this->set_error($message);
			}
		}
		
		if($this->get_option('mimes'))
		{
			$valid_mimes = $this->get_option('mimes');
			$message = $this->language->get()->FileMime;
			
			if(!is_array($object['tmp_name']))
			{
				$mime = ($this->getMimeType($object['tmp_name']) !== false) ? $this->getMimeType($object['tmp_name']) : $object['type'];
				
				if(!in_array($mime, $valid_mimes))
					return $this->set_error($message, $this->get_option('field'));
			}
			else
			{
				foreach($objects['tmp_name'] as $key => $tmp)
				{
					$mime = ($this->getMimeType($tmp) !== false) ? $this->getMimeType($tmp) : $object['type'][$key];
				
					if(!in_array($mime, $valid_mimes))
						return $this->set_error($message, $this->get_option('field'));
				}
			}
		}
			
		if($this->get_option('isImage'))
		{
			$settings = $this->get_option('isImage');
			
			if(!is_array($settings))
			{
				$img = getimagesize($object['tmp_name']);
				
				if(!$img)
				{
					$message = $this->language->get()->FileIsNotImage;
					return $this->set_error($message);
				}
			}
			else
			{
				$img = getimagesize($object['tmp_name']);
				
				if($img[0] > $settings['width'] && $img[1] > $settings['height'])
				{
					$message = $this->error[$this->language->get()->getLang()]['invalidSize'];
					return $this->set_error($message);
				}
			}
		}
		
		return true;
	}
	
	
	private function getMimeType($file)
	{
		if(function_exists('finfo_open'))
		{
			$mime = new finfo(FILEINFO_MIME);
			return $mime->file($file);
		}
		else if(function_exists('mime_content_type'))
		{
			return mime_content_type($file);
		}
		return false;
	}
}

?>