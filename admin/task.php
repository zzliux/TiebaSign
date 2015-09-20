<?php
	session_start();
	if($_SESSION['admin']!=1){
		die('请<a href="./">登录</a>');
	}
	date_default_timezone_set('Asia/Shanghai');
	if(isset($_POST['sub'])){
		$re = array(
			'update' => $_POST['updateStartTime'].' '.$_POST['updateEndTime'],
			'refresh' => $_POST['refreshStartTime'].' '.$_POST['refreshEndTime'],
			'zhidao' => $_POST['zhidaoStartTime'].' '.$_POST['zhidaoEndTime'],
			'wenku' => $_POST['wenkuStartTime'].' '.$_POST['wenkuEndTime'],
			'zhidaoLuck' => $_POST['zhidaoLuckStartTime'].' '.$_POST['zhidaoLuckEndTime'],
			'tieba' => $_POST['tiebaStartTime'].' '.$_POST['tiebaEndTime'],
		);
		file_put_contents('../cronlog.php', json_encode($re));
	}
	$result = json_decode(file_get_contents('../cronlog.php'),true);
	foreach($result as $key => $value){
		$result[$key] = explode(' ',$value);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>贴吧签到托管-计划任务</title>
		<meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<style>
		body {
			padding-top: 10px;
			background-color: #eee;
		}
		footer,.form-panel{
			max-width: 380px;
			margin: 0 auto;
		}
		</style>
	</head>
	<body>
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">计划任务管理</div>
			<div class="panel-body">
			<p>当前任务:<?php echo getTask(date('H:i',time())) ?></p>
				<form class="form-horizontal" role="form" method="post">
					<div class="input-group">
						<span class="input-group-addon" style="width:120px">重置贴吧队列</span>
						<input type="text" class="form-control" name="updateStartTime" value="<?php echo $result['update'][0] ?>">
						<span class="input-group-addon">-</span>
						<input type="text" class="form-control" name="updateEndTime" value="<?php echo $result['update'][1] ?>">
					</div>
					<br>
					<div class="input-group">
						<span class="input-group-addon" style="width:120px">更新贴吧</span>
						<input type="text" class="form-control" name="refreshStartTime" value="<?php echo $result['refresh'][0] ?>">
						<span class="input-group-addon">-</span>
						<input type="text" class="form-control" name="refreshEndTime" value="<?php echo $result['refresh'][1] ?>">
					</div>
					<br>
					<div class="input-group">
						<span class="input-group-addon" style="width:120px">知道签到</span>
						<input type="text" class="form-control" name="zhidaoStartTime" value="<?php echo $result['zhidao'][0] ?>">
						<span class="input-group-addon">-</span>
						<input type="text" class="form-control" name="zhidaoEndTime" value="<?php echo $result['zhidao'][1] ?>">
					</div>
					<br>
					<div class="input-group">
						<span class="input-group-addon" style="width:120px">文库签到</span>
						<input type="text" class="form-control" name="wenkuStartTime" value="<?php echo $result['wenku'][0] ?>">
						<span class="input-group-addon">-</span>
						<input type="text" class="form-control" name="wenkuEndTime" value="<?php echo $result['wenku'][1] ?>">
					</div>
					<br>
					<div class="input-group">
						<span class="input-group-addon" style="width:120px">知道抽奖</span>
						<input type="text" class="form-control" name="zhidaoLuckStartTime" value="<?php echo $result['zhidaoLuck'][0] ?>">
						<span class="input-group-addon">-</span>
						<input type="text" class="form-control" name="zhidaoLuckEndTime" value="<?php echo $result['zhidaoLuck'][1] ?>">
					</div>
					<br>
					<div class="input-group">
						<span class="input-group-addon" style="width:120px">贴吧签到</span>
						<input type="text" class="form-control" name="tiebaStartTime" value="<?php echo $result['tieba'][0] ?>">
						<span class="input-group-addon">-</span>
						<input type="text" class="form-control" name="tiebaEndTime" value="<?php echo $result['tieba'][1] ?>">
					</div>

					<br>
					<button type="submit" class="btn btn-primary btn-block" name="sub">更改</button>
				</form>
			</div>
			&nbsp;&nbsp;&nbsp;&nbsp;<a href="./">用户管理</a>
		</div>
	</body>
	<footer>© 2015 <a href="http://www.zzliux.com" target="_blank">zzliux</a></footer>
	<!-- 感谢星弦雪大神提供的BaiduUtil和登录模板 -->
</html>
<?php
	function getTask($time){
		$re = json_decode(file_get_contents('../cronlog.php'),1);
		foreach($re as $key => $value){
			$t = explode(' ',$value);
			if(strtotime($time)>=strtotime($t[0])&&strtotime($time)<=strtotime($t[1])){
				return $key;
			}
		}
	}
?>
