<?php
namespace index\model;
use framework\Model;
//用户表
class Likes extends Model
{
	//查找id的全部数据
	public function likesList($data)
	{
		return $this->where($data)->select();
	}
	//更新数据
	public function likesUpdate($data, $datal)
	{
		return $this->where($data)->update($datal);
	}
	//插入数据
	public function likesInsert($data)
	{
		return $this->insert($data);
	}
}