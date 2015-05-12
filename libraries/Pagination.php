<?php 

/**
*Volta framework

*@author marcio <opi14@op.pl>, <polishvodka7@gmail.com>
*@copyright Copyright (c) 2012, marcio
*@version 1.0
*/


class Vf_Pagination
{
	protected $pagination = array();
	
	protected $urls = array();
	
	protected $view;
	
	protected $uriSegment = null;
	
	protected $pages;
	
	protected $perPage;
	
	protected $total;
	
	protected $currentPage;
	
	protected $first = 1;
	
	protected $last;
	
	protected $baseUrl;
	
	private $config = null;
	
	private $configRouter = null;
	
	private $uri = null;
	
	
	public function __construct()
	{
		$this->uri = new Vf_Router();
		$this->configRouter = new Vf_Config('config.Router');
		$this->config = new Vf_Config('config.Pagination');
		$this->view = $this->config->view;
	}
	
	
	protected function processPagination()
	{
		$this->pages = ceil(($this->getTotal() / $this->getPerPage()));
		$this->pagination['current'] = ($this->uri->getSegment($this->getUriSegment()-1) == 'page') ? $this->uri->getSegment($this->getUriSegment()) : 0;
		
		$this->pagination['last'] = $this->pages;	
		$this->pagination['first'] = 1;	
				
		if($this->pagination['current'] < 1) {
			$this->pagination['current'] = 1;
		} elseif (($this->pagination['current'] - 1) > 1) {
			$this->pagination['prev'] = $this->pagination['current'] - 1;
		}
			
		if(($this->pagination['current'] + 1) <= $this->pages) {
			$this->pagination['next'] = $this->pagination['current'] + 1;
		} else {
			$this->pagination['next'] = 0;
		}
	}
	
	
	protected function createUrls()
	{	
		$this->urls['page'] = ($this->uri->getSegment($this->getUriSegment()-1) == 'page') ? $this->uri->getSegment($this->getUriSegment()) : 1;
		$base = ($this->getBaseUrl() != '') ? $this->getBaseUrl() : '';
		
		foreach ($this->pagination as $key => $urlParam) {
			$this->urls[$key]['link'] = $base . 'page' . $this->configRouter->delimiter . $urlParam;
			$this->urls[$key]['param'] = $urlParam;
		}
	}
	
	
	public function display($create_url = false)
	{
		$this->processPagination();
		if ($this->pages == 1) {
			return;
		}
		if (!$create_url) {
			return $this->pagination;
		} else {
			$this->createUrls();
			extract($this->urls, EXTR_SKIP);
			ob_start();
		
			include(DIR_VIEWS . 'Pagination/' . $this->view . '.php');
			$pager = ob_get_contents();
			ob_end_clean();
			return $pager;
		}
	}
	
	
	public function getUriSegment()
	{
		return $this->uriSegment;
	}
	
	
	public function getBaseUrl()
	{
		return $this->baseUrl;
	}
	
	
	public function getPerPage()
	{
		return $this->perPage;
	}
	
	
	public function getOffset()
	{
		$start = ($this->uri->getSegment($this->getUriSegment()-1) == 'page') ? $this->uri->getSegment($this->getUriSegment()) : 0;
		if($start == 0) {
			return 0;
		} else {
			return ($start-1) * $this->getPerPage();
		}
	}
	
	
	public function getTotal()
	{
		return $this->total;
	}
	
	
	public function getCurrentPage()
	{
		return $this->pagination['current'];
	}
	
	
	public function getFirst()
	{
		return $this->pagination['first'];
	}
	
	
	public function getLast()
	{
		return $this->pagination['last'];
	}
	
	
	public function setPerPage($perPage)
	{
		$this->perPage = $perPage;
	}
	
	
	public function setTotal($total)
	{
		$this->total = $total;
	}
	

	public function setCurrentPage($current)
	{
		$this->currentPage = $current;
	}
	
	
	public function setFirst($first)
	{
		$this->first = $first;
	}
	
	
	public function setLast($last)
	{
		$this->last = $last;
	}
	
	
	public function setBaseUrl($url = '')
	{
		$this->baseUrl = $url;
	}
	
	
	public function setUriSegment($segment)
	{
		$this->uriSegment = $segment-1;
	}
	
	
	public function setView($view)
	{
		$this->view = $view;
	}
}
?>