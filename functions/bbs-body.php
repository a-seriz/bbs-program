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
				if($this->json != "null"){	
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
		}
		
		function search_msg(){
			//$all_msg_infoから指定された文字列を検索して$search_resultに突っ込む
		}
		
		function print_msg_html(){
			//$msg_htmlを出力する関数
			
		}
		
		function __construct(){
				
		}
	}
?>