<!--
retAdd.php

Egress Network Monitoring
ECSE 477

Jake Levine				260206403
Eubene Sa 				260271182
Frédéric Weigand-Warr	260191111
-->
<?php
	// Make whois call on first argument, pull out strings beginning in key address related fields
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
