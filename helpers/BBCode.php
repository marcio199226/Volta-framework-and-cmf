<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_BBCode_Helper
{
	public static function parse($text)
	{
		$text = stripslashes($text);
		//$text = htmlspecialchars($text);
		$text = preg_replace("#\[b\](.*?)\[/b\]#si",'<b>\\1</b>', $text);
		$text = preg_replace("#\[i\](.*?)\[/i\]#si",'<i>\\1</i>', $text);
		$text = preg_replace("#\[u\](.*?)\[/u\]#si",'<u>\\1</u>', $text);
		$text = preg_replace("#\[s\](.*?)\[/s\]#si",'<s>\\1</s>', $text);
		$text = preg_replace("#\[url\](http.*?)\[/url\]#si", "<A HREF=\"\\1\">\\1</A>", $text);
		$text = preg_replace("#\[url=(http.*?)\](.*?)\[/url\]#si", "<A HREF=\"\\1\" TARGET=\"_blank\">\\2</A>", $text);
		$text = preg_replace("#\[url\](.*?)\[/url\]#si", "<A HREF=\"http://\\1\">\\1</A>", $text);
		$text = preg_replace("#\[url=(.*?)\](.*?)\[/url\]#si", "<A HREF=\"http://\\1\">\\2</A>", $text);
		$text = preg_replace_callback("#\[code\](.*?)\[/code\]#si", "Vf_BBCode_Helper::highlight_code", $text);
		$text = preg_replace_callback("#\[code=(.*?)\](.*?)\[/code\]#si", "Vf_BBCode_Helper::parseCode", $text);
		$text = preg_replace("#\[quote\](.*?)\[/quote\]#si",'<blockquote>\\1</blockquote>', $text);
		$text = str_replace("\n", "<br />", $text);
		return $text;
	}
	
	
	public static function cutText($text, $words = 15, $endText = '...')
	{
		$txt = explode(" ", $text);
		if (sizeof($txt) > $words) {
			array_splice($txt, $words, sizeof($txt));
			return implode(" ", array_merge($txt, (array)$endText));
		} else {
			return implode(" ", $txt);
		}
	}
	
	
	public static function parseCode($settings, $line = false)
	{
		$geshi = new GeSHi(htmlspecialchars_decode($settings[2]), $settings[1]);
		
		if($line) {
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
		}
		$geshi->set_header_type(GESHI_HEADER_NONE);
		$code = $geshi->parse_code();	
		$code = str_replace("<br />", "", $code);
		return $code;
	}
	
	
	public static function highlight_code($code)
	{
		$c = highlight_string(htmlspecialchars_decode($code[1]), true);
		return '<code style="white-space:nowrap;">'.$c.'</code>';
	}
}

?>