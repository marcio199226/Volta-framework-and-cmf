<?php 

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

interface IValidation
{
	public function configure($config);
	public function get_option($key);
	public function get_options();
	public function is_valid($object);
}

?>