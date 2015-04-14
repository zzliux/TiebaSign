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
	</head>
	<body>
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">更新贴吧</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="post" action="refreshTieba.php">
					<div class="form-group">
						<label for="input_user_name" class="col-sm-3 control-label">用户名</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="input_user_name" name="un" placeholder="仅可使用百度ID" value="<?php echo $_GET[un] ?>">
						</div>
					</div>
					<button type="submit" class="btn btn-primary btn-block">提交</button>
				</form>
				<br>
				<a href="query.php">签到查询</a>
			</div>
		</div>
	</body>
	<footer>© 2015 <a href="http://www.zzliux.tk" target="_blank">zzliux</a></footer>
	<!-- 感谢星弦雪大神提供的BaiduUtil和登录模板 -->
</html>
