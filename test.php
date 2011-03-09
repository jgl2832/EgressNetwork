<?php
	exec("whois as47",$result);
	foreach($result as $i)
		printf("{$i}\n");


?>
