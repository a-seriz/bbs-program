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

	//textからURLを探してaタグで囲って返す
	function url_to_link($text){
			//処理後返される文字列
			$returns = preg_replace('/(https?|ftp)(:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)/', '<a href="\\1\\2" target=\"_blank\">\\1\\2</a>', $text);
			
		return $returns;
	}
	//csvファイルから配列を作る
	function array_from_csv($csv_file_path){
		$array = array();
		$fp = fopen($csv_file_path,"r");
		while($line = fgetcsv($fp)){
				array_push($array,$line);
			}
		return $array;
	}
		
	
	
		
?>
