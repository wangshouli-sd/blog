<?php
include 'boot/Psr4Autoloader.php';
class Start
{
	public static function init()
	{
		$namespace = include 'config/namespace.php';

		new Psr4Autoloader($namespace);
	}
	public static function route()
	{
		$_GET['m'] = empty($_GET['m']) ? 'index' : $_GET['m'] ;//前后台模块  index admin
		$_GET['c'] = empty($_GET['c']) ? 'index' : $_GET['c'] ;//控制器 
		$_GET['a'] = empty($_GET['a']) ? 'index' : $_GET['a'] ;//成员方法
		$className = $_GET['m'] . '\\controller\\' . ucfirst($_GET['c']);//index\controller\addlist

		call_user_func([new $className, $_GET['a']]);//自动触发
	}
}
