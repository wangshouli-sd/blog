<?php
class Psr4Autoloader
{
	protected $namespaces;//命名空间和所在文件夹对应关系
	public function __construct($namespace = null)
	{

		spl_autoload_register([$this,'loadClass']);
		if (is_array($namespace)) {
			$this->addNamespace($namespace);
		}
	}
	//实例化一个类的时候触发
	public function loadClass($className)
	{
		//将类名和命名空间分开
		$pos = strrpos($className, '\\');//index/controller

		$namespace = substr($className, 0, $pos);
		if (!empty($this->namespaces) &&  array_key_exists($namespace, $this->namespaces)) {
			//找到真正的映射关系
			$realClass = substr($className, $pos+1);//Controller;
			$this->loadMappedFile($namespace, $realClass);
		} else {
			$className = str_replace('\\', '/', $className) . '.php';
			if (file_exists($className)) {
				include  $className ;
			}
		}
		
		
	}
	//include 类文件
	protected function loadMappedFile($namespace, $realClass)
	{
		//index/controller/Controller.php
		$className = $this->namespaces[$namespace] . $realClass . '.php';

		if (file_exists($className)) {
			include $className;
		}
	}
	//存储命名空间和文件夹对应关系
	public function addNamespace($namespace, $realPath = null)
	{
		if (is_array($namespace)) {
			foreach ($namespace as $key => $value) {
				$this->addPsr4($key, $value );
			}
		} else {
			$this->addPsr4($namespace, $realPath );
		}
		
	}
	protected function addPsr4($namespace, $realPath )
	{
		$this->namespaces[$namespace] = rtrim($realPath , '/') . '/';
	}
}
