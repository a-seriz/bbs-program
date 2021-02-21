<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" href="css/index.css" type="text/css">
<title>BBS-Sample</title>
<!-- bbs top page -->
<?php
	ini_set("display_errors",1);
	define('FILE_PATH',"./logs/messages.csv");
	
	//csrf対策のtokenをセット
	session_start();
	if(!isset($_SESSION["csrf_token"])){
		$_SESSION["csrf_token"] = bin2hex(random_bytes(32));
	}
	
	
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
	$fp = fopen(FILE_PATH,"r");
	if($fp){
		while($line = fgetcsv($fp)){
			
			
			echo "<div>";
			echo "<p class = \"username_view\">".$line[0]."</p>";
			echo "<p class = \"message_view\">".$line[1]."</p>";
			echo "<p class = \"date_view\">".$line[2]."</p>";
			echo "</div>";
			
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
