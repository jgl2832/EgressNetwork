<html>
<head>
<title>IP Address <?php echo $_GET['ip']; ?></title>
<?php 
/** 
 * Convert an xml file to an associative array (including the tag attributes): 
 * 
 * @param Str $xml file/string. 
 */ 
class xmlToArrayParser { 
  /** 
   * The array created by the parser which can be assigned to a variable with: $varArr = $domObj->array. 
   * 
   * @var Array 
   */ 
  public  $array; 
  private $parser; 
  private $pointer; 

  /** 
   * $domObj = new xmlToArrayParser($xml); 
   * 
   * @param Str $xml file/string 
   */ 
  public function __construct($xml) { 
    $this->pointer =& $this->array; 
    $this->parser = xml_parser_create("UTF-8"); 
    xml_set_object($this->parser, $this); 
    xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false); 
    xml_set_element_handler($this->parser, "tag_open", "tag_close"); 
    xml_set_character_data_handler($this->parser, "cdata"); 
    xml_parse($this->parser, ltrim($xml)); 
  } 

  private function tag_open($parser, $tag, $attributes) { 
    $this->convert_to_array($tag, '_'); 
    $idx=$this->convert_to_array($tag, 'cdata'); 
    if(isset($idx)) { 
      $this->pointer[$tag][$idx] = Array('@idx' => $idx,'@parent' => &$this->pointer); 
      $this->pointer =& $this->pointer[$tag][$idx]; 
    }else { 
      $this->pointer[$tag] = Array('@parent' => &$this->pointer); 
      $this->pointer =& $this->pointer[$tag]; 
    } 
    if (!empty($attributes)) { $this->pointer['_'] = $attributes; } 
  } 

  /** 
   * Adds the current elements content to the current pointer[cdata] array. 
   */ 
  private function cdata($parser, $cdata) { 
    if(isset($this->pointer['cdata'])) { $this->pointer['cdata'] .= $cdata;} 
    else { $this->pointer['cdata'] = $cdata;} 
  } 

  private function tag_close($parser, $tag) { 
    $current = & $this->pointer; 
    if(isset($this->pointer['@idx'])) {unset($current['@idx']);} 
    $this->pointer = & $this->pointer['@parent']; 
    unset($current['@parent']); 
    if(isset($current['cdata']) && count($current) == 1) { $current = $current['cdata'];} 
    else if(empty($current['cdata'])) { unset($current['cdata']); } 
  } 

  /** 
   * Converts a single element item into array(element[0]) if a second element of the same name is encountered. 
   */ 
  private function convert_to_array($tag, $item) { 
    if(isset($this->pointer[$tag][$item])) { 
      $content = $this->pointer[$tag]; 
      $this->pointer[$tag] = array((0) => $content); 
      $idx = 1; 
    }else if (isset($this->pointer[$tag])) { 
      $idx = count($this->pointer[$tag]); 
      if(!isset($this->pointer[$tag][0])) { 
        foreach ($this->pointer[$tag] as $key => $value) { 
            unset($this->pointer[$tag][$key]); 
            $this->pointer[$tag][0][$key] = $value; 
    }}}else $idx = null; 
    return $idx; 
  } 
} 
?>


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

	##echo 'codeAddress("'.getAddress($_GET['ip']).'","'.$_GET['ip'].'");';
	echo 'addLatLon("'.$_GET['ip'].'");';
?>

  }

function addLatLon(id) {
	<?php
// initiate curl and set options

$ver = 'v1/';
$method = 'ipinfo/';
$apikey = '100.kmjxyacu4rru3kkmt8rt';  
$secret = 'vQvKHVwN';  
$timestamp = gmdate('U'); // 1200603038   
$sig = md5($apikey . $secret . $timestamp);
$service = 'http://api.quova.com/';


$querystring = $service.'v1/ipinfo/'.$_GET['ip'].'?apikey='.$apikey.'&sig='.$sig;
exec('curl "'.$querystring.'"',$curlResult);
foreach($curlResult as $i) {
	$domObj = new xmlToArrayParser($i); 
  	$domArr = $domObj->array; 
	
	$lat = $domArr['ipinfo']['Location']['latitude'];
	$lon = $domArr['ipinfo']['Location']['longitude'];
}
	?>

	var latlon = new google.maps.LatLng(<?php echo $lat ?>, <?php echo $lon ?>);
	map.setCenter(latlon);
	var marker = new google.maps.Marker({
		map: map,
		cursor: id,
		title: id,
		position: latlon
	});
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


$row = mysql_fetch_array($result);
$error = 0;
if($row['path'] == '') { 
	echo "No IP information found for the given IP address.<br />";
	$error = 1;}
else {
	echo 'Routes from McGill to subnets containing this IP and the date when the route was discovered:<br><ul>';
	while($row) {

		$token = strtok($row['path'],' ');
		echo '<li>';
		echo $row['ip'].'/'.$row['range'].': ';
		while ($token != false) {
			echo '<a href="asPage.php?as='.$token.'">'.$token.'</a> ';
			//echo $token.' ';
			$token = strtok(" ");
		}
		echo ' ('.'<a href="route.php?id='.$row['idRoute'].'">details</a>) - '.$row['date'].'</li><br>';
		$row = mysql_fetch_array($result);
	}

	echo '</ul>';
	mysql_close($conn);



	$arr = array();
	exec("whois ".$_GET['ip'],$result);

	
?>
<div id="map_canvas" style="width: 500px; height: 200px"></div>	
<!--
<br><b><a href="traceroute.php?ip=<?php echo $_GET['ip']?>">Click here for traceroute info</a></b> (May take up to a minute to display)<br>
-->
<br><b>Whois info:</b>
<?php
	foreach($result as $i) {
		if (substr($i,0,1) != "#") {
			print $i."<br>";
		}
	}
}
?>
</div>
</body>

