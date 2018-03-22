<?php
namespace framework;

class Template
{
	protected $tplPath;//模板文件路径
	protected $cachePath;//缓存文件
	protected $vars;
	protected $validTime;
	public function __construct($tplPath = './view/', $cachePath = './cache/template', $validTime = 3600)
	{
		$this->tplPath = $this->checkPath($tplPath);
		$this->cachePath = $this->checkPath($cachePath);
		$this->validTime = $validTime;
	}
	//检查目录
	protected function checkPath($dir)
	{
		$dir = rtrim($dir, '/') . '/';
		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}
		if (!is_writeable($dir) || !is_readable($dir)) {
			chmod($dir, 0777);
		}
		return $dir;
	}
	//分配变量
	public function assign($name,$value)
	{
		$this->vars[$name] = $value;
	}
	public function display($tplFile, $isInclude = true)
	{
		$cacheFile = $this->getCacheFile($tplFile);//./cache/te
		$tplFile = $this->tplPath . $tplFile;//拼接模板文件路径 ./view/index.html
		if (!file_exists($tplFile)) {
			exit($tplFile . '模板文件不存在');
		}
		//缓存文件不存在的时候  缓存文件的修改时间<模板文件的修改时间  缓存文件的修改时间<3600
		if (!file_exists($cacheFile) ||
			 filemtime($cacheFile) < filemtime($tplFile)  ||
			 (filemtime($cacheFile) + $this->validTime) < time()
			) {
			$con = $this->compile($tplFile);
			$this->checkPath(dirname($cacheFile));
			file_put_contents($cacheFile, $con);
		} else {
			//更新include文件-->index.html-->include header.html
			$this->updateInclude($tplFile);//index.html
		}
		
		if (!empty($this->vars)) {
			extract($this->vars);
		}
		if ($isInclude) {
			include $cacheFile;
		}
	}
	protected function updateInclude($tplFile)
	{
		$con = file_get_contents($tplFile);
		$reg = '/\{include (.+)\}/U';
		if (preg_match_all($reg, $con, $matches)) {
			//header.html
			$this->display($matches[1][0], false);
		}
	}

	protected function getCacheFile($tplFile)
	{
		return $this->cachePath . str_replace('.', '_', $tplFile) . '.php';
	}
	protected function compile($tplFile)
	{
		$content = file_get_contents($tplFile);
		$keys = [
			'__%%__'		 => '<?php echo \1;?>',
			'{include %%}'	 =>  "这是假的",
			'${%%}'			 =>  '<?php echo \1;?>',
			'{$%%}' 		 =>  '<?=$\1; ?>',
			'_$%%_' 		 =>  '<?=$\1; ?>',
			'{if %%}'		 =>  '<?php if (\1): ?>',
			'{elseif %%}'	 =>  '<?php elseif (\1): ?>',
			'{else}' 		 =>  '<?php else: ?>',
			'{/if}'  		 =>  '<?php endif; ?>',
			'{switch %% case %%}' => '<?php switch(\1): case \2: ?>',
			'{case %%}'  		  => '<?php case \1:?>',
			'{break}'    		  => '<?php break;?>',
			'{/switch}'  		  => '<?php endswitch;?>',
			'{continue}'     =>  '<?php continue; ?>',
			'{default}'   	 =>  '<?php default: ?>',
			'{/switch}'    	 =>  '<?php endswitch; ?>',	
			'{foreach %%}'	 =>  '<?php foreach (\1): ?>',	
			'{/foreach}'  	 =>  '<?php endforeach; ?>',
			'{while %%}'  	 =>  '<?php while (\1): ?>',
			'{/while}'     	 =>  '<?php endwhile; ?>',
			'{for %%}'     	 =>  '<?php for (\1): ?>',
			'{/for}'      	 =>  '<?php endfor; ?>',
			];
		foreach ($keys as $key => $value) {
			$key = preg_quote($key, '#');
			$reg = '#' . str_replace('%%', '(.+)', $key)  . '#U';
			if (stripos($key, 'include')){
				$content = preg_replace_callback ($reg, [$this, 'includeFile'], $content);
			}
			else{
				$content = preg_replace ($reg, $value, $content);
			}
		}
		return $content;
	}
	protected function includeFile($matchs)
	{
		$this->display($matchs[1], false);//header.html
		$cacheFile = $this->getCacheFile($matchs[1]);//./cache/template/header_html.php
		return "<?php include '$cacheFile'; ?>";
	}
	public function clearCache()
	{
		$this->clearDir($this->cachePath);
	}
	protected function clearDir($dir)
	{
		$dir = rtrim($dir,'/') . '/';
		$dp = opendir($dir);
		while ($file = readdir($dp)) {

			if ($file == '.' || $file == '..') {
				continue;
			}
			$fileName = $dir . $file; 
			//echo $fileName;
			if (is_dir($fileName)) {
				$this->clearDir($fileName);
			} else {
				unlink($fileName);
			}
		}
		closedir($dp);
		rmdir($dir);
	}
}
