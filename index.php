<?php
	require_once('install/config.php');
	$DB = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
	if($DB->connect_errno){
		die($DB->connect_error);
	}
	$DB->query('SET NAMES utf8');
	if(isset($_COOKIE['user'])){
		$name=$_COOKIE['user'];
	}
	if(isset($_GET['un'])){
		$name=$_GET['un'];
	}
	if(!empty($name)){
		$sql="select * from info where un='{$name}'";
		$result=$DB->query($sql);
		$row=$result->fetch_assoc();
		if(empty($row['uid'])){
			die('本站木有这个用户哟~<a href="./">返回</a>~');
		}
		setcookie('user',$name,time()+60*60*24*30);
		session_start();
		if($_SESSION['admin']==1){
			setcookie('user', '', time()-3600);
		}
		$sql="select * from tieba where uid={$row[uid]}";
		$result=$DB->query($sql);
		$i=1;
		while($row=$result->fetch_assoc()){
			$re[$i]['tieba']=$row['tieba'];
			switch($row['is_sign']){
				case 0: $re[$i]['is_sign']='<font color="orange">Queueing</font>'; break;
				case 1: $re[$i]['is_sign']='<font color="green">Yes</font>'; break;
				case 2: $re[$i]['is_sign']='<font color="green">Signed</font>'; break;
				case 3: $re[$i]['is_sign']='<font color="red">Unknown</font>'; break;
				case 4: $re[$i]['is_sign']='<font color="red">Too fast</font>'; break;
			}
			$i++;
		}
	}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>贴吧签到托管-签到情况查询</title>
		<meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<style>
		body {
			padding-top: 10px;
			background-color: #eee;
		}
		footer,.form-panel{
			max-width: 330px;
			margin: 0 auto;
		}
		</style>
	</head>
	<body>
		<?php
			echo '<table class="table" style="max-width:330px;margin:0px auto;">';
			echo '<thead><th>#</th><th>贴吧</th><th>status</th>';
			for($i=1;isset($re[$i]);$i++){
				echo '<thead><th>'.$i.'</th><th>'.$re[$i]['tieba'].'</th><th>'.$re[$i]['is_sign'].'</th></thead>';
			}
			echo '</table>';
		?>
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">签到查询</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="get">
					<div class="form-group">
						<label for="input_user_name" class="col-sm-3 control-label">用户名</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="input_user_name" name="un" placeholder="仅可以使用百度ID" value="<?php echo $_GET[un] ?>">
						</div>
					</div>
					<button type="submit" class="btn btn-primary btn-block">查询</button>
				</form>
				<br><a href="./refresh.php?un=<?php echo $_GET[un] ?>">更新贴吧</a>&nbsp;&nbsp;&nbsp;<a href="submitBDUSS.php">提交BDUSS</a>
			</div>
		</div>
	</body>
	<footer>© 2015 <a href="http://www.zzliux.tk" target="_blank">zzliux</a></footer>
	<!-- 感谢星弦雪大神提供的BaiduUtil和登录模板 -->
</html>
