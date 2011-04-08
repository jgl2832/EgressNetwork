<!--
random.php

Egress Network Monitoring
ECSE 477

Jake Levine				260206403
Eubene Sa 				260271182
Frédéric Weigand-Warr	260191111
-->
<html><head>
<?php
include("login_info.php");

$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = $username;
$dbpass = $password;
$dbname = 'egressNetworkProj';

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
mysql_select_db($dbname);
$query = 'CALL getRandomRoute();';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);
$row = mysql_fetch_assoc($result);
	mysql_close($conn);
	?>

	</head>
	<body>
	<?php echo $result['idRoute']; ?>
	<script type="text/javascript">
	var str = "<?php echo $row['idRoute']; ?>";
	window.location.href = "route.php?id=" + str ;
	</script>
	</body>
</html>
