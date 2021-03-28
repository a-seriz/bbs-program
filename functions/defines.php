<?php
	//rss関連ファイル・ディレクトリのパス
	//define(RSS_FILES_DIR,'./rss/');
	//define(RSS_FILE,'./rss/rss.rdf');
	//ログディレクトリのパス
	define('MSG_LOG_DIR_PATH','./logs/');
	//掲示板のレス内容が記録されるファイルへのパス
	define('MSG_LOG_FILE_PATH',MSG_LOG_DIR_PATH . 'messages.json');
	//過去ログディレクトリ
	define('OLD_THREAD_DIR_PATH',MSG_LOG_DIR_PATH . 'old-threads/');
	//1スレッドあたりのレス数上限
	define('THREAD_RES_LIMIT',100);
	define('DEFAULT_USER_NAME',"名無しさん＠開発中");
	
	//掲示板情報
	define('BBS_TITLE','雑談掲示板');
	define('BBS_INFO',"雑談にどうぞ。");
?>
