<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

//definiujemy nasze hooki
$configs['events'] = array(
	'system.redirect' => array(
		array('Vf_FlashMessages_Events', 'register')
	),
	'system.pre.action' => array(
		array('Vf_FlashMessages_Events', 'loadFromPreviousRequest')
	),
	'system.post.action' => array(
		array('Vf_FlashMessages_Events', 'register')
	),
	'system.display' => array(
		array(new Vf_InfoTags_Events(), 'replace'),
		array(new Vf_Assets(), 'replaceAssets'),
		array('Vf_CompressApp_Events', 'compress')//if is class name is string so inject container into class contructor
	)
);


//ustawiamy kontener dla aplikacji globalny jesli tworzymy kontener jako singleton
$configs['container'] = array(
	'proprieties' => array(
		'compressionLevel' => function($container) {
			return $container -> config -> compression_level;
		}
	),
	'objects' => array(
		array(
			'propertyName' => 'router',
			'class' => 'Vf_Router',
			'is_static' => true,
			'method' => 'instance'
		)
	),
	'shared' => array(
		array(
			'propertyName' => 'request',
			'class' => 'Vf_Request'
		),
		array(
			'propertyName' => 'config',
			'class' => 'Vf_Config',
			'args' => array(DIR_CONFIG, 'Xml')
		),
		array(
			'propertyName' => 'configCore',
			'class' => 'Vf_Config',
			'args' => array('config.Core')
		),
		array(
			'propertyName' => 'user',
			'class' => 'Vf_User'
		),
		array(
			'propertyName' => 'acl',
			'class' => 'Vf_Acl'
		),
		array(
			'propertyName' => 'aclCore',
			'closure' => function($container) {
				$request = $container -> request;
				$user = $container -> user;
				$acl = $container -> acl;
				$user -> get($request -> session('user'));
				$acl -> set_user_role($user -> role, $user -> id);
				$acl -> load_rules();
				return $acl;
			}
		)
	)
);


$configs['di'] = array(
	'class' => array(
	),
	'classSingleton' => array(
	),
	'value' => array(
	)
);

return $configs;

?>