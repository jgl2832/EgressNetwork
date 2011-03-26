
<html>
	<head>
		<title>BGP Route Graph</title>
		<script language="javascript" type="text/javascript" src="hypertree1.0.js"></script>
		
		<?php
		$dbhost = 'hansonbros.ece.mcgill.ca';
		$dbuser = 'bgp';
		$dbpass = 'bgppasswd';

		$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
			or die('Error Connecting to mySQL');
		$dbname = 'egressNetworkProj';
		mysql_select_db($dbname);
		date_default_timezone_set('America/Montreal');
		$datetime = date( 'Y-m-d H:i:s');
		$query = '
			SELECT 
				fGetASN(idAS) as asn1,
				fGetASN(idNextAS) as asn2
			FROM RouteTree
			WHERE idNextAS IS NOT NULL';
			
		$result = mysql_query($query)
			or die(mysql_error()."Query: ".$query);
		$s = '';
		while($row = mysql_fetch_assoc($result)) {

			$s = $s.'|'.$row['asn1'].';'.$row['asn2'];

		}
		?>
		
		<script type="text/javascript">
		
		function addEvent( obj, type, fn ) {
			if (obj.addEventListener) {
				obj.addEventListener( type, fn, false );
				EventCache.add(obj, type, fn);
			}
			else if (obj.attachEvent) {
				obj["e"+type+fn] = fn;
				obj[type+fn] = function() { obj["e"+type+fn]( window.event ); }
				obj.attachEvent( "on"+type, obj[type+fn] );
				EventCache.add(obj, type, fn);
			}
			else {
				obj["on"+type] = obj["e"+type+fn];
			}
		}

		var EventCache = function(){
			var listEvents = [];
			return {
				listEvents : listEvents,
				add : function(node, sEventName, fHandler){
					listEvents.push(arguments);
				},
				flush : function(){
					var i, item;
					for(i = listEvents.length - 1; i >= 0; i = i - 1){
						item = listEvents[i];
						if(item[0].removeEventListener){
							item[0].removeEventListener(item[1], item[2], item[3]);
						};
						if(item[1].substring(0, 2) != "on"){
							item[1] = "on" + item[1];
						};
						if(item[0].detachEvent){
							item[0].detachEvent(item[1], item[2]);
						};
						item[0][item[1]] = null;
					};
				}
			};
		}();
	
		function init(){
		
			Config.showLabels = true;
		
			canvas = new Canvas('hypertree', '#555', '#555');
			canvas.setPosition();
			canvas.translateToCenter();
			addEvent(canvas.canvas, 'click', function (e) { Mouse.capturePosition(e); ht.translate(Mouse); });

			ht = new HT(Config, canvas);

			var str = "<?php echo $s; ?>";
			var str = str.substr(1);
			var data = str.split('|');
			document.getElementById('total').innerHTML = data.length;
			var count = 0;
			for (var i = 0; i < data.length; i++){
				var edge = data[i].split(';');
				
				for(var n = 1; n < count + 1; n++){
					if(ht.nodes[n + "_a"].info == edge[0]) break;
				}
				if(n > count){
					ht.addNode(n + "_a",edge[0]);
					count++;
				}
				for(var m = 1; m < count + 1; m++){
					if(ht.nodes[m + "_a"].info == edge[1]) break;
				}
				if(m > count){
					ht.addNode(m + "_a",edge[1]);
					count++;
				}
				
				ht.addAdjacence(n + "_a", m + "_a");
				document.getElementById('progress').innerHTML = i+1;
			}
			ht.initialize();
			ht.printOn(canvas);
		}
		</script>
	</head>
	<body onload="init();">
	<?php include("nav.php"); ?>
	
	<div id="content" style="margin-left:230px;" >

		<h1>BGP Route Graph</h1>
		<br />
		<br />
		
		Loaded: <span id="progress"></span> of <span id="total"></span>
					
		
			
		
		<canvas id="hypertree" style="border:1px solid #fff; margin-bottom:15px;" width="500" height="500"></canvas>

		
		
	</div>
	</body>
</html>
