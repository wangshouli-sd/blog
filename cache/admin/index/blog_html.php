<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <title></title>  
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>pintuer.css">
    <link rel="stylesheet" href="<?php echo CSS_PATH;?>admin.css">
    <script src="<?php echo JS_PATH;?>jquery.js"></script>
    <script src="<?php echo JS_PATH;?>pintuer.js"></script>  
</head>
<body>
<form method="post" action="index.php?m=admin&c=index&a=blog">
  <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 博客管理</strong></div>
    <div class="padding border-bottom">
      <ul class="search">
        <li>
          <button type="button"  class="button border-green" id="checkall"><span class="icon-check"></span> 全选</button>
          <button type="submit" name="tj" class="button border-red"><span class="icon-trash-o"></span> 批量删除</button>
        </li>
      </ul>
    </div>
    <div class="table table-hover text-center">
      <div>     
        <ul class="search">
			<li style="width:150px;height:100px;line-height:100px;">&nbsp;</li>
			<li style="width:115px;height:100px;line-height:100px;">封面</li>
			<li style="width:350px;height:100px;line-height:100px;">主题</li>
			<li style="width:200px;height:100px;line-height:100px;">歌名</li>
			<li style="width:115px;height:100px;line-height:100px;">歌手</li>
			<li style="width:115px;height:100px;line-height:100px;">标签</li>
			<li style="width:115px;height:100px;line-height:100px;">点赞数</li>
			<li style="width:250px;height:100px;line-height:100px;">发表时间</li>
        </ul>
		<?php if (!empty($data)): ?>
		<?php foreach ($data as $key=>$value): ?>
		<ul class="search">
			<li style="width:150px;height:100px;line-height:100px;"><input type="checkbox" name="id[]" value="<?=$value['id']; ?>" /></li>
			<li style="width:115px;height:100px;line-height:100px;"><img src="<?=$value['icon']; ?>" width="115px" height="80px" style="margin:10px 0px"/></li>
			<li style="width:350px;height:100px;line-height:100px;"><?=$value['title']; ?></li>
			<li style="width:200px;height:100px;line-height:100px;"><?=$value['song_name']; ?></li>
			<li style="width:115px;height:100px;line-height:100px;"><?=$value['singer']; ?></li>
			<li style="width:115px;height:100px;line-height:100px;"><?=$value['label']; ?></li>
			<li style="width:115px;height:100px;line-height:100px;"><?=$value['praise']; ?></li>
			<li style="width:250px;height:100px;line-height:100px;"><?php echo date("Y-m-d H:i:s",$value['time']);?></li>
        </ul>
		<?php endforeach; ?>
		<?php endif; ?>
		<div class="pagelist"><a href="<?=$result['head']; ?>">首页</a><a href="<?=$result['prev']; ?>">上一页</a><a href="<?=$result['next']; ?>">下一页</a><a href="<?=$result['last']; ?>">尾页</a> <a href="#"><?=$page; ?>/<?=$count; ?></a></div>
    </div>
  </div>
</form>
<script type="text/javascript">

function del(id){
	if(confirm("您确定要删除吗?")){
		
	}
}

$("#checkall").click(function(){ 
  $("input[name='id[]']").each(function(){
	  if (this.checked) {
		  this.checked = false;
	  }
	  else {
		  this.checked = true;
	  }
  });
})

function DelSelect(){
	var Checkbox=false;
	 $("input[name='id[]']").each(function(){
	  if (this.checked==true) {		
		Checkbox=true;	
	  }
	});
	if (Checkbox){
		var t=confirm("您确认要删除选中的内容吗？");
		if (t==false) return false; 		
	}
	else{
		alert("请选择您要删除的内容!");
		return false;
	}
}

</script>
</script>
</body></html>