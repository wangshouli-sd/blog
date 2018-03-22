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
<form method="post" action="index.php?m=admin&c=index&a=user">
  <div class="panel admin-panel">
    <div class="panel-head"><strong class="icon-reorder"> 用户管理</strong></div>
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
			<li style="width:50px;">&nbsp;</li>
			<li style="width:150px;">用户名</li>
			<li style="width:115px;">性别</li>
			<li style="width:200px;">邮箱</li>
			<li style="width:115px;">积分</li>
			<li style="width:160px;">注册时间</li>
			<li style="width:115px;">是否锁定</li>
			<li style="width:130px;">IP</li>
        </ul>
		<?php foreach ($data as $key=>$value): ?>
		<ul class="search">
			<li style="width:50px;"><input type="checkbox" name="id[]" value="<?=$value['id']; ?>" /></li>
			<li style="width:150px;"><?=$value['username']; ?></li>
			<?php if ($value['sex']=='1'): ?>
			<li style="width:115px;">男</li>
			<?php elseif ($value['sex']=='2'): ?>
			<li style="width:115px;">女</li>
			<?php else: ?>
			<li style="width:115px;">保密</li>
			<?php endif; ?>
			<li style="width:200px;"><?=$value['email']; ?></li>
			<li style="width:115px;"><?=$value['score']; ?></li>
			<li style="width:160px;"><?php echo date("Y-m-d H:i:s",$value['regtime']);?></li>
			<?php if ($value['islock']=='0'): ?>
			<li style="width:115px;"><a href="index.php?m=admin&c=index&a=lock&id=<?=$value['id']; ?>&islock=1">锁定</a></li>
			<?php else: ?>
			<li style="width:115px;"><a href="index.php?m=admin&c=index&a=lock&id=<?=$value['id']; ?>&islock=0">解锁</a></li>
			<?php endif; ?>
			<li style="width:130px;"><?php echo long2ip($value['ip']);?></li>
        </ul>
		<?php endforeach; ?>
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
</body></html>