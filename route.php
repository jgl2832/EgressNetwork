<!--
route.php

Egress Network Monitoring
ECSE 477

Jake Levine				260206403
Eubene Sa 				260271182
Frédéric Weigand-Warr	260191111
-->
<html>
<head>
<title>Route Details</title>
<?php
	date_default_timezone_set('America/Montreal');
	// Get address info from an ASID - for attempting to map
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

// Connect to DB

$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';
$dbname = 'egressNetworkProj';

//Route Details
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
mysql_select_db($dbname);
$datetime = date( 'Y-m-d H:i:s');

// Get the info for this route
$query = 'CALL getRoute('.$_GET['id'].')';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);
?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
	// Init map
  var map;
  var geocoder;
  var bounds;
  var poly;
  var locs = [];
    var latlng = new google.maps.LatLng(73, 43);
    var bounds = new google.maps.LatLngBounds();
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
	// Set up path line
    var polyOptions = {
    		strokeColor: '#000000',
    		strokeOpacity: 1.0,
    		strokeWeight: 3
  	}

    poly = new google.maps.Polyline(polyOptions);
	// Attach map to map_canvas div in html
    map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);
    poly.setMap(map);
<?php
	// For each php query result
	while($row = mysql_fetch_assoc($result)) {
		$last = $row['path'];
	}
	$token = strtok($last, ' ');
	while ($token != false) {
		// Code the address and attempt to add to map
		echo 'codeAddress("'.getAddress($token).'","'.$token.'");';
		$token = strtok(' ');
	}
?>

	var path = poly.getPath();

  }
function codeAddress(address, id) {
	// Code a given address, attempt to add to path as well
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
	var path = poly.getPath();
	// Add path and extend map bounds to include this new location
  	path.push(results[0].geometry.location);
	bounds.extend(results[0].geometry.location);
        map.fitBounds(bounds);

        var marker = new google.maps.Marker({
            map: map, 
	    cursor: id,
	    title: id,
            position: results[0].geometry.location
        });
      } 
    });
	
  }

</script>

</head>
<body onload=initialize()>


<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >

<h1>Route Details</h1><br /><br />
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
		// Display each AS on the path and make it linked
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
?>
<div id="map_canvas" style="width: 500px; height: 200px"></div>	
<?php
//Prefixes
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
mysql_select_db($dbname);
$query = 'SELECT * FROM Prefix WHERE idRoute = '.$_GET['id'];
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

echo '<br /><br />Subnets reached:<br><ul>';

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
