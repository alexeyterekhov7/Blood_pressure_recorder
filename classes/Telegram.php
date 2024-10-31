<?php
class Telegram
{
	private $token;
	private $chatId;
	private $filename;
	private $backup_dir;
	private $prefix;

	public function __construct($_token, $_chatId, $_filename = 'filetime.txt', $_backup_dir = "", $_prefix = "tg_") {
		$this->filename = $_filename;
		$this->chatId = $_chatId;
		$this->token = $_token;
		$this->backup_dir = $_backup_dir;
		$this->prefix = $_prefix;
	}

	private function request($query, $save_answer = true) {
//		return file_get_contents("/var/www/html/Pressure/BACKUP/1729886367.json");
		$res = @file_get_contents("https://api.telegram.org/bot". $this->token .$query);
		if ($res !== false) {
			if ($save_answer) {
				file_put_contents($this->backup_dir.time().".json",$res);
			}
			return $res;
		}
		return false;
	}

	public function get_filetime()
	{
		if (file_exists($this->filename)) {
			return file_get_contents($this->filename);
		} else {
			return 0;
		}
	}

	public function get_filetimestamp()
	{
		return date("Y-m-d H:i:s", $this->get_filetime());
	}

	public function update_filetime()
	{
		if (file_put_contents($this->filename,time()))
			return true;
		else
			return false;
	}

	public function send_message($chatid, $textMessage)
	{
		if ($chatid === 0) {
			$chatid = $this->chatId;
		}
		$textMessage = urlencode($textMessage);
		return $this->request("/sendMessage?chat_id=". $chatid ."&text=" . $textMessage);
	}

	public function get_updates()
	{
		$update_id = $this->get_update_index();
		$upstr="";
		if ($update_id>0)
			$upstr = "?offset=$update_id";
		return $this->request("/getUpdates" . $upstr);
	}

	public function set_update_index($update) 
	{
		if (file_put_contents($this->filename,$update))
			return true;
		else
			return false;
	}

	public function get_update_index()
	{
		if (file_exists($this->filename)) {
			return file_get_contents($this->filename);
		} else {
			return 0;
		}
	}

	public function mood_reaction($chat_id, $message_id, $_mood) 
	{
		$data = http_build_query([
		    'chat_id' => $chat_id,
		    'message_id' => $message_id
		]);

		// https://core.telegram.org/bots/api#reactiontype
		switch ($_mood) {
			case "pills": $mood = '💊'; break;
			case "cry": $mood = '😢'; break;
			case "heart": $mood = '❤'; break;
			case "write": $mood = '✍'; break;
			default:
				$mood = $_mood;
		}

		$reactions = json_encode([
		    [ 'type' => 'emoji', 'emoji' => $mood ]
		]);
		return $this->request("/setMessageReaction?" . $data . "&reaction=" . $reactions);
	}
};
?>