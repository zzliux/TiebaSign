<?php
	require_once('install/config.php');
	require_once('BaiduUtil.php');
	$t1=((int)date('H',time())+8)%24;
	$t2=(int)date('i',time());
	$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
	if($DB->connect_errno){
		die($DB->connect_error);
	}
	$DB->query("SET NAMES utf8");
	if($t1==0&&$t2<=10){
		$sql = 'update tieba set is_sign = 0';
		$DB->query($sql);
		die('Update Queueing Succesfully');
	}
	$n=10;
	$last=0;
	while($n--){
		$sql="select * from tieba where is_sign = 0 order by rand() limit 1";
		$result = $DB->query($sql);
		$row = $result->fetch_assoc();
		if(($st==1||$st==4)&&$last==$row['uid']){
			sleep(10);
		}
		$last=$row['uid'];
		if(empty($row)){
			$sql="select * from tieba where is_sign = 4 order by rand() limit 1";
			$result = $DB->query($sql);
			$row = $result->fetch_assoc();
			if(empty($row)){
				$sql="select * from tieba where is_sign = 3 order by rand() limit 1";
				$result = $DB->query($sql);
				$row = $result->fetch_assoc();
			}else if(empty($row)){
				die('over');
			}
		}
		$sql="select * from info where uid={$row[uid]}";
		$result_=$DB->query($sql);
		$row_=$result_->fetch_assoc();
		$bduss=$row_['bduss'];
		$utl=new BaiduUtil($bduss);
		$re=$utl->sign($row['tieba']);
		switch($re['status']){
			case '0': $st=1; break;
			case '160002': $st=2; break;
			case '110001': $st=3; break;
			case '340011': $st=4; break;
		}
		$sql="update tieba set is_sign = {$st} where uid={$row[uid]} and tieba = '{$row[tieba]}'";
		$DB->query($sql);
		unset($utl);
		var_dump($re);
	}
	echo 'ok';
?>
