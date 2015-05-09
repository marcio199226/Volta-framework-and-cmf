<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Plugin.php');

class Vf_disquis_Plugin extends Vf_Plugin
{
	public function loadDisquis()
	{
		$uri = $this -> container -> router;
		$view = new Vf_View('disquisComments', 'plugin', 'disquis');
		$view -> loadHelper('Uri');
		//set disquis configuration
		$view -> disquisShortName = $this -> settings['short_name'];
		$view -> disquisIdentifier = $this -> settings['short_name'].'_'.$uri -> getSegment($this -> settings['segment_for_id']);
		$view -> disquisUrlSite = $this -> settings['absolute_url'];
		
		return $view -> render();
	}
}

?>