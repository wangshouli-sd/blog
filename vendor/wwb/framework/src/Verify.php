<?php
/**
* 
*/
namespace framework;

class Verify
{
	
	protected $height;
	protected $width;
	protected $length;
	protected $type;
	protected $code;
	protected $image;
	public function __construct($height = 50, $width = 200, $length = 4, $type = 1 )
	{
		$this->height = $height;
		$this->width = $width;
		$this->length = $length;
		$this->type = $type;
		$this->outPut();
	}
	public static function ver($height = 50, $width = 200, $length = 4, $type = 1)
	{
		$ver = new Verify($height, $width, $length, $type);
		return $ver->code;
	}
	protected function outPut()
	{
		//创建画布
		$this->createImg();
		//画字符串
		$this->verCode();
		//画干扰元素
		$this->setDisturb();
		//发送图片
		$this->sendImg();
	}
	protected function createImg()
	{
		$this->image = imagecreatetruecolor($this->width, $this->height);
		$color = $this->getColor(true);
		imagefill($this->image, 0, 0, $color);
	}
	protected function getColor($isLight = false)
	{
		//0 1
		$start = (int)$isLight * 127;//0*127 = 0 1*127=127
		$stop = $start + 128;
		$red  =  mt_rand($start,$stop);
		$green  =  mt_rand($start,$stop);
		$blue  =  mt_rand($start,$stop);
		return imagecolorallocate($this->image, $red, $green, $blue);
	}
	protected function verCode()
	{
		$size = $this->height / 2;
		$code = $this->randString();
		$count = strlen($code);
		$perWidth = $this->width / $count;
		$offsetY = ($this->height + $size) / 2;
		
		$delta = ($perWidth - $size) / 2;
		for ($i=0; $i < $count; $i++) { 
			$angle = mt_rand(-30, 30);
			$color = $this->getColor();
			$offsetX = $i * $perWidth + $delta;
			imagettftext($this->image, $size, $angle, $offsetX, $offsetY, $color, './public/font/lxkmht.ttf', $code[$i]);
		}
	}
	protected function setDisturb()
	{
		//点
		$total = $this->width * $this->height / 50 ;
		for ($i=0; $i < $total; $i++) { 
			$color = $this->getColor();
			$x = mt_rand(0, $this->width);
			$y = mt_rand(0, $this->height);
			imagesetpixel($this->image, $x, $y, $color);
		}
		for ($i=0; $i < 5; $i++) { 
			$color = $this->getColor();

			imageline($this->image,mt_rand(0,$this->width), mt_rand(0,$this->height), mt_rand(0,$this->width), mt_rand(0,$this->height), $color);
		}
	}
	protected function randString()
	{
		switch ($this->type) {
			case 1://纯数字
				$str = $this->randNum();
				break;
			case 2://纯字母
				$str = $this->randAlpha();
				break;
			case 3://数字字母混合双打
				$str = $this->randMixed();
				break;
			case 4://等式
				$str = $this->randCom();
				break;
		}
		if ($this->type == 4) {
			$this->code = $str['code'];
			$str = $str['str'];
		} else{
			$this->code = $str;
		}
		return $str;
	}
	protected function randCom()
	{
		$num1 = mt_rand(0,9);
		$num2 = mt_rand(0,9);
		$arr = ['+', '-', '*'];
		$com = $arr[mt_rand(0,2)];
		$str = $num1 . $com . $num2 . '=?';
		switch ($com) {
			case '+':
				$re = $num1 + $num2;
				break;
			case '-':
				$re = $num1 - $num2;
				break;
			case '*':
				$re = $num1 * $num2;
				break;	
		}
		return ['str'=>$str, 'code'=>$re];

	}
	protected function randNum()
	{
		$str = 1234567890;
		return substr(str_shuffle($str), 0, $this->length);
	}
	protected function randAlpha()
	{
		$str = join('',range('a', 'z'));
		return substr(str_shuffle($str), 0, $this->length);
	}
	protected function randMixed()
	{
		return substr(md5(mt_rand(1,99)), 0, $this->length);
	}
	protected function sendImg()
	{
		header('content-type:image/png');
		imagepng($this->image);
	}
	public function code()
	{

		return $this->code;
	}

}

