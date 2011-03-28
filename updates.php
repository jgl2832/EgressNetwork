<html>
<head>
<title>Recent BGP Route Changes</title>
<link rel="stylesheet" type="text/css" href="calendar.css" />
<script LANGUAGE="JavaScript" SRC="calendar.js">
</script>
</head>
<body>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >

<h1>Recent BGP Route Changes</h1>
<br />
<br />

<form action="updates.php" method="GET">
View changes between
<input type="text" name="date1" id="date1" />
<script type="text/javascript">
 		calendar.set("date1");
</script>
and
<input type="text" name="date2" id="date2" />
<script type="text/javascript">
 		calendar.set("date2");
</script>.
<br />
Limit to <input type="text" name="limit" id="limit" size="4" value="10"/> results.<br />
<!--
Include routes that have been removed? <input type="checkbox" name="inactive" id="inactive" /><br />
-->
<input type="submit" value="View"/><br />
</form>
<br />
<br />
<br />
<br />
Recent Changes:<br /><br />
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
		WHERE ';
	$and = '';
	if($_GET['date1'] != ''){
		$query = $query.'`date` >= \''.$_GET['date1'].'\' ';
		$and = ' AND ';
	}
	if($_GET['date2'] != ''){
		$query = $query.$and.' `date` <= \''.$_GET['date2'].'\' ';
		$and = ' AND ';
	}
	if($_GET['inactive'] != 'on'){
		$query = $query.$and.' inactiveDate IS NULL ';
	} else {
		$query = $query.$and.' inactiveDate IS NOT NULL ';
	}
	$query = $query.'
		ORDER BY -`date` ';
	if($_GET['limit'] != '')
		$query = $query.' limit '.$_GET['limit'];
	else
		$query = $query.' limit 10';
		
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
</div>
</body>
</html>
