<html>
<head>
<title>BGP Statistics</title>
</head>
<body>
<h1>BGP Statistics</h1>
<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
$datetime = date( 'Y-m-d H:i:s');
$query = 'SELECT count, avgLength FROM RouteStatsHistory ORDER BY -date LIMIT 1;';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

while($row = mysql_fetch_assoc($result)) {

	echo 'Number of distinct routes: ';
	echo $row['count'].'<br /><br />';
	echo 'Average BGP Route Length: ';
	echo $row['avgLength'].'<br /><br />';
}
mysql_close($conn);

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
$datetime = date( 'Y-m-d H:i:s');
$query = 'CALL getLengthDistribution(\''.$datetime.'\')';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

echo 'Route Length Distribution:<br><ul>';

while($row = mysql_fetch_assoc($result)) {
	
	echo $row['length'].': '.$row['count'].'<br />';
}
echo '</ul>';
mysql_close($conn);

?>


</body>
</html>
