<?php
	session_start();
	if(isset($_POST['query'])){
		header('location:../?un='.$_POST['query']);
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
		#id {
			word-wrap:break-word;
			max-width:100px;
		}
		</style>
		<script>
		function operate(name){
			javascript:scroll(0,0);
			var out = '<pre><div class="form-horizontal" role="form" method="post"><span><font color="red">'+name+'</font>操作<div class="btn-group"><button type="submit" name="query" value="'+name+'" class="btn btn-success"><a href="../?un='+name+'"><font color="white">签到情况</font></a></button><input type="hidden" id="opn" value="'+name+'"><button type="submit" name="refresh" value="'+name+'" class="btn btn-primary" onclick="refresh()">刷新贴吧</button><button type="submit" name="deun" value="'+name+'" class="btn btn-danger" onclick="deleteu(false)">删除</button></div></div></pre>';
			document.getElementById('info').innerHTML=out;
		}
		function refresh(){
			var opn = document.getElementById('opn').value;
			document.getElementById('info').innerHTML='更新中...请稍后...';
			var xmlhttp = new XMLHttpRequest();
			var url = location.href.replace(new RegExp('/admin(/index.php)?',''),'');
			xmlhttp.open('post',url+'/refreshTieba.php',true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlhttp.send('un='+opn);
			xmlhttp.onreadystatechange=function(){
				if(xmlhttp.readyState==4 && xmlhttp.status==200){
					document.getElementById('info').innerHTML=xmlhttp.responseText;
				}
			}
		}
		function deleteu(flag){
			var opn = document.getElementById('opn').value;
			if(flag){
				var xmlhttp = new XMLHttpRequest();
				xmlhttp.open('post',location.href,true);
				xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
				xmlhttp.send('deun='+opn);
				location.reload(true);
			}else{
				var out = '<input type="hidden" id="opn" value="'+opn+'"><pre>用户<font color="red">'+opn+'</font>确认删除?<br><div class="btn-group"><button class="btn btn-danger" onclick="deleteu(true)">确认</button><button class="btn btn-primary" onclick="operate(\''+opn+'\')">取消</button></div></pre>';
				document.getElementById('info').innerHTML=out;
			}
		}
		</script>
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
			/* 统计队列数 */
			$sql = 'select count(*) AS queueing from tieba where is_sign=0';
			$result=$DB->query($sql);
			$re = $result->fetch_assoc();
			$res['queueing'] = $re['queueing'];

			/* 统计成功数 */
			$sql = 'select count(*) AS signed from tieba where is_sign=1 or is_sign=2';
			$result=$DB->query($sql);
			$re = $result->fetch_assoc();
			$res['signed'] = $re['signed'];

			/* 统计失败数 */
			$sql = 'select count(*) AS failed from tieba where is_sign=3 or is_sign=4';
			$result=$DB->query($sql);
			$re = $result->fetch_assoc();
			$res['failed'] = $re['failed'];

			/* 统计总数 */
			$sql = 'select count(*) AS total from tieba';
			$result=$DB->query($sql);
			$re = $result->fetch_assoc();
			$res['total'] = $re['total'];

			$sql = "select * from info";
			$result = $DB->query($sql);
			
			echo '<div class="panel panel-primary form-panel" style="max-width:500px">
				<div class="panel-heading">用户管理</div>
				<div class="panel-body"><table class="table" s
				tyle="max-width:500px;margin:0px auto;"><thead><th>#</th><th>ID</th><th>成功数('.$res['signed'].')</th><th>失败数('.$res['failed'].')</th><th>队列数('.$res['queueing'].')</th><th>总数('.$res['total'].')</th></thead>';
			echo '<div id="info"></div>';
			if(isset($_GET['query']) && $_GET['query']=='refreshAllTieba'){
				$sql = 'UPDATE tieba SET is_sign = 0';
				$DB->query($sql);
				echo '<font color="red">重置贴吧成功</font>';
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
				echo "<tr><td>{$t}</td><td id=\"id\"><a onclick=\"operate('{$row[un]}')\">{$row[un]}</a></td><td>{$y}</td><td>{$f}</td><td>{$q}</td><td>{$i}</td></tr>";
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
	<footer style="margin:0px auto;max-width:<?php if($flag) echo '500px'; else echo '330px'; ?>">© 2015 <a href="http://www.zzliux.com">zzliux</a></footer>
</html>
