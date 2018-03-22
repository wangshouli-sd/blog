<?php
namespace admin\model;
use framework\Model;
//博文发表
class Song extends Model
{
	//发表博文
	public function songInsert($data)
	{
		if (!empty($data)) {
			return $this->inSert($data);
		} else {
			return '发表失败';
		}
	}
	//查找id的全部数据
	public function songAll($data, $limit=null)
	{
		if (empty($limit)) {
			return $this->where($data)->select();
		} else {
			return $this->where($data)->limit($limit)->select();
		}
	}
	//查count
	public function songCount($data)
	{
		return $this->where($data)->select('count(*) as count');
	}
	//删除数据
	public function songDelete($data)
	{
		return $this->where($data)->delete();
	}
}