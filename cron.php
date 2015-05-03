<?php
	date_default_timezone_set('PRC');//设置北京时间
	require_once('install/config.php');
	require_once('BaiduUtil.php');
	$time = date('H:i',time());

	$t = explode(':',$time);
	if($t[1]>=0&&$t[1]<=2 && $t[0]%4==0){
		refresh();
		die();
	}

	echo getTask($time);	
	switch(getTask($time)){
		case 'refresh': refresh(); break;
		case 'tieba': signForTieba(20); break;
		case 'zhidao': signForZhidao(); break;
		case 'wenku': signForWenku(); break;
		case 'update': update(); break;
		default: echo 'over'; break;
	}

	function refresh($n=5){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		while($n--){
			$sql = 'SELECT * FROM info LIMIT 1';
			$result = $DB->query($sql);
			$row = $result->fetch_assoc();
			$bduss = $row['bduss'];
			$uid = $row['uid'];
			$name = $row['un'];
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
			}
			unset($utl);
			echo '更新成功,用户'.$name.'新增'.$count.'个贴吧'.'<br>';
		}
		$DB->close();
		return '';
	}

	function update(){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$sql = 'update tieba set is_sign = 0';
		$DB->query($sql);
		$DB->close();
	}

	function signForWenku(){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		$sql = 'select * from info';
		$result = $DB->query($sql);
		while($row = $result->fetch_assoc()){
			$utl = new BaiduUtil($row['bduss']);
			$utl->signForWenku();
		}
		$DB->close();
	}

	function signForZhidao(){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		$sql = 'select * from info';
		$result = $DB->query($sql);
		while($row = $result->fetch_assoc()){
			$utl = new BaiduUtil($row['bduss']);
			$utl->signForZhidao();
		}
		$DB->close();
	}

	function getTask($time){
		$re = json_decode(file_get_contents('cronlog.php'),1);
		foreach($re as $key => $value){
			$t = explode(' ',$value);
			if(strtotime($time)>=strtotime($t[0])&&strtotime($time)<=strtotime($t[1])){
				return $key;
			}
		}
	}

	function signForTieba($n=15){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		$last=0;
		while($n--){
			$sql="select * from tieba where is_sign = 0 order by rand() limit 1";
			$result = $DB->query($sql);
			$row = $result->fetch_assoc();
			if(($st==1||$st==4)&&$last==$row['uid']){
				sleep(10);/*
				$sql="select * from tieba where is_sign = 0 and uid = {$row[uid]} order by rand() limit 1";
				$result = $DB->query($sql);
				$row = $result->fetch_assoc();*/
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
					return 'over';
				}
			}
			$sql="select * from info where uid={$row[uid]}";
			$result_=$DB->query($sql);
			if(empty($result_)){
				return;
			}
			$row_=$result_->fetch_assoc();
			$bduss=$row_['bduss'];
			$utl=new BaiduUtil($bduss);
			$re=$utl->sign($row['tieba']);
			var_dump($re);
			switch($re['status']){
				case '0': $st=1; break;
				case '160002': $st=2; break;
				case '110001': $st=3; break;
				case '340011': $st=4; break;
				case '1':case '3':
					$sql = "delete from tieba where uid = {$row[uid]}";
					$DB->query($sql);
					break;
			}
			$sql="update tieba set is_sign = {$st} where uid={$row[uid]} and tieba = '{$row[tieba]}'";
			$DB->query($sql);
			unset($utl);
	//		sleep(1);
		}
		$DB->close();
		return 'ok';
	}
?>
