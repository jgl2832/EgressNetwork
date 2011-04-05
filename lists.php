<html>
<head>
	<title>BGP Statistics Lists</title>
	<link rel="stylesheet" type="text/css" href="calendar.css" />
	<script type="text/javascript" src="calendar.js"></script>
</head>
<body>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >

<h1>BGP Statistics Lists</h1>
<br />
<br />

<div id="form">
<form action="lists.php" method="GET">
<!--
View data between
<input type="text" name="date1" id="date1" />
<script type="text/javascript">
 		calendar.set("date1");
</script>
and
<input type="text" name="date2" id="date2" />
<script type="text/javascript">
 		calendar.set("date2");
</script>.
-->
<span>List type: </span>
<select name="type" id="type">
	<option value="long">Longest routes</option>
	<option value="short">Shortest routes</option>
	<option value="far">Farthest ASs</option>
	<option value="close">Closest ASs</option>
	<!--TODO
	<option value="hub">ASs with most connections</option>
	<option value="routes">Hub ASs with most routes</option>
	<option value="dest">Destination ASs with most routes</option>
	-->
</select>
<br /><br />
<span>Limit results: </span>
<input type="text" name="limit" id="limit" size="4" value="10"/><br /><br />
<!--
Include routes that have been removed? <input type="checkbox" name="inactive" id="inactive" /><br />
-->
<input type="submit" value="View"/><br />
</form>
</div>
<br />
<br />



<script type="text/javascript">
var a="<?php echo $_GET['type']; ?>"
for (i=0;i<document.getElementById('type').length;i++) {
	if (document.getElementById('type').options(i).value == a) {
		document.getElementById('type').options(i).selected = true;
		break;
	}
}
</script>


<?php
	$dbhost = 'hansonbros.ece.mcgill.ca';
	$dbuser = 'bgp';
	$dbpass = 'bgppasswd';

	$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
		or die('Error Connecting to mySQL');
	$dbname = 'egressNetworkProj';
	mysql_select_db($dbname);
	$datetime = date( 'Y-m-d H:i:s');
	
	if($_GET['type'] == 'long' || $_GET['type'] == 'short') {

		$query = '
			SELECT idRoute, MAX(position) as routeLength, getRouteStr(idRoute) as path
			FROM RouteTree
			GROUP BY idRoute
			ORDER BY '.(($_GET['type'] == 'long')?'-':'').'max(position) ';
		if($_GET['limit'] != '')
			$query = $query.' limit '.$_GET['limit'];
		else
			$query = $query.' limit 10';
			
		
		echo (($_GET['type'] == 'long')?'Longest':'Shortest').' routes<br /><br />';
			
		$result = mysql_query($query)
			or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

		while($row = mysql_fetch_assoc($result)) {

			echo '<li>';
			echo ($row['routeLength']+1).': ';
			$token = strtok($row['path'],' ');
			while ($token != false) {
				echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
				//echo $token.' ';
				$token = strtok(" ");
			}
			echo ' ('.'<a href="route.php?id='.$row['idRoute'].'">details</a>)</li><br>';
		}
		mysql_close($conn);
		
	} else if($_GET['type'] == 'far' || $_GET['type'] == 'close') {

		$query = '
			select min(position) as distance, asn
			from RouteTree
			join ASys on ASys.idAS = RouteTree.idAS
			group by RouteTree.idAS
			order by '.(($_GET['type'] == 'far')?'-':'').'min(position) ';
		if($_GET['limit'] != '')
			$query = $query.' limit '.$_GET['limit'];
		else
			$query = $query.' limit 10';
			
		
		echo (($_GET['type'] == 'far')?'Farthest':'Closest').' autonomous systems:<br /><br />';
			
		$result = mysql_query($query)
			or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

		while($row = mysql_fetch_assoc($result)) {

			echo '<li>';
			echo ($row['distance']+1).': ';
			$token = strtok($row['asn'],' ');
			while ($token != false) {
				echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
				//echo $token.' ';
				$token = strtok(" ");
			}
			echo '</li><br>';
		}
		mysql_close($conn);
		
	}

?>
</div>
</body>
</html>
