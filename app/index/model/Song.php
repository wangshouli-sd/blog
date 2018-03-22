<?php
namespace index\model;
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
		if (empty($data)) {
			return $this->limit($limit)->select();
		} else {
			return $this->where($data)->limit($limit)->select();
		}
	}
	//查找id的数据
	public function songSelect($data)
	{
			return $this->where($data)->select();
	}
	//查count
	public function songCount($data = null,$datal = null)
	{
		if (empty($data)) {
			return $this->select('count(*) as count');
		} else {
			if (empty($datal)) {
				return $this->where($data)->select('count(*) as count');
			} else {
				return $this->where($data)->select("count($datal) as count");
			}
			
		}
	}
	//删除数据
	public function songDelete($data)
	{
		return $this->where($data)->delete();
	}
	//更新数据
	public function songUpdate($data, $datal)
	{
		return $this->where($data)->update($datal);
	}
}