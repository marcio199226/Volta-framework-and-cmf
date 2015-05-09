<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

$configs['delimiter'] = ',';
$configs['self'] = '/';
$configs['includes'] = array('news');

$configs['static_routes'] = array(
	'Contact' => 'Home,Contact',
	'About' => 'Home,About'
);
								
$configs['dynamic_routes'] = array(
	'restful' => array(
		'pattern' => '/^rest,(.*),(.*)([\,a-z0-9]+)$/D',
		'frontcontroller' => 'Rest',
		'frontaction' => 'Index'
	)
);

$configs['rest'] = array(
);
								
return $configs;
?>