<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

require_once(DIR_LIBRARY.'Language.php');

if (!function_exists('__')) {
	function __($key)
	{
		return Vf_Language::instance()->get()->$key;
	}
}


if (!function_exists('transToJsArray')) {
	function transToJsArray()
	{
		return json_encode(Vf_Language::instance()->get()->getAllTranslations());
	}
}


if (!function_exists('t'))  {
	function t($key, $from, $to, $pluralize = false, $count = null)
	{
		return Vf_Language::instance()->get()->phrase($key, $from, $to, $pluralize, $count);
	}
}