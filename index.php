<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" href="css/index.css" type="text/css">
<title>BBS-Sample</title>
<?php
	//エラー出力設定
	ini_set("display_errors",1);
	
	//csrf対策のtokenをセット
	session_start();
	if(!isset($_SESSION["csrf_token"])){
		$_SESSION["csrf_token"] = bin2hex(random_bytes(32));
	}
	//初期設定
	define('FILE_PATH',"./logs/messages.csv");
	date_default_timezone_set('Asia/Tokyo');
	//functions/からインクルード
	require_once("./functions/rss.php");
	require_once("./functions/utility.php");
	
	
	//rssの初期設定
	$rss = new RssSetting("掲示板",
	get_url(),
	"掲示板ですよ～",
	"./rss/rss.rdf");
	
	//変数宣言
	$disp_num = 50;//表示件数
	
	//POSTが存在しているか
	if(!empty($_POST) && !empty($_POST["message"])){
	//tokenを検証して一致していれば書き込み処理
		if($_POST["csrf_token"] == $_SESSION["csrf_token"]){
		
			$user_name = htmlspecialchars($_POST["user_name"]);
			$message = htmlspecialchars($_POST["message"]);
			$submit_date =  date("y/n/d H:i:s");
			$user_id = create_ID_from_ip_addr();
			$message_array = array($user_name,$message,$submit_date,$user_id);
			//ログファイルに書き込み
			$fp = fopen(FILE_PATH,"a");
			if($fp){
				fputcsv($fp,$message_array);
			}
			fclose($fp);
			//rss更新
			$url = get_url();
			$rss->update_rss($user_name,$url,"掲示板更新のお知らせ","新着レス",$message,$submit_date);
			
			//その他変数
			$disp_num = $_POST["display_num"];
		
		}
	}
	
	
?>

<!-- bbs top page -->
<form action="index.php" method="post">
	<p>
		<label><input type="radio" name="display_num" value="50" required <?php if($disp_num == 50) {echo "checked";}?>>50件表示</label>
		<label><input type="radio" name="display_num" value="100" <?php if($disp_num == 100) {echo "checked";}?>>100件表示</label>
		<label><input type="radio" name="display_num" value="1000" <?php if($disp_num == 1000) {echo "checked";}?>>全件表示(重いかも)</label>
	</p>
	<p>名前:<br><input type="text" value="名無しさん@テスト中" name="user_name"></p>
	<p>本文:<br><textarea name="message"></textarea></p>
	<input type="hidden" name="csrf_token" value="<?php	echo $_SESSION["csrf_token"]?>">
	<input type="submit" value="送信 / 更新">
</form>
<div id="thread_main">
<?php
	//最初にログファイルがなければ作成
	if(!file_exists (FILE_PATH)){
		touch(FILE_PATH);
	}
	
	//ログファイルを読み取って表示する
	$fp = fopen(FILE_PATH,"r");
	if($fp){
		$msg_array = array();
		$message_num_counter = 1;
		
		while($line = fgetcsv($fp)){
			/*
			*line[0]→ユーザー名
			*line[1]→本文
			*line[2]→日付
			*line[3]→ID
			*/
			$msg = str_replace("\n","<br>",$line[1]);
			
			//「>>レス番号」　をアンカーにするために正規表現で探し出して$anchersに代入
			if(preg_match_all('/&gt;&gt;[0-9]{1,}/',$line[1],$anchers,PREG_SET_ORDER) >= 1){
				//レスポンス先へのリンク
				foreach($anchers as $ancher){
						
					$ancher_res_id = str_replace("&gt;&gt;","",$ancher[0]);
					$msg = str_replace("${ancher[0]}","<a href=\"index.php#message_${ancher_res_id}\">${ancher[0]}</a>",$msg);
				}
				
			}
			
			
			//divタグにレス番号をidとしてつける
			$div = <<<__DIV__
			<div id="message_${message_num_counter}">
			<p><span class="message_num">$message_num_counter:</span><span class="username_view">$line[0]</span><span class="id_view">ID:$line[3]</span></p>
			<p class ="message_view">$msg</p>
			<p class ="date_view">$line[2]</p>
			</div>
__DIV__;
			array_push($msg_array,$div);
			$message_num_counter++;
			
			
		}
//上が最新になるように表示
		$msg_array = array_reverse($msg_array);

		for($i = 0;$i < count($msg_array) && $i < $disp_num;$i++){
				echo $msg_array[$i];
			}	
}
	fclose($fp);
?>
</div>


