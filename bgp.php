<html>
<head>
	<title>BGP Statistics</title>
	<script language="javascript" type="text/javascript" src="flot/jquery.js"></script>
    <script language="javascript" type="text/javascript" src="flot/jquery.flot.js"></script>
</head>
<body>
<h1>BGP Statistics</h1>
<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
$datetime = date( 'Y-m-d H:i:s');
$query = 'SELECT count, avgLength FROM RouteStatsHistory ORDER BY -date LIMIT 1;';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

while($row = mysql_fetch_assoc($result)) {

	echo 'Number of distinct routes: ';
	echo $row['count'].'<br /><br />';
	echo 'Average BGP Route Length: ';
	echo $row['avgLength'].'<br /><br />';
}
mysql_close($conn);

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
$datetime = date( 'Y-m-d H:i:s');
$query = 'CALL getLengthDistribution(\''.$datetime.'\')';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

echo 'Route Length Distribution:<br><ul>';
$a = array();
$i = 0;
$s = '';
while($row = mysql_fetch_assoc($result)) {
	
	//echo $row['length'].': '.$row['count'].'<br />';
	$a[$i] = $row['length'].': '.$row['count'];
	$i = $i+1;
	$s = $s.$row['length'].':'.$row['count'].' ';
}
echo '</ul>';
mysql_close($conn);

?>

<div id="placeholder" style="width:600px;height:300px;"></div>

<script type="text/javascript">
$(function () {
	var str = "<?php echo $s; ?>"
    var d1 = str.split(' ');
    for (var i = 0; i < d1.length; i ++)
        d1[i] = d1[i].split(':');

    //var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];

    // a null signifies separate line segments
    //var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];
    
    $.plot($("#placeholder"), [ d1 ]);
});
</script>
<br />
Values: <br /><ul>
<?php 
foreach($a as $j)
	echo '<li>'.$j.'</li>';

?>
</ul>
</body>
</html>
