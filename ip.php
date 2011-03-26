<html>
<head>
<title>IP Address <?php echo $_GET['ip']; ?></title>

<?php
	function getAddress($asid) {
		exec("whois ".$asid,$asResult);
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
    };
  
    map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);
	
<?php

	echo 'codeAddress("'.getAddress($_GET['ip']).'","'.$_GET['ip'].'");';

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

echo 'Subnets and their routes from McGill:<br><ul>';

while($row = mysql_fetch_assoc($result)) {

$row = mysql_fetch_array($result);
$error = 0;
if($row == '') { 
	echo "No IP information found for the given IP address.<br />";
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
}
echo '</ul>';
mysql_close($conn);



	$arr = array();
	exec("whois ".$_GET['ip'],$result);

	
?>
<div id="map_canvas" style="width: 500px; height: 200px"></div>	
<p><a href="traceroute.php?ip=<?php echo $_GET['ip']?>">Click here for traceroute info</a> (May take up to a minute to display)</p> 
<p>Whois info:</p>
<?php
	foreach($result as $i) {
		if (substr($i,0,1) != "#") {
			print $i."<br>";
		}
	}
?>
</div>
</body>

