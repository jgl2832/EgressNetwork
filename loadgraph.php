<html>
<head>
<title>Loading, please wait...</title>

<script>
var ctr = 1;
var ctrMax = 500; // how many is up to you-how long does your end page take?
var intervalId;
function Begin()
{
//set this page's window.location.href to the target page
window.location.href = "map.php";
// but make it wait while we do our progress...
intervalId = window.setInterval("ctr=UpdateIndicator(ctr, ctrMax)",
500);
}
function End() {
// once the interval is cleared, we yield to the result page (which has been running)
window.clearInterval(intervalId);
}

function UpdateIndicator(curCtr, ctrMaxIterations)
{
curCtr += 1;
if (curCtr <= ctrMaxIterations) {
indicator.style.width =curCtr +"px";
return curCtr;
}
else
{
indicator.style.width =0;
return 1;
}
}
</script>
</HEAD>
<body onload="Begin()" onunload="End()">
<?php include("nav.php") ?>
<div id="content" style="margin-left:230px;">
<h1>BGP Route Graph</h1>
		<br />
		<br />
		<div id="loading">Please wait while the hyperbolic graph is loaded.</div>

<form id="Form1" method="post" runat="server">
<table id=indicator border="0" cellpadding="0" cellspacing="0"
width="0" height="20" align="center" >
<tr>
<td align="center" bgcolor=white width="100%"></td>
</tr>
</table>
</form>
</div>
</body>
</html>