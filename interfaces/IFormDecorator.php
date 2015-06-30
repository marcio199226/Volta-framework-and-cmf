<?php

/**
*Form Builder & Admin Generator

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2011, marcio
*@version 1.6.5
*/

interface IFormDecoratorSimple
{
	public function render();
}

interface IFormDecoratorWrapped
{
	public function open();
	public function close();
	public function attach($content);
	public function getContent();
}

interface IFormDecoratorEnhanced
{
	public function append();
}

?>