<?php

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_Feed
{
	
	protected $encoding = '';

	protected $description = '';
	
	protected $title = '';
	
	protected $link = '';

	protected $items = array();
	
	protected $tags = array();
	
	protected $config = null;
	

	public function __construct()
	{
		$this->config = new Vf_Config('config.Feed');
		$this->encoding = $this->config->encoding;
	}
	
	
	public function addItems($items = array())
	{
		$this->items = $items;
	}
	
	
	public function setItemTag($tag, $value)
	{
		$this->tags[$tag] = $value;
	}
	
	
	protected function parseItem()
	{
		if(sizeof($this->items) > 0)
		{
			foreach($this->items as $item)
			{
				$items_feed .= '<item>';
				
				foreach($this->tags as $item_name => $item_value)
				{
					if($item_name == 'link')
					{
						$items_feed .= '<' . $item_name . '>' . $item_value[0] . $item[$item_value[1]] . '</' . $item_name . '>';
					}
					else
					{
						$items_feed .= '<' . $item_name . '>' . stripslashes($item[$item_value]) . '</' . $item_name . '>';
					}
				}
				
				$items_feed .= '</item>';	
			}
			return $items_feed;
		}
		return null;
	}
	
	
	public function saveAsXml()
	{
		$items = $this->parseItem();
		
		$feed = '<?xml version="1.0" encoding="' . $this->getEncoding() . '" ?>';
		$feed .= '<rss version="2.0">';
		$feed .= '<channel>';
		$feed .= $this->getTitle();
		$feed .= $this->getLink();
		$feed .= $this->getDescritpion();
		$feed .= $items;
		$feed .= '</channel>';
		$feed .= '</rss>';
		file_put_contents($this->config->rss_directory . $this->config->rss_filename, $feed);
	}
	
	
	public function getDescritpion()
	{
		return $this->description;
	}
	
	
	public function getTitle()
	{
		return $this->title;
	}
	
	
	public function getLink()
	{
		return $this->link;
	}
	
	
	public function getEncoding()
	{
		return $this->encoding;
	}
	
	
	public function getFeedFile()
	{
		return $this->config->rss_directory . $this->config->rss_filename;
	}

	
	public function setDescription($description)
	{
		$this->description = '<description>' . $description . '</description>';
	}
	
	
	public function setTitle($title)
	{
		$this->title = '<title>' . $title . '</title>';
	}
	
	
	public function setLink($link)
	{
		$this->link = '<link>' . $link . '</link>';
	}
	

	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}
}

?>
