<html>
<head>
<title>Route details</title>
<?php
	function getAddress($asid) {
		exec("whois as".$asid,$asResult);
		$queryString = "";
		foreach($asResult as $i) {
			
			if (strncmp(strtolower($i), 'address:',8) == 0) {
				$addString = trim(substr($i, 8));
				$queryString = $queryString."".$addString.",";
			}
			if (strncmp(strtolower($i),'city:',5) == 0) {
				$addString = trim(substr($i,5));
				$queryString = $queryString."".$addString.",";
			}
			if (strncmp(strtolower($i),'stateprov:',10) == 0) {
				$addString = trim(substr($i,10));
				$queryString = $queryString."".$addString.",";
			}
			if (strncmp(strtolower($i),'country:',8) == 0) {
				$addString = trim(substr($i,8));
				$queryString = $queryString."".$addString;
			}	
		}
		return $queryString;
	}
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
?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
  var map;
  var geocoder;
  var bounds;
    var latlng = new google.maps.LatLng(73, 43);
    var bounds = new google.maps.LatLngBounds();
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
    map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);

<?php

	while($row = mysql_fetch_assoc($result)) {
		$last = $row['path'];
	}
	$token = strtok($last, ' ');
	while ($token != false) {
		echo 'codeAddress("'.getAddress($token).'");';
		$token = strtok(' ');
	}
?>
  }
function codeAddress(address) {
	
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
	bounds.extend(results[0].geometry.location);
        map.fitBounds(bounds);
        var marker = new google.maps.Marker({
            map: map, 
            position: results[0].geometry.location
        });
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
	
  }

</script>

</head>
<body onload=initialize()>


<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >

<h1>Route Details</h1>
<?php

mysql_close($conn);


//Agregator information
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
mysql_select_db($dbname);
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
?>

	<div id="map_canvas" style="width: 500px; height: 200px"></div>	
<?php

	$arr = array();
	exec("whois as".$_GET['as'],$result);


?>
</div>
</body>
</html>
