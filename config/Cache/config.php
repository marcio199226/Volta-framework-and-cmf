<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

$configs['driver'] = 'Apc'; //File,Apc,Xcache,Eaccelerator
$configs['cache_file_directory'] = 'cache/';
$configs['cache_file_lifetime'] = 3600;//time in sec

return $configs;

?>