<?php 

/**
* Volta framework

* @author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
* @copyright Copyright (c) 2012, marcio
* @version 1.0
*/

class Vf_InfoTags_Events
{
	/**
	* Podmienia w gotowym kodzie html tagi informacyjne
	* @access public 
	* @param string $output glowny response naszej aplikacji przekazywany przez referencje wiec return nie jest potrzebny
	*/
	public function replace($output)
	{
		$benchmark = Vf_Benchmark::get('core');
		$csrf = Vf_Core::getContainer()->csrf;
		$token = $csrf->getToken();
		$output = str_replace(
			array('{@time@}', '{@sql@}', '{@memory@}', '{@csrf_token@}'), 
			array($benchmark['time'], $benchmark['sql'], $benchmark['memory'], $token),
			$output 
		);
	}
}

?>