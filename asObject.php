<?php
	$arr = array();
	exec("whois as".$argv[1],$result);
	foreach($result as $i) {
		if ((substr($i,0,1) != "#") && (strlen($i) > 1) ) {
			$pos = strpos($i, ':');
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
	foreach($arr as $key => $val) {
		echo $key." = ";
		print_r($val);
		echo "\n";
	}
?>
