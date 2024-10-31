<?php
	require_once $home_dir . "classes/Telegram.php";
	$telegram = new Telegram($telegtam_botkey, $telegram_chatId, $file_update_index, $backup_dir);

	// Check updates
	if ( ($result = $telegram->get_updates()) !== false) 
	{
		$msg = json_decode($result,true);
		if (empty($msg["ok"]) || $msg["ok"]!==true) {
			die("Error, JSON:<BR>\n".$result);
		}
		$update_id = 0;

		foreach($msg["result"] as &$value) {
			if (!empty($value["update_id"]))
				$update_id = $value["update_id"];
			else break;

			// edit_message controlled and delete and etc.
			$key = "message";
			if (!empty($value["edited_message"]))
				$key = "edited_message";
			
			if (!empty($value[$key]))
				$date = $value[$key]["date"];
			else break;

			$from_id = 0;
			if (!empty($value[$key]["from"]["id"]))
				$from_id = $value[$key]["from"]["id"];
			else break;

			$message_id=0;
			if (!empty($value[$key]["message_id"]))
				$message_id = $value[$key]["message_id"];
			else break;

			$username="";
			if (!empty($value[$key]["from"]["username"])) {
				$username="@".$value[$key]["from"]["username"];
			}
			if (!empty($value[$key]["from"]["first_name"])) {
				$username.=" ".$value[$key]["from"]["first_name"];
			}
			if (!empty($value[$key]["from"]["last_name"])) {
				$username.=" ".$value[$key]["from"]["last_name"];
			}

			if (!empty($value[$key]["text"])) {
				$item = command_parser($telegram, $message_id, $from_id, $date, $value[$key]["text"]);
				if ($item !== false) {
					$TG_DB[$from_id]["pressure_list"][$message_id] = $item;
					$TG_DB[$from_id]["username"] = trim($username);
				}
			}
		}
		if ($update_id>0)
			$telegram->set_update_index($update_id+1);
	}

	// Update clients data
	if (!empty($TG_DB)) {
		foreach($TG_DB as $client_id=>&$newvalue) {
			$client_file = $DB_dir.$client_id.".json";
			unset($DB_client);
			if (file_exists($client_file)) {
				$DB_client = json_decode(file_get_contents($client_file),true);
			}

			foreach($newvalue["pressure_list"] as $key=>&$item) {
				$DB_client["pressure_list"][$key]=$item;
			}
			$DB_client["username"] = $TG_DB[$client_id]["username"];
			file_put_contents($client_file, json_encode($DB_client));
		}
	}

function command_parser($telegram, $message_id, $user_id, $date, $command) {
global $start_msg,$DB_dir;
	if (!empty($command)) {
		$text = $command;
		$act = "";
		$value = "";
		if (preg_match('/^([0-9]{2,3})[\/,-: ]([0-9]{2,3})[\/,-: ]([0-9]{2,3})/', $text , $matches) === 1) {
			$act="pressure";
			$value = $matches[1].";".$matches[2].";".$matches[3];
			$mood = "cry";
			if ($matches[1]<140)  // sizeof($matches) === 4
				$mood = "heart";
			$telegram->mood_reaction($user_id, $message_id, $mood);
		} else if (strpos($text,"take")!==false) {
			$act="pills";
			replace_val($text);
			$value = str_replace("take", "", $text);
			$telegram->mood_reaction($user_id, $message_id, "write");
		} else {
			if ($text === "/start") {
				$telegram->send_message($user_id, $start_msg);
			} 
			else if ($text === "/getstat") {
				$telegram->send_message($user_id, make_table(get_clinetinfo($DB_dir.$user_id.".json")));
			} else if (preg_match('/time ([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4}) ([0-9]{1,2}):([0-9]{2}) ([0-9]+)[\/,-: ]([0-9]+)[\/,-: ]([0-9]+)/', $text , $matches) === 1) {
				$newdate= mktime($matches[4], $matches[5], 0, $matches[2], $matches[1], $matches[3]);
				$value=$matches[6].";".$matches[7].";".$matches[8];
				$telegram->mood_reaction($user_id, $message_id, "write");
				return array("time"=>$newdate,"act"=>"pressure","value"=>$value);
			} else
				echo "unknows text: ".$text."<br>\n";
			return false;
		}
		return array("time"=>$date,"act"=>$act,"value"=>$value);
	}
	return false;
}

function replace_val(&$val) {
	if ($val === "take i")
		$val = "take индаприл 8мг/2,5мг";
	if ($val === "take n")
		$val = "take нифекард 30мг х 2шт";
}
?>