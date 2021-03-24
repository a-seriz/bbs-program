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
	require_once("./functions/rss.php");
	require_once("./functions/utility.php");
	require_once("./functions/bbs-body.php");
	
	//rssの初期設定
	$rss = new RssSetting("掲示板",
	get_url(),
	"掲示板ですよ～",
	"./rss/rss.rdf");	
	
	//ログファイルがなければ生成
	if(!file_exists(MSG_LOG_FILE_PATH)){
		touch(MSG_LOG_FILE_PATH);
	}
	
	//変数宣言
	$disp_num = 1000;//表示件数
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
			
			//rss更新
			$url = get_url();
			$rss->update_rss($user_name,$url,"掲示板更新のお知らせ","新着レス",$message,$submit_date);
		
		}
	}
	
	
?>
<section id="page_header">
	<h1>雑談掲示板</h1>
	雑談にどうぞ。<br>
	100レスごとに自動的にアーカイブされます。<br>
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
	<p>名前:<input type="text" value=<?php if(isset($_POST["user_name"]) && !empty($_POST["user_name"])){ echo $_POST["user_name"];}else{echo DEFAULT_USER_NAME;}?> name="user_name"></p>
	<p>本文:<br><textarea name="message"></textarea></p>
	<input type="hidden" name="csrf_token" value="<?php	echo $_SESSION["csrf_token"]?>">
	<input type="submit" value="送信 / 更新">
</form>
<script src="./script/index.js"></script>




