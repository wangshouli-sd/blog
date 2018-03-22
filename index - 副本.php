<?php
class Psr4Autoloader
{
	protected $namespaces;
	public function __construct($namespace = null)
	{

		spl_autoload_register([$this,'loadClass']);
		if (is_array($namespace)) {
			$this->addNamespace($namespace);
		}
	}
	public function loadClass($className)
	{
		//将类名和命名空间分开
		$pos = strrpos($className, '\\');

		$namespace = substr($className, 0, $pos);
	
		if (!empty($this->namespaces) &&  array_key_exists($namespace, $this->namespaces)) {
			//找到真正的映射关系
			$realClass = substr($className, $pos+1);
			$this->loadMappedFile($namespace, $realClass);
		} else {
			$className = str_replace('\\', '/', $className) . '.php';
			if (file_exists($className)) {
				include  $className ;
			}
		}
		
		
	}
	protected function loadMappedFile($namespace, $realClass)
	{
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

$arr = include 'config/namespace.php';
$psr = new Psr4Autoloader($arr);
//$obj = new \test\Test();

//$psr->addNamespace('index\\controller','app/index/controller');
//$psr->addNamespace($arr);

$obj = new index\controller\Controller;  //----->app/index/controller/Cotroller.php
$user = new index\model\User;
//$obj = new test\Test();
//$obj = new index\model\User; //----->app/index/model/User
//$obj = new admin\model\User; //--->app/admin/model
$_GET['m'] = empty($_GET['m']) ? 'index' : $_GET['m'] ;
$_GET['c'] = empty($_GET['c']) ? 'index' : $_GET['c'] ;
$_GET['a'] = empty($_GET['a']) ? 'index' : $_GET['a'] ;
$className = $_GET['m'] . '\\controller\\' . ucfirst($_GET['c']); //index\controller\Index
// $obj = new $className();
call_user_func([new $className, $_GET['a']]);