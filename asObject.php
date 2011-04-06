<!--
asObject.php

Egress Network Monitoring
ECSE 477

Jake Levine				260206403
Eubene Sa 				260271182
Frédéric Weigand-Warr	260191111
-->

<?php
	$arr = array();
	//Make a whois call on given AS
	exec("whois as".$argv[1],$result);
	foreach($result as $i) {
		// Get lines not beginning with # (warning lines from using whois for AS call)
		if ((substr($i,0,1) != "#") && (strlen($i) > 1) ) {
			// Separate var name from var value - split at ':'
			$pos = strpos($i, ':');
			// check if key exists in array, if not initiate, else add
			$sub = substr($i, 0,$pos);
			$sub2 = trim(substr($i, $pos +1));
			if (array_key_exists($sub,$arr)) {
				if (!is_array($arr[$sub])) {
					$arr[$sub] = array($arr[$sub]);
				}
				$arr[$sub][] = $sub2;
			} else {
				$arr[$sub] = $sub2;
			}
		}
	}
	// Print each key/value pair
	foreach($arr as $key => $val) {
		echo $key." = ";
		print_r($val);
		echo "\n";
	}
?>
