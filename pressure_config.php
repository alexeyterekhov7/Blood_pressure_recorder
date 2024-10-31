<?php 
	date_default_timezone_set('Europe/Minsk'); // 'UTC'
	$telegtam_botkey = "!!! TELEGRAM BOT KEY !!!";
	$telegram_chatId = !!! TELEGRAM BOT ID !!!;
	$home_dir = getcwd()."/";
	$backup_dir = $home_dir . "BACKUP/";
	$DB_dir = $home_dir . "DB/";
	$file_update_index = $backup_dir . "tg_timestamp.ts";
	$base_url = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["SCRIPT_NAME"];
	$css_dir = $_SERVER["REQUEST_SCHEME"]."://".$_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"] . "/Pressure/css";
	$start_msg = "Welcome to Blood pressure Bot recorder!\nUse format: \n120/80-60\n(up pressure/down pressure/pulse)\n-\n/getstat - for stat\n";
?>