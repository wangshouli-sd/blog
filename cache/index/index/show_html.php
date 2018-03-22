
<!DOCTYPE HTML>
<html>
<head>
<title>随风就是我的个人博客</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link href="<?php echo CSS_PATH;?>style.css" rel="stylesheet" type="text/css" media="all" />
<link rel="icon" href="<?php echo IMG_PATH;?>ico.png" type="image/x-icon"/>
<!-- start top_js_button -->
<script type="text/javascript" src="<?php echo JS_PATH;?>jquery.min.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>move-top.js"></script>
<script type="text/javascript" src="<?php echo JS_PATH;?>easing.js"></script>
   <script type="text/javascript">
		jQuery(document).ready(function($) {
			$(".scroll").click(function(event){		
				event.preventDefault();
				$('html,body').animate({scrollTop:$(this.hash).offset().top},1200);
			});
		});
	</script>
</head>
<body>
<!-- start header -->
<div class="header_bg">
<div class="wrap">
	<div class="header">
		<div class="logo">
			<a href="index.php?c=index&a=me"><img src="<?php echo IMG_PATH;?>logo.png" alt=""/> </a>
		</div>
		<?php if (empty($data)): ?>
		<div class="social-icons">
				<a href="index.php?c=index&a=index">随风首页</a>
				<a href="index.php?c=index&a=reslog">登录</a>
				<a href="index.php?c=index&a=reslog">注册</a>
				<a href="index.php?c=index&a=forget">忘记密码？</a>
		</div>
		<?php else: ?>
		<div class="social-icons">
		    <a href="index.php?c=index&a=index">随风首页</a>
			<a href="index.php?c=index&a=self"><?=$data[0]['username']; ?></a>
			<a href="#">个人中心</a>
			<a href="index.php?c=index&a=announce">写心情</a>
			<a href="index.php?c=index&a=collects">收藏</a>
			<a href="index.php?c=index&a=reset">修改密码</a>
			<a href="index.php?c=index&a=jump">退出</a>
		</div>
		<?php endif; ?>
		<div class="clear"></div>
	</div>
</div>
</div>
<!-- start header -->
<div class="header_btm">
<div class="wrap">
	<div class="header_sub">
		<div class="h_menu">
			<ul>
				<li class="active"><a href="index.php?c=index&a=index">首页</a></li>
				<li><a href="index.php?c=index&a=look">随便看看</a></li>
				<li><a href="index.php?c=index&a=me">个人听歌历程</a></li>
				<li><a href="index.php?c=index&a=show">精彩分享</a></li>
				<li><a href="index.php?c=index&a=announce">分享一刻</a></li>
			</ul>
		</div>
		<div class="h_search">
    		<form>
    			<input type="text" value="" placeholder="搜索">
    			<input type="submit" value="">
    		</form>
		</div>
        <div class="menu">
			<ul>
				<li class="active"><a href="index.php?c=index&a=index">首页</a></li>
				<li><a href="index.php?c=index&a=look">随便看看</a></li>
				<li><a href="index.php?c=index&a=me">个人听歌历程</a></li>
				<li><a href="index.php?c=index&a=show">精彩分享</a></li>
				<li><a href="index.php?c=index&a=announce">分享一刻</a></li>
			</ul>
        </div>
        <div class="search">
            <form action="/iphone/search.html">
                <input type="text" value="Search" onFocus="this.value = '';" onBlur="if (this.value == '') {this.value = 'Search';}" class="text">
            </form>
        </div>
        <div class="sub-head">
        	<ul>
            	<li><a href="#" id="menu">Menu  <span></span></a></li>
            	<li><a href="#" id="search">Search <span></span></a></li>
            </ul>
            <div class="clear"></div>
        </div>
	   <script type="text/javascript">
		$(".menu,.search").hide();
		$("#menu").click(function(){
			$(".menu").toggle();
			$(".search").hide();
			$("#search").removeClass("active");
			$("#menu").toggleClass("active");
		});
		$("#search").click(function(){
			$(".search").toggle();
			$(".menu").hide();
			$("#menu").removeClass("active");
			$("#search").toggleClass("active");
			$(".text").focus();
		});
	</script>
	<script type="text/javascript" src="<?php echo JS_PATH;?>script.js"></script>
	<div class="clear"></div>

		<div class="clear"></div>
</div>
</div>
</div>
<!-- start top_bg2 -->
<div class="top_bg2">
<div class="wrap">
	<div class="top">
		<h2>LIFE</h2>
 	</div>
</div>
</div>
<!-- start main -->
<div class="wrap">
<div class="main">
		<?php if (!empty($data_song)): ?>
		<?php foreach ($data_song as $key=>$value): ?>
		<div class="about" style="margin:20px 0px">
	       <div class="cont-grid">
	       <div class="abt-para">
	       	<span><?=$value['title']; ?></span>
	       	<p class="para"><?=$value['content']; ?></p>
			<div class="read_more">
				 <a class="btn" style="padding:6px 10px" href="index.php?c=index&a=details&tid=<?=$value['id']; ?>">点击阅读</a>
			</div>
	       	</div>
	      	 <div class="clear"></div>
	      	</div>
			 <div class="cont-grid-img img_style">
	     		<a href="index.php?c=index&a=details&tid=<?=$value['id']; ?>"><img src="<?=$value['icon']; ?>" alt=""></a>
	     	</div>	      	
	      	<div class="clear"></div>
		</div>
		<?php endforeach; ?>
		<?php endif; ?>
		<div style="margin-top:20px">
			<a class="btn" href="<?=$result['head']; ?>" style="padding:2px 4px">首页</a>
			<a class="btn" href="<?=$result['prev']; ?>" style="padding:2px 4px">上一页</a>
			<a class="btn" href="<?=$result['next']; ?>" style="padding:2px 4px">下一页</a>
			<a class="btn" href="<?=$result['last']; ?>" style="padding:2px 4px">尾页</a>
			<a class="btn" href="#" style="padding:2px 4px"><?=$page; ?>/<?=$count; ?></a>
		</div>
		<div class="clear"></div>
	</div>
</div>
<!-- start footer -->
<div class="footer_bg">
<div class="wrap">
	<div class="footer">
		<!-- start span_of_4 -->
		<div class="span_of_4">
			<div class="span1_of_4">
				<h4>网站信息</h4>
				<p><?php echo WEB_WEL;?></p>
				<ul class="f_nav1">
					<li class="timer"><a href="<?php echo WEB_URL;?>"><?php echo WEB_URL;?></a></li>
				</ul>
				<p class="top"><?php echo WEB_ICP;?></p>
				<ul class="f_nav1">
					<li class="timer"><a href="#"><?php echo date("Y-m-d H:i:s",time());?></a></li>
				</ul>
			</div>
			<div class="span1_of_4">
				<h4>标签</h4>
				<p><?php echo WEB_DESC;?></p>
			</div>
			<div class="span1_of_4">
				<h4>博主信息</h4>
				<p class="btm"><?php echo WEB_SIGN;?></p>

			</div>
			<div class="span1_of_4">
				<h4>博主联系方式</h4>
				<p class="btm"><?php echo WEB_TITLE;?></p>
				<p class="btm1 pin"><?php echo WEB_ADDR;?></p>
				<p class="btm1 mail"><a href="mailto:info@mycompany.com">邮箱:<?php echo WEB_EMAIL;?></a></p>
				<p class="call"><?php echo WEB_PHONE;?></p>
			</div>
			<div class="clear"></div>
		</div>
	</div>
</div>
</div>
<!-- start footer -->
<div class="footer_bg1">
<div class="wrap">
	<div class="footer1">
		<!-- scroll_top_btn -->
	    <script type="text/javascript">
			$(document).ready(function() {
			
				var defaults = {
		  			containerID: 'toTop', // fading element id
					containerHoverID: 'toTopHover', // fading element hover id
					scrollSpeed: 1200,
					easingType: 'linear' 
		 		};
				
				
				$().UItoTop({ easingType: 'easeOutQuart' });
				
			});
		</script>
		 <a href="#" id="toTop" style="display: block;"><span id="toTopHover" style="opacity: 1;"></span></a>
		<!--end scroll_top_btn -->
		<div class="social-icons">
		    <ul>
		      <li><a href="#" target="_blank"></a></li>
			  <li><a href="#" target="_blank"></a></li>
		      <li><a href="#" target="_blank"></a></li>
			  <li><a href="#" target="_blank"></a></li>
			</ul>
		</div>
|
		<div class="clear"></div>
	</div>
</div>
</div>
<div style="display:none"><script src='http://v7.cnzz.com/stat.php?id=155540&web_id=155540' language='JavaScript' charset='gb2312'></script></div>
</body>
</html>