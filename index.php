<?php
	//csrf対策のtokenをセット
	session_start();
	if(!isset($_SESSION["csrf_token"])){
		$_SESSION["csrf_token"] = bin2hex(random_bytes(32));
	}
	
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" href="css/index.css" type="text/css">
<title>BBS-Sample</title>
<!-- bbs top page -->
<?php
	ini_set("display_errors",1);
	define('FILE_PATH',"./logs/messages.csv");
	date_default_timezone_set('Asia/Tokyo');
	
	
	
	
	
	//POSTが存在しているか
	if(!empty($_POST)){
	//tokenを検証して一致していれば書き込み処理
		if($_POST["csrf_token"] == $_SESSION["csrf_token"]){
		
			$user_name = htmlspecialchars($_POST["user_name"]);
			$message = htmlspecialchars($_POST["message"]);
			$submit_date =  date("y/n/d H:i:s");
		
			$message_array = array($user_name,$message,$submit_date);
			//ログファイルに書き込み
			$fp = fopen(FILE_PATH,"a");
			if($fp){
				fputcsv($fp,$message_array);
			}
			fclose($fp);
		
		}
	}
	
	
?>


<?php
	//最初にログファイルがなければ作成
	if(!file_exists (FILE_PATH)){
		touch(FILE_PATH);
	}
	$fp = fopen(FILE_PATH,"r");
	if($fp){
		$message_num_counter = 1;
		while($line = fgetcsv($fp)){
			
			$msg = str_replace("\n","<br>",$line[1]);
			echo "<div>";
			echo "<p><span class=\"message_num\" id=message_".$message_num_counter.">".$message_num_counter.":</span><span class = \"username_view\">".$line[0]."</span></p>";
			echo "<p class = \"message_view\">".$msg."</p>";
			echo "<p class = \"date_view\">".$line[2]."</p>";
			echo "</div>";
			$message_num_counter++;
			
		}
	}
	fclose($fp);
?>



<form action="index.php" method="post">
	<p>名前:<br><input type="text" value="名無しさん@テスト中" name="user_name"></p>
	<p><br><textarea required name="message"></textarea></p>
	<input type="hidden" name="csrf_token" value="<?php	echo $_SESSION["csrf_token"]?>">
	<input type="submit" value="送信">
</form>
