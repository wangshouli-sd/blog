<?php
/**
* 
*/
namespace framework;

class Upload 
{
	protected $savePath = './upload';
	protected $randName = true;
	protected $mime = ['image/png', 'image/jpeg', 'image/gif'];
	protected $errorInfo= ['code'=>0, 'message' =>'成功'];
	//protected $errorMessage = '成功';
	protected $datePath = false; //日期路径
	protected $maxSize = 2000000;
	protected $ex = 'png';
	protected $uploadInfo;
	protected $pathName;
	public function __construct($options = null)
	{
		$this->setOption($options);
	}
	protected function setOption($options)
	{
		if (is_array($options)) {
			//获取类中的成员属性返回值是数组
			$keys = get_class_vars(__CLASS__);
			foreach ($options as $key => $value) {
				//判断参数中key在不在成员属性列表中
				if (array_key_exists($key, $keys)) {
					$this->$key = $value;
				}
			}
		}
	}
	public function uploadFile($filed)
	{
		//1.检查保存路径
		if (!$this->checkSavePath()) {
			return $this->errorInfo;
		}
		//2.检查上传的文件信息
		if (!$this->checkUploadInfo($filed)) {
			return $this->errorInfo;
		}
		//3.检查error信息
		if (!$this->checkUploadError()) {
			return $this->errorInfo;
		}
		//4.检查自定义的信息
		if (!$this->checkAllow()) {
			return $this->errorInfo;
		}
		//5.检查是不是上传文件
		if (!$this->checkUploadFile()) {
			return $this->errorInfo;
		}
		//6.拼接保存路径
		if (!$this->joinPathName()) {
			return $this->errorInfo;
		}
		//7.移动文件
		if (!$this->moveUploadFile()) {
			return $this->errorInfo;
		}
		$pos = strrpos($this->pathName, '/');
		$this->pathName = './upload/'.substr($this->pathName, $pos + 1);
		return $this->pathName;
	}
	protected function moveUploadFile()
	{
		if (!move_uploaded_file($this->uploadInfo['tmp_name'], $this->pathName)) {
			$this->errorInfo['code'] = -6;
			$this->errorInfo['message'] = '移动失败';
			return false;
		}
		return true;
	}
	protected function joinPathName() 
	{
		//路径
		$this->pathName = $this->savePath;
		//要不要目录采用日期方式
		if ($this->datePath) {
			//./upload/2017/09/34/fjdfh.jpg
			$this->pathName .= date('Y/m/d') . '/';
			if (!file_exists($this->pathName)) {
				mkdir($this->pathName, 0777, true);
			} else {
				$info = pathinfo($this->uploadInfo['name']);
				$this->pathName .= $info['filename'];
			}
		}
		//名字
		if ($this->randName) {
			$this->pathName .= uniqid();
		}
		//后缀
		$this->pathName .= '.' . $this->ex;
		return true;

	}
	protected function checkUploadFile()
	{
		if (!is_uploaded_file($this->uploadInfo['tmp_name'])) {
			$this->errorInfo['code'] = -5;
			$this->errorInfo['message'] = '不是post传来的';
			return false;
		}
		return true;
	}
	protected function checkAllow()
	{
		if (!in_array($this->uploadInfo['type'], $this->mime)) {
			$this->errorInfo['code'] = -4;
			$this->errorInfo['message'] = '没有这个mime';
			return false;
		}
		if ($this->uploadInfo['size'] > $this->maxSize) {
			$this->errorInfo['code'] = -5;
			$this->errorInfo['message'] = '超过自定义大小';
			return false;
		}
		return true;
	}
	protected function checkUploadError()
	{
		if (!$this->uploadInfo['error']) {
			return true;
		}
		switch ($this->uploadInfo['error']) {
			case UPLOAD_ERR_INI_SIZE:
				$this->errorInfo['message'] = '上传文件的大小超出了配置文件的最大限制';
				$this->errorInfo['code'] = UPLOAD_ERR_INI_SIZE;
				break;
			case UPLOAD_ERR_FORM_SIZE:
				$this->errorInfo['message']  = '上传文件超出了form表单的限制';
				$this->errorInfo['code'] = UPLOAD_ERR_FORM_SIZE;
				break;
			case UPLOAD_ERR_PARTIAL:
				$this->errorInfo['message']  = '上传文件只有部分上传';
				$this->errorInfo['code'] = UPLOAD_ERR_PARTIAL;
				break;
			case UPLOAD_ERR_NO_FILE:
				$this->errorInfo['message']  = '不是上传文件';
				$this->errorInfo['code'] = UPLOAD_ERR_NO_FILE;
				break;
			case UPLOAD_ERR_NO_TMP_DIR:
				$this->errorInfo['message']  = '找不到临时文件夹';
				$this->errorInfo['code'] = UPLOAD_ERR_NO_TMP_DIR;
				break;
			case UPLOAD_ERR_CANT_WRITE:
				$this->errorInfo['message']  = '文件写入失败';
				$this->errorInfo['code'] = UPLOAD_ERR_CANT_WRITE;
				break;
		}
		return false;

	}
	protected function checkUploadInfo($filed)
	{
		if (empty($_FILES[$filed])) {
			$this->errorInfo['code'] = -3;
			$this->errorInfo['message'] = '没有这个name';
			return false;
		}
		$this->uploadInfo = $_FILES[$filed];
		return true;
	}
	protected function checkSavePath()
	{
		if (!is_dir($this->savePath)) {
			$this->errorInfo['code'] = -1;
			$this->errorInfo['message'] = '不是一个目录';
			return false;
		}
		if (!is_writeable($this->savePath)) {
			$this->errorInfo['code'] = -2;
			$this->errorInfo['message'] = '目录不可写';
			return false;
		}
		$this->savePath = rtrim($this->savePath, '/') . '/';
		return true;
	}
}
