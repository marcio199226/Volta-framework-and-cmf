<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Box_Helper
{
	/**
	* Box z informacja gdy jakas operacja powiodla sie
	* @static
	* @access public 
	* @param string $msg tresc informacji
	* @return string tresc informacji w html
	*/
	public static function success($msg)
	{
		return '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;-moz-border-radius: 15px;border-radius: 15px;">'.$msg.'</div>';
	}
	
	
	/**
	* Box z informacja gdy jakas operacja nie powiodla sie
	* @static
	* @access public 
	* @param string $msg tresc informacji
	* @return string tresc informacji w html
	*/
	public static function error($msg)
	{
		return '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #ff0033;-moz-border-radius: 15px;border-radius: 15px;">'.$msg.'</div>';
	}
	
	
	/**
	* Box z ostrzezeniem
	* @static
	* @access public 
	* @param string $msg tresc informacji
	* @return string tresc informacji w html
	*/
	public static function info($msg)
	{
		return '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #FFFF00;-moz-border-radius: 15px;border-radius: 15px;">'.$msg.'</div>';
	}
	
	
	/**
	* Box z przyciskami tak/nie
	* @static
	* @access public 
	* @param string $msg tresc informacji
	* @param string $redirect strona na ktora przekierowac
	* @return string
	*/
	public static function alert($msg, $redirect = '')
	{
		$translate = Vf_Language::instance();
		$translate->get()->load('alert.php');
		
		$box = new Vf_View('alert');
		$box->loadHelper('Form');
		$box->importFunctions('common');
		$box->msg = $msg;
		$box->redirect = $redirect;
		return $box->render();
	}
}
?>