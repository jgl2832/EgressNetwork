<html><head>
<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';
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