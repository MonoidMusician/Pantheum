<!DOCTYPE html>
<meta charset="utf-8">
<style>
:not(input):not(button):not(select) {
	font-family: Linux Libertine Display O;
}

.node circle {
	fill: #fff;
	stroke: steelblue;
	stroke-width: 1.5px;
}

.node {
}

.link {
	fill: none;
	stroke: #EFDBEE;
	stroke-width: 1.2px;
}

.svg {
	position: absolute;
}
</style>
<body>
<div id="edit">
Language comparison tool. (Alt-)Click on each node to show its children. Click on each word to go to Wiktionary. Hit esc to show/hide this.
<br><br>
Left:
<select class="languages"></select>
<button id="swap"><></button>
Right:
<select class="languages"></select>
<br>
Open to:
<span id="levels">
</span>
<!--<select id="opento">
</select>-->
<br>
Add a node:
<span id="newnode">
</span>
under
<select id="parent"></select>
<button id="enter">Add!</button>
<a id="export" download="graph.json" target="_blank" href="">Export JSON tree</a>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script>
$(document).on('keyup', function(e) {
	if (e.keyCode === 27) $('#edit').toggle();
	$w.resize();
});
var selected = ["la","en"];

var root = <?php
	include('graph.json');
?>;

function wiktify(name, lang) {
	if ($.isArray(name)) name = name[0];
	if (name[0].indexOf(" ") >= 0) return;
	if (lang === 'la') name = name.replace(/\u0304/g, '');
	lang = root.plural[lang];
	return 'http://en.wiktionary.org/wiki/'+name+'#'+lang;
}

$.each(root.plural, function(code, name) {
	$('select.languages').append('<option value="'+code+'">'+name+'</option>');
	$('#newnode').append('<input id="'+code+'" placeholder="'+name+'">');
	$('#newnode').append('<input id="'+code+'-pl" placeholder="'+name+' (plural)">');
});
$('#newnode input').on('keyup', function(e) {
	if (e.keyCode === 13) $('#enter').click();
})

var anim_duration = 200,
    i = 0,
    last_timer = 0,
    text_padding = 110,
    text_padding_ratio = 0.7,
    x_pow = 1.5;
var graphs = {};
function updateAll(parent) {
	$.each(graphs, function(_,graph) {
		graph.update(parent);
	});
}

var cachename = function(index,node) {
	var doname = function(n) {
		var w;
		if (!n) return "";
		if ($.isArray(n)) return $.map(n, doname).join(", ");
		if (w = wiktify(n, index))
			return '<a xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'+w+'" target="_blank">'+n+'</a>';
		else return n;
	};
	var doname1 = function(n) {
		if ($.isArray(n)) return n[0];
		return n;
	};

	var namer = function(d,both,singular) {
		var ret;
		if (both) {
			var t = (singular === true ? d._children : (singular === false ? d.children : false));
			if (t)
				toggle(d);
			toggle(d);namer(d);toggle(d);
			ret = namer(d);
			if (t)
				toggle(d);
		} else {
			var names = d._children ? (d.plural || d.name) : d.name;
			var cache = d._children ? d._plural : d._name;
			if (!cache) {
				if (d._children) d._plural = cache = {};
				else d._name = cache = {};
			} else if (cache[index]) return cache[index];
			ret = doname(names[index]);
			if (!ret) {
				ret = "?";
				$.each(root.name, function(i) {
					if (names[i])
						if (ret === "?")
							ret = doname1(names[i]);
						else ret += "/" + doname1(names[i]);
				});
				ret = "[" + ret + "]";
			}
			cache[index] = ret;
		}
		return ret;
	};
	return node ? namer(node) : namer;
};

// Construct left or right half of the graph
function construct_half(root, index, second) {
	$('select.languages:'+(second?'last':'first')+' option[value='+index+']').attr('selected', true);
	$('select.languages:'+(second?'first':'last')+' option[value='+index+']').attr('disabled', true);

	var cluster = d3.layout.cluster();
	var x = function(x) {
		// Bias the x-value so more recent branches are more
		// important and have more space
		var w = svg_size[0] - text_padding * 2;
		x = Math.pow(x/w,x_pow)*w;
		return second ? svg_size[0] - x : x;
	};

	var diagonal = d3.svg.diagonal()
		.projection(function(d) {
			return [x(d.y), d.x];
		});

	var pos = text_padding*text_padding_ratio; if (second) pos *= -1;
	var svg = d3.select("body").append("svg")
		.attr("class", "diamond_tree")
	.append("g")
		.attr("transform", "translate("+pos+",0)");

	var first = true;
	var doname = cachename(index);
	var update = function(parent) {
		var duration = d3.event && d3.event.ctrlKey ? anim_duration * 10 : anim_duration;
		var nodes = cluster.nodes(root),
			links = cluster.links(nodes);

		// Update the nodes…
		var node = svg.selectAll("g.node")
			.data(nodes, function(d) {
				return d.id || (d.id = ++i);
			});

		// Enter any new nodes at the parent's previous position.
		var nodeEnter = node.enter().append("svg:g")
			.attr("class", "node")
			.attr("transform", function(d) {
				if (first)
					return "translate(" + x(d.y) + "," + d.x + ")";
				else
					return "translate(" + x(parent.y0[index]) + "," + parent.x0[index] + ")";
			})
			.on("click", function(d) {
				if ((!d.children && !d._children) || d3.event.shiftKey) {
					d = d.parent;
				}
				toggle(d);
				updateAll(d);
				d3.event.preventDefault();
				d3.event.stopPropagation();
			});

		nodeEnter.append("svg:circle")
			.attr("r", 1e-6)
			.style("fill", function(d) {
				return d._children ? "lightsteelblue" : "#fff";
			});

		nodeEnter.append("svg:text")
			.attr("x", function(d) {
				var sign = (d.children || d._children ? -1 : 1) * (second ? -1 : 1);
				return sign * 10;
			})
			.attr("dy", ".35em")
			.attr("text-anchor", function(d) {
				var sign = (d.children || d._children ? -1 : 1) * (second ? -1 : 1);
				return sign < 0 ? "end" : "start";
			})
			.style("fill-opacity", 1e-6);

		// Transition nodes to their new position.
		var nodeUpdate = node.transition()
			.duration(duration)
			.attr("transform", function(d) {
				return "translate(" + x(d.y) + "," + d.x + ")";
			});

		nodeUpdate.select("circle")
			.attr("r", 6)
			.style("fill", function(d) {
				return d._children ? "lightsteelblue" : "#fff";
			});

		node.select("text")
			.style("fill-opacity", 1)
			.html(doname)
			.selectAll('a')
				.on('click', function(d) {
					console.log(d);
					if (d3.event.altKey) d3.event.preventDefault();
					else d3.event.stopPropagation();
				});

		// Transition exiting nodes to the parent's new position.
		var nodeExit = node.exit().transition()
			.duration(duration)
		    .ease("linear")
			.attr("transform", function(d) {
				return "translate(" + x(parent.y) + "," + parent.x + ")";
			})
			.remove();

		nodeExit.select("circle")
			.attr("r", 1e-6);

		nodeExit.select("text")
			.style("fill-opacity", 1e-6);

		// Update the links…
		var link = svg.selectAll("path.link")
			.data(links, function(d) {
				return d.target.id;
			});

		// Enter any new links at the parent's previous position.
		if (first)
			link.enter().insert("svg:path", "g")
				.attr("class", "link")
				.attr("d", diagonal);
		else
			link.enter().insert("svg:path", "g")
				.attr("class", "link")
				.attr("d", function(d) {
					var o = {
						x: parent.x0[index],
						y: parent.y0[index]
					};
					return diagonal({
						source: o,
						target: o
					});
				})
				.transition()
				.duration(duration)
				.attr("d", diagonal);

		// Transition links to their new position.
		link.transition()
			.duration(duration)
			.attr("d", diagonal);

		// Transition exiting nodes to the parent's new position.
		link.exit().transition()
			.duration(duration)
			.attr("d", function(d) {
				var o = {
					x: parent.x,
					y: parent.y
				};
				return diagonal({
					source: o,
					target: o
				});
			})
			.remove();

		// Stash the old positions for transition.
		nodes.forEach(function(d) {
			if (!d.x0) d.x0 = {};
			if (!d.y0) d.y0 = {};
			d.x0[index] = d.x;
			d.y0[index] = d.y;
		});
		first = false;
	};

	graphs[index] = {
		update: update,
		cluster: cluster,
		root: root,
	};
}

function toggle(d) {
	if (d.children) {
		d._children = d.children;
		d.children = null;
	} else {
		d.children = d._children;
		d._children = null;
	}
}


// Preload _name field (compiled from name field)
(function() {
	var doname;
	var recurse = function(i,node) {
		doname(node,true,true);
		var children = node.children;
		if (!children) children = node._children;
		if (children) {
			$.each(children, recurse);
		}
	};
	$.each(selected, function(_,index) {
		doname = cachename(index);
		recurse(null,root);
	});
})();


function reconstruct() {
	d3.selectAll('svg.diamond_tree').remove();
	construct_half(root, selected[0], false);
	construct_half(root, selected[1], true);
};
reconstruct();

var $w = $(window);
var svg_size = [0,0];
function resize() {
	var width = svg_size[0];
	var height = svg_size[1];
	d3.selectAll("svg").attr("width", width).attr("height", height);
	$.each(graphs, function(_,graph) {
		if (graph === null) return;
		graph.cluster.size([height, width - text_padding * 2]);
		graph.update(root);
	});
}


// Allow the graph to expand with the window,
// but only after a 50ms delay, to ensure
// the resizing has truly stopped, thus
// avoiding excessive resizing and computation.
$w.resize(function() {
	var width = $w.width() / 2 - 30,
		height = $w.height() - 30;
	if ($('#edit').css('display') !== 'none')
		height -= $('#edit').height();
	if (width == svg_size[0] && height == svg_size[1]) return;
	svg_size = [width, height];
	last_timer += 1;
	var idx = last_timer;
	d3.timer(function() {
		if (idx !== last_timer) return;
		resize();
		return true; // no looping
	}, 50);
}).resize();





// Swap selected trees
$('#swap').on('click', function() {
	$('select.languages option').attr('disabled', false);
	var s = selected[0];
	selected[0] = selected[1];
	selected[1] = s;
	list_nodes();
	reconstruct();
	resize();
});


// Construct the lists of nodes
var node_map = {};
var list_nodes;
(list_nodes = function() {
	var last = "";
	$('select#parent, select#opento').html('');
	var recurse = function(i,node) {
	};
	var recurse = function(root) {
		return function(i,node) {
			// preload parents (cluster will do this
			// too, but only for visible nodes)
			node.parent = root;

			// add to lists
			var name = node._name[selected[0]].replace(/<(?:.|\n)*?>/gm, '') + " \u2014 " + node._name[selected[1]].replace(/<(?:.|\n)*?>/gm, '');
			if (i === null) name = "[root]";
			var opt = '<option value="'+node.name[selected[0]]+'">'+last+name+'</option>';
			$('select#parent').append(opt);
			$('select#opento').append(opt);

			// add to node_map
			if (node.name) {
				node_map[node.name[selected[0]]] = node;
				node_map[node.name[selected[1]]] = node;
			}

			var children = node.children;
			if (!children) children = node._children;
			if (children) {
				var l = last;
				last += "\u00B7 "; // middle dot
				$.each(children, recurse(node));
				last = l;
			}
		};
	}
	recurse(null)(null,root);
})();


// Implement "open to level X" buttons
(function() {
	var level = 0; var max_level = -1;
	var recurse = function(i,node) {
		if (level > max_level) {
			$('#levels').append('<button data-level="'+level+'">Level '+level+'</button>');
			max_level = level;
		}
		var children = node.children;
		if (!children) children = node._children;
		if (children) {
			level += 1;
			$.each(children, recurse);
			level -= 1;
		}
	};
	recurse(null,root);
	$('#levels button:first').attr('id', 'closeall').text('Close all nodes');
	$('#levels button:last').attr('id', 'openall').text('Open all');
	var recurse = function(i,node) {
		var change = true;
		if (level < 0) {
			if (node._children) node.children = node._children;
			else change = false;
			node._children = null;
		} else {
			if (node.children) node._children = node.children;
			else change = false;
			node.children = null;
		}
		if (change) updateAll(node);
		updateAll(node);
		var children = node.children;
		if (!children) children = node._children;
		if (children) {
			level += 1;
			$.each(children, recurse);
			level -= 1;
		}
	};
	$('#levels button:not([id])').on('click', function() {
		level = -$(this).data('level');
		recurse(null,root);
	});
})();
(function() {
	var open;
	var recurse = function(i,node) {
		var change = true;
		if (open) {
			if (node._children) node.children = node._children;
			else change = false;
			node._children = null;
		} else {
			if (node.children) node._children = node.children;
			else change = false;
			node.children = null;
		}
		if (change) updateAll(node);
		var children = node.children;
		if (!children) children = node._children;
		if (children) {
			$.each(children, recurse);
		}
	};
	$('#openall').on('click', function() {
		open=true;
		recurse(null,root);
	});
	$('#closeall').on('click', function() {
		open=false;
		recurse(null,root);
	});
})();

// Implement "open to node X" selector
$('#opento, #parent').first().on('change', function() {
	var $this = $(this), val = $this.val();
	if (val === "[root]") return;
	var node = node_map[val];
	if ($(this).is('#parent')) {
		if (node.children) node = node.children[0];
		else if (node._children) node = node._children[0];
	}
	while (node.parent) {
		node = node.parent;
		if (node._children) node.children = node._children;
		node._children = null;
		updateAll(node);
	}
	if ($(this).is('#opento'))
		$(this).val("[root]");
});

// Implement add a node support
$('#enter').on('click', function() {
	var trim = function(x) {
		return $.map(x, function(xx) {
			return xx.trim();
		});
	}
	var names = {}, plurals = {};
	$.each(root.name, function(code, name) {
		var val = $('#'+code).val().trim();
		if (val)
			if (val.indexOf(",") >= 0)
				names[code] = trim(val.split(","));
			else names[code] = val;
		val = $('#'+code+'-pl').val().trim();
		if (val)
			if (val.indexOf(",") >= 0)
				plurals[code] = trim(val.split(","));
			else plurals[code] = val;
	});

	var parent = node_map[$('#parent').val()];
	if (!parent) alert('Error! Could not insert node into tree');
	if (parent._children) {
		parent.children = parent._children;
		parent._children = null;
	}
	if (!parent.children) parent.children = [];
	parent.children.push({name:names,plural:plurals});
	updateAll(parent);
	while (parent.parent) {
		if (parent._children) {
			parent.children = parent._children;
			parent._children = null;
			updateAll(parent);
		}
		parent = parent.parent;
	}
	list_nodes();
});


// Exporting
$('#export').on('click', function() {
	var last = [];
	var recurse = function(i,node) {
		var novus = {};
		$.each(["name","plural"], function(_,attr) {
			if (attr in node) novus[attr] = node[attr];
		});
		var children = node.children;
		if (!children) children = node._children;
		last.push(novus);
		if (children) {
			var l = last;
			last = [];
			$.each(children, recurse);
			if (node._children) novus._children = last;
			else novus.children = last;
			last = l;
		}
	};
	recurse(null,root);
	var json = last.pop();
	console.log(json);
	json = JSON.stringify(json, null, "\t");
	$(this).attr('href', 'data:application/json;charset=utf-8,' + encodeURIComponent(json));
})

</script>









