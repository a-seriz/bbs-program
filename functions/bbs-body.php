<?php
	/*
	*BBS Class File
	*/
	require_once("defines.php");
	require_once("utility.php");
	class BBS{
		/*
		* $all_msg_infoが持つ要素
		* msg_id--レス番号
		* msg--本文
		* user_name--ユーザー名
		* submit_date--書き込み日時
		* user_id--ユーザーID(IPから生成)
		*/
		protected $all_msg_info = array();//すべてのmessage情報が入った配列　$jsonを$json_decodeしたもの
		protected $json;//jsonの読み込み、書き込み時に使う
		protected $search_result;//検索結果が入る
		protected $msg_html;//htmlに整形された最終的に出力されるmessage
		
		function get_msg_from_file(){
			//ログファイルからメッセージを読み込む関数
			if(file_exists(MSG_LOG_FILE_PATH)){
				$this->json = file_get_contents(MSG_LOG_FILE_PATH);
				if($this->json != null){	
					$this->all_msg_info = mb_convert_encoding($this->json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
					$this->all_msg_info = json_decode($this->all_msg_info,true);
				}
				
			}
			
		}
		
		function put_msg_to_file(){
			//ログファイルにメッセージを書き込む関数
			$this->json = json_encode($this->all_msg_info,JSON_UNESCAPED_UNICODE);
			file_put_contents(MSG_LOG_FILE_PATH,$this->json,LOCK_EX);
		}
		
		function push_msg($user_name,$msg,$submit_date,$user_id){
			$this->get_msg_from_file();//新しいメッセージを確認
			$msg_id = count($this->all_msg_info) + 1;
			//$all_msg_infoにメッセージを追加する関数
			$msg_info = array(
				"msg_id" => $msg_id,
				"user_name" => $user_name,
				"msg" => $msg,
				"submit_date" => $submit_date,
				"user_id" => $user_id,
			);
			array_push($this->all_msg_info,$msg_info);
			
		}
		
		function msg_to_html(){
			//$all_msg_infoを$msg_htmlに整形する関数
			$this->get_msg_from_file();
			//メッセージがなければ終了
			if(!is_array($this->all_msg_info)){
					return;
				}
			foreach($this->all_msg_info as $msg_info){
				//改行変換
				$msg = str_replace("\n","<br>",$msg_info["msg"]);
				
				//「>>レス番号」　をアンカーにするために正規表現で探し出して$anchersに代入
				if(preg_match_all('/&gt;&gt;[0-9]{1,}/',$msg,$anchers,PREG_SET_ORDER) >= 1){
					//アンカーをaタグで囲う
					foreach($anchers as $ancher){
						$ancher_res_id = str_replace("&gt;&gt;","",$ancher[0]);//idにするため「>>」を除去
						$msg = str_replace("${ancher[0]}","<a href=\"#message_${ancher_res_id}\">${ancher[0]}</a>",$msg);//idに飛ばすリンク　例：index.php#message_23
					}
				
				}
				$msg = url_to_link($msg);
				//$msg_htmlに追加
				$this->msg_html .= <<<__HTML__
				<div id="message_${msg_info["msg_id"]}">
					<p><span class="message_num">${msg_info["msg_id"]}:</span><span class="username_view">${msg_info["user_name"]}</span><span class="id_view">ID:${msg_info["user_id"]}</span></p>
					<p class ="message_view">${msg}</p>
					<p class ="date_view">${msg_info["submit_date"]}</p>
				</div>
__HTML__;
			}
			
			
		}
		
		function search_msg(){
			//$all_msg_infoから指定された文字列を検索して$search_resultに突っ込む
		}
		
		function print_msg_html(){
			//$msg_htmlを出力する関数
			echo $this->msg_html;
		}
		
		function check_archive(){
			//レス数が上限を超えたとき、スレッドをhtmlファイルとしてアーカイブする機能
			
			if(!is_array($this->all_msg_info)||count($this->all_msg_info) < THREAD_RES_LIMIT){
					//上限に達していなかったら終了
					return;
				}
			$archived_date = date("y-n-d-H-i-s");
			$h = <<< _HTML_
			<meta charset="utf-8">
			<title>雑談過去ログ-${archived_date}-</title>
			<link rel="stylesheet" href="../../css/index.css"
			<div id="thread_body">
				$this->msg_html
			</div>
_HTML_;
			file_put_contents("./logs/old-thread/${archived_date}.html",$h,LOCK_EX);
			
			//message.jsonを初期化
			$fp = fopen(MSG_LOG_FILE_PATH,"r+");
			flock($fp,LOCK_EX);
			ftruncate($fp,0);
			flock($fp,LOCK_UN);
			fclose($fp);
			
		}
		
		function __construct(){
			//ログファイル・ディレクトリがなければ生成
			if(!file_exists(MSG_LOG_DIR_PATH)){
				mkdir(MSG_LOG_DIR_PATH);
			}
			if(!file_exists(MSG_LOG_FILE_PATH)){
				touch(MSG_LOG_FILE_PATH);
			}
			if(!file_exists(OLD_THREAD_DIR_PATH)){
				mkdir(OLD_THREAD_DIR_PATH);
			}
		}
	}
?>
