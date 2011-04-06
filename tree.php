<!--
tree.php

Egress Network Monitoring
ECSE 477

Jake Levine				260206403
Eubene Sa 				260271182
Frédéric Weigand-Warr	260191111
-->
<html>
	<head>
		<title>BGP Route Tree</title>
		<!-- CSS Files --> 
		<link type="text/css" href="base.css" rel="stylesheet" /> 
		<link type="text/css" href="spacetree.css" rel="stylesheet" /> 
		<!--<script language="javascript" type="text/javascript" src="hypertree1.0.js"></script>-->
		<script language="javascript" type="text/javascript" src="Jit/jit.js"></script> 

		<?php
		$dbhost = 'hansonbros.ece.mcgill.ca';
		$dbuser = 'bgp';
		$dbpass = 'bgppasswd';
		$dbname = 'egressNetworkProj';

		$conn = mysql_connect($dbhost,$dbuser,$dbpass, true, 65536) 
			or die('Error Connecting to mySQL');
		mysql_select_db($dbname);
		
		$query = '
			select distinct
				a1.asn as asn1,
				a2.asn as asn2
				from RouteTree
				join ASys AS a1 on a1.idAS = RouteTree.idAS
				join ASys AS a2 on a2.idAS = RouteTree.idNextAS ';
		if($_GET['as'] != '') $query = $query . 'where a1.asn = ' . $_GET['as']; 
		$query = $query . ' limit 50;';
			
		$result = mysql_query($query)
			or die(mysql_error()."Query: ".$query);
		$s = '{ id: "' . $_GET['as'] . '", name: "' . $_GET['as'] . '", data: {}, children: [  ';
		while($row = mysql_fetch_assoc($result)) {

			$s = $s.'{ id: "' . $row['asn2'] . '", name: "' . $row['asn2'] . '", data: {}, children: []}, ';
		}
		$s = substr($s, 0, $s.length - 2);
		$s = $s.']}';
		mysql_close($conn);
		?>
		
		
		<script language="javascript" type="text/javascript">
		
		var st;
		var arr;
		
		function buildJSON(parent){
			
			if(parent != ""){
				var children = "";
				for(var i = 0; i < arr.length; i++){
				
					var edge = arr[i].split(";");
					if(parent == edge[0]){
						children = children + ", " + buildJSON(edge[1]);
					}
				}
				children = children.substr(2);
				return "{ id: \"" + parent + "\", name: \"" + parent + "\", data: {}, children: [" + children + "]}";
			} else return "";
			
		}
		
		function init(){
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
			st = new $jit.ST({  
			//id of viz container element  
			injectInto: 'infovis',  
			//set duration for the animation  
			duration: 500,  
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
				if(localStorage.getItem("as" + node.name)) label.innerHTML = node.name;
				else label.innerHTML = "<a href=tree.php?as=" + node.name + ">" + node.name + "</a>";  
				label.onclick = function(){  
					  st.onClick(node.id);
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
						//node.eachSubnode(function(n) { count++; });  
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
			
		
			var str = <?php echo "'".$s."'"; ?>;
			var asn = <?php echo '"'.$_GET['as'].'"'; ?>;
			
			
			localStorage.setItem( "as" + asn, str);
			
			
			//load data  
			json = eval('(' + localStorage.getItem('as17356') + ')');
			
			//load json data  
			st.loadJSON(json); 
			
			
			
			//compute node positions and layout  
			st.compute();  
			//optional: make a translation of the tree  
			st.geom.translate(new $jit.Complex(-200, 0), "current");  
			//emulate a click on the root node.  
			st.onClick(st.root);  
			
			
			for (var i = 0; i < localStorage.length; i++){
				var askey = localStorage.key(i)
				var subtree = eval('(' + localStorage.getItem(askey) + ')');
				subtree.id = askey.substr(2);
				st.addSubtree(eval(subtree),"replot",{onComplete:function(){}});
			}
			
			setTimeout("center()", 2000);
			
			
		}
		function center(){
			st.onClick(<?php echo '"'.$_GET['as'].'"'; ?>);
		}
		
		</script> 
		
	</head>
	<body onload="init();">
	<?php include("nav.php"); ?>
	
	<div id="content" style="margin-left:230px;" >

		<h1>BGP Route Tree</h1>
		<br />
		<br />
						 
		<div id="center-container"> 
			<div id="infovis"></div> 
			<div id="log" ></div> 			
		</div> 
		
		
	</div>
	
	</body>
</html>
