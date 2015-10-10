<?php 
	header("Content-type: text/html; charset=utf-8");
	if(file_exists('config.php')){
		die ('已经成功安装数据库!');
	}
	if(isset($_POST['sub'])){
		$hostName = $_POST['hostname'];
		$userName = $_POST['username'];
		$passWord = $_POST['password'];
		$dataBaseName = $_POST['databasename'];
		$admPsW = md5( $_POST['admpassword'] );
		
		$DB = new mysqli( $hostName , $userName , $passWord );
		
		if( $DB->connect_errno ){
			die( 'ERROR('.$DB->connect_errno.'): '.$DB->connect_error() );
		}
		
		$sql = "CREATE DATABASE $dataBaseName DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
		if( !( $DB->query( $sql ) ) ){
			die( 'ERROR' );
		}
		
		if( !( $DB->select_db( $dataBaseName ) ) ){
			$DB->query ( "DROP DATABASE $dataBaseName"  );
			die('ERROR');
		}
		
		$sql = "CREATE TABLE IF NOT EXISTS `info` (
  `uid` int(11) NOT NULL,
  `un` text,
  `bduss` text,
  `is_refresh` tinyint(1) NOT NULL DEFAULT '0',
  `is_sign_zhidao` tinyint(1) NOT NULL DEFAULT '0',
  `is_sign_wenku` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
		$DB->query($sql);
		$sql="CREATE TABLE IF NOT EXISTS `tieba` (
  `uid` int(11) DEFAULT NULL,
  `tieba` text,
  `is_sign` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
";
		if( !( $DB->query( $sql ) ) ){
			echo '<td>ERROR</td>';
			$DB->query ( "DROP DATABASE $dataBaseName"  );
			die ();
		}	
		
		$fp = fopen('config.php', 'w+');
		fwrite( $fp , '<?php'."\n");
		fwrite( $fp , "define( 'HOSTNAME' , '$hostName' );\n");
		fwrite( $fp , "define( 'HOSTUSER' , '$userName' );\n");
		fwrite( $fp , "define( 'HOSTPASSWORD' , '$passWord' );\n");
		fwrite( $fp , "define( 'HOSTDB' , '$dataBaseName' );\n");
		fwrite( $fp , "define( 'ADMPSW' , '$admPsW' );\n");
		fclose( $fp );
		$DB->close();
		die('成功安装!');
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>贴吧签到托管-数据库安装</title>
		<meta name="viewport" charset="utf-8" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.2/css/bootstrap.min.css">
		<style>
		body {
			padding-top: 80px;
			background-color: #eee;
		}
		</style>
	</head>
	<body>
		<div class="panel panel-primary" style="width:300px;margin-left:auto;margin-right:auto;">
			<div class="panel-heading">
      			<h5 style="color:#FFFFFF">安装</h5>
			</div>
			<div class="container" style="width:300px;">
				<br />
				<form method="post">
					<div class="input-group">
						<span class="input-group-addon" style="width:110px">数据库服务器</span>
						<input type="text" class="form-control" name="hostname" value="localhost">
					</div>
					<br />
		
					<div class="input-group">
						<span class="input-group-addon" style="width:110px">数据库用户名</span>
						<input type="text" class="form-control" name="username" value="root">
					</div>
					<br />
					
					<div class="input-group">
						<span class="input-group-addon" style="width:110px">数据库密码</span>
						<input type="text" class="form-control" name="password">
					</div>
					<br />
						
					<div class="input-group">
						<span class="input-group-addon" style="width:110px">数据库名</span>
						<input type="text" class="form-control" name="databasename" value="signer">
					</div>
					<br />
					
					<div class="input-group">
						<span class="input-group-addon" style="width:110px">管理员密码</span>
						<input type="password" class="form-control" name="admpassword">
					</div>
					<br />
					<button type="submit" style="width:270px" class="btn btn-primary" name ="sub">安装</button>
				</form>
				<br />
			</div>
		</div>
	</body>
</html>
