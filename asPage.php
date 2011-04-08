<!--
asPage.php

Egress Network Monitoring
ECSE 477

Jake Levine				260206403
Eubene Sa 				260271182
Frédéric Weigand-Warr	260191111
-->
<html>
<head>
<title>Autonomous System <?php echo $_GET['as']; ?></title>
<?php
	// Function to pull address from whois data
	function getAddress($asid) {
		// Make a whois call
		exec("whois as".$asid,$asResult);
		$queryString = "";
		foreach($asResult as $i) {
			// For each line, scan for certain info including address, city, stateprov, country
			// Strip the line of everything except the viable info.
			// Append each found to a query string, separated by commas.
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
		// Return string to be used by google code
		return $queryString;
	}

include("login_info.php");

// Connect to DB
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = $username;
$dbpass = $password;
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536)
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
// Make SQL call to get all route strings
$result = mysql_query('CALL getRouteStrByASN('.$_GET['as'].')')
	or die(mysql_error());
?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
	// Googlemaps code
  var map;
  var geocoder;
	// Default centering pt (required for some reason)
    var latlng = new google.maps.LatLng(73, 43);
  function initialize() {
    geocoder = new google.maps.Geocoder();
    var myOptions = {
      zoom: 8,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.ROADMAP
    }
   // Attach map to map_canvas div in html
    map = new google.maps.Map(document.getElementById("map_canvas"),
        myOptions);

<?php
	// Code the address 
	echo 'codeAddress("'.getAddress($_GET['as']).'","'.$_GET['as'].'");';

?>

  }
function codeAddress(address, id) {
	// Geocode an address string, attempt to find lat/lon info
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
		// If found, set the center of the map to this point
        map.setCenter(results[0].geometry.location);
		// And add a marker
        var marker = new google.maps.Marker({
            map: map, 
	    cursor: id,
	    title: id,
            position: results[0].geometry.location
        });
      } else {
		// If not found, hide the map instead of showing a blank one.
		document.getElementById("map_canvas").style.visibility = 'hidden'; 
	}
    });
	
  }

</script>
</head>
<body onload=initialize()>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >

<h1>Autonomous System <?php echo $_GET['as']; ?></h1>
<?php
// Display info from sql call above
$row = mysql_fetch_array($result);
$error = 0;
// If no results, display message
if($row == '') { 
	echo "No route information found for the given ASN.<br />";
	$error = 1;}
else {
// Otherwise display each route, make individual AS'es linked.
	echo 'Route(s) From McGill:<br><ul>';
	while($row) {
		$last = $row['path'];
	
		$token = strtok($row['path'],' ');
		echo '<li>';
		while ($token != false) {
			if($token == $_GET['as']) echo '<b>';
			// Link'em
			echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
			if($token == $_GET['as']) echo '</b>';
			$token = strtok(" ");
		}
		// Add a link to the entire route
		echo ' ('.'<a href="route.php?id='.$row['idRoute'].'">details</a>)</li><br>';
		$row = mysql_fetch_array($result);
	}
}
echo '</ul>';
?>


<?php

	
	if($error == 0){
?>
	<div id="map_canvas" style="width: 500px; height: 200px"></div>	
<?php
		$arr = array();

	
	}
		// Get and display whois info for AS
		exec("whois as".$_GET['as'],$result);

		echo "<br><b>Whois info:</b>";

		foreach($result as $i) {
			if (substr($i,0,1) != "#") {
				// Make the error message look a bit nicer
				if ($i == "Unknown AS number or IP network. Please upgrade this program.") {
					print "Unknown AS number or IP network.<br>";
				} else {
					// Print each line
					print $i."<br>";
				}
			}
			
		}
?>
</div>
</body>
</html>
