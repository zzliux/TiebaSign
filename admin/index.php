<?php
	session_start();
	if(isset($_POST['query'])){
		header('location:../query.php?un='.$_POST['query']);
	}
	if(isset($_POST['refresh'])){
		header('location:../refreshTieba.php?un='.$_POST['refresh']);
	}
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
		<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<style>
		body {
			padding-top: 10px;
			background-color: #eee;
		}
		.form-panel{
			max-width: 330px;
			margin: 0 auto;
		}
		button {
			width: 100px;
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
				<div class="panel-body"><table class="table" s
				tyle="max-width:500px;margin:0px auto;"><thead><th>#</th><th>ID</th><th>成功数</th><th>失败数</th><th>队列数</th><th>总数</th></thead>';
			if(isset($_GET['deun'])){
				echo "
					<form class=\"form-horizontal\" role=\"form\" method=\"post\">
						<span><font color=\"red\">{$_GET[deun]}</font>操作
						<div class=\"btn-group\">
							<button type=\"submit\" name=\"query\" value=\"{$_GET[deun]}\" class=\"btn btn-success\">签到情况</button>
							<button type=\"submit\" name=\"refresh\" value=\"{$_GET[deun]}\" class=\"btn btn-primary\">刷新贴吧</button>
							<button type=\"submit\" name=\"deun\" value=\"{$_GET[deun]}\" class=\"btn btn-danger\">删除</button>
						</div>
					</form>
";
			}
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
				echo "<tr><th>{$t}</th><th><a href=\"?deun={$row[un]}\">{$row[un]}</a></th><th>{$y}</th><th>{$f}</th><th>{$q}</th><th>{$i}</th></tr>";
			}
			echo '</table>';
			echo <<<aaa
			
					<form class="form-horizontal" role="form" method="post">
						<button type="submit" name="logout" class="btn btn-primary btn-block" style="width:100px">退出登录</button>
					</form>
aaa;
			if(isset($_POST['deun'])){
				$deun = $_POST['deun'];
				$sql = "SELECT * FROM info WHERE un = '{$deun}'";
				$result = $DB->query($sql);
				if($row = $result->fetch_assoc()){
					$sql = "DELETE FROM info WHERE uid = $row[uid]";
					$DB->query($sql);
					$sql = "DELETE FROM tieba WHERE uid = $row[uid]";
					$DB->query($sql);
					header('location:./');
				}
			}
			echo '</div>&nbsp;&nbsp;&nbsp;&nbsp;<a href="task.php">计划任务</a></div>';
		}
	?>
	</body>
	<footer style="margin:0px auto;max-width:<?php if($flag) echo '500px'; else echo '330px'; ?>">© 2015 <a href="http://www.zzliux.tk">zzliux</a></footer>
</html>
