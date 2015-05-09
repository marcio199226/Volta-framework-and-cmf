<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

class Vf_Form_Helper {

	/**
	*Skladowa klasy ktora przechowywuje kod html formularza
	*@static
	*@access protected
	*@var string $form
	*/
	protected static $form = '';
	
	/**
	*Skladowa klasy ktora przechowywuje formularz jako osobne elementy tablicy
	*@static
	*@access protected
	*@var array $array_form
	*/
	protected static $array_form = array();

		
	/**
	*Tworzy formularz
	*@static
	*@access public 
	*@param string $action odslylanie do akcji formylarza
	*@param string $method metoda wysylania formularza get/post
	*@param bool $enctype gdy true formularz dla plikow
	*/
	public static function open($action = '', $method = 'post', $self = false, $enctype = null, $attr = array(), $form_open_key_array = 'form_open') 
	{	
		$attributes = '';
	
		if($self)
			$act_self = $_SERVER['PHP_SELF'];
		else
			$act_self = '';
			
		if(sizeof($attr) > 0)
		{
			foreach($attr as $key => $value)
			{
				$attributes .= $key.'="'.$value.'" ';
			}
		}
		
		if($enctype !== null)
		{
			self::$array_form[$form_open_key_array] .= htmlspecialchars('<form method="'.$method.'" action="'.$act_self.$action.'" '.$attributes.' enctype="multipart/form-data">');
		}
			
		else
		{
			self::$array_form[$form_open_key_array] .= htmlspecialchars('<form method="'.$method.'" action="'.$act_self.$action.'" '.$attributes.'>');
		}
	}
	
		
	/**
	*Tworzy html dla input text
	*@static
	*@access public 
	*@param string $name atrybut name input text'a
	*@param string $value atrybut value input text'a
	*@param string $css css dla input text'a
	*/
	public static function text($name, $value = null, $css = null) 
	{	
		if($css === null)
		{
			$def_css = '';
		}
		else if(empty($css))
		{
			$def_css = 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;';
		}	
		else
		{		
			$def_css = $css;
		}
		
		if(is_string($name))
		{
			self::$array_form[$name] = htmlspecialchars('<input type="text" name="'.$name.'" value="'.$value.'" style="'.$def_css.'">');
		}
		
		else
		{
			self::$array_form[$name['name']] .= htmlspecialchars('<input type="text" ');
		
			if(!in_array('style', $name) && $css !== null)
				self::$array_form[$name['name']] .= 'style="'.$def_css.'"';
		
			foreach($name as $tag => $value)
			{		
				self::$array_form[$name['name']] .= htmlspecialchars($tag . '="' .$value. '" ');
			}
			
			self::$array_form[$name['name']] .= '>';
		}
	}
	
	/**
	*Tworzy html dla input password
	*@static
	*@access public 
	*@param string $name atrybut name input pwd
	*@param string $value atrybut value input pwd
	*@param string $css css dla input pwd
	*/
	public static function password($name, $value = null, $css = null) 
	{		
		if($css === null)
		{
			$def_css = '';
		}
		else if(empty($css))
		{
			$def_css = 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;';
		}	
		else
		{		
			$def_css = $css;
		}
			
		if(is_string($name))
		{
			self::$array_form[$name] = htmlspecialchars('<input type="password" name="'.$name.'" value="'.$value.'" style="'.$def_css.'">');
		}
		
		else
		{
			self::$array_form[$name['name']] .= htmlspecialchars('<input type="password" ');
		
			if(!in_array('style', $name) && $css !== null)
				self::$array_form[$name['name']] .= 'style="'.$def_css.'"';
		
			foreach($name as $tag => $value)
			{		
				self::$array_form[$name['name']] .= htmlspecialchars($tag . '="' .$value. '" ');
			}
			
			self::$array_form[$name['name']] .= '>';
		}
	}
	
	/**
	*Tworzy html dla button'a
	*@static
	*@access public 
	*@param string $name atrybut name dla button'a
	*@param string $value atrybut value dla button'a
	*@param string $css css dla button'a
	*/
	public static function button($name, $value = null, $css = null) 
	{	
		if($css === null)
		{
			$def_css = '';
		}
		else if(empty($css))
		{
			$def_css = 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;';
		}	
		else
		{		
			$def_css = $css;
		}
			
		if(is_string($name))
		{
			self::$array_form[$name] = htmlspecialchars('<input type="button" name="'.$name.'" value="'.$value.'" style="'.$def_css.'">');
		}
		
		else
		{
			self::$array_form[$name['name']] .= htmlspecialchars('<input type="button" ');
		
			foreach($name as $tag => $value)
			{		
				self::$array_form[$name['name']] .= htmlspecialchars($tag . '="' .$value. '" ');
			}
	
			self::$array_form[$name['name']] .= '>';
		}
	}
	
	/**
	*Tworzy html dla textarea
	*@static
	*@access public 
	*@param string $name atrybut name dla textarea
	*@param string $value atrybut value dla textarea
	*@param string $css css dla textarea
	*/
	public static function textarea($name, $value = null, $css = null) 
	{
		if($css === null)
		{
			$def_css = '';
		}
		else if(empty($css))
		{
			$def_css = 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;';
		}	
		else
		{		
			$def_css = $css;
		}
			
		if(is_string($name))
		{
			self::$array_form[$name] = htmlspecialchars('<textarea name="'.$name.'" style="'.$def_css.'">'.$value.'</textarea>');
		}
        
		else
		{
			self::$array_form[$name['name']] .= htmlspecialchars('<textarea ');
			
			if(!in_array('style', $name) && $css !== null)
				self::$array_form[$name['name']] .= 'style="'.$def_css.'"';
			
			foreach($name as $tag => $value)
			{
				if($tag != 'value')
				{
					self::$array_form[$name['name']] .= htmlspecialchars($tag . '="' .$value. '" ');
				}
				
				else if($tag == 'value')
				{
					self::$array_form[$name['name']] .= htmlspecialchars('>'.$value);
				}
			}
			
			self::$array_form[$name['name']] .= htmlspecialchars('</textarea>');
		}
	}
	
	/**
	*Tworzy html dla input file
	*@static
	*@access public 
	*@param string $name atrybut name input file'a
	*@param string $css css dla input file'a
	*/
	public static function input_file($name, $css = null, $inputs = 1) 
	{
		if($inputs == 1)
		{
			self::$array_form[$name] .= htmlspecialchars('<input type="file" name="'.$name.'" style="'.$css.'">');
		}
		else
		{
			for($i = 0; $i < $inputs; $i++)
			{
				self::$array_form[$name][] = htmlspecialchars('<input type="file" name="'.$name.'[]" style="'.$css.'">');
			}
		}
	}
	
	/**
	*Tworzy kod html dla input hidden
	*@static
	*@access public 
	*@param string $name atrybut name dla input hidden
	*@param string $css css dla input hidden
	*/
	public static function hidden($name, $value = null) 
	{		
		if(is_string($name))
		{
			self::$array_form[$name] = htmlspecialchars('<input type="hidden" name="'.$name.'" value="'.$value.'">');
		}
		
		else
		{
			self::$array_form[$name['name']] .= htmlspecialchars('<input type="hidden" ');
		
			foreach($name as $tag => $value)
			{		
				self::$array_form[$name['name']] .= htmlspecialchars($tag . '="' .$value. '" ');
			}
		
			self::$array_form[$name['name']] .= '>';
		}
	}
	
	/**
	*Tworz kod html dla checkbox
	*@static
	*@access public 
	*@param string $name atrybut name input text'a
	*@param string $value atrybut value input text'a
	*@param bool $checked czy checkbox ma byc zaznaczony
	*@param string $css css dla input text'a
	*/
	public static function checkbox($name, $value = null, $checked = false, $css = null) 
	{
		$check = ($checked === false) ? '' : 'checked="checked"';
		
		if(is_array($name))
		{
			self::$array_form[$name['name']] .= htmlspecialchars('<input type="checkbox" ');
		
			foreach($name as $tag => $value)
			{		
				self::$array_form[$name['name']] .= htmlspecialchars($tag . '="'.$value.'" ');
			}
			
			self::$array_form[$name['name']] .= '>';
		}
		else if($value === null)
		{
			self::$array_form[$name] .= htmlspecialchars('<input type="checkbox" name="'.$name.'" value="'.$name.'" '.$check.' style="'.$css.'">'.$name);
		}
		else
		{
			self::$array_form[$name] .= htmlspecialchars('<input type="checkbox" name="'.$name.'" value="'.$value.'" '.$check.' style="'.$css.'">'.$value);
		}
	}
	
	/**
	*@static
	*@access public 
	*@param string $name atrybut name dla radio
	*@param string $value atrybut value dla radio
	*@param bool $checked czy radio ma byc zaznaczony
	*@param string $css css dla radio
	*/
	//zrobic zeby metoda zwracala checlbox w jednej zmiennej jak menu
	public static function radio($name, $value = null, $checked = false, $css = null) 
	{		
		$check = ($checked === false) ? '' : 'checked="checked"';
		
		if(is_array($value)) 
		{
			foreach($value as $key => $data) 
			{
				self::$array_form[$name.'_'.$key] = htmlspecialchars('<input type="radio" name="'.$name.'" value="'.$key.'" '.$check.' style="'.$css.'">'.$data);
			}
			
			foreach($value as $k => $v)
			{
				self::$array_form[$name] .= self::$array_form[$name.'_'.$k];
				unset(self::$array_form[$name.'_'.$k]);
			}
		}
		
		else if($value === null)
		{
			self::$array_form[$name] .= htmlspecialchars('<input type="radio" name="'.$name.'" value="'.$name.'" '.$check.' style="'.$css.'">'.$name);
		}
		
		else
		{
			self::$array_form[$name] .= htmlspecialchars('<input type="radio" name="'.$name.'" value="'.$value.'" '.$check.' style="'.$css.'">'.$value);
		}
	}
	
	/**
	*@static
	*@access public 
	*@param string $name atrybut name dla menu
	*@param string|array $value atrybut value dla menu
	*@param bool $multiple czy menu jest multiple
	*@param string $css css dla menu
	*@param string $selected nazwa pola ktore ma byc zaznaczone
	*@param bool $value_key czy zaznaczyc pole po kluczy lub wartosci tagu menu
	*/
	public static function menu($name, $value, $multiple = false, $css = null, $selected = null, $value_key = false) 
	{
		$multi = ($multiple === false) ? '' : 'multiple="multiple"';
		
		self::$array_form[$name.'_open'] = htmlspecialchars('<select name="'.$name.'" '.$multi.' style="'.$css.'">');
		
		if(is_array($value)) 
		{	
			foreach($value as $key => $option)
			{
				if($value_key)
				{
					if($selected == $key)
					{
						$check = 'selected';
					}
					else
					{
						$check = '';
					}
					 
					self::$array_form[$name.'_'.$key] = htmlspecialchars('<option value="'.$key.'" '.$check.'>'.$option.'</option>');
				}
				else
				{
					if($selected == $option)
					{
						$check = 'selected';
					}
					else
					{
						$check = '';
					}
						
					self::$array_form[$name.'_'.$key] = htmlspecialchars('<option value="'.$option.'" '.$check.'>'.$option.'</option>');
				}
			}
		}	
		else
		{
			self::$array_form[$name] .= htmlspecialchars('<option value="'.$value.'">'.$value.'</option>');
		}
		
		self::$array_form[$name.'_close'] .= htmlspecialchars('</select>');
		
		self::$array_form[$name] = self::$array_form[$name.'_open'];
		unset(self::$array_form[$name.'_open']);
		
		foreach($value as $key => $value)
		{
			self::$array_form[$name] .= self::$array_form[$name.'_'.$key];
			unset(self::$array_form[$name.'_'.$key]);
		}
		
		self::$array_form[$name] .= self::$array_form[$name.'_close'];
		unset(self::$array_form[$name.'_close']);
	}
	
	/**
	*Tworzy kod html dla input submit
	*@static
	*@access public 
	*@param string $name atrybut name input sumbit
	*@param string $value atrybut value input submit
	*@param string $css css dla input submit
	*/
	public static function submit($name = 'form_sbt', $value = 'submit', $css = null) 
	{	
		if($css === null)
		{
			$def_css = '';
		}
		else if(empty($css))
		{
			$def_css = 'height:30px;width:200px;padding:2px;background-color:#ffffff;border:2px solid #B8860B;';
		}	
		else
		{		
			$def_css = $css;
		}
		
		if(is_string($name))
		{
			self::$array_form[$name] = htmlspecialchars('<input type="submit" name="submit_'.$name.'" value="'.$value.'" style="'.$def_css.'">');
		}
		else
		{
			self::$array_form[$name['name']] .= htmlspecialchars('<input type="submit" ');
		
			foreach($name as $tag => $val)
			{		
				if($tag == 'name')
				{
					self::$array_form[$name['name']] .= htmlspecialchars($tag.'="submit_'.$val.'" ');
				}
				else
				{
					self::$array_form[$name['name']] .= htmlspecialchars($tag.'="'.$val.'" ');
				}
			}
			
			self::$array_form[$name['name']] .= '>';
		}
	}
	
	/**
	*Zamyka formularz
	*@static
	*@access public 
	*/
	public static function close($form_close_key_array = 'form_close') 
	{	
		self::$array_form[$form_close_key_array] .= htmlspecialchars('</form>');
	}
	
	/**
	*Zwraca formularz
	*@static
	*@access public 
	*@return string self::$form kod html formularza
	*/
	public static function get_form()
	{
		foreach(self::$array_form as $key => $value)
		{
			if(!is_array($value))
			{
				self::$array_form[$key] = html_entity_decode($value);
			}
			else
			{
				foreach(self::$array_form[$key] as $k => $v)
				{
					self::$array_form[$key][$k] = html_entity_decode($v);
				}
			}
		}
		return self::$array_form;
	}
}

?>