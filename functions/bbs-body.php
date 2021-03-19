<?php
	/*
	*BBS Class File
	*/
	require_once("defines.php");
	require_once("utility.php");
	class BBS{
		protected $all_msg_info;//すべてのmessage情報が入った配列
		protected $search_result;//検索結果が入る
		protected $msg_html;//htmlに整形された最終的に出力されるmessage
		function push_msg(){
			//$all_msg_infoにメッセージを追加する関数
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
	}
?>