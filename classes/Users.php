<?php
function get_clinetinfo($client_file) {
	if (file_exists($client_file)) {
		return json_decode(file_get_contents($client_file),true);
	}
	return false;
}

function make_table($client_info, $full = false) {
	if (empty($client_info["pressure_list"]))
		return false;
	$outtext="";
	$old_day="";
	foreach($client_info["pressure_list"] as &$value) {
		if ((!$full) && ($value["act"]!=="pressure"))
			continue;
		$date = date("H:i",$value["time"]);
		$day = date("d-m-Y",$value["time"]);
		$text = $value["value"];
		if ($value["act"]==="pressure") {
			list($up_p,$down_p,$pulse) = explode(";", $text);
			$text = $up_p."/".$down_p."-".$pulse;
		}
		if ($old_day!==$day) {
			$old_day=$day;
			$outtext .= "--- ".$day." ---\n";
		}
		$outtext .= $date." ".$text."\n";
	}
	if (!empty($outtext))
		return $outtext;

	return false;
}
?>