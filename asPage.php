<html>
<head>
<title>Autonomous System <?php echo $_GET['as']; ?></title>
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
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536)
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);

$result = mysql_query('CALL getRouteStrByASN('.$_GET['as'].')')
	or die(mysql_error());
?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false">
</script>
<script type="text/javascript">
  var map;
  var geocoder;
    var latlng = new google.maps.LatLng(73, 43);
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

	echo 'codeAddress("'.getAddress($_GET['as']).'","'.$_GET['as'].'");';

?>

  }
function codeAddress(address, id) {
	
    geocoder.geocode( { 'address': address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        map.setCenter(results[0].geometry.location);

        var marker = new google.maps.Marker({
            map: map, 
	    cursor: id,
	    title: id,
            position: results[0].geometry.location
        });
      } else {
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

$row = mysql_fetch_array($result);
$error = 0;
if($row == '') { 
	echo "No route information found for the given ASN.<br />";
	$error = 1;}
else {
	echo 'Route(s) From McGill:<br><ul>';
	while($row) {
		$last = $row['path'];
	
		$token = strtok($row['path'],' ');
		echo '<li>';
		while ($token != false) {
			if($token == $_GET['as']) echo '<b>';
			echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
			if($token == $_GET['as']) echo '</b>';
			//echo $token.' ';
			$token = strtok(" ");
		}
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

		exec("whois as".$_GET['as'],$result);

		echo "<br><b>Whois info:</b>";

		foreach($result as $i) {
			if (substr($i,0,1) != "#") {
				if ($i == "Unknown AS number or IP network. Please upgrade this program.") {
					print "Unknown AS number or IP network.<br>";
				} else {
					print $i."<br>";
				}
			}
			
		}
?>
</div>
</body>
</html>
