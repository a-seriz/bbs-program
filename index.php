<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" href="css/index.css" type="text/css">
<title>雑談掲示板</title>
<?php
	//エラー出力設定
	ini_set("display_errors",1);
	
	//csrf対策のtokenをセット
	session_start();
	if(!isset($_SESSION["csrf_token"])){
		$_SESSION["csrf_token"] = bin2hex(random_bytes(32));
	}
	//初期設定
	date_default_timezone_set('Asia/Tokyo');
	//functions/からインクルード
	require_once("./functions/defines.php");
	require_once("./functions/utility.php");
	require_once("./functions/bbs-body.php");

	
	
	$bbs_body = new BBS();
	$bbs_body -> get_msg_from_file();
	
	//POSTが存在しているか
	if(!empty($_POST) && !empty($_POST["message"])){
	//tokenを検証して一致していれば書き込み処理
		if($_POST["csrf_token"] == $_SESSION["csrf_token"]){
		
			$user_name = htmlspecialchars($_POST["user_name"]);
			$message = htmlspecialchars($_POST["message"]);
			$submit_date =  date("y/n/d H:i:s");
			$user_id = create_ID_from_ip_addr();
			
			
			$bbs_body -> push_msg($user_name,$message,$submit_date,$user_id);
			$bbs_body -> put_msg_to_file();
		}
	}
	
	
?>
<section id="page_header">
	<h1><?php echo BBS_TITLE; ?></h1>
	<?php echo BBS_INFO ?><br>
	<?php echo THREAD_RES_LIMIT; ?>レスごとに自動的にアーカイブされます。<br>
	アーカイブ一覧は<a href="./archive_list.php" target="_blank">こちら</a>
</section>

<div id="thread_body">
<?php
	$bbs_body -> msg_to_html();
	$bbs_body -> print_msg_html();
	$bbs_body -> check_archive();
?>
</div>
<form action="index.php" method="post">
	<p>名前:<input type="text" value=<?php if(isset($_POST["user_name"]) && !empty($_POST["user_name"])){ echo $_POST["user_name"];}else{echo DEFAULT_USER_NAME;}?> name="user_name" maxlength="20"></p>
	<p>本文:<br><textarea name="message" maxlength="500"></textarea></p>
	<input type="hidden" name="csrf_token" value="<?php	echo $_SESSION["csrf_token"]?>">
	<input type="submit" value="送信 / 更新">
</form>
<script src="./script/index.js"></script>




