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
	date_default_timezone_set('Asia/Tokyo');
	//functions/からインクルード
	require_once("./functions/defines.php");
	require_once("./functions/rss.php");
	require_once("./functions/utility.php");
	
	
	//rssの初期設定
	$rss = new RssSetting("掲示板",
	get_url(),
	"掲示板ですよ～",
	"./rss/rss.rdf");
	
	//変数宣言
	$disp_num = 1000;//表示件数
	
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
			$fp = fopen(MSG_LOG_FILE_PATH,"a");
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
<form action="index.php" method="get">
	<p>検索：<input type="text" name="search_query"><input type="submit" value="検索"></p>
	
</form>
<form action="index.php" method="post">
	<p>
		<label><input type="radio" name="display_num" value="50" required <?php if($disp_num == 50) {echo "checked";}?>>50件表示</label>
		<label><input type="radio" name="display_num" value="100" <?php if($disp_num == 100) {echo "checked";}?>>100件表示</label>
		<label><input type="radio" name="display_num" value="1000" <?php if($disp_num == 1000) {echo "checked";}?>>全件表示(重いかも)</label>
	</p>
	<p>名前:<input type="text" value="名無しさん@テスト中" name="user_name"></p>
	<p>本文:<br><textarea name="message"></textarea></p>
	<input type="hidden" name="csrf_token" value="<?php	echo $_SESSION["csrf_token"]?>">
	<input type="submit" value="送信 / 更新">
</form>
<div id="thread_body">
<?php
	//最初にログファイルがなければ作成
	if(!file_exists (MSG_LOG_FILE_PATH)){
		touch(MSG_LOG_FILE_PATH);
	}
	$logs = array_from_csv(MSG_LOG_FILE_PATH);//csvの中身そのままの配列
	$msg_array = array();//URLへのリンクやレスアンカーのリンク化などの処理が施された、最終的に出力するdivタグの配列
	$message_num_counter = 1;//レス番号カウンター
	foreach($logs as $line){
		/*
			*line[0]→ユーザー名
			*line[1]→本文
			*line[2]→日付
			*line[3]→ID
		*/
		//改行をbrタグ化
		$msg = str_replace("\n","<br>",$line[1]);
		
		//「>>レス番号」　をアンカーにするために正規表現で探し出して$anchersに代入
			//preg_replaceを使うともっとスマートになる気がする？
			if(preg_match_all('/&gt;&gt;[0-9]{1,}/',$line[1],$anchers,PREG_SET_ORDER) >= 1){
				//該当レス番号へのリンク
				foreach($anchers as $ancher){
					$ancher_res_id = str_replace("&gt;&gt;","",$ancher[0]);//idにするため「>>」を除去
					$msg = str_replace("${ancher[0]}","<a href=\"index.php#message_${ancher_res_id}\">${ancher[0]}</a>",$msg);//idに飛ばすリンク　例：index.php#23
				}
				
			}
			$msg = url_to_link($msg);
			//HN、日付、IDは配列そのまま　メッセージのみ整形後にしてdivタグで囲う
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

	
//検索欄が入力されていて空白ではない場合の処理
/*
 * 現状、htmlタグをつけた後の$msg_arrayに対して検索をかけているので
 * htmlタグに含まれる文字列(div や id など)を検索すると全部引っかかってしまう問題がある
 * しかしcsvから読み込んだ直後の配列に対して検索を行うと
 * csvにはレス番号を保存していないので表示時にレス番号がわからないという問題もある
 * 解決方法として
 * ・書き込み時にレス番号を計算してcsvに同時に保存する
 * ・csvから読み込んだ後、一旦レス番号を付加し直す
 * が考えられる
 * 前者のほうが直感的な気がする
 * いずれにせよ既存コードの書き直しが発生して面倒なので一旦この状態で保存
 * */

if(!empty($_GET) && $_GET["search_query"] != ""){
		$results = array();
		$search_query = $_GET["search_query"];
		foreach($msg_array as $msg){
				if(strstr($msg,$search_query) != false){
						echo $msg;
					}
			}
	}
	
//検索欄が入力されてなかった・空白である場合
else{
		//上が最新になるように表示
		$msg_array = array_reverse($msg_array);
		for($i = 0;$i < count($msg_array) && $i < $disp_num;$i++){
				echo $msg_array[$i];
			}
	}	


?>
</div>


