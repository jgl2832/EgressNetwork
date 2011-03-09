<?php
	exec("whois as".$argv[1],$result);
	foreach($result as $i) {
		if (strncmp($i,'Address:',8) == 0)
			printf("{$i}\n");
		if (strncmp($i,'City:',5) == 0)
			printf("{$i}\n");
		if (strncmp($i,'StateProv:',10) == 0)
			printf("{$i}\n");
		if (strncmp($i,'Country:',8) == 0)
			printf("{$i}\n");
	}


?>
