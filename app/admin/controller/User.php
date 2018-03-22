<?php
namespace admin\controller;
use admin\model\User as UserModel;
class User extends Controller
{
	protected $user;
	public function __construct()
	{
		parent::__construct();
		$this->user = new UserModel();
	}
	public function info()
	{

		//$user = new UserModel();
		$info = $this->user->userList();
		$this->assign('info', $info[0]);
		$this->display();
	}

	public function add()
	{

	}
}