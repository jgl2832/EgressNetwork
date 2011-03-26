<html>
<head>
<title>IP Address <?php echo $_GET['ip']; ?></title>
</head>
<body>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >

<h1>IP Address <?php echo $_GET['ip']; ?></h1>
<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
$query = 'CALL getPrefixByIP(\''.$_GET['ip'].'\')';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);



$row = mysql_fetch_array($result);
$error = 0;
if($row == '') { 
	echo "No IP information found with the given IP address.<br />";
	$error = 1;}
else {
	echo 'Subnets and their routes from McGill:<br><ul>';
	while($row) {
	
		$token = strtok($row['path'],' ');
		echo '<li>';
		echo $row['ip'].'/'.$row['range'].': ';
		while ($token != false) {
			echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
			//echo $token.' ';
			$token = strtok(" ");
		}
		echo ' ('.'<a href="route.php?id='.$row['idRoute'].'">details</a>)</li><br>';
		$row = mysql_fetch_array($result);
	}
}
echo '</ul>';
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

	if($error == 0){
	
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
		exec("whois ".$_GET['ip'],$result);

	
		echo "<p><a href=\"traceroute.php?ip=\"".$_GET['ip']."\">Click here for traceroute info</a> (May take up to a minute to display)</p>";
		echo "<p>Whois info:</p>";

		foreach($result as $i) {
			if (substr($i,0,1) != "#") {
				print $i."<br>";
			}
		}
	}
?>
</div>
</body>

