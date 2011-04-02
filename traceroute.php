<html>
<head>
<title>Traceroute</title>
</head>
<body>

<?php include("nav.php"); ?>

<div id="content" style="margin-left:230px;" >


<?php     
 exec("traceroute ".$_GET['ip'],$result);
?>

<h1>Traceroute info:</h1>
<br />
<?php
	$open = '(';
	$close = ')';
        foreach($result as $i) {
                if (substr($i,0,1) != "#") {
			$pos1 = strpos($i,$open);
			$pos2 = strpos($i,$close);
			if (($pos1 == false) || ($pos2 == false)) {
				echo $i;
			} else {
				echo substr($i,0,$pos1 + 1);
				echo '<a href="ip.php?ip=';
				echo substr($i,$pos1 + 1,$pos2-$pos1 - 1);
				echo '">';
				echo substr($i,$pos1 + 1,$pos2-$pos1 - 1);
				echo '</a>';
				echo substr($i,$pos2);
			}
			print "<br>";
                }
        }
?>


</div>


</body>
</html>
