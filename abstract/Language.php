<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

abstract class Vf_Language_Abstract
{
	protected $data = array();
	
	protected $lang = null;
	
	protected $request = null;
	
	protected $config = null;
	
	
	public function __construct()
	{	
		$this->request = new Vf_Request();
		$this->config = new Vf_Config('config.Language');
		$this->lang = $this->config->default_lang;
	}
	
	
	public function __get($key)
	{
		return (isset($this->data[$this->getLang()][$key])) ? $this->data[$this->getLang()][$key] : null;
	}
	
	
	public function getAllTranslations()
	{
		return $this->data;
	}
	
	
	public function setLang($lang)
	{
		$this->lang = $lang;
		
		if ($this->request->session($this->config->session_lang)) {
			$this->request->response->setSession($this->config->session_lang, $lang, true);
		} else {
			$this->request->response->setSession($this->config->session_lang, $lang);
		}
	}
	
	
	public function getLang()
	{
		if ($this->request->session($this->config->session_lang)) {
			return $this->request->session($this->config->session_lang);
		}
		return $this->lang;
	}
	
	
	public function translate($key)
	{
		return (isset($this->data[$this->getLang()][$key])) ? $this->data[$this->getLang()][$key] : null;
	}

	
	public function phrase($key, $from, $to, $pluralize = false, $count = null)
	{
		$ruleCode = $this->getPluralizationRulesCode($count);

		if (!$pluralize) {
			$phrase = str_replace($from, $to, $this->data[$this->getLang()][$key]);
		} else {
			if (isset($this->data[$this->getLang()][$key]['pluralize'])) {
				preg_match_all('#\%(.*?)\%#', $this->data[$this->getLang()][$key]['text'], $vars);
				
				foreach ($vars[0] as $var) {
					if(!in_array($var, $from)) {
						$replaceVars[] = $var;
						$replaceVarsValue[] = $this->data[$this->getLang()][$key]['pluralize'][$var][$ruleCode];
					}
				}
				
				$fromPluralize = array_merge($from, $replaceVars);
				$toPluralize = array_merge($to, $replaceVarsValue);
				$phrase = str_replace($fromPluralize, $toPluralize, $this->data[$this->getLang()][$key]['text']);
			}
		}
		return $phrase;
	}
	
	
	protected function getPluralizationRulesCode($number)
	{
		switch ($this->getLang()){
			case 'pl':
				return ($number == 1) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2);
				break;	
				
			case 'de':
			case 'es':
			case 'it':
			case 'en':
				return ($number == 1) ? 0 : 1;
				break;
					
			case 'fr':
				return (($number == 0) || ($number == 1)) ? 0 : 1;
				break;
					
			case 'ro':
				return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2);
				break;
					
			case 'be':
			case 'ru':
	        case 'uk':
				return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);
				break;
					
			case 'cs':
            case 'sk':
	            return ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2);
				break;
					
			default:
				return 0;
				break;
		}
	}
	
	
	abstract public function load($file);
}

?>