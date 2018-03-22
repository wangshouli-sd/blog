<?php
namespace index\controller;
use framework\Template;
include 'config/config.php';
class Controller extends Template
{
	
	public function __construct()
	{
		parent::__construct('app/index/view/', 'cache/index');
	}

	public function display($tplFile = null, $isInclude = true)
	{
		if (is_null($tplFile)) {
			$tplFile = $_GET['c'] . '/' . $_GET['a']  . '.html';
		} else {
			$tplFile = $_GET['c'] . '/' . $tplFile;
		}
		
		parent::display($tplFile, $isInclude);
	}

}