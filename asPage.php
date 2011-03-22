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

$result = mysql_query('CALL get1RouteStrByASN('.$_GET['as'].')')
	or die(mysql_error());

echo 'Route(s) From McGill:<br><ul>';

while($row = mysql_fetch_array($result)) {
	$token = strtok($row['path'],' ');
	echo '<li>';
	while ($token != false) {
		echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
		//echo $token.' ';
		$token = strtok(" ");
	}
	echo '</li><br>';
}
echo '</ul';
?>

<p>Whois info:</p>
<?php
	$arr = array();
	exec("whois as".$_GET['as'],$result);
	foreach($result as $i) {
		if (substr($i,0,1) != "#") {
			print $i."<br>";
		}
	}
?>
<p>test</p>
</body>

