/*
 * Author: Nicolas Garcia Belmonte
 * Copyright: Copyright 2007 by Nicolas Garcia Belmonte. All rights
 * reserved.
 * License: MIT License
 * Homepage: http://hypertree.woot.com.ar
 * Source: http://hypertree.woot.com.ar/js/hypertree1.0.js
 * Version: 1.0
 */

               

/*
   File: Hyperbolic Tree

   Sets of classes and objects to plot and calculate a hyperbolic tree.
*/

/*
   Class: Canvas

   A multi-purpose Canvas object decorator.
*/

/*
   Constructor: Canvas

   Canvas initializer.

   Parameters:

      canvasId - The canvas tag id.
      fillStyle - (optional) fill color style. Default's to black
      strokeStyle - (optional) stroke color style. Default's to black

   Returns:

      A new Canvas instance.
*/
var Canvas= function (canvasId, fillStyle, strokeStyle) {
	//browser supports canvas element
	if ("function" == typeof(HTMLCanvasElement) || "object" == typeof(HTMLCanvasElement)) {
		this.canvasId= canvasId;
		//canvas element exists
		if((this.canvas= document.getElementById(this.canvasId)) 
			&& this.canvas.getContext) {
      this.ctx = this.canvas.getContext('2d');
      this.ctx.fillStyle = fillStyle || 'black';
      this.ctx.strokeStyle = strokeStyle || 'black';
		} else {
			return null;
		}
	} else {
		return null;
	}
};

//canvas instance methods
Canvas.prototype= {
	/*
	   Method: getContext

	   Returns:
	
	      Canvas context handler.
	*/
	getContext: function () {
		return this.ctx;
	},

	/*
	   Method: setPosition
	
	   Calculates canvas absolute position on HTML document.
	*/	
	setPosition: function() {
		var obj= this.canvas;
		var curleft = curtop = 0;
		if (obj.offsetParent) {
			curleft = obj.offsetLeft
			curtop = obj.offsetTop
			while (obj = obj.offsetParent) {
				curleft += obj.offsetLeft
				curtop += obj.offsetTop
			}
		}
		this.position= { x: curleft, y: curtop };
	},

	/*
	   Method: getPosition

	   Returns:
	
	      Canvas absolute position to the HTML document.
	*/
	getPosition: function() {
		return this.position;
	},

	/*
	   Method: clear
	
	   Clears the canvas object.
	*/		
	clear: function () {
		this.ctx.clearRect(-this.getSize().x / 2, -this.getSize().x / 2, this.getSize().x, this.getSize().x);
	},

	/*
	   Method: drawMainCircle
	
	   Draws the boundary circle for the Hyperbolic Tree.
	*/	
	drawMainCircle: function () {	
	  var ctx= this.ctx;
	  ctx.beginPath();
  	ctx.arc(0, 0, this.getSize().x / 2, 0, Math.PI*2, true);
  	ctx.stroke();
 		ctx.closePath();
	},
	
	/*
	   Method: translateToCenter
	
	   Translates canvas coordinates system to the center of the canvas object.
	*/
	translateToCenter: function() {
		this.ctx.translate(this.getSize().x / 2, this.getSize().y / 2);
	},
	

	/*
	   Method: getSize

	   Returns:
	
	      An object that contains the canvas width and height.
	      i.e. { x: canvasWidth, y: canvasHeight }
	*/
	getSize: function () {
		return { x: this.canvas.width, y: this.canvas.height };
	}
	
};


/*
   Class: Mouse

   A multi-purpose Mouse class.
*/
var Mouse = {
	  
	  position: null,

		/*
		   Method: getPosition
		
		   Returns mouse position relative to canvas.
		
		   Parameters:
		
		      canvas - A canvas object.
		
		   Returns:
		
		      A Complex instance representing the mouse position on the canvas.
		*/
		getPosition: function (canvas) {
			var posx = this.posx;
			var posy = this.posy;
				
			var coordinates= {
			  x: ((posx - canvas.getPosition().x) - canvas.getSize().x / 2) / (canvas.getSize().x / 2),
			  y: (((posy - canvas.getPosition().y) - canvas.getSize().y / 2) / (canvas.getSize().y / 2))
			};
			
			this.position= new Complex(coordinates.x, -coordinates.y);
			return this.position;
		},


		/*
		   Method: capturePosition
		
		   Captures mouse position.
		
		   Parameters:
		
		      e - Triggered event.
		*/
	  capturePosition: function(e) {
			var posx = 0;
			var posy = 0;
			if (!e) var e = window.event;
			if (e.pageX || e.pageY) 	{
				posx = e.pageX;
				posy = e.pageY;
			}
			else if (e.clientX || e.clientY) 	{
				posx = e.clientX + document.body.scrollLeft
					+ document.documentElement.scrollLeft;
				posy = e.clientY + document.body.scrollTop
					+ document.documentElement.scrollTop;
			}
			
			this.posx= posx;
			this.posy= posy;
	}
};


/*
   Class: HyperLine

 		The HyperLine class allows you to draw "lines" on the Poincare's disk model
 		for Hyperbolic Geometry. You just have to specify two Complex numbers that
 		will be used for drawing the line.
*/

/*
   Constructor: HyperLine

   HyperLine constructor.

   Parameters:

      p1 - A Complex representing one point on the Hyperbolic Plane.
      p2 - Another Complex representing one point on the Hyperbolic Plane.


   Returns:

      A new HyperLine instance.
*/
var HyperLine= function(p1, p2) {
	this.p1= p1;
	this.p2= p2;
};


HyperLine.prototype= {
	
	/*
	   Method: printOn

	   Draws the HyperLine on canvas.
	   
	   Parameters:
	
	      canvas - A Canvas object.
	*/
  printOn: function(canvas) {
  	var ctx= canvas.getContext();
  	var p1= this.p1;
  	var p2= this.p2;
  	var radius= canvas.getSize().x / 2;
  	var circleCenter= this.arcThroughTwoPoints();
  	var angleBegin= Math.atan(Math.abs((p2.y - circleCenter.y) / (p2.x - circleCenter.x)));
  	var angleEnd= Math.atan(Math.abs((p1.y - circleCenter.y) / (p1.x - circleCenter.x)));
  	angleBegin= this.correctAngle(circleCenter, p2, angleBegin);
  	angleEnd= this.correctAngle(circleCenter, p1, angleEnd);
  
  	var sense= this.sense(angleBegin, angleEnd, p1, p2);
  	ctx.beginPath();
  	if(circleCenter.x == 0 && circleCenter.y== 0) {
  		ctx.moveTo(p1.x*radius, p1.y*radius);
  		ctx.lineTo(p2.x*radius, p2.y*radius);
  		ctx.moveTo(-p1.x*radius, -p1.y*radius);
  	} else {
    		ctx.arc(circleCenter.x*radius, circleCenter.y*radius, circleCenter.ratio*radius, angleBegin, angleEnd, sense);
  	}
  	ctx.stroke();
  	ctx.closePath();
  
  }, 


	/*
	   Method: correctAngle

	   For private use only: corrects angle value based on quadrant.
	   
	   Parameters:
	
	      relTo - A Complex instance pointing to the origin of one circle.
	      dot - An arbitrary point on Canvas.
	      angle - The angle between relTo and dot.

	   Returns:
	
	      The corrected angle.
	*/
  correctAngle: function(relTo, dot, angle) {
  	if (dot.x >= relTo.x && dot.y >= relTo.y) return angle;
  	if (dot.x <= relTo.x && dot.y >= relTo.y) return (Math.PI - angle);
  	if (dot.x <= relTo.x && dot.y <= relTo.y) return (Math.PI + angle);
  	if (dot.x >= relTo.x && dot.y <= relTo.y) return (Math.PI*2 - angle);
  	
  },


	/*
	   Method: sense

	   For private use only: sets angle direction to clockwise (true) or counterclockwise (false).
	   
	   Parameters:
	
	      angleBegin - Starting angle for drawing the arc.
	      angleEnd - The HyperLine will be drawn from angleBegin to angleEnd.

	   Returns:
	
	      A Boolean instance describing the sense for drawing the HyperLine.
	*/
  sense: function(angleBegin, angleEnd) {
  	if(angleBegin < angleEnd) {
  		if (angleBegin + Math.PI > angleEnd) {
  			return false;
  		} else {
  			return true;
  		}
  	} else {
  		if (angleEnd + Math.PI > angleBegin) {
  			return true;
  		} else {
  			return false;
  		}
  		
  	}
  	
  },


	/*
	   Method: arcThroughTwoPoints

	   For private use only. 
	   Calculates the line equation that contains p1 and
	 	 p2 over the poincare disk model of hyperbolic geometry.
	 	 For more information go to:
	 	 http://en.wikipedia.org/wiki/Poincar%C3%A9_disc_model
	   

	   Returns:
	
	      A set of properties describing univoquely a Hyperbolic Line.
	      For more information on these properties please refer to the wikipedia
	      page:  http://en.wikipedia.org/wiki/Poincar%C3%A9_disc_model
	*/
  arcThroughTwoPoints: function() {
  	var p1= this.p1;
  	var p2= this.p2;
  	var delta= 0;
  	var aDen = (p1.x*p2.y - p1.y*p2.x);
  	var bDen = (p1.x*p2.y - p1.y*p2.x);
  	if (aDen == delta || bDen == delta ) {
  		return {
  			x:0,
  			y:0
  		};
  	}
  	var a = (p1.y*(p2.squaredNorm()) - p2.y * (p1.squaredNorm()) + p1.y - p2.y) / aDen;
  	var b = (p2.x*(p1.squaredNorm()) - p1.x * (p2.squaredNorm()) + p2.x - p1.x) / bDen;
  	var x = -a / 2;
  	var y = -b / 2;
  	var ratio = Math.sqrt((a*a + b*b) / 4 -1);
  	
  	var out= {
  		x: x,
  		y: y,
  		ratio: ratio,
  		a: a,
  		b: b
  	};

  	return out;
  }
	
};





/*
   Class: Complex
	
	 A multi-purpose Complex Class with common methods.

*/


/*
   Constructor: Complex

   Complex constructor.

   Parameters:

      re - A real number.
      im - An real number representing the imaginary part.


   Returns:

      A new Complex instance.
*/
var Complex= function() {
	if (arguments.length > 1) {
		this.x= arguments[0];
		this.y= arguments[1];
		
	} else {
		this.x= null;
		this.y= null;
	}
	
}

Complex.prototype= {
	/*
	   Method: norm
	
	   Calculates the complex norm.
	
	   Returns:
	
	      A real number representing the complex norm.
	*/
	norm: function () {
		return Math.sqrt(this.squaredNorm());
	},
	
	/*
	   Method: squaredNorm
	
	   Calculates the complex squared norm.
	
	   Returns:
	
	      A real number representing the complex squared norm.
	*/
	squaredNorm: function () {
		return this.x*this.x + this.y*this.y;
	},

	/*
	   Method: add
	
	   Returns the result of adding two complex numbers.
	   Does not alter the original object.

	   Parameters:
	
	      pos - A Complex initialized instance.
	
	   Returns:
	
	     The result of adding two complex numbers.
	*/
	add: function(pos) {
		return new Complex(this.x + pos.x, this.y + pos.y);
	},

	/*
	   Method: prod
	
	   Returns the result of multiplying two complex numbers.
	   Does not alter the original object.

	   Parameters:
	
	      pos - A Complex initialized instance.
	
	   Returns:
	
	     The result of multiplying two complex numbers.
	*/
	prod: function(pos) {
		return new Complex(this.x*pos.x - this.y*pos.y, this.y*pos.x + this.x*pos.y);
	},


	/*
	   Method: moebiusTransformation
	
	   Calculates a moebius transformation for this point / complex.
	   	For more information go to:
			http://en.wikipedia.org/wiki/Moebius_transformation.

	   Parameters:
	
	      theta - A real number representing a rotation angle.
	      c - An initialized Complex instance representing a translation Vector.
	*/
	moebiusTransformation: function(theta, c) {
		var num= this.add(c.scale(-1));
		var den= new Complex(1, 0).add(c.conjugate().prod(this).scale(-1));
		var numProd= den.conjugate();
		var denProd= den.prod(den.conjugate()).x;
		num= num.prod(numProd).scale(1 / denProd);
		return new Complex(num.x, num.y);
	},

	/*
	   Method: conjugate
	
	   Returns the conjugate por this complex.

	   Returns:
	
	     The conjugate por this complex.
	*/
	conjugate: function() {
		return new Complex(this.x, -this.y);
	},


	/*
	   Method: scale
	
	   Returns the result of scaling a Complex instance.
	   Does not alter the original object.

	   Parameters:
	
	      factor - A scale factor.
	
	   Returns:
	
	     The result of scaling this complex to a factor.
	*/
	scale: function(factor) {
		return new Complex(this.x * factor, this.y * factor);
	},


	/*
	   Method: toString
	
	   Returns a string that shows the Complex properties.

	   Returns:
	
	     A string that shows the Complex properties.
	*/
	toString: function () {
	  return "{x:"+this.x+" y:"+this.y+"}";
	}
};


/*
   Class: Node
	
	 Behaviour of the hyperbolic tree node.

*/

/*
   Constructor: Node

   Node constructor.

   Parameters:

      id - The node id.
      info - Place to store extra information (can be left to null).


   Returns:

      A new Node instance.
*/
function Node (id, info) {
	//Property: id
	this.id= id;
	
	//Property: drawn
	//Node flag
	this.drawn= false;

	//Property: initLength
	//node distance to center
	this.initLength= 0;

	//Property: angle span
	//allowed angle span for adjacencies placement
	this.angleSpan= {
		begin:0,
		end:0
	};

	//Property: pos
	//node position
	this.pos= new Complex(0, 0);

	//Property: info
	//additional node information
	this.info= info;

	//Property: adjacencies
	//node adjacencies
	this.adjacencies= new Array();
	
}

Node.prototype= {
	
		/*
	   Method: adjacentTo
	
	   Indicates if the node is adjacent to the node indicated by the specified id

	   Parameters:
	
	      id - A node id.
	
	   Returns:
	
	     A Boolean instance indicating whether this node is adjacent to the specified by id or not.
	*/
	adjacentTo: function(id) {
		for(var index=0; index<this.adjacencies.length; index++) {
			if(id== this.adjacencies[index]) {
				return true;
			}
		}
		return false;
	},

		/*
	   Method: addAdjacency
	
	   Connects the node to the specified by id.

	   Parameters:
	
	      id - A node id.
	*/	
	addAdjacency: function(id) {
		this.adjacencies.push(id);
	},
	
		/*
	   Method: printInfoAsRootOn
	
	   Creates a label containing the root node information. This works by creating a div containing
	   the node's *info* property. The div has *ht_rootLabel* as CSS className. Finally,
	   the node is placed near the canvas plotted node. 

	   Parameters:
	
	      id - The label container id.
	      canvas - A Canvas instance.
	*/	
	printInfoAsRootOn: function (id, canvas) {
		var obj= document.getElementById(id);
		var output=	'<div id="'+this.id+'">' + this.info + '</div>';
		obj.innerHTML+= output;
		this.setDivProperties('ht_rootLabel', canvas, this.id);
	},
	
		/*
	   Method: printInfoOn
	
	   Creates a label containing the node information. This works by creating a div containing
	   the node's *info* property. The div has *ht_label* as CSS className. Finally,
	   the node is placed near the canvas plotted node. 

	   Parameters:
	
	      id - The label container id.
	      canvas - A Canvas instance.
	*/	
	printInfoOn: function (id, canvas) {
		var ide= this.id + '_a';
		var obj= document.getElementById(id);
		var output=	'<div id="'+this.id+'_child">' 
			+ '<a id=\"'+this.id+'\" href=\'javascript:ht.labelOnEventHandler("'+this.id+'");\'>' 
			+ this.info + '</a></div>';
		obj.innerHTML+= output;
		this.setDivProperties('ht_label', canvas, this.id);
			//alert(obj.innerHTML);
	},
	
		/*
	   Method: printOn
	
		 Plots the node on canvas.

	   Parameters:
	
	      canvas - A Canvas instance.
	*/	
	printOn: function(canvas) {
		var ctx= canvas.getContext();
		if(ctx != null) {
			ctx.beginPath();
  		ctx.arc(this.pos.x*canvas.getSize().x / 2,
  						 this.pos.y*canvas.getSize().y / 2, 3, 0, Math.PI*2, true); 
  		ctx.fill();
  		ctx.closePath();
		}
	},

		/*
	   Method: setDivProperties
	
	   Intended for private use: sets some label properties, such as positioning and className.

	   Parameters:
	
	      cssClass - A class name.
	      canvas - A Canvas instance.
	      key - A label (or node) id.
	*/	
	setDivProperties: function(cssClass, canvas, key) {
		var radius= canvas.getSize().x / 2;
		var pos= {
				x: Math.round(this.pos.x*radius + canvas.getPosition().x + radius) - 20,
				y: Math.round(this.pos.y*radius + canvas.getPosition().y + radius)
			};

		var div= document.getElementById(key);
		div.className= cssClass;
	  div.style.position='absolute';
		div.style.top= pos.y+'px';
		div.style.left= pos.x+'px';
		div.style.display= '';
		
	},

		/*
	   Method: norm
	
	   Calculates node's distance to origin.

	   Returns:
		 A Number instance indicating the node's distance to origin.	
	*/		
	norm: function () {
		return this.pos.norm();
	}
	
};




/*
   Class: Config

	Hyperbolic Tree configuration object. This object must be
	passed when calling a new hyperbolic tree instance:
	>  var ht= new HT(Config, canvas);
	The top 9 parameters (animationTime included) are necessary to
	execute the HT algorithm properly.
*/

var Config= {
		//Property: labelContainer
		//id for label container
		labelContainer: 'index_container',
		
		//Property: drawMainCircle
		//show/hide main circle
		drawMainCircle: true,

		//Property: showNodes
		//show/hide nodes
		showNodes: true,

		//Property: showEdges
		//show/hide edges
		showEdges: true,

		//Property: showLabels
		//show/hide labels, must be set to false if the nodes have no additional *info*.
		showLabels: true,

		//Property: initLength
		//initial edge length
		initLength: 0.6,

		//Property: angleRate
		//angle span rate
		angleRate: 1.1,

		//Property: timeSlot
		//time slot for animation frame
		timeSlot: 30,

		//Property: animationTime
		animationTime: 1000,

		//Property: depth
		//for non-JSON generated trees: tree depth
		depth: 1,

		//Property: children
		//for non-JSON generated trees: number of children
		children: 5,

		//Property: prob
		//for non-JSON generated trees: probability of edge creation
		prob: 1,
		
		//Property: loadTreeArray
		//for non-JSON generated trees: customizeable array of nodes per depth level
		loadTreeArray: [{children:5, prob:1}, {children: 8, prob:0.9},
		 {children:3, prob:0.9}]
};

/*
   Class: HT

	Hyperbolic Tree (HT) class. There are three ways of generating a
	HT object. You can use *depth*, *children* and *prob*
	properties of the <Config> object to define a HT. You can also use
	the *loadTreeArray* property of the <Config> object to define each level
	of the tree. Finally, you can load a HT with a JSON array.
*/

/*
 Constructor: HT

 Creates a new HT instance.
 
 Parameters:

    config - The Configuration object <Config>.
    canvas - A <Canvas> instance.
*/	
var HT= function(config, canvas)  {
	this.config= config;
	this.canvas= canvas;
	
	//where to move the hypertree
	this.complexTo= null;
	
	//persistence of an interval object
	this.intervalObj= null;
	this.relativeScale= 1;
	this.timesToInterval= null;
	
  this.flag= true;

	//graph nodes
	this.nodes= new Array();
};
	
	
HT.prototype= {
	
	//Common Graph methods:

	/*
	 Method: addAdjacence
	
	 Connects nodes specified by *id1* and *id2*. If not found, nodes are created.
	 
	 Parameters:
	
	    id1 - Node's id.
	    id2 - Another Node's id.
	*/	
  addAdjacence: function (id1, id2) {
  	if(!this.hasNode(id1)) this.addNode(id1, null);
  	if(!this.hasNode(id2)) this.addNode(id2, null);
  
  	for(var i in this.nodes) {
  		
  		if(this.nodes[i].id == id1) {
  			if(!this.nodes[i].adjacentTo(id2)) {
  				this.nodes[i].addAdjacency(id2);
  			}
  			
  		}
  		
  		if(this.nodes[i].id == id2) {	
  			if(!this.nodes[i].adjacentTo(id1)) {
  				this.nodes[i].addAdjacency(id1);
  			}
  			
  		}
  	
  	}
 },

	/*
	 Method: addNode
	
	 Adds a node.
	 
	 Parameters:
	
	    id - Node's id.
	    info - Some extra information you might find useful.
	*/	
  addNode: function(id, info) {
  	if(!this.nodes[id]) {
	  	var node= new Node(id, info);
	  	this.nodes[id]= node;
  	}
  },


	/*
	 Method: hasNode
	
	 Returns a Boolean instance indicating if node belongs to graph or not.
	 
	 Parameters:
	
	    id - Node's id.

	 Returns:
	  
	 		A Boolean instance indicating if node belongs to graph or not.
	*/	
  hasNode: function(id) {
  	for(var index in this.nodes) {
  		if (index== id) {
  			return true;
  		}
  	}
  	return false;	
  },


	//Extended hyperbolic tree methods
  
	/*
	 Method: initialize
	
	 Loads parameters and places the HT.
	*/	
  initialize: function () {
  	this.calculatePositions('1_a');
  	this.translateTo(new Complex(-0.1, -0.05), 1000);
  	if (document.getElementById(this.config.labelContainer))
	  	document.getElementById(this.config.labelContainer).innerHTML= ''; 	
  },

	/*
	 Method: moebiusTransformation
	
	Calculates a moebius transformation for the hyperbolic tree.
	For more information go to:
	<http://en.wikipedia.org/wiki/Moebius_transformation>
	 
	 Parameters:
	
	    theta - Rotation angle.
	    c - Translation Complex.
	*/	
  moebiusTransformation: function(theta, c) {
  	for (var id in this.nodes) {
	  		this.nodes[id].pos= this.nodes[id].pos.moebiusTransformation(theta, new Complex(c.x, c.y));
  	}
  },


	/*
	 Method: calculatePositions
	
	 Intended for private use: calculates node positions on canvas by performing a BFS-like algorithm	 

	 Parameters:
	
	    id - Root node id.
	*/	
  calculatePositions: function(id) {
  	id= (new String(id)).replace(/\+/g, ' ');
  	var node= this.nodes[id];
  	var queue= new Array();
		var ctx= this.canvas.getContext();
  	ctx.moveTo(0, 0);
  	this.nodes[id].drawn= false;
  	this.nodes[id].angleSpan.begin= 0;
  	this.nodes[id].angleSpan.end= Math.PI*2;
  	this.nodes[id].initLength= this.config.initLength;
  	this.nodes[id].pos= new Complex(0, 0);
  	
  	queue.push(this.nodes[id]);
  	
  	this.calculateChildrenPositions(queue);
  },

	/*
	 Method: calculateChildrenPositions
	
	 Intended for private use: calculates positions for children and grandchildren of root node.

	 Parameters:
	
	    queue - A queue (or Array instance) containing the root node.
	*/	
  calculateChildrenPositions: function(queue) {
  	while ( queue.length > 0 ) {
  		if(queue[0].drawn== false) {
  			var node= queue[0];
  			var transformed= false;
  			var c;
  			if (node.pos.x != 0 || node.pos.y != 0) {
  				//tranformation parameters
  				var theta= new Complex(1, 0);
  				c= new Complex(this.nodes[node.id].pos.x, this.nodes[node.id].pos.y);
  				this.moebiusTransformation(theta, c.scale(1));
  				transformed= true;
  				
  			}
  
  				
  			this.nodes[node.id].drawn= true;
  			var len= node.adjacencies.length;
  			var angleStep;
  			var angleSpan= Math.abs(node.angleSpan.end - node.angleSpan.begin);
  			
 			if(len > 2 && angleSpan < Math.PI*2) {
  				angleStep= Math.abs(node.angleSpan.end - node.angleSpan.begin) / (len-2);
  			} else if (len == 2 && angleSpan < Math.PI*2) {
					angleStep= 0;
					node.angleSpan.begin= (node.angleSpan.begin + node.angleSpan.end) / 2;
					 
					/*alert(node.angleSpan.begin + " " + node.angleSpan.end);
					angleStep= 0;*/
  			} else {
  				angleStep= Math.abs(node.angleSpan.end - node.angleSpan.begin) / (len);
  			}

  			var angle= node.angleSpan.begin;
  			for(var i=0; i< len; i++) {
  				if (this.nodes[node.adjacencies[i]].drawn== false) {
  					
  					var posTo= 	{
  									x:  node.initLength*Math.cos(angle), 
  									y:  node.initLength*Math.sin(angle)
  								};
  					this.nodes[node.adjacencies[i]].pos= new Complex(posTo.x, posTo.y);
  					this.nodes[node.adjacencies[i]].angleSpan.begin= angle - angleStep * this.config.angleRate /*/ 2*/;
  					this.nodes[node.adjacencies[i]].angleSpan.end= angle + angleStep * this.config.angleRate /*/ 2*/;
  					this.nodes[node.adjacencies[i]].initLength= node.initLength;
  					angle+= angleStep;
  				}
  			}
  
  
  			//volver a poner al arbol en su respectivo origen
  			if (transformed== true) {
  				this.moebiusTransformation(theta, c.scale(-1));
  			}
  			
  			for(var i=0; i<node.adjacencies.length; i++) {
  				queue.push(this.nodes[node.adjacencies[i]]);
  			}
  
  		}
  
  		queue.shift();
  		
  	}	
  },

	/*
	 Method: printEdges
	
	 Prints graph / tree edges
	*/	
  printEdges: function() {
  	var ctx= this.canvas.getContext();
  	for(var i in this.nodes) {
  		//doesn't have drawn edges yet
  		this.nodes[i].drawn= true; 
  		var pos= {
  			x:this.nodes[i].pos.x,
  			y:this.nodes[i].pos.y
  		};
  		
  		for(var j=0; j<this.nodes[i].adjacencies.length; j++) {
  			if(this.nodes[this.nodes[i].adjacencies[j]].drawn== false) {
  				var pos_j= {
  					x: this.nodes[this.nodes[i].adjacencies[j]].pos.x,
  					y: this.nodes[this.nodes[i].adjacencies[j]].pos.y
  				};
  				ctx.moveTo(0, 0);
  				var hl= new HyperLine(new Complex(pos.x, pos.y), new Complex(pos_j.x, pos_j.y));
  				hl.printOn(canvas);
  			}
  		}
		}
  },
  
	/*
	 Method: translateTo
	
   Tranlates the HT to a given position

	 Parameters:
	
	    complexTo - A Complex instance indicating where to translate the HT.
	    millisec - Animation time.
	*/	
  translateTo: function(complexTo, millisec) {
    if (this.flag) {
    	this.flag= false;  
      var timeSlot= this.config.timeSlot;
      var directionVector= complexTo.scale( -timeSlot / millisec );
      var tempVector= directionVector;
      
      this.complexTo= new Complex(-complexTo.x, complexTo.y);
      this.printNodesAt({x: tempVector.x, y: tempVector.y});
      this.intervalObj= setInterval("ht.printNodesRelativeAt({x: "+tempVector.x+", y: "+tempVector.y+"})", timeSlot);
    }
  },

	/*
	 Method: setFlag
	
   For private use: sets a flag.

	 Parameters:
	
	    val - Value.
	*/	
  setFlag: function (val) {
    this.flag= val;
  },

	/*
	 Method: printNodesAt
	
   Used for drawing the HT on a frame

	 Parameters:
	
	    at - A Complex instance indicating where to move the HT before plotting its nodes.
	*/	
  printNodesAt: function(at) {
    var at= new Complex(-at.x, at.y);
  	this.moebiusTransformation(1, at);
		this.printOn(this.canvas);
  	this.moebiusTransformation(1, at.scale(-1));
  },

	/*
	 Method: printOn
	
	 Prints the HT on canvas

	 Parameters:
	
	    canvas - A Canvas instance.
	*/	
	printOn: function(canvas) {
		var ctx= this.canvas.getContext();
  	ctx.moveTo(0, 0);
  	this.canvas.clear();
		if (this.config.drawMainCircle) {
		  canvas.drawMainCircle();
		}  	
  	for(var i in this.nodes) {
  		this.nodes[i].drawn= false;
  		ctx.moveTo(0, 0);	
  		if(this.config.showNodes) {
	  		this.nodes[i].printOn(canvas);
  		}
  	}

  	if(this.config.showEdges) {
  		this.printEdges();
  	}
  	
		
	},

	/*
	 Method: printNodesRelativeAt
	
	 Prints the HT nodes after being translated to a relative position.

	 Parameters:
	
	    at - A Complex instance indicating where to relatively move the HT before plotting its nodes.
	*/	
  printNodesRelativeAt: function(at) {
    var at= new Complex(-at.x, at.y);
  	this.moebiusTransformation(1, at.scale(this.relativeScale));
		this.printOn(this.canvas);    
    if(this.complexTo.add(at.scale(this.relativeScale -1)).norm() < 0.05) {
    	clearInterval(this.intervalObj);
    	this.relativeScale= 1;
    	this.timesToInterval= 0;
    	this.setFlag(true);
    	if(this.config.showLabels)
    		setTimeout("ht.makeLabelsForCenteredNode();", 200);
    } else {
    	this.moebiusTransformation(1, at.scale(-this.relativeScale));
    }
	  this.relativeScale+=2;
  },

	/*
	 Method: makeLabelsForCenteredNode

			Creates and displays labels for the centered node and its adjacent nodes.	
	*/	
	makeLabelsForCenteredNode: function () {
		var c= new Complex(0, 0);
		var key= null;
		//get closest node to origin
		for(var keyA in this.nodes) {
			if(key == null || this.nodes[key].norm() > this.nodes[keyA].norm()) {
				key = keyA;
			}
		}
		//print root node label information
		this.nodes[key].printInfoAsRootOn(this.config.labelContainer, this.canvas);		
		
		//print childrens' labels
		for(var j=0; j<this.nodes[key].adjacencies.length; j++) {
			var id= this.nodes[key].adjacencies[j];
			this.nodes[id].printInfoOn(this.config.labelContainer, this.canvas);		
		}
	},
	
	/*
	 Method: translate

		Translates the HT to the mouse position. It can also be used with any other
		Object / Class implementing a <Mouse>-like interface.

	 Parameters:
	
	    Mouse - The Mouse object.

	*/	
	translate: function (Mouse) {
	  if(Mouse.getPosition(this.canvas).norm() < 1) {
		  this.translateTo(Mouse.getPosition(this.canvas), this.config.animationTime);
	  }
	  if(document.getElementById(this.config.labelContainer))
		  document.getElementById(this.config.labelContainer).innerHTML= '';
	},
  
  
	/*
	 Method: loadTree

		 Loads an HT with the *depth*, *children* and *prob* parameters stored in the
		 <Config> object.
	*/	
  loadTree: function () {
  	var depth= this.config.depth;
  	var branchRate= this.config.children;
  	var prob= this.config.prob;
  	
  	var counter= 2;
  	var queue= new Array();
  	queue.push([1]);
  	
		while( queue.length > 0 && depth-- >= 0 ) {
			var queueColumn= new Array();
			for(var j=0; j<queue[0].length; j++) {
				var rootElem= queue[0][j];
				for(var i=0; i<branchRate; i++) {
					if (Math.random() < prob) {
						var child= counter++;
						this.addAdjacence(rootElem + '_a', child + '_a');
						queueColumn.push(child);
					}
				}
			}
			queue.push(queueColumn);
			queue.shift();
		}
		this.initialize();
  },
  
	/*
	 Method: loadTreeByArray

	 		Loads an HT with the *loadTreeArray* parameter stored in the
	 		<Config> object.
	*/	
  loadTreeByArray: function () {
  	var configArray= this.config.loadTreeArray;
  	var depth= configArray.length--;
  	var counter= 2;
  	var index= 0;
  	var queue= new Array();
  	queue.push([1]);
  	
		while( queue.length > 0 && --depth > 0 ) {
			var config= configArray[index++];
			var queueColumn= new Array();
			for(var j=0; j<queue[0].length; j++) {
				var rootElem= queue[0][j];
				for(var i=0; i<config.children; i++) {
					if (Math.random() < config.prob) {
						var child= counter++;
						this.addAdjacence(rootElem + '_a', child + '_a');
						queueColumn.push(child);
					}
				}
			}
			queue.push(queueColumn);
			queue.shift();
		}
		this.initialize();
  },  
  
  /*
	 Method: loadTreeByJSON

		Loads the HT with a JSON array of the form:
		(start code)
		{"InfoRootNode": {"InfoFirstChild": ["InfoGrandChild1", ..., "InfoGrandChildN"], ..., "InfoLastChild": ["InfoGrandChild1", ..., "InfoGrandChildN"]}}
		(end code) 		

	 Parameters:
	
	    json_array - A JSON array.

	*/	
  loadTreeByJSON: function (json_array) {
  	this.nodes= {};
  	var counter= 1;
  	//take root name
  	for(var i in json_array) {
  		var id1= counter++ + '_a';
  		//take children
  		for(var j in json_array[i]) {
				var id2= counter++ + '_a';
				this.addNode(id1, i);
				this.addNode(id2, j);
				this.addAdjacence(id1, id2);
			
				//take grandchildren
				for(var n=0; n<json_array[i][j].length; n++) {
					var id3= counter++ + '_a';
					this.addNode(id3, json_array[i][j][n]);
					this.addAdjacence(id2, id3);
				}
			}
   	}
		this.initialize();
  },

  /*
	 Method: getNodeInfoById

			Returns a node *info* property.
	
	 Parameters:
	
	    ide - A node id.

	*/	  
  getNodeInfoById: function(ide) {
  	return this.nodes[ide].info;
  },
  
  /*
	 Method: getNodeInfoById

	  To be set by user: execute this function when performing an
	  event to the label. Receives the label id. This label id is
	  also the node id, so feel free to fetch the node information by
	  calling <HT.getNodeInfoById>
	
	 Parameters:
	
	    ide - The label id.

	*/	  
  labelOnEventHandler: function (ide) {

  }
};

/*
Section: Objects

In this section you'll find the objects that you *must* use in order to make a Canvas
or HyperbolicTree instance. There's three ways of doing this:

- Loading a HT with specified *depth* (<Config.depth>), *children* (<Config.children>)
  and probability of edge creation <Config.prob>. Each level will be completed with the
  same amount of nodes. Take for example:
 (start code)
			 canvas= new Canvas('hypertree', 'white', 'white');
			  canvas.setPosition();
			  canvas.translateToCenter();
			  //use a cross-browser event handler function
			  addEvent(canvas.canvas, 'click', function (e) { Mouse.capturePosition(e); ht.translate(Mouse); });
			  
			  ht= new HT(Config, canvas);
			  ht.loadTree();
	(end code)

- Loading a HT from a specified array (<Config.loadTreeArray>)
 (start code)
			  canvas= new Canvas('hypertree', 'white', 'white');
			  canvas.setPosition();
			  canvas.translateToCenter();
			  addEvent(canvas.canvas, 'click', function (e) { Mouse.capturePosition(e); ht.translate(Mouse); });
			  
			  ht= new HT(Config, canvas);
			  ht.loadTreeByArray();
(end code)

- Loading a HT from a JSON array that displays some extra-information.
In order to do this you must set a handler function(<HT.labelHandler>) that will be called onclick.
(start code)
			function	labelHandler(ide) {
					var content= ht.getNodeInfoById(ide).replace(/ /g, '+');
			  	//This function could make an AJAX call to
			  	//grab some JSON information somewhere
			  	loadFromJSON(content);
			} 
			 
			  
			//when document loads...
			function initOnLoad(content) {
			  canvas= new Canvas('hypertree', 'white', 'white');
			  canvas.setPosition();
			  canvas.translateToCenter();
			  addEvent(canvas.canvas, 'click', function (e) { Mouse.capturePosition(e); ht.translate(Mouse); });
			  
			  ht= new HT(Config, canvas);
			  ht.labelOnEventHandler= labelHandler;
			 
			 
			  loadFromJSON(content);
			
			}
(end code)
*/
 

//Object: canvas
//Use this object to store a new <Canvas> instance.
var canvas;

//Object: ht
//Use this object to store a new <HT> instance.
var ht;
