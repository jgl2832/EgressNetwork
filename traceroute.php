<!--
traceroute.php

Egress Network Monitoring
ECSE 477

Jake Levine				260206403
Eubene Sa 				260271182
Frédéric Weigand-Warr	260191111
-->
<html>
<head>
<title>Traceroute</title>
</head>
<body>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >


<?php     
	// Execute a traceroute call on the given IP address
 exec("traceroute ".$_GET['ip'],$result);
?>

<h1>Traceroute info:</h1>
<br />
<?php
	// Display the traceroute info line by line -
	// Parse for i.p. addresses to make them linkable

	// Scan for IPs contained within '()'
	$open = '(';
	$close = ')';
        foreach($result as $i) {
				// if not a comment line
                if (substr($i,0,1) != "#") {
			// Get position of first ()'s
			$pos1 = strpos($i,$open);
			$pos2 = strpos($i,$close);
			// If none, do nothing
			if (($pos1 == false) || ($pos2 == false)) {
				echo $i;
			} else {
				// Otherwise, pull out contents and surround them with a link to itself.
				echo substr($i,0,$pos1 + 1);
				echo '<a href="ip.php?ip=';
				echo substr($i,$pos1 + 1,$pos2-$pos1 - 1);
				echo '">';
				echo substr($i,$pos1 + 1,$pos2-$pos1 - 1);
				echo '</a>';
				echo substr($i,$pos2);
			}
			print "<br>";
                }
        }
?>


</div>


</body>
</html>
