<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/

class Vf_Box_Helper
{
	public static function success($msg)
	{
		return '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #33ff33;-moz-border-radius: 15px;border-radius: 15px;">'.$msg.'</div>';
	}
	
	
	public static function error($msg)
	{
		return '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #ff0033;-moz-border-radius: 15px;border-radius: 15px;">'.$msg.'</div>';
	}
	
	
	public static function info($msg)
	{
		return '<div align="center" style="padding:5px;margin:5px;background-color:#C0C0C0;border:1px solid #FFFF00;-moz-border-radius: 15px;border-radius: 15px;">'.$msg.'</div>';
	}
	
	
	public static function alert($msg, $redirect = '')
	{
		$translate = Vf_Language::instance();
		$translate -> get() -> load('alert.php');
		
		$box = new Vf_View('alert');
		$box -> loadHelper('Form');
		$box -> importFunctions('common');
		$box -> msg = $msg;
		$box -> redirect = $redirect;
		return $box -> render();
	}
}
?>