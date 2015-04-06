<?php
	session_start();
	if($_SESSION['admin'] == 1){
		$flag = 1;
	}else{
		$flag = 0;
	}
	if(isset($_POST['psw'])){
		require_once('../install/config.php');
		if( md5($_POST['psw']) == ADMPSW ){
			$flag = 1;
			$_SESSION['admin'] = 1;
		}
	}
	if(isset($_POST['logout'])){
		$_SESSION['admin'] = 0;
		$flag = 0;
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>贴吧签到托管-用户管理</title>
		<meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../css/bootstrap.min.css">
		<style>
		body {
			padding-top: 80px;
			background-color: #eee;
		}
		.form-panel{
			max-width: 330px;
			margin: 0 auto;
		}
		button {
			max-width: 270px;
			margin-right:0px auto;
		}
		</style>
	</head>
	<body>
	<?php 
		if ($flag==0){
			echo <<<aaa
			<div class="panel panel-primary form-panel">
				<div class="panel-heading">用户管理</div>
				<div class="panel-body">
					<form class="form-horizontal" role="form" method="post">
						<div class="form-group">
							<label for="input_user_name" class="col-sm-3 control-label">密码</label>
							<div class="col-sm-9">
								<input type="password" class="form-control" name="psw" placeholder="管理员密码">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-9">
							<button type="submit" class="btn btn-primary btn-block">登录</button>
						</div>
					</form>
				</div>
			</div>
aaa;
		}else{
			require_once("../install/config.php");
			$DB = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
			if($DB->connect_errno){
				die($DB->connect_error);
			}
			$DB->query("SET NAMES utf8");
			$sql = "select * from info";
			$result = $DB->query($sql);
			echo '<div class="panel panel-primary form-panel" style="max-width:500px">
				<div class="panel-heading">用户管理</div>
				<div class="panel-body"><table class="table" style="max-width:500px;margin:0px auto;"><thead><th>#</th><th>ID</th><th>成功数</th><th>失败数</th><th>队列数</th><th>总数</th></thead>';
			$t=0;
			while($row = $result->fetch_assoc()){
				$t++;
				$sql = "select * from tieba where uid = {$row[uid]}";
				$result_ = $DB->query($sql);
				for($i = 0, $q = 0, $y = 0, $f = 0; $row_ = $result_->fetch_assoc();$i++){
					if($row_['is_sign'] == 0){
						$q++;
					}else if($row_['is_sign']==1||$row_['is_sign']==2){
						$y++;
					}else{
						$f++;
					}
				}
				echo "<tr><th>{$t}</th><th>{$row[un]}</th><th>{$y}</th><th>{$f}</th><th>{$q}</th><th>{$i}</th></tr>";
			}
			echo '</table>';
			echo <<<aaa
			
					<form class="form-horizontal" role="form" method="post">
						<button type="submit" name="logout" class="btn btn-primary btn-block" style="width:100px">退出登录</button>
					</form>
				</div>
			</div>
aaa;
		}
	?>
	</body>
</html>
