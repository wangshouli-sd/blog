<?php
namespace admin\model;
use framework\Model;
//用户表
class User extends Model
{
	//查找id的全部数据
	public function userList($data = null, $limit)
	{
		if (empty($data)) {
			return $this->limit($limit)->select();
		} else {
			return $this->where($data)->limit($limit)->select();
		}
	}
	//查找id
	public function userId($data)
	{
		$data['password'] = md5($data['password']);
		return $this->where($data)->select();
	}
	//查count
	public function userCount($data = null)
	{
		if (empty($data)) {
			return $this->select('count(*) as count');
		} else {
			return $this->where($data)->select('count(*) as count');
		}
		
	}
	//更新数据
	public function userUpdate($data, $datal)
	{
		return $this->where($data)->update($datal);
	}
	//删除数据
	public function userDelete($data)
	{
		var_dump($data);
		return $this->where($data)->delete();
	}
}