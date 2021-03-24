<meta charset="utf-8">
<title>雑談掲示板過去ログ</title>
<?php
	require_once('functions/defines.php');
	$archive_html_list = glob("./logs/old-thread/*.html");
	if(!empty($archive_html_list)){
			echo "上にあるほど新しいです<br>";
			$reverse_list = array_reverse($archive_html_list);
			foreach($reverse_list as $archive_html){
					$link = "<a href=\"${archive_html}\">${archive_html}</a>";
					echo $link."<br>";
			}
		}
	else{
			echo "まだスレッドはアーカイブされてないよ";
		}
?>
