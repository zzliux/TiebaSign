<?php
	require_once('install/config.php');
	$DB = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
	if($DB->connect_errno){
		die($DB->connect_error);
	}
	$DB->query('SET NAMES utf8');
	$userTotolNumObj = $DB->query('select count(uid) as totol from info');
	$userTotolNum = $userTotolNumObj->fetch_assoc()['totol'];
	if(isset($_COOKIE['user'])){
		$name=$DB->real_escape_string($_GET['un']);
	}
	if(isset($_GET['un'])){
		$name=$DB->real_escape_string($_GET['un']);
	}
	if(isset($_POST['resetTieba'])){
		$resName = $_POST['resetTieba'];
		$sql = "SELECT * FROM info WHERE un = '{$resName}'";
		$result = $DB->query($sql);
		$row = $result->fetch_assoc();
		$sql = "UPDATE tieba SET is_sign = '0' WHERE uid = {$row['uid']}";
		$DB->query($sql);
		header('location:./?un='.$name);
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>贴吧签到托管-签到情况查询</title>
		<meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="//cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<link href="//cdn.bootcss.com/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet">
		<style>
		body {
			padding-top: 10px;
			padding-left:10px;
			padding-right:10px;
			background-color: #eee;
		}
		footer,.form-panel{
			max-width: 350px;
			margin: 0 auto;
		}
		.totolNum{
			margin-bottom: 10px;
		}
		.fa-check{
			color:#008000;
		}
		.fa-times{
			color:#A00000;
		}
		</style>
		<script>
			function send(){
				var btn = document.getElementsByTagName("button")[0];
				btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
				btn.disabled = true;
				return true;
			}
		</script>
	</head>
	<body>
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">签到查询</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="get" onsubmit="return send()">
					<div class="form-group">
						<label for="input_user_name" class="col-sm-3 control-label">用户名</label>
						<div class="col-sm-9">
							<input type="text" class="form-control" id="input_user_name" name="un" placeholder="仅可以使用百度ID" value="<?php echo htmlentities($_GET['un']) ?>">
						</div>
					</div>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-9">
							<button type="submit" class="btn btn-primary btn-block">查询</button>
						</div>
					</div>
				</form>
				<div class="totolNum col-sm-offset-3 col-sm-9">
					总计有<?php echo $userTotolNum ?>名用户加入本签到站
				</div>
				<div class="form-group">
					<div class="col-sm-offset-3 col-sm-9">
						<a href="./refresh.php?un=<?php echo $name ?>">更新贴吧</a>&nbsp;&nbsp;<a href="submitBDUSS.php">提交BDUSS</a>
					</div>
				</div>
		<?php
			if(!empty($name)){
				$sql="select * from info where un='{$name}'";
				$result=$DB->query($sql);
				$row=$result->fetch_assoc();
				$fff = true;
				if(empty($row['uid'])){
					$fff = false;
					echo '<font color="red">本站木有这个用户哟~~</font>&nbsp;&nbsp;点击提交BDUSS加入本签到站';
				}
				$is_sign_zhidao = $row['is_sign_zhidao']?'fa fa-check':'fa fa-times';
				$is_sign_wenku = $row['is_sign_wenku']?'fa fa-check':'fa fa-times';
				setcookie('user',$name,time()+60*60*24*30);
				session_start();
				if($_SESSION['admin']==1){
					setcookie('user', '', time()-3600);
				}
				$sql="select * from tieba where uid={$row[uid]}";
				$result=$DB->query($sql);
				$i=1;
				$y=0;
				if($fff){
					while($row=$result->fetch_assoc()){
						$re[$i]['tieba']=$row['tieba'];
						switch($row['is_sign']){
							case 0: $re[$i]['is_sign']='<font color="#FFA500">Queueing</font>'; break;
							case 1: $re[$i]['is_sign']='<font color="#008000">Yes</font>'; $y++; break;
							case 2: $re[$i]['is_sign']='<font color="#008000">Signed</font>'; $y++; break;
							case 3: case 5:$re[$i]['is_sign']='<font color="#FF0000">Unknown</font>'; break;
							case 4: $re[$i]['is_sign']='<font color="#FF0000">Too fast</font>'; break;
						}
						$i++;
					}
				}
				$i--;
				if($fff){
					echo "<pre style=\"max-width:160px\">贴吧:<font color=\"#008000\"><b>{$y}</b></font>/<font color=\"#FFA500\"><b>{$i}</b></font><br>知道:<i class=\"".($is_sign_zhidao)."\"></i><br>文库:<i class=\"".($is_sign_wenku)."\"></i></pre>";
					echo "<form method=\"post\"><button class=\"btn btn-danger\" name=\"resetTieba\" value=\"{$name}\">重置贴吧</button></from>";
					echo '<table class="table" style="max-width:330px;margin:0px auto;">';
					echo '<thead><th>#</th><th>贴吧</th><th>status</th>';
					for($i=1;isset($re[$i]);$i++){
						echo '<thead><th>'.$i.'</th><th>'.$re[$i]['tieba'].'</th><th>'.$re[$i]['is_sign'].'</th></thead>';
					}
					echo '</table>';
				}
			}
			$DB->close();
		?>
				<div class="col-sm-offset-3 col-sm-9" style="margin-top:20px">
				<?php
				if(!$_GET['un'])
					echo file_get_contents('admin/board.txt');
				?>
				</div>
			</div>
		</div>
	</body>
	<footer>© 2015 <a href="http://www.zzliux.com" target="_blank">zzliux</a><?php echo file_get_contents("tongji.txt") ?></footer>
	<!-- 感谢星弦雪大神提供的BaiduUtil和登录模板 -->
</html>
