
<html>
	<head>
		<title>BGP Route Graph</title>
		<script language="javascript" type="text/javascript" src="hypertree1.0.js"></script>
		<script language="javascript" type="text/javascript" src="Jit/jit-yc.js"></script> 
		
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
			Config.labelContainer = "ht";
		
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
				//document.getElementById('progress').innerHTML = i+1;
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
		
		 Autonomous systems: <span id="total"></span>
		
		<div id="ht"></div>
		<canvas id="hypertree" style="border:1px solid #fff; margin-bottom:15px;" width="500" height="500"></canvas>
		
		<div id="infovis"></div>
		
		<script type="text/javascript">
		
			var Log = {
				elem: false,
				write: function(text){
					if (!this.elem) 
						this.elem = document.getElementById('log');
					this.elem.innerHTML = text;
					this.elem.style.left = (500 - this.elem.offsetWidth / 2) + 'px';
				}
			};
		
			//Create a new ST instance  
			var st = new $jit.ST({  
			//id of viz container element  
			injectInto: 'infovis',  
			//set duration for the animation  
			duration: 800,  
			//set animation transition type  
			transition: $jit.Trans.Quart.easeInOut,  
			//set distance between node and its children  
			levelDistance: 50,  
			//enable panning  
			Navigation: {  
			  enable:true,  
			  panning:true  
			},  
			//set node and edge styles  
			//set overridable=true for styling individual  
			//nodes or edges  
			Node: {  
				height: 20,  
				width: 60,  
				type: 'rectangle',  
				color: '#aaa',  
				overridable: true  
			},  
			  
			Edge: {  
				type: 'bezier',  
				overridable: true  
			},  
			  
			onBeforeCompute: function(node){  
				Log.write("loading " + node.name);  
			},  
			  
			onAfterCompute: function(){  
				Log.write("done");  
			},  
			  
			//This method is called on DOM label creation.  
			//Use this method to add event handlers and styles to  
			//your node.  
			onCreateLabel: function(label, node){  
				label.id = node.id;              
				label.innerHTML = node.name;  
				label.onclick = function(){  
					if(normal.checked) {  
					  st.onClick(node.id);  
					} else {  
					st.setRoot(node.id, 'animate');  
					}  
				};  
				//set label styles  
				var style = label.style;  
				style.width = 60 + 'px';  
				style.height = 17 + 'px';              
				style.cursor = 'pointer';  
				style.color = '#333';  
				style.fontSize = '0.8em';  
				style.textAlign= 'center';  
				style.paddingTop = '3px';  
			},  
			  
			//This method is called right before plotting  
			//a node. It's useful for changing an individual node  
			//style properties before plotting it.  
			//The data properties prefixed with a dollar  
			//sign will override the global node style properties.  
			onBeforePlotNode: function(node){  
				//add some color to the nodes in the path between the  
				//root node and the selected node.  
				if (node.selected) {  
					node.data.$color = "#ff7";  
				}  
				else {  
					delete node.data.$color;  
					//if the node belongs to the last plotted level  
					if(!node.anySubnode("exist")) {  
						//count children number  
						var count = 0;  
						node.eachSubnode(function(n) { count++; });  
						//assign a node color based on  
						//how many children it has  
						node.data.$color = ['#aaa', '#baa', '#caa', '#daa', '#eaa', '#faa'][count];                      
					}  
				}  
			},  
			  
			//This method is called right before plotting  
			//an edge. It's useful for changing an individual edge  
			//style properties before plotting it.  
			//Edge data proprties prefixed with a dollar sign will  
			//override the Edge global style properties.  
			onBeforePlotLine: function(adj){  
				if (adj.nodeFrom.selected && adj.nodeTo.selected) {  
					adj.data.$color = "#eed";  
					adj.data.$lineWidth = 3;  
				}  
				else {  
					delete adj.data.$color;  
					delete adj.data.$lineWidth;  
				}  
			}  
		});  
		//load data  
		var str = "<?php echo $s; ?>";
		var str = str.substr(1);
		var data = str.split('|');
		document.getElementById('total').innerHTML = data.length;
		var count = 0;
		for (var i = 0; i < data.length; i++){
			var edge = data[i].split(';');
			
			if(!st.graph.nodes[edge[0]]){
				st.graph.addNode(edge[0],edge[0]);
			}
			
			if(!st.graph.nodes[edge[1]]){
				st.graph.addNode(edge[1],edge[1]);
			}
			
			st.graph.addAdjacence(edge[0], edge[1]);
			//document.getElementById('progress').innerHTML = i+1;
		} 
		//compute node positions and layout  
		st.compute();  
		//optional: make a translation of the tree  
		st.geom.translate(new $jit.Complex(-200, 0), "current");  
		//emulate a click on the root node.  
		st.onClick(st.root);  
		</script>
		
		
	</div>
	<div id="log"></div> 
	</body>
</html>
