<link rel="stylesheet" href="nav.css" /> 

<SCRIPT LANGUAGE="JavaScript">
<!-- Original:  Jay Bienvenu -->
<!-- Web Site:  http://www.bienvenu.net -->

<!-- This script and many more are available free online at -->
<!-- The JavaScript Source!! http://javascript.internet.com -->

<!-- Begin
function verifyIP (IPvalue) {
errorString = "";
theName = "IPaddress";

var ipPattern = /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/;
var ipArray = IPvalue.match(ipPattern);

if (IPvalue == "0.0.0.0")
errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
else if (IPvalue == "255.255.255.255")
errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
if (ipArray == null)
errorString = errorString + theName + ': '+IPvalue+' is not a valid IP address.';
else {
for (i = 0; i < 4; i++) {
thisSegment = ipArray[i];
if (thisSegment > 255) {
errorString = errorString + theName + ': '+IPvalue+' is not a valid IP address.';
i = 4;
}
if ((i == 0) && (thisSegment > 255)) {
errorString = errorString + theName + ': '+IPvalue+' is a special IP address and cannot be used here.';
i = 4;
      }
   }
}
extensionLength = 3;
if (errorString == ""){
	document.getElementById('validIP').innerHTML = '<br />';
	return true;}
else{
	document.getElementById('validIP').innerHTML = 'Invalid IP Address';
	return false;}
}
//  End -->

function verifyASN(asn){

	if (parseInt(asn) != asn-0 ){
		document.getElementById('validASN').innerHTML = 'Invalid ASN';
		return false;
	}
	else {
		document.getElementById('validASN').innerHTML = '<br />';
		return true;
	}
}


</script>


	
<div id="nav">

	<div id="mcgill"> McGill University </div>
	<a href="index.php">BGP Network <br />Monitoring Utility</a>
	
	<hr>
	
	<a href="map.php">Route Map</a>
	<a href="bgp.php">BGP Statistics</a>
	<a href="route.php">Route of the Day</a>
	<a href="updates.php">Newest Routes</a>
	
	<hr>
	
	<div id="input">
		<form name="as_input" onsubmit="return verifyASN(as.value);" action="asPage.php" method="get">
			Autonomous System Lookup <br />
			<input type="text" name="as" size=15/>
			<input type="submit" value="Search"/>
			<div id="validASN" style="color:red"><br /></div>
		</form>
	</div>
	<div id="input">
		<form id="ip_input"  onsubmit="return verifyIP(ip.value);" action="ip.php" method="GET" >
			IP Address Lookup <br />
			<input size=15 name="ip">
			<input type="submit" value="Search" >
			<div id="validIP" style="color:red"><br /></div>
		</form>
	</div>
	
	<hr>
	<!--
	<div id="update">
	Data last updated:<br />
	<br />-->
	<div id="update">
	Page last modified:<br />
	<?php 
		date_default_timezone_set('America/Montreal');
		echo date('F d Y H:i:s', getlastmod() );
	?><br />
	
	</div>
</div>
