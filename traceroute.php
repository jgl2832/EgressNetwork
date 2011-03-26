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

<p>Traceroute info:</p>
<?php
        foreach($result as $i) {
                if (substr($i,0,1) != "#") {
                        print $i."<br>";
                }
        }
?>


</div>


</body>
</html>
