<?php
echo "Start\n";
$mkurl = "http://localhost/Pressure/"; 
	while(1) {
		echo file_get_contents($mkurl)."\n";
		sleep(3);
	}
?>