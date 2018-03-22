<?php
/**
* 
*/
namespace framework;

class Image
{
	protected $savePath;//保存的路径
	protected $randName;//随机名字
	protected $extension; //后缀
	protected $fileName;
	function __construct($savePath = './', $randName = true, $extension = 'png')
	{
		$this->savePath = $savePath;
		$this->randName = $randName;
		$this->extension = $extension;
	}
	public function waterMark($dstPath, $srcPath, $pos = 9, $pct = 100)
	{
		//1.判断文件
		if (!is_file($dstPath)) {
			return '目标图不存在';
		} else if (!is_file($srcPath)) {
			return '水印图不存在';
		} else if (!is_dir($this->savePath)) {
			return '路径不对'; 
		} else if (!is_writeable($this->savePath)) {
			return '保存路径不可写';
		}
		//2.判断尺寸
		list($dstWidth, $dstHeight) = getimagesize($dstPath);
		list($srcWidth, $srcHeight) = getimagesize($srcPath);
		if ($srcWidth > $dstWidth || $srcHeight > $dstHeight) {
			return '水印图过大';
		}

		//3.计算位置
		if ($pos >= 1 && $pos <= 9) {
			$offsetX = ($pos - 1) % 3 * ceil(($dstWidth - $srcWidth) / 2);
			$offsetY = floor(($pos - 1)/3) * ceil(($dstHeight - $srcHeight) / 2);
		} else {
			//随机位置
			$offsetX = mt_rand(0, $dstWidth - $srcWidth);
			$offsetY = mt_rand(0, $dstHeight - $srcHeight);
		}
		//打开图片
		$dstImg = $this->openImg($dstPath);
		$srcImg = $this->openImg($srcPath);
		imagecopymerge($dstImg, $srcImg, $offsetX, $offsetY, 0, 0, $srcWidth, $srcHeight, $pct);
		$this->saveImg($dstImg, $dstPath);
		imagedestroy($dstImg);
		imagedestroy($srcImg);
		return $this->fileName;
	}
	protected function saveImg($dstImg, $dstPath) 
	{
		$this->fileName = rtrim($this->savePath, '/') . '/'; // ./
		$info = pathinfo($dstPath);
		//名字
		if ($this->randName) {
			$this->fileName .= uniqid(); //./dghjsgtuetyi

		} else {
			$this->fileName .= $info['filename'];
		}
		//./gfhjhkj.jpg
		$this->fileName .= '.' . ltrim($this->extension, '.');
		if (!strcasecmp(ltrim($this->extension, '.'), 'jpg')) {
			$this->extension = 'jpeg';
		}
		$savePath = 'image' . $this->extension;
		$savePath($dstImg, $this->fileName);

	}
	protected function openImg($filePath) {
		$info = getimagesize($filePath);
		$extension = image_type_to_extension($info['2'], false);
		if (!strcasecmp($extension, 'jpg')) {
			$extension = 'jpeg';
		}
		$openFun = 'imagecreatefrom' . $extension;
		return $openFun($filePath);
	}
	public function zoomImg($imgPath, $width, $height)
	{
		//检查
		if (!file_exists($imgPath)) {
			return '原图不存在';
		} else if (!is_dir($this->savePath)) {
			return '路径不对'; 
		} else if (!is_writeable($this->savePath)) {
			return '保存路径不可写';
		}
		//计算位置
		list($srcWidth, $srcHeight) = getimagesize($imgPath);
		$size = $this->getSize($width, $height , $srcWidth, $srcHeight);
		//新画布的创建
		$srcImg = $this->openImg($imgPath);//打开原来的资源
		$img = imagecreatetruecolor($width, $height);//按照缩放的尺寸
		//合并图
		$this->mergeImg($img, $srcImg, $size);
		//保存
		$this->saveImg($img, $imgPath);
		imagedestroy($img);
		imagedestroy($srcImg);
		return $this->fileName;

	}
	protected function mergeImg($img, $srcImg, $size)
	{
		//获取原始图片的透明色  值为-1代表原图无透明色
		$lucidColor = imagecolortransparent($srcImg);
		if ($lucidColor == -1 )
		{
			$lucidColor = imagecolorallocate($img, 0, 0, 0);
		}
		//填充透明色
		imagefill($img, 0, 0, $lucidColor);
		imagecolortransparent($img, $lucidColor);
		//合并图
		imagecopyresampled($img, $srcImg, $size['offsetX'], $size['offsetY'], 0, 0, $size['width'], $size['height'], $size['srcWidth'], $size['srcHeight']);
	}
	protected function getSize($width, $height , $srcWidth, $srcHeight)
	{
		$size['srcWidth'] = $srcWidth;
		$size['srcHeight'] = $srcHeight;
		//计算宽高个子比例
		$scaleWdith = $width / $srcWidth;
		$scaleHeight  = $height / $srcHeight;
		//取得最小的比例
		$scaleFinal = min($scaleWdith, $scaleHeight);
		//计算缩放后宽高个子的尺寸
		$size['width'] = $srcWidth * $scaleFinal;
		$size['height'] = $srcHeight * $scaleFinal;
		//新画布合并时候的偏移量

		$size['offsetX'] = ($width - $size['width']) / 2;
		$size['offsetY'] = ($height - $size['height']) / 2; 
		return $size;
	}
}
$img = new Image();
// echo $img->waterMark('2.png', '1.png');
//echo $img->zoomImg('2.png',100,200);
//Image::waterMark();
//Image::zoomImg();