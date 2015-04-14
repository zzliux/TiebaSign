<meta charset="utf-8">
<?php
	if(isset($_POST['un'])||isset($_GET['un'])){
		if(isset($_POST['un'])){
			$un = $_POST['un'];
		}else{
			$un = $_GET['un'];
		}
		require_once('install/config.php');
		$DB = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query('SET NAMES utf8');
		$sql = "select * from info where un='{$un}'";
		$result = $DB->query($sql);
		if(!($row = $result->fetch_assoc())){
			die('本站木有这个用户哟~~<a href="refresh.php">返回</a>');
		}
		$bduss = $row['bduss'];
		$uid = $row['uid'];
		$name = $row['un'];
		require_once('BaiduUtil.php');
		$utl = new BaiduUtil($bduss);
		$sql = "DELETE FROM info WHERE uid = {$uid}";
		$DB->query($sql);
		$sql = "DELETE FROM tieba WHERE uid = {$uid}";
		$DB->query($sql);
		$utl->un();
		$result = $utl->fetchWebLikedForumList();
		if(isset($utl->lastFetch['user']['id'])){
			$sql="INSERT INTO `info` (`uid`, `un`, `bduss`) VALUES ('{$uid}', '{$name}', '{$bduss}');";
			$DB->query($sql);
			for($i=0;isset($result['data'][$i]);$i++){
				$sql="INSERT INTO `tieba` (`uid`, `tieba`, `is_sign`) VALUES ('{$uid}','{$result[data][$i][forum_name]}', '0');";
				$DB->query($sql);
			}
			die("更新成功,总计{$i}个吧~~<a href=\"query.php?un={$name}\">签到查询</a>(刚刚更新贴吧的话会全部都在队列中哦~)");
		}else{
			die('这个用户的BDUSS已经过期了哟~~<a href="./">提交BDUSS</a>');
		}
	}else{
		die('请输入用户名');
	}
?>
