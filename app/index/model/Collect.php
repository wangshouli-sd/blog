<?php
namespace index\model;
use framework\Model;
//用户表
class Collect extends Model
{
	//查找id的全部数据
	public function collectList($data, $datal=null)
	{
		if (empty($datal)) {
			return $this->where($data)->select();
		} else {
			return $this->where($data)->select($datal);
		}
		
	}
	//更新数据
	public function collectUpdate($data, $datal)
	{
		return $this->where($data)->update($datal);
	}
	//插入数据
	public function collectInsert($data)
	{
		return $this->insert($data);
	}
}