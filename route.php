<html>
<head>
<title>Route details</title>
</head>
<body>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >

<h1>Route Details</h1>
<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';
$dbname = 'egressNetworkProj';

//Route Details
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
mysql_select_db($dbname);
$datetime = date( 'Y-m-d H:i:s');
$query = 'CALL getRoute('.$_GET['id'].')';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

while($row = mysql_fetch_assoc($result)) {

		$last = $row['path'];
		echo 'AS path: ';
		$token = strtok($row['path'],' ');
		while ($token != false) {
			echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
			//echo $token.' ';
			$token = strtok(" ");
		}
		echo '<br /><br />';
		echo 'Added: '.$row['date'].'<br /><br />';
		if($row['inactiveDate'] != ''){
			echo 'Removed: '.$row['inactiveDate'].'<br /><br />';
		}
}
mysql_close($conn);


//Agregator information
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
mysql_select_db($dbname);
$query = 'SELECT ip FROM Aggregator WHERE idRoute = '.$_GET['id'];
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

while($row = mysql_fetch_assoc($result)) {
	echo 'Aggregator IP: '.$row['ip'].'<br />';
}
echo '<br />';
mysql_close($conn);

//Prefixes
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
mysql_select_db($dbname);
$query = 'SELECT * FROM Prefix WHERE idRoute = '.$_GET['id'];
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

echo 'Subnets reached:<br><ul>';

while($row = mysql_fetch_assoc($result)) {
	echo '<li>'.$row['ip'].'/'.$row['range'].'</li>';
}
echo '</ul><br />';
mysql_close($conn);






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
	echo 'Route Map<br />';
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
</div>
</body>
</html>
