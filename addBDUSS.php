<meta charset="utf-8">
<?php
	require_once('install/config.php');
	$con = new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
	if($con->connect_errno){
		die($con->connect_error);
	}
	$bduss=$_POST['bduss'];
	require_once('BaiduUtil.php');
	$utl = new BaiduUtil($bduss);
	$con->query('SET NAMES utf8');
	$utl->un();
	if(!empty($utl->lastFetch['user']['id'])){
		$id=$utl->lastFetch['user']['id'];
		$name=$utl->lastFetch['user']['name'];
		$tieba=$utl->fetchWebLikedForumList();
		$sql="SELECT * FROM `info` where uid = {$id}";
		$result=$con->query($sql);
		$row = $result->fetch_assoc();
		if($row['uid']==$id){
			$sql="DELETE FROM `info` WHERE uid={$id};";
			$con->query($sql);
			$sql="DELETE FROM `tieba` WHERE uid={$id};";
			$con->query($sql);
		}
		$sql="INSERT INTO `info` (`uid`, `un`, `bduss`) VALUES ('{$id}', '{$name}', '{$bduss}');";
		$con->query($sql);
		for($i=0;isset($tieba['data'][$i]);$i++){
			$sql="INSERT INTO `tieba` (`uid`, `tieba`, `is_sign`) VALUES ('{$id}','{$tieba[data][$i][forum_name]}', '0');";
			$con->query($sql);
		}
		die('成功添加<a href="./">返回</a');
	}
?>
