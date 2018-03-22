<?php
namespace index\model;
use framework\Model;
//用户表
class User extends Model
{
	//查找id的全部数据
	public function userList($data)
	{
		return $this->where($data)->select();
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
		if ($this->where('username' .'=\''.$data['username'] .'\'')->select()) {
			return '该用户名已被注册';
		}
		if (empty($data['username'])||empty($data['password'])||empty($data['email'])) {
			return '信息缺失,注册失败';
		}
		if (strlen($data['username']) > 10) {
			return '用户名不能超过10个字符';
		}
		if (strlen($data['password']) < 6) {
			return '密码不能少于6位';
		}
		if($data['password']!= $data['repassword']){
			return '两次密码不一致';
		}
		$password = trim($data['password']);
		$data['password'] = md5($password);
		//邮箱验证
		$reg = '/\w+@(\w+\.)+(cn|com|org|net|edu)$/';
		preg_match($reg,$data['email'],$match);
		if(!$match){
			return '邮箱格式不正确';
		}
		//验证码
		if(trim($data['yzm']) != $_SESSION['yzm']){
			return '验证码错误';
		}
		//注册时间
		$data['regtime'] = time();
		//创建用于激活识别码 
		$token = md5($data['username'].$data['password'].$data['regtime']);
		$data['token'] = $token; 
		//过期时间为3小时后 
		$token_exptime = time()+60*60*3;
		$data['tokenExptime'] = $token_exptime;
		//用户IP
		$data['ip'] = $_SERVER['REMOTE_ADDR'];
		if ($data['ip'] == '::1') {
			$data['ip'] = ip2long('127.0.0.1');
		}
		return $this->insert($data);
	}
	//积分增加
	public function scoreAdd($data)
	{
		return $this->where("id = $data")->setInc('score',3);
	}
	//更新密码
	public function userUpdate($data)
	{
		$username = $data['username'];
		if (strlen($data['password']) < 6) {
			return '密码不能少于6位';
		}
		if($data['password']!= $data['repassword']){
			return '两次密码不一致';
		}
		if(trim($data['code']) != $_SESSION['yzm']) {
			return '验证码错误';
		}
		$arr['password'] = md5($data['password']);
		return $this->where("username = '$username'")->update($arr);
	}
}