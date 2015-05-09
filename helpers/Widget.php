<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_Widget_Helper
{
	public static function load($widget)
	{
		if(Vf_Loader::existsFile(DIR_WIDGETS_CTRL.$widget.'.php'))
		{
			require_once(DIR_WIDGETS_CTRL.$widget.'.php');
			
			$classWidget = 'Vf_'.$widget.'_Widget';
			
			if(class_exists($classWidget))
			{
				$object = new $classWidget();
				
				if($object instanceof IWidget)
				{
					return $object -> display();
				}
				return false;
			}
			return false;
		}
		return false;
	}
}

?>