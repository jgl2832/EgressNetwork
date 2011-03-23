<html>
<head>
<title>Recent BGP Route Changes</title>
</head>
<body>
<h1>Recent BGP Route Changes</h1>
<?php
	$dbhost = 'hansonbros.ece.mcgill.ca';
	$dbuser = 'bgp';
	$dbpass = 'bgppasswd';

	$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
		or die('Error Connecting to mySQL');
	$dbname = 'egressNetworkProj';
	mysql_select_db($dbname);
	$datetime = date( 'Y-m-d H:i:s');
	$query = '
		SELECT idRoute, `date`, getRouteStr(idRoute) as path
		FROM Route
		WHERE inactiveDate IS NULL
		ORDER BY -`date`
		limit 10;';
	$result = mysql_query($query)
		or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

	while($row = mysql_fetch_assoc($result)) {

		echo '<li>';
		echo $row['date'].': ';
		$token = strtok($row['path'],' ');
		while ($token != false) {
			echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
			//echo $token.' ';
			$token = strtok(" ");
		}
		echo ' ('.'<a href="route.php?id='.$row['idRoute'].'">details</a>)</li><br>';
	}
	mysql_close($conn);


?>

</body>
</html>