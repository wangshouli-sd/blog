<?php
/**
* model  
*auth wanglijuan wnagliajun@100phone.com
$@param 
*/
namespace framework;

$config = include 'config/database.php';

class Model 
{
	//数据库资源
	protected $link;
	//主机名
	protected $host;
	//用户名
	protected $user;
	//密码
	protected $pwd;
	//数据库名
	protected $dbName;
	//字符集
	protected $charset;
	//表前缀
	protected $prefix;
	//表名
	protected $tableName;
	//参数
	protected $option;
	protected $sql;
	protected $fileds;//表字段
	protected $cache;//放表字段缓存文件
	protected $funcList = ['count','sum','avg','min','max'];
	public function __construct($config = null)
	{
		if (is_null($config)) {
			if (empty($GLOBALS['database'])) {
				$config = include 'config/database.php';
			} else {
				$config = $GLOBALS['database'];
			}
		}
		$this->host    = $config['DB_HOST'];
		$this->user    = $config['DB_USER'];
		$this->pwd     = $config['DB_PWD'];
		$this->dbName  = $config['DB_NAME'];
		$this->charset = $config['DB_CHARSET'];
		$this->prefix  = $config['DB_PREFIX'];
		$this->cache   = $this->checkCache($config['DB_CACHE']);

		if (!$this->link = $this->connect()) {
			exit('数据库链接失败');
		}
		$this->tableName = $this->getTableName();
		$this->fileds = $this->getFileds();
		$this->option = $this->setOption();
	}
	//sql条件初始化
	protected function setOption()
	{
		return [
			'where'  => '',
			'order'  => '',
			'group'  => '',
			'having' => '',
			'limit'  => '',
			'fileds' => '*',
			'table'  => $this->tableName,
			'values' => '',
		];
	}
	public function __call($method, $args)
	{
		if (!strncmp($method, 'getBy', 5)) {
			$filed = substr($method, 5);
			return $this->getBy($filed, $args[0]);
		}
		if (in_array($method, $this->funcList)) {
			$arg = empty($args) ? $this->fileds['_pk'] : $args[0];
			return $this->cal($method, $arg);
		}
	}
	protected function cal($method, $filed)
	{   
		$where = $this->option['where'];
		$sql ="select $method($filed) from $this->tableName $where";
		$re = $this->query($sql, MYSQLI_ASSOC);
		return $re[0];
	}
	public function getBy($filed, $value)
	{
		//从驼峰createTime---->create_time
		$realFiled = '';
		for ($i=0; $i < strlen($filed); $i++) { 
			$lower = strtolower($filed[$i]);//create_time
			if ($lower != $filed[$i]) {
				$realFiled .= '_';
			}
			$realFiled .= $lower;
		}
		if (is_string($value)) {
			$value = '\'' . $value . '\'';
		}
		$where = $realFiled . ' = ' . $value;
		return $this->where($where)->select();
	}
	//积分自增
	public function setInc($filed, $value)
	{
		if (empty($this->option['where'])) {
			return '条件不能为空';
		}
		if (!in_array($filed, $this->fileds)) {
			return '修改的字段不存在';
		}
		//money = money+10
		$this->option['set'] = "$filed=$filed+$value";
		$sql = "update %table% set %set% %where%";
		$sql = str_replace(
					[
						'%table%',
						'%set%',
						'%where%',
					], 
					[
						$this->option['table'],
						$this->option['set'],
						$this->option['where'],
					], 
					$sql);
		
		return $this->exec($sql);
	}
	public function find()
	{
		$re = $this->select();
		return $re[0];
	}
	public function priKey($value)
	{
		$filed = $this->fileds['_pk'];
		if (is_int($value)) {
			$this->option['where'] =  " where $filed=$value ";
		} else if (is_array($value)) {
			$value = join(',', $value);
			$this->option['where'] =  " where $filed in($value) ";
		} else if (is_string($value)) {
			$this->option['where'] =  " where $filed in($value) ";
		}
		return $this->select();
	}
	public function filed($fileds)
	{
		if (is_string($fileds)) {
			$this->option['fileds'] = $fileds;
		} else if (is_array($fileds)) {
			$this->option['fileds'] = join(',', $fileds);
		}
		return $this;
	}
	public function where($where)
	{
		if (is_string($where)) {
			$this->option['where'] = ' where ' . $where;
		} else if (is_array($where)) {
			$where = $this->checkInsert($where);
			foreach ($where as $key => $value) {
				$where[$key] = $key . '=' . $value;
			}
			$this->option['where'] = ' where ' . join(' and ', $where);
		}
		return $this;
	}
	public function order($order)
	{
		
		if (is_string($order)) {
			$this->option['order'] = ' order by ' . $order;
		} else if (is_array($order)) {
			$this->option['order'] = ' order by ' . join(',', $order);
		}
		return $this;
	}
	public function limit($limit)
	{
		if (is_string($limit)) {
			$this->option['limit'] = ' limit ' . $limit;
		} else if (is_array($limit)) {
			$this->option['limit'] = ' limit ' . join(',', $limit);
		}
		return $this;
	}
	public function group($group)
	{
		if (is_string($group)) {
			$this->option['group'] = ' group by ' . $group;
		} else if (is_array($group)) {
			$this->option['group'] = ' group by ' . join(',', $group);
		}
		return $this;
	}
	public function having($having)
	{
		if (is_string($having)) {
			$this->option['having'] = ' having ' . $having;
		} else if (is_array($having)) {
			$this->option['having'] = ' having ' . join(' and ', $having);
		}
		return $this;
	}
	public function insert($data)
	{
		if (!is_array($data)) {
			return '插入的数据参数要求是个数组呦，小伙子';
		}
		$data = $this->checkInsert($data);
		$this->option['fileds'] = join(',', array_keys($data));
		$this->option['values'] = join(',', $data);
		$sql = "insert into %table%(%fileds%) values(%values%)";
		$sql = str_replace(
					[
						'%table%',
						'%fileds%',
						'%values%',
					], 
					[
						$this->option['table'],
						$this->option['fileds'],
						$this->option['values'],
					], 
					$sql);
		return $this->exec($sql, true);

	}
	public function delete()
	{
		if (empty($this->option['where'])) {
			return '作死，没有条件删全表';
		}
		$sql = 'delete from %table% %where% %order% %limit%';
		$sql = str_replace(
					[
						'%table%',
						'%where%',
						'%order%',
						'%limit%',
					], 
					[
						$this->option['table'],
						$this->option['where'],
						$this->option['order'],
						$this->option['limit'],
					], 
					$sql);
		return $this->exec($sql);

	}

	public function update($data)
	{
		if (!is_array($data)) {
			return '拜拜了你类,要数组';
		}
		if (empty($this->option['where'])) {
			return '作死，没有条件改全表';
		}
		$this->option['set'] = $this->checkUpdate($data);//nickname='dfg',email='sdgh@qq.com'

		$sql = "update %table% set %set% %where%";
		$sql = str_replace(
					[
						'%table%',
						'%set%',
						'%where%',
					], 
					[
						$this->option['table'],
						$this->option['set'],
						$this->option['where'],
					], 
					$sql);
		return $this->exec($sql); 
	}
	public  function select($fileds = null)
	{
		if (!empty($fileds)) {
			$this->option['fileds'] = $fileds;
		}
		$sql = "select %fileds% from %table% %where% %order% %group% %having% %limit%";
		$sql = str_replace( 
					[
						'%table%',
						'%where%',
						'%order%',
						'%group%',
						'%having%',
						'%limit%',
						'%fileds%',
					], 
					[
						$this->option['table'],
						$this->option['where'],
						$this->option['order'],
						$this->option['group'],
						$this->option['having'],
						$this->option['limit'],
						$this->option['fileds'],
					], 
					$sql);
		return $this->query($sql, MYSQLI_ASSOC);
	}
	protected function exec($sql, $isInsert = false )
	{
		$this->sql = $sql;
		$this->option = $this->setOption();
		$result = mysqli_query($this->link, $sql);
		if ($result && $isInsert) {
			return mysqli_insert_id($this->link);//返回值是刚插入数据的主键
		}
		return $result;
	}
	protected function query($sql, $resultType = MYSQLI_BOTH)
	{
		$this->sql = $sql;
		$this->option = $this->setOption();//初始化参数
		$result = mysqli_query($this->link, $sql);
		if ($result) {
			return mysqli_fetch_all($result, $resultType);
		}

	}
	protected function checkUpdate($data)
	{
		//看看数组中的key在字段里就留下，不在就走你
		$fileds = array_flip($this->fileds);
		$data = array_intersect_key($data, $fileds);
		$data = $this->addQuotes($data);
		$realData = '';
		foreach ($data as $key => $value) {
			# code...nickname='dfg',email='sdgh@qq.com'
			$realData .= $key . '=' . $value . ',';
		}
		return rtrim($realData, ',');
	}
	protected function checkInsert($data)
	{
		//第一步：检查数组中key在不在表字段里
		$fileds = array_flip($this->fileds);//交换键值对
		$data = array_intersect_key($data, $fileds);//处理data数组留下与字段匹配的
		$data = $this->addQuotes($data);
		return $data;
	}
	//添加单引号
	protected function addQuotes($data)
	{
		if (is_array($data)) {
			foreach ($data as $key => $value) {
				if (is_string($value)) {
					$data[$key] = '\'' . $value . '\'';
				}
			}
		}
		return $data;
	}
	public function getLastSql()
	{
		return $this->sql;
	}
	//拼接表名
	protected function getTableName()
	{
		$className = get_class($this);
		//app\model\user;
		
		if ($pos = strrpos($className, '\\')) {
			$className = strtolower(substr($className, $pos + 1));
		}
		return $this->prefix . $className;
	}
	protected function connect()
	{
		$link = mysqli_connect($this->host, $this->user, $this->pwd);
		if (!$link) {
			return false;
		}
		if (!mysqli_select_db($link, $this->dbName)) {
			mysqli_close($link);
			return false;
		}
		if (!mysqli_set_charset($link, $this->charset)) {
			mysqli_close($link);
			return false;
		}
		return $link;
	}
	protected function checkCache($dir)
	{
		$dir = rtrim($dir, '/') . '/';
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);///cache/database/
		}
		if (!is_readable($dir) || !is_writeable($dir)) {
			chmod($dir, 0777);
		}
		return $dir;
	}
	protected function getFileds()
	{
		//拼接缓存文件路径
		$cacheFile = $this->cache . $this->tableName . '.php';
		if (file_exists($cacheFile)) {
			return include $cacheFile;
		} 
		//不存在缓存文件就创建文件并且将数组写入文件中
		$sql = 'desc ' . $this->tableName;
		$data = $this->query($sql,MYSQLI_ASSOC);
		$fileds = [];
		foreach ($data as $key => $value) {
			if ($value['Key'] == 'PRI') {
				$fileds['_pk'] = $value['Field'];
			}
			$fileds[] = $value['Field'];
		}
		$str = "<?php \n return " . var_export($fileds, true) . ';';
		file_put_contents($cacheFile, $str);
		return $fileds;
	}
}
