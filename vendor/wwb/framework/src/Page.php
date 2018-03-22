<?php
/**
* 
*/
namespace framework;


class Page
{
	protected $total;//总数
	protected $pageSize;//每一页显示
	protected $page; //当前页
	protected $pageCount;
	protected $url;

	public function __construct($total, $pageSize = 5)
	{
		$this->total = $total;
		$this->pageSize = $pageSize;
		$this->pageCount = ceil($total / $pageSize);
		$this->page = $this->getPage();
		$this->url = $this->getUrl();
		//echo $this->setPage();
	}
	public function urlList()
	{
		return [
			'head' => $this->headPage(),
			'last' => $this->lastPage(),
			'prev' => $this->prevPage(),
			'next' => $this->nextPage()
		];
	}
	//首页
	protected function headPage()
	{
		return $this->setPage(1);
	}
	//尾页
	protected function lastPage()
	{
		return $this->setPage($this->pageCount);
	}

	//上一页
	protected function prevPage()
	{
		if ($this->page < 2) {
			return $this->headPage();
		}else {
			return $this->setPage($this->page - 1);
		}
	}
	//下一页
	protected function nextPage()
	{
		if ($this->page < $this->pageCount) {
			return $this->setPage($this->page + 1);
		} else {
			return $this->lastPage();
		}
	}
	//跳转到指定页面
	public function givenPage($page)
	{
		if ($page < 1) {
			$page = 1;
		} else if ($page > $this->pageCount) {
			$page = $this->pageCount;
		}
		return $this->setPage($page);
	}
	protected function getUrl()
	{
		$url = $_SERVER['REQUEST_SCHEME'] . '://';//协议
		$url .= $_SERVER['HTTP_HOST'];//主机
		$url .= ':' . $_SERVER['SERVER_PORT'];//端口号
		$url .= $_SERVER['REQUEST_URI'];///day04/code/page.php?page=1&cid=3

		//http://www.woqu.com:80/day04/code/page.php?page=1&cid=3
		//http://www.woqu.com:80/day04/code/page.php?cid=3&page=1&tid=3
		//http://www.woqu.com:80/day04/code/page.php?cid=3&page=1
		//http://www.woqu.com:80/day04/code/page.php?page=1
		$replaceStr = 'page='. $this->page;
		$replaceArr = [
				$replaceStr . '&',
				'&' . $replaceStr,
				'?' . $replaceStr,
		];
		return str_replace($replaceArr, '', $url);
	}

	public function getPage()
	{
		return empty($_GET['page']) ? 1 : (int)$_GET['page'];
	}
	public function setPage($page)
	{
		if (strpos($this->url, '?')) {
			return $this->url . '&page=' . $page;
		} else {
			return $this->url . '?page=' . $page;
		}
	}
	public function limit()
	{
		//0,10  10,10
		return ($this->page - 1) * $this->pageSize . ',' . $this->pageSize;
	}
}
