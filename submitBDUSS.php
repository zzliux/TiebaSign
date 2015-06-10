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
		function subBDUSS(){
			document.getElementById('info').innerHTML='更新中...请稍候....';
			var xmlhttp;
			var href = location.href.replace('submitBDUSS.php','');
			xmlhttp=new XMLHttpRequest();
			xmlhttp.open("POST",href+'/addBDUSS.php',true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send('bduss='+document.getElementById('bduss').value);
			xmlhttp.onreadystatechange=function(){
				if(xmlhttp.readyState==4 && xmlhttp.status==200){
					document.getElementById('info').innerHTML=xmlhttp.responseText;
				}
			}
		}
		</script>
	</head>
	<body>
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">提交BDUSS</div>
			<div class="panel-body">
				<div class="form-horizontal" role="form">
					<div class="form-group">
						<label for="input_user_name" class="col-sm-3 control-label">BDUSS</label>
						<div class="col-sm-9">
							<input type="text" id='bduss' class="form-control" id="input_user_name"placeholder="BDUSS(不带'BDUSS='和';')">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-9">
							<button type="submit" class="btn btn-primary btn-block" onclick="subBDUSS()">提交</button>
						</div>
					</div>
					<div id='info'></div>
				</div>
				<div class="col-sm-offset-3 col-sm-9">
					<a href="./">签到查询</a>&nbsp;&nbsp;<a href="refresh.php">更新贴吧</a><br><a href="BDUSSbyLogin.php">自动获取BDUSS</a>
				</div>
			</div>
		</div>
	</body>
	<footer>© 2015 <a href="http://www.zzliux.com" target="_blank">zzliux</a></footer>
	<!-- 感谢星弦雪大神提供的BaiduUtil和登录模板 -->
</html>
