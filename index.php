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
		.form-panel{
			max-width: 330px;
			margin: 0 auto;
		}
		</style>
	</head>
	<body>
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">提交BDUSS</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="post" action="addBDUSS.php">
					<div class="form-group">
						<label for="input_user_name" class="col-sm-3 control-label">BDUSS</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="input_user_name" name="bduss" placeholder="BDUSS(不带'BDUSS='和';')">
						</div>
					</div>
					<button type="submit" class="btn btn-primary btn-block">提交</button>
				</form>
				<br>
				<a href="query.php">签到查询</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="refresh.php">更新贴吧</a>
			</div>
		</div>
	</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> origin/master
