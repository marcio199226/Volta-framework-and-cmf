<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

ob_start();
session_start();

require_once('libraries/Core.php');

$core = new Vf_Core();
$core -> dispatch();

?>