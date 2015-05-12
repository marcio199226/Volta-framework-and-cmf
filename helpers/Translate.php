<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Translate_Helper
{
	/**
	* Tlumaczy podany tekst
	* @static
	* @access public 
	* @param string $klucz z pliku pod ktorym jest nasza wiadomosc do przetlumaczenia
	* @return string
	*/
	public static function __($key)
	{
		return Vf_Language::instance()->get()->$key;
	}
	
	
	/**
	* Tlumaczy tekst uwzgledniajac liczbe mnoga
	* @static
	* @access public 
	* @param string $klucz z pliku pod ktorym jest nasza wiadomosc do przetlumaczenia
	* @param array $from zmienne to zamiany %nazwaZmiennej% bez %%
	* @param array $to tekst na ktory zamienic zmienna
	* @param boolean $plularize czy uwzglednic liczbe mnoga
	* @param int $count ilosc przymotnika zeby obliczyc jako zwrot uzyc dla danej liczby
	* @return string
	*/
	public static function t($key, $from, $to, $pluralize = false, $count = null)
	{
		return Vf_Language::instance()->get()->phrase($key, $from, $to, $pluralize, $count);
	}
}
?>