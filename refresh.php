<!DOCTYPE html>
<html>
	<head>
		<title>贴吧签到托管-提交BDUSS</title>
		<meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<style>
		body {
			padding-top: 80px;
			background-color: #eee;
		}
		footer,.form-panel{
			max-width: 330px;
			margin: 0 auto;
		}
		</style>
		<script>
		function subName(){
			document.getElementById('info').innerHTML='更新中...请稍候....';
			document.getElementById('btn').disabled=true;
			var xmlhttp;
			var href = location.href.replace(new RegExp('refresh.php((\.+)?)',''),'');
			xmlhttp=new XMLHttpRequest();
			xmlhttp.open("POST",href+'/refreshTieba.php',true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send('un='+document.getElementById('un').value);
			xmlhttp.onreadystatechange=function(){
				if(xmlhttp.readyState==4 && xmlhttp.status==200){
					document.getElementById('info').innerHTML=xmlhttp.responseText;
					document.getElementById('btn').disabled=false;
				}
			}
		}
		</script>
	</head>
	<body>
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">更新贴吧</div>
			<div class="panel-body">
				<div class="form-horizontal" role="form" method="post" action="refreshTieba.php">
					<div class="form-group">
						<label for="input_user_name" class="col-sm-3 control-label">用户名</label>
						<div class="col-sm-9">
							<input id="un" type="text" class="form-control" id="input_user_name" placeholder="仅可使用百度ID" value="<?php echo $_GET[un] ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-9">
							<button type="submit" id="btn" class="btn btn-primary btn-block" onclick="subName()">提交</button>
						</div>
					</div>
				</div>
				<div id="info"></div>
				<div class="col-sm-offset-3 col-sm-9">
					<a href="./">签到查询</a>
				</div>
			</div>
		</div>
	</body>
	<footer>© 2015 <a href="http://www.zzliux.com" target="_blank">zzliux</a></footer>
	<!-- 感谢星弦雪大神提供的BaiduUtil和登录模板 -->
</html>
