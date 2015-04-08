<?php
	require_once ('BaiduUtil.php');
	if($_POST){
		session_start();
		foreach ($_POST as &$data) {
			$data=trim($data);
		}
		$username=$_POST['username'];
		$password=$_POST['password'];
		@$vcode=$_POST['vcode'];
		try{
			$client =  json_decode('{"_client_id":"wappc_1386816224047_167","_client_type":1,"_client_version":"6.0.1","_phone_imei":"a6ca20a897260bb1a1529d1276ee8176","cuid":"96D360F8BCF3AF6DA212A1429F6B2D75|046284918454666","model":"M1"}',true);
			$utl=new BaiduUtil(NULL,$client);
			if(empty($vcode)){
				$result=$utl->login($username,$password);
			}else{
				$result=$utl->login($username,$password,$vcode,$_SESSION['vcode_md5']);
			}
		}catch(exception $e){
		}
		switch ($result['status']) {
			case 0:
				require_once('install/config.php');
				$DB = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
				if($DB->connect_errno){
					die($DB->connect_error);
				}
				$DB->query('SET NAMES utf8');
				if(!empty($utl->lastFetch['user']['id'])){
					$id=$utl->lastFetch['user']['id'];
					$name=$utl->lastFetch['user']['name'];
					$tieba=$utl->fetchWebLikedForumList();
					$sql="SELECT * FROM `info` where uid = {$id}";
					$result=$DB->query($sql);
					$row = $result->fetch_assoc();
					if($row['uid']==$id){
						$sql="DELETE FROM `info` WHERE uid={$id};";
						$DB->query($sql);
						$sql="DELETE FROM `tieba` WHERE uid={$id};";
						$DB->query($sql);
					}
					$sql="INSERT INTO `info` (`uid`, `un`, `bduss`) VALUES ('{$id}', '{$name}', '{$bduss}');";
					$DB->query($sql);
					for($i=0;isset($tieba['data'][$i]);$i++){
						$sql="INSERT INTO `tieba` (`uid`, `tieba`, `is_sign`) VALUES ('{$id}','{$tieba[data][$i][forum_name]}', '0');";
						$DB->query($sql);
					}
					echo '<pre>成功添加<a href="./">返回</a></pre>';
				}
				break;
			case 5:
				$_SESSION['vcode_md5'] = $result['data']['vcode_md5'];
				$need_vcode = 1;
				break;
			default:
				var_dump($test_login);
				break;
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>贴吧签到托管-自动获取BDUSS</title>
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
	<style>
		body {
			padding-top: 80px;
			background-color: #eee;
		}
		pre,footer,.form-panel{
			max-width: 330px;
			margin: 0 auto;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="panel panel-primary form-panel">
			<div class="panel-heading">自动获取BDUSS</div>
			<div class="panel-body">
				<form class="form-horizontal" role="form" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
					<div class="form-group">
						<label for="input_user_name" class="col-sm-3 control-label">用户名</label>
					<div class="col-sm-9">
					<input type="text" class="form-control" id="input_user_name" name="username" placeholder="用户名/邮箱" value="<?php if(isset($username)) echo $username; ?>">
					</div>
					</div>
					<div class="form-group">
						<label for="input_password" class="col-sm-3 control-label">密码</label>
						<div class="col-sm-9">
							<input type="password" class="form-control" id="input_password" name="password" placeholder="密码" value="<?php if(isset($password)) echo $password; ?>">
						</div>
					</div>
						<?php if(isset($need_vcode)){ ?>
					<div class="form-group">
						<label for="input_vcode" class="col-sm-3 control-label">验证码</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" id="input_vcode" name="vcode" placeholder="验证码">
						</div>
						<div class="col-sm-5">
							<img src="<?= $result['data']['vcode_pic_url'] ?>" alt="">
						</div>
					</div>
					<?php } ?>
					<div class="form-group">
						<div class="col-sm-offset-3 col-sm-9">
							<button type="submit" class="btn btn-primary btn-block">登录</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<pre><font color="red">1.本站不会窃取任何有关用户密码的信息,请放心使用
2.开启登录保护的用户请手动添加BDUSS(可能会有伪开启的状态,百度官网显示未开启,但是还是无法提交的话就开启再关闭)</font></pre>
</body>
<footer>© 2015 <a href="http://www.zzliux.tk" target="_blank">zzliux</a></footer>
<!-- 感谢星弦雪大神提供的BaiduUtil和登录模板 -->
</html>
