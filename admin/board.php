<?php
session_start();
if(!$_SESSION['admin']){
	header("location:.");
}
if($_POST['change']){
	file_put_contents('board.txt', $_POST['contents']);
	$out = '<font color="#f00">修改成功</font>';
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>贴吧签到托管-公告管理</title>
	<meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<link href="//cdn.bootcss.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
	<style>
	body {
		padding-top: 10px;
		background-color: #eee;
	}
	footer,.form-panel{
		max-width: 380px;
		margin: 0 auto;
	}
	textarea.form-control{
		width: 95%;
		height: 300px;
		margin:10px 10px 10px 10px;
	}
	.btn-primary,#info,#a{
		width:95%;
		margin:0px 10px 10px 10px;
	}
	</style>
	<script>
		function check(){
			var btn = document.getElementById("btn");
			btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
			btn.disabled=true;
			return true;
		}
	</script>
</head>
<body>
	<div class="panel panel-primary form-panel">
		<div class="panel-heading">公告管理</div>
		<form method="post" onsubmit="return check()">
			<textarea class="form-control" name="contents"><?php echo file_get_contents('board.txt') ?></textarea>
			<input type="hidden" name="change" value="true">
			<div id="info">
				<?php echo $out ?>
			</div>
			<button type="submit" class="btn btn-primary" id="btn">修改</button>
		</form>
		<a href="." id="a">返回</a>
	</div>
	<footer>© 2015 <a href="http://www.zzliux.com" target="_blank">zzliux</a></footer>
</body>
</html>