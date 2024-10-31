<?php

function show_html($content){
global $home_dir, $base_url, $css_dir;
	$page = file_get_contents($home_dir."classes/TPL/main.tpl");
	$tab = "";
	foreach($content["pressure_list"] as &$key) {
		make_echo_tab($tab,$key["time"],$key["act"],$key["value"]);
	}
	make_echo_tab($tab,time(),"close_tab","");
	$page = str_replace("%MENU%", "<a href='".$base_url."'>Home</a>", $page);
	$page = str_replace("%TITLE%", "Blood pressure recorder", $page);
	$page = str_replace("%CSS%", $css_dir, $page);

	return str_replace("%BODY%", $tab, $page);
}

function make_echo_tab(&$tab,$time, $act, $value) {
	$noon=array("h_begin"=>10,"m_begin"=>30,"h_end"=>17,"m_end"=>0);
	$hypertension[0]=array("up"=>130,"down"=>85,"mood"=>"");
	$hypertension[1]=array("up"=>140,"down"=>90,"mood"=>"");
	$hypertension[2]=array("up"=>160,"down"=>100,"mood"=>"");
	$hypertension[3]=array("up"=>180,"down"=>110,"mood"=>"");

	static $lasttime=0;
	static $TD_date="";
	static $TD_AD_morning="";
	static $TD_AD_noon="";
	static $TD_AD_evening="";
	static $TD_pills="";
	static $uneven = true;

	$day_start = mktime(0, 0, 0, date("m",$time), date("d",$time), date("Y",$time)); //intval($time/86400) * 86400 - 3600*3;
	$noon_start = $day_start + $noon["h_begin"] * 3600 + $noon["m_begin"] * 60;
	$noon_end = $day_start + $noon["h_end"] * 3600 + $noon["m_end"] * 60;

	// New table
	if ($tab==="")
		$tab="<h2>Дневник контроля артериального давления</h2><table><tr><th>Дата</TH><th>АД утром</th><th>АД днём</th><th>АД вечером</th><th>Таблетки</th></re>\n";


	// New day
	if (($lasttime < $day_start) || ($act === "close_tab")) {
		$uneven = !$uneven;
		$tr_class = "uneven";
		if ($uneven)
			$tr_class = "even";

		// close old day;
		if ($TD_date !=="") {
			$tab .= "<tr class='$tr_class'><TD>$TD_date</TD><TD>$TD_AD_morning</TD><TD>$TD_AD_noon</TD><TD>$TD_AD_evening</TD><TD>$TD_pills</TD></TR>";
		}
		if ($act === "close_tab") {
			$tab .= "</table>
<div>
*По уровню артериального давления (АД) выделяют 3 степени гипертонической болезни:<br>
<span class='degree_n'>Нормальное АД ~120/80</span><br>
<span class='degree0'>Внимание - АД равное или превышающее ".$hypertension[0]["up"]."/".$hypertension[0]["down"]." мм. рт. ст.</span><br>
<span class='degree1'>1 степень (мягкая гипертония) - АД равное или превышающее ".$hypertension[1]["up"]."/".$hypertension[1]["down"]." мм. рт. ст.</span><br>
<span class='degree2'>2 степень (умеренная) - АД равное или превышающее ".$hypertension[2]["up"]."/".$hypertension[2]["down"]." мм. рт. ст.</span><br>
<span class='degree3'>3 степень (тяжелая) - АД равное или превышающее ".$hypertension[3]["up"]."/".$hypertension[3]["down"]." мм. рт. ст.</span><br>
</div>
";
			return;
		}
		$lasttime = $day_start;
		$TD_date = date("d-m-Y",$day_start);
		$TD_AD_morning="";
		$TD_AD_noon="";
		$TD_AD_evening="";
		$TD_pills="";
	}

	// New event
	if ($act==="pressure") {
		list($pressure_up,$pressure_down,$pulse) = explode(";", $value);
		$spanclass_u = "degree_n";
		$spanclass_d = "degree_n";
		for($i=0;$i<4;$i++) {
			if ($pressure_up>=$hypertension[$i]["up"])
				$spanclass_u = "degree".$i;
			if ($pressure_down>=$hypertension[$i]["down"])
			$spanclass_d = "degree".$i;
		}

		$item = date("H:i",$time)." "."<span class='$spanclass_u'>$pressure_up</span>/<span class='$spanclass_d'>$pressure_down</span> $pulse<BR>\n";		


		// Morning?
		if ($time < $noon_start)
			$TD_AD_morning .= $item;
		// noon
		else if ($time < $noon_end)
			$TD_AD_noon .= $item;
		else
			$TD_AD_evening .= $item;

	} else	
		$TD_pills .= date("H:i",$time)." ".$value."<BR>\n";	

	return;
}
?>