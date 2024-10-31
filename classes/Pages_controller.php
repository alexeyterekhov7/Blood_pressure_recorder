<?php
	require_once $home_dir . "classes/Pages.php";
	if (!empty($_GET["client_id"])) {
		$client_id = intval($_GET["client_id"]);
		$client_file = $DB_dir.$client_id.".json";
		$client = get_clinetinfo($client_file);
		if ($client!==false) {
			echo show_html($client,true);
		} else echo "unknown client id";

	} else {
  		// Create clients id list;
		unset($Clients);
		foreach(glob($DB_dir . '*.json') as $fileName) {
			$tmp = intval(str_replace('.json', '', basename($fileName)));
			if ($tmp>0) {
				$name = get_clinetinfo($fileName)["username"];
				$Clients[]=array("id" => $tmp, "name" => $name);
			}
		}
		echo "<div>\nClients list:\n<UL>";
		foreach($Clients as $client) {
			echo "<li><a href='?client_id=".$client["id"]."'>".$client["name"]." (".$client["id"].")</a></li>";
		}
	}
?>