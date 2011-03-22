<html>
<head>
<title>Autonomous System <?php echo $_GET['as']; ?></title>
</head>
<body>
<h1>Autonomous System <?php echo $_GET['as']; ?></h1>
<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';
$conn = mysql_connect($dbhost,$dbuser,$dbpass) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);

$result = mysql_query('CALL getRouteStrByASN('.$_GET['as'].')')
	or die(mysql_error());

echo 'Route(s) From McGill:<br><ul>';

while($row = mysql_fetch_array($result)) {
	$last = $row['path'];
	$token = strtok($row['path'],' ');
	echo '<li>';
	while ($token != false) {
		echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
		//echo $token.' ';
		$token = strtok(" ");
	}
	echo '</li><br>';
}
echo '</ul>';
?>


<?php
	function getAddress($asid) {
		exec("whois as".$asid,$asResult);
		$queryString = "";
		foreach($asResult as $i) {
			
			if (strncmp(strtolower($i), 'address:',8) == 0) {
				$addString = str_replace(" ","+",trim(substr($i, 8)));
				$queryString = $queryString."".$addString.",";
			}
			if (strncmp(strtolower($i),'city:',5) == 0) {
				$addString = str_replace(" ","+",trim(substr($i,5) ));
				$queryString = $queryString."".$addString.",";
			}
			if (strncmp(strtolower($i),'stateprov:',10) == 0) {
				$addString = str_replace(" ","+",trim(substr($i,10)));
				$queryString = $queryString."".$addString.",";
			}
			if (strncmp(strtolower($i),'country:',8) == 0) {
				$addString = str_replace(" ","+",trim(substr($i,8)));
				$queryString = $queryString."".$addString;
			}	
		}
		return $queryString;
	}

	echo '<img src="http://maps.google.com/maps/api/staticmap?size=500x200';
	$token = strtok($last, ' ');

	while ($token != false) {
		echo '&markers=size:large|color:green|'.getAddress($token);
		$token = strtok(' ');
	}
/*
	echo '&path=color:0xff0000ff|weight:5';
	$token = strtok($last, ' ');
	while ($token != false) {
		echo '|'.getAddress($token);
	}
*/
	echo '&sensor=false" />';

	$arr = array();
	exec("whois as".$_GET['as'],$result);

	
?>
<p>Whois info:</p>
<?php
	foreach($result as $i) {
		if (substr($i,0,1) != "#") {
			print $i."<br>";
		}
	}
?>

</body>

