<html>
<head>
<title>Autonomous System <?php echo $_GET['as']; ?></title>
</head>
<body>
<h1>Autonomous System <?php echo $_GET['as']; ?></h1>
<?php
	$arr = array();
	exec("whois as".$_GET['as'],$result);
	foreach($result as $i) {
		if (substr($i,0,1) != "#") {
			print $i."<br>";
		}
	}
?>
<p>test</p>
</body>

