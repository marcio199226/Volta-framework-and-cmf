<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


$routes['static'] = array(
	'rss' => 'Home,Index,rss'
);
								
$routes['dynamic'] = array(		  
	'news.pager' => array( 
		'pattern' => '/^page\,[0-9]{1,4}$/D',
		'frontcontroller' => 'Home',
		'frontaction' => 'Index',
		'cmpaction' => 'Index'
	),
											 
	'news.read' => array(
		'pattern' => '/^[0-9]{1,4}$/D',
		'frontcontroller' => 'Home',
		'frontaction' => 'Index',
		'cmpaction' => 'readNews',
		'uri' => array(':id:'),
		'params' => array(':id:' => 0)
	)
);

$routes['rest'] = array(
	'news' => array(
		'getLastNews' => array(
			'method' => 'get',
			'apiKey' => true,
			'status' => 200
		),
		'test' => array(
			'method' => 'post',
			'apiKey' => true,
			'format' => 'uri',
			'status' => 200
		),
		'test2' => array(
			'method' => 'get',
			'format' => 'uri',
			'status' => 200
		),
		'test3' => array(
			'method' => 'any',
			'format' => 'uri',
			'status' => 200,
			'apiKey' => true, //401 Unauthorized
			'checkIp' => true,
			'resource' => 'news_Admin', //news for normal controller role and with _Admin suffix for roles from admin controllers
			'roles' => array(
				'addNews' // 403 Forbidden
			)
		)
	)
);			
			
return $routes;
?>