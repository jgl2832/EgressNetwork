<html>
<head>
	<title>BGP Statistics</title>
	<script language="javascript" type="text/javascript" src="flot/jquery.js"></script>
    <script language="javascript" type="text/javascript" src="flot/jquery.flot.js"></script>
	<script language="javascript" type="text/javascript" src="flot/jquery.flot.crosshair.js"></script> 
	<link rel="stylesheet" type="text/css" href="calendar.css" />
<script LANGUAGE="JavaScript" SRC="calendar.js">
</script>
</head>
<body>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >


<!--
<div style="float:right">
<form action="bgp.php" method="GET">
<div style="float:right">
From
<input type="text" name="date1" id="date1" size="38" value="<?php echo $_GET['date1']?>"/>
<script type="text/javascript">
 		calendar.set("date1");
</script>
</div>
<br />
<div style="float:right">
To
<input type="text" name="date2" id="date2" size="38" value="<?php echo $_GET['date2']?>"/>
<script type="text/javascript">
 		calendar.set("date2");
</script>
</div>
<br />
<input style="float:right" type="submit" value="View"/><br />
</form>
</div>
-->

<h1>BGP Statistics</h1>

<br />
<br />
<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);

//setup graph
$graph = new stdclass;
$graph->width = 500;
$graph->height = 350;

$graph->data=array();

$query = 'select tbl.idAS,a.asn,count(*) from (select distinct idAS,idNextAS from RouteTree) AS tbl JOIN ASys a on a.idAS=tbl.idAS group by tbl.idAS order by count(*) desc limit 10';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

while($row = mysql_fetch_assoc($result)) {
	$graph->data[$row['asn']] = $row['count(*)'];
}

$graph->setGradient = array('red', 'maroon');
$graph->setLegend = 'true';
$graph->setLegendTitle = 'Connections';
$graph->setTitle = 'Top Autonomous Systems (by # of AS connected to)';
$graph->setTitleLocation = 'left';
 
//JSON encode graph object
$encoded = urlencode(json_encode($graph));
 
//retrieve XML
$target = 'http://www.ebrueggeman.com/phpgraphlib/api/?g=' . $encoded . '&type=xml';
$xml_object =  new SimpleXMLElement($target, NULL, TRUE);
 
//if there are no errors, display graph
if (empty($xml_object->error)) {
  echo $xml_object->imageTag;
  echo "<br>";
}
else {
  echo 'There was an error generating the graph: '. $xml_object->error;
}


mysql_close($conn);

?>

<?php
$dbhost = 'hansonbros.ece.mcgill.ca';
$dbuser = 'bgp';
$dbpass = 'bgppasswd';

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);

//setup graph
$graph = new stdclass;
$graph->width = 500;
$graph->height = 350;

$graph->data=array();

$query = 'select r.idAS,a.asn,count(*) from RouteTree r JOIN ASys a on a.idAS=r.idAS group by r.idAS order by count(*) desc limit 10';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

while($row = mysql_fetch_assoc($result)) {
	$graph->data[$row['asn']] = $row['count(*)'];
}

$graph->setGradient = array('red', 'maroon');
$graph->setLegend = 'true';
$graph->setLegendTitle = 'Routes';
$graph->setTitle = 'Top Autonomous Systems (by number of routes they belong to)';
$graph->setTitleLocation = 'left';
 
//JSON encode graph object
$encoded = urlencode(json_encode($graph));
 
//retrieve XML
$target = 'http://www.ebrueggeman.com/phpgraphlib/api/?g=' . $encoded . '&type=xml';
$xml_object =  new SimpleXMLElement($target, NULL, TRUE);
 
//if there are no errors, display graph
if (empty($xml_object->error)) {
  echo $xml_object->imageTag;
  echo "<br>";
}
else {
  echo 'There was an error generating the graph: '. $xml_object->error;
}


mysql_close($conn);

?>

<?php

$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
$datetime = date( 'Y-m-d H:i:s');
$query = '
	SELECT count,avgLength,`date`
	FROM RouteStatsHistory
	WHERE ';
	
$and = '';
if($_GET['date1'] != ''){
	$query = $query.'`date` >= \''.$_GET['date1'].'\' ';
	$and = ' AND ';
}
if($_GET['date2'] != ''){
	$query = $query.$and.' `date` <= \''.$_GET['date2'].'\' ';
	$and = ' AND ';
}
$query = $query.$and.'
	id IS NOT NULL 
	ORDER BY `date`;';

$result = mysql_query($query)
	or die(mysql_error()."Query: ".$query);
$sc = '';
$sl = '';
$last;
while($row = mysql_fetch_assoc($result)) {

	$last = $row;
	$sc = $sc.'|'.$row['date'].';'.$row['count'];
	$sl = $sl.'|'.$row['date'].';'.$row['avgLength'];

}
echo 'Number of Distinct Routes: ';
echo $last['count'].'<br /><br />';
?>

<div id="countGraph" style="width:600px;height:300px;"></div>
<p id="hoverdata"></p> 

<script type="text/javascript">
var plot1;
$(function () {
	var str = "<?php echo $sc; ?>"
	str = str.substr(1);
    var d1 = str.split('|');
    for (var i = 0; i < d1.length; i ++){
        d1[i] = d1[i].split(';');
		ts = (new Date(d1[i][0])).getTime();
		d1[i][0] = ts;
	}
	d1.push([(new Date()).getTime(),d1[d1.length-1][1]]);
	plot1 = $.plot($("#countGraph"),
		  [ { data: d1, label: "Count = " + d1[d1.length-1][1]} ], {
				series: {
					lines: { show: true }
				},
				crosshair: { mode: "x" },
				grid: { hoverable: true, autoHighlight: false },
				xaxis: { mode: "time" }
			});
	
	var legends1 = $("#countGraph .legendLabel");
    legends1.each(function () {
        // fix the widths so they don't jump around
        $(this).css('width', $(this).width());
    });
 
    var updateLegendTimeout1 = null;
    var latestPosition1 = null;
    
    function updateLegend1() {
        updateLegendTimeout1 = null;
        
        var pos = latestPosition1;
        
        var axes = plot1.getAxes();
        if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
            pos.y < axes.yaxis.min || pos.y > axes.yaxis.max)
            return;
 
        var i, j, dataset = plot1.getData();
        for (i = 0; i < dataset.length; ++i) {
            var series = dataset[i];
 
            // find the nearest points, x-wise
            for (j = 0; j < series.data.length; ++j)
                if (series.data[j][0] > pos.x)
                    break;
            
            // now interpolate
            var y = series.data[j - 1];
            legends1.eq(i).text(series.label.replace(/=.*/, "= " + y[1]));
        }
    }
    
    $("#countGraph").bind("plothover",  function (event, pos, item) {
        latestPosition1 = pos;
        if (!updateLegendTimeout1)
            updateLegendTimeout1 = setTimeout(updateLegend1, 50);
    });
	
});
</script>
<br />

<?php
echo 'Average BGP Route Length: ';
echo $last['avgLength'].'<br /><br />';

mysql_close($conn);
?>
<div id="avgGraph" style="width:600px;height:300px;"></div>

<script type="text/javascript">
var plot2;
$(function () {
	var str = "<?php echo $sl; ?>"
	str = str.substr(1);
    var d2 = str.split('|');
    for (var i = 0; i < d2.length; i ++){
        d2[i] = d2[i].split(';');
		ts = (new Date(d2[i][0])).getTime();
		d2[i][0] = ts;
	}
	d2.push([(new Date()).getTime(),d2[d2.length-1][1]]);
	plot2 = $.plot($("#avgGraph"),
		  [ { data: d2, label: "Average = " + d2[d2.length-1][1]} ], {
				series: {
					lines: { show: true }
				},
				crosshair: { mode: "x" },
				grid: { hoverable: true, autoHighlight: false },
				xaxis: { mode: "time" }
			});
	
	var legends2 = $("#avgGraph .legendLabel");
    legends2.each(function () {
        // fix the widths so they don't jump around
        $(this).css('width', $(this).width());
    });
 
    var updateLegendTimeout2 = null;
    var latestPosition2 = null;
    
    function updateLegend2() {
        updateLegendTimeout2 = null;
        
        var pos = latestPosition2;
        
        var axes = plot2.getAxes();
        if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
            pos.y < axes.yaxis.min || pos.y > axes.yaxis.max)
            return;
 
        var i, j, dataset = plot2.getData();
        for (i = 0; i < dataset.length; ++i) {
            var series = dataset[i];
 
            // find the nearest points, x-wise
            for (j = 0; j < series.data.length; ++j)
                if (series.data[j][0] > pos.x)
                    break;
            
            // now interpolate
            var y = series.data[j - 1];
            legends2.eq(i).text(series.label.replace(/=.*/, "= " + (new Number(y[1])).toFixed(2)));
        }
    }
    
    $("#avgGraph").bind("plothover",  function (event, pos, item) {
        latestPosition2 = pos;
        if (!updateLegendTimeout2)
            updateLegendTimeout2 = setTimeout(updateLegend2, 50);
    });
	
});
</script>
<br /><br />

<?php
$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
	or die('Error Connecting to mySQL');
$dbname = 'egressNetworkProj';
mysql_select_db($dbname);
$datetime =  ($_GET['date2']=='')?date( 'Y-m-d H:i:s'):$_GET['date2'];
$query = 'CALL getLengthDistribution(\''.$datetime.'\')';
$result = mysql_query($query)
	or die("Query failed: " . mysql_error() . "<br /> Query: " . $query);

echo 'Route Length Distribution:<br><ul>';
$ad = array();
$i = 0;
$sd = '';
while($row = mysql_fetch_assoc($result)) {
	
	//echo $row['length'].': '.$row['count'].'<br />';
	$ad[$i] = $row['length'].': '.$row['count'];
	$i = $i+1;
	$sd = $sd.' '.$row['length'].':'.$row['count'];
}
echo '</ul>';
mysql_close($conn);

?>

<div id="distGraph" style="width:600px;height:300px;"></div>

<script type="text/javascript">
var plot;
$(function () {
	var str = "<?php echo $sd; ?>"
	str = str.substr(1);
    var d3 = str.split(' ');
    for (var i = 0; i < d3.length; i++)
        d3[i] = d3[i].split(':');
	d3.push([d3.length+1,0]);
	
	plot = $.plot($("#distGraph"),
		  [ { data: d3, label: "Length: Count: "} ], {
				series: {
					lines: { show: true }
				},
				crosshair: { mode: "x" },
				grid: { hoverable: true, autoHighlight: false }
			});
	
	var legends = $("#distGraph .legendLabel");
    legends.each(function () {
        // fix the widths so they don't jump around
        $(this).css('width', $(this).width());
    });
 
    var updateLegendTimeout = null;
    var latestPosition = null;
    
    function updateLegend() {
        updateLegendTimeout = null;
        
        var pos = latestPosition;
        
        var axes = plot.getAxes();
        if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
            pos.y < axes.yaxis.min || pos.y > axes.yaxis.max)
            return;
 
        var i, j, dataset = plot.getData();
        for (i = 0; i < dataset.length; ++i) {
            var series = dataset[i];
 
            // find the nearest points, x-wise
            for (j = 0; j < series.data.length + 1; ++j)
                if (series.data[j][0] > pos.x)
                    break;
            
            // now interpolate
            var y = series.data[j-1];
			var lbl = series.label.replace(/Length[^C]*/, "Length: " + y[0] + " ");
			lbl = lbl.replace(/Count.*/, " Count: " + y[1]);
            legends.eq(i).text(lbl);
        }
    }
    
    $("#distGraph").bind("plothover",  function (event, pos, item) {
        latestPosition = pos;
        if (!updateLegendTimeout)
            updateLegendTimeout = setTimeout(updateLegend, 50);
    });
	
});
</script>
<br /><!--
Values: <br /><ul>
<?php 
//foreach($ad as $j)
//	echo '<li>'.$j.'</li>';

?>
</ul>
-->
</div>
</body>
</html>
