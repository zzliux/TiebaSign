<?php
	header("Content-type: text/html; charset=utf-8");
	date_default_timezone_set('PRC');//设置北京时间
	require_once('install/config.php');
	require_once('BaiduUtil.php');
	$time = date('H:i',time());
	$t = explode(':',$time);

	if($t[1]>=0&&$t[1]<=2 && $t[0]%4==0){
		update(false,true,true,true);
		die();
	}

	echo getTask($time).'<br>';	
	
	switch(getTask($time)){
		case 'refresh': refresh(); break;
		case 'tieba': signForTieba(20); break;
		case 'zhidao': signForZhidao(); break;
		case 'wenku': signForWenku(); break;
		case 'update': update(); break;
		case 'zhidaoLuck': zhidaoLuck(); break;
		default: echo 'over'; break;
	}

	function refresh($n=5){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		$sql = 'SELECT * FROM info WHERE is_refresh = 0 LIMIT '.$n;
		$result_ = $DB->query($sql);
		while($row = $result_->fetch_assoc()){
			$bduss = $row['bduss'];
			$uid = $row['uid'];
			$name = $row['un'];
			$utl = new BaiduUtil($bduss);
			try{
				$utl->un();
				$result = $utl->fetchWebLikedForumList();
				$utl->lastFetch['user']['id'];
				$DB->query("update info set is_refresh = 1 where uid = {$uid}");
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
			}catch(Exception $e){
				var_dump($e);
			}
			unset($utl);
			echo '更新成功,用户'.$name.'新增'.$count.'个贴吧'.'<br>';
		}
		$DB->close();
		return '';
	}

	function update($refresh=true, $tieba=true, $zhidao=true, $wenku=true){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		if($tieba){
			$sql = 'update tieba set is_sign = 0';
			$DB->query($sql);
		}
		if($refresh){
			$sql = 'update info set is_refresh = 0';
			$DB->query($sql);
		}
		if($zhidao){
			$sql = 'update info set is_sign_zhidao = 0';
			$DB->query($sql);
		}
		if($wenku){
			$sql = 'update info set is_sign_wenku = 0';
			$DB->query($sql);
		}
		$DB->close();
	}

	function signForWenku(){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		$sql = 'select * from info where `is_sign_wenku` = 0';
		$result = $DB->query($sql);
		while($row = $result->fetch_assoc()){
			echo '<br>'.$row['un'];
			$utl = new BaiduUtil($row['bduss']);
			$utl->signForWenku();
			$sql="update info set is_sign_wenku = 1 where uid={$row['uid']}";
			$DB->query($sql);
		}
		$DB->close();
	}

	function signForZhidao(){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		$sql = 'select * from info where `is_sign_zhidao` = 0';
		$result = $DB->query($sql);
		while($row = $result->fetch_assoc()){
			$utl = new BaiduUtil($row['bduss']);
			$utl->signForZhidao();
			$sql="update info set is_sign_zhidao = 1 where uid={$row['uid']}";
			$DB->query($sql);
		}
		$DB->close();
	}
	function zhidaoLuck(){
		$DB=new mysqli(HOSTNAME, HOSTUSER, HOSTPASSWORD, HOSTDB);
		if($DB->connect_errno){
			die($DB->connect_error);
		}
		$DB->query("SET NAMES utf8");
		$sql = 'select * from info order by uid';
		$result = $DB->query($sql);
		while($row = $result->fetch_assoc()){
			$utl = new BaiduUtil($row['bduss']);
			$utl->zhidaoFreeLuck();
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
		}
		$DB->close();
		return 'ok';
	}
?>
