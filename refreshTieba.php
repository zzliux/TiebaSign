<meta charset="utf-8">
<?php
	if(isset($_POST['un'])||isset($_GET['un'])){
		require_once('install/config.php');
		$DB = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		if(isset($_POST['un'])){
			$un = $DB->real_escape_string($_POST['un']);
		}else{
			$un = $_GET['un'];
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
		$utl->un();
		$result = $utl->fetchWebLikedForumList();
		if(isset($utl->lastFetch['user']['id'])){
			$sql = "DELETE FROM info WHERE uid = {$uid}";
			$DB->query($sql);
			$sql="INSERT INTO `info` (`uid`, `un`, `bduss`) VALUES ('{$uid}', '{$name}', '{$bduss}');";
			$DB->query($sql);
			$count = 0;
			for($i=0;isset($result['data'][$i]);$i++){
				$sql = "SELECT * FROM tieba WHERE tieba = '{$result['data'][$i]['forum_name']}' AND uid = {$uid}";
				$result2 = $DB->query($sql);
				$row = $result2->fetch_assoc();
				if(empty($row['uid'])){
					$sql="INSERT INTO `tieba` (`uid`, `tieba`, `is_sign`) VALUES ('{$uid}','{$result[data][$i][forum_name]}', '0');";
					$DB->query($sql);
					$count++;
				}
			}
			unset($utl);
			die('更新成功,用户'.$name.'新增'.$count.'个贴吧<br><a href="./?un='.$name.'">签到查询</a>');
		}else{
			die('这个用户的BDUSS已经过期了哟~~<a href="./">提交BDUSS</a>');
		}
	}else{
		die('请输入用户名');
	}
?>
