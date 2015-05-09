<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Translate_Helper
{
	public static function __($key)
	{
		return Vf_Language::instance() -> get() -> $key;
	}
	
	
	public static function t($key, $from, $to, $pluralize = false, $count = null)
	{
		return Vf_Language::instance() -> get() -> phrase($key, $from, $to, $pluralize, $count);
	}
}
?>