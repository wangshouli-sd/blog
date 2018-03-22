<?php
namespace admin\controller;

use admin\model\Admin;
use admin\model\User;
use framework\Verify;
use framework\Upload;
use admin\model\Song;
use framework\Page;
class Index extends Controller
{
	//后台首页
	public function index()
	{
		if (isset($_SESSION['name'])) {
			$this->display();
		} else {
			$this->display('login.html');	
		}
	}
	//退出
	public function login()
	{
		//$this->assign('title', '标题');
		$this->display('login.html');
	}
	//tips
	public function tips()
	{
		//$this->assign('title', '标题');
		$this->display('tips.html');
	}
	//博客
	public function blog()
	{
		$song = new Song();
		//删除博客
		if (isset($_POST['tj'])) {
			foreach ($_POST['id'] as $key => $value) {
				$song->songDelete("id = $value");
			}
		}
		/*分页*/
			$totalarr   = $song->songCount('rid = 0');
			$total = (int)$totalarr[0]['count'];
			$pageobj = new Page($total);
			$result  = $pageobj->urlList();//四条url
			$page    = $pageobj->getPage();//1
			$count   = ceil($total / 5);//60                           
			$limit   = $pageobj->limit();//0,5
			/*分页的各个数据*/
			$this->assign('result', $result);
			$this->assign('page', $page);
			$this->assign('count', $count);

		$data = $song->songAll('rid = 0', $limit);
		//var_dump($data);
		$this->assign('data', $data);
		$this->display('blog.html');
	}
	public function cate()
	{
		//$this->assign('title', '标题');
		$this->display('cate.html');
	}
	public function cateedit()
	{
		//$this->assign('title', '标题');
		$this->display('cateedit.html');
	}
	public function info()
	{
		//$this->assign('title', '标题');
		$this->display('info.html');
	}
	public function pass()
	{
		$admin = new Admin();
		$name = $_SESSION['name'];
		$this->assign('name', $name);
		if (isset($_POST['tj'])) {
			$password = $_POST['mpass'];
			$checka = $admin->userId(['name'=>$name,'password'=>$password]);
			if (empty($checka)) {
				$check = '原密码输入错误';
				$this->assign('is', '5');
				$this->assign('check', $check);
			} else {
				if ($_POST['password'] ==$_POST['repassword']) {
					$data = $_POST;
					$admin->userUpdate("name = $name", $data);
					$check = '修改成功';
					$this->assign('is', '2');
					$this->assign('check', $check);
				} else {
					$check = '修改失败';
					$this->assign('is', '5');
					$this->assign('check', $check);
				}
			}
			$this->display('jump.html');	
		} else {
			$this->display('pass.html');
		}
		
	}
	//用户表
	public function user()
	{
		$user = new User();
		//删除用户
		if (isset($_POST['tj'])) {
			foreach ($_POST['id'] as $key => $value) {
				$user->userDelete("id = $value");
			}
		}
		/*分页*/
			$totalarr   = $user->userCount();
			$total = (int)$totalarr[0]['count'];
			$pageobj = new Page($total);
			$result  = $pageobj->urlList();//四条url
			$page    = $pageobj->getPage();//1
			$count   = ceil($total / 5);//60                           
			$limit   = $pageobj->limit();//0,5
			/*分页的各个数据*/
			$this->assign('result', $result);
			$this->assign('page', $page);
			$this->assign('count', $count);

		$data = $user->userList('', $limit);
		$this->assign('data', $data);
		$this->display('user.html');
	}
	//新增超管
	public function admin()
	{
		if (isset($_POST['tj'])) {
			$user = new Admin();
			$id = $user->userInsert($_POST);
			if (is_int($id)) {
				$this->assign('is', '4');
				$check = '新增超管成功！';
				$this->assign('check', $check);
			} else {
				$this->assign('is', '3');
				$check = $id;
				$this->assign('check', $check);
			}
			$this->display('jump.html');

		} else {
			$this->display('admin.html');
		}
		
	}
	//验证码方法
	public function verify()
	{
		$code =	Verify::ver();
		$_SESSION['yzm'] = $code;
		return $code;
	}
	//验证登录注册
	public function check()
	{			
		$arr = $_POST;
		$admin = new Admin;
		//登录
		if (isset($_POST['tj'])) {
			$data = $admin->userId($arr);
			if (!empty($data)) {
				if ($_POST['code'] == $_SESSION['yzm']) {
					$_SESSION['name'] = $data[0]['name'];
				$name = $_SESSION['name'] ;
				$this->assign('is', '1');
				$this->assign('check', '欢迎'. $name .'登录成功！');
			} else {
				$this->assign('is', '0');
				$check = '验证码错误！';
				$this->assign('check', $check);
			}
				
			} else {
				$this->assign('is', '0');
				$check = '登录信息错误！';
				$this->assign('check', $check);
			}
		}
		$this->display('jump.html');
	}
	//退出
	public function jump()
	{
		session_unset();
		session_destroy();
		$this->assign('is', '2');
		$this->assign('check', '退出成功！');
		$this->display('jump.html');
	}
	//锁定用户
	public function lock()
	{
		$id = $_GET['id'];
		$user = new User();
		$data = [
			'islock' => (int)$_GET['islock'],
		];
		$user->userUpdate("id = $id", $data);
		header("location:\index.php?m=admin&c=index&a=user");
	}
	public function config()
	{
		if (isset($_POST['tj'])) {
			array_pop($_POST);
			//var_dump($_POST);
			$info = file_get_contents("config/config.php");
			//var_dump($info);
			foreach($_POST as $k=>$v){ 
				$pattern="/define\('$k','.*?'\);/";
            	$replace="define('$k','$v');";
				$info = preg_replace($pattern, $replace, $info);
			}
			file_put_contents('./config/config.php',$info);
            header("location:".$_SERVER['HTTP_REFERER']);
		} else {
			$this->display('info.html');
		}
		
	}
}