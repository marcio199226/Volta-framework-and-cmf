<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Model.php');

class Vf_about_Model extends Vf_Model
{
	public function getAboutMeContents($locale)
	{
		$q = $this -> db -> SetQuery('Select contents from about_me where locale='."'{$locale}'");
		$contents = $this -> db -> FetchRow($q);
		return $contents[0];
	}
	
	
	public function saveContents($contents, $locale)
	{
		$exists = $this -> getAboutMeContents($locale);
		
		if(!$exists)
		{
			$data = array(
				'id' => null,
				'contents' => $contents,
				'locale' => $locale
			);
			return $this -> db -> Insert('about_me', $data, true);
		}
		return $this -> db -> Update('about_me', array('contents' => $contents), array('locale' => $locale));
	}
}

?>