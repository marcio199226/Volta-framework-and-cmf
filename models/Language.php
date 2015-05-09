<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Model.php');

class Vf_Language_Model extends Vf_Model
{

	public function getLocales()
	{
		$locales = $this -> db -> Select('*', 'locales') -> Execute();
		if($fetch = $this -> db -> FetchAllAssoc($locales))
		{
			return $fetch;
		}
		return null;
	}
	
	
	public function getLocalesAsLanguages()
	{
		$langs = array();
		$locales = $this -> db -> Select('*', 'locales') -> Execute();
		
		if($fetch = $this -> db -> FetchAllAssoc($locales))
		{
			foreach($fetch as $locale)
			{
				$langs[$locale['locale']] = $locale['language'];
			}
			return $langs;
		}
		return null;
	}

	
	public function getLanguagesAsLocales()
	{
		$locales = array();
		$languages = $this -> db -> Select('*', 'locales') -> Execute();
		
		if($fetch = $this -> db -> FetchAllAssoc($languages))
		{
			foreach($fetch as $locale)
			{
				$locales[$locale['language']] = $locale['locale'];
			}
			return $locales;
		}
		return null;
	}

}

?>