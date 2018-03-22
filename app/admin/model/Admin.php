<?php
namespace admin\model;
use framework\Model;
//用户表
class Admin extends Model
{
	//查找id的全部数据
	public function userList($data = null)
	{
		if (empty($data)) {
			return $this->select();
		} else {
			return $this->where($data)->select();
		}
	}
	//查找id
	public function userId($data)
	{
		$data['password'] = md5($data['password']);
		return $this->where($data)->select();
	}
	public function userInsert($data)
	{
	//注册判断条件页面
		if ($this->where('name' .'=\''.$data['name'] .'\'')->select()) {
			return '该用户名已被使用';
		}
		if($data['password']!= $data['repassword']){
			return '两次密码不一致';
		}
		$password = trim($data['password']);
		$data['password'] = md5($password);
		//注册时间
		$data['time'] = time();
		//用户IP
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		if ($data['ip'] == '::1') {
			$data['ip'] = ip2long('127.0.0.1');
		}
		return $this->insert($data);
	}
	//更新数据
	public function userUpdate($data, $datal)
	{
		return $this->where($data)->update($datal);
	}
}