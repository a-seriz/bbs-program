<?php
	//現在のURLを返す
	function get_url(){
		return (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
	//ipアドレスからIDを生成して返す
	function create_ID_from_ip_addr(){
		$ip_addr = $_SERVER["REMOTE_ADDR"];
		//ハッシュ化する
		$id = hash("sha256",$ip_addr);
		//10桁残して切り捨て
		$id = substr($id,0,10);
		return $id;
	}
?>