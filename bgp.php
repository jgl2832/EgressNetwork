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
$query = 'CALL getAverageLength(\''.$datetime.'\')';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

echo 'Average BGP Route Length:<br><ul>';

while($row = mysql_fetch_assoc($result)) {
	
	echo $row['length'].'<br />';
}
echo '</ul>';
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
