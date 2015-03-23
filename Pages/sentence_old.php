<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/misc.php');
	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');
	global $OP_COMMA;
?>
<header>
	<h1>Sentence viewer</h1>
</header>
<article>
<?php
	$dir = "/var/www/Data/";
	function get_id($value,$sentence) {
		foreach ($sentence as $_ => $__) {
			if (ISOP($__)) continue;
			if ($__["value"] === $value) return $_;
		}
	}
	$sentences_data = json_decode(file_get_contents("$dir/la/sentences.json"),true);
	$sentence_data = $sentences_data[array_keys($sentences_data)[0]];
	$sentence = $sentence_data["sentence"];
	$phrases = $sentence_data["phrases"];
	foreach ($sentence as &$word) {
		if (array_key_exists("space_before",$word)
		and array_key_exists("space_after",$word)
		and array_key_exists("text",$word)) {
			$word = OP($word["space_before"],$word["text"],$word["space_after"]);
		}
	}
	$_ = [];
	foreach ($phrases as $k => $v) $_[$k-1] = $v;
	$phrases_others = [];
	$last = [];
	foreach (array_reverse($phrases,true) as $_ => $phr) {
		foreach ($phr as $k) {
			$apply = &safe_getr_vector($k,$phrases_others);
			foreach ($phr as $o) {
				$level = $_;
				if (in_array($o,$last)) $level += 1; # promote
				if (!array_key_exists($o, $apply) or $apply[$o] < $level)
					$apply[$o] = $level;
			}
		}
		$last = $phr;
	}
	$width = 500;
?>
<p class="sentence">
<?php
	//echo htmlentities(json_encode($sentence[9]));
	$allow_space = FALSE;
	foreach ($sentence as $id => $v) {
		//echo gettype($sentence[9]);
		//if (is_string($v)) echo $id;
		if (ISOP($v)) {
			if ($v->space_before and $allow_space) echo " ";
			echo $v->text;
			$allow_space = $v->space_after;
			continue;
		}
		if ($allow_space) echo " "; $allow_space = TRUE;
		if (array_key_exists("link",$v)) {
			$l1 = "<a href='$v[link]'>";
			$l2 = "</a>";
		} else $l1 = $l2 = "";
?><span class="word" id="sentence-<?= $id ?>"><?= $l1.$v["value"].$l2 ?></span><?php
	}
	//echo htmlentities(json_encode($sentence[9]));
?>.
</p>
<div class="description-parent">
<p class="description" id="desc-default">
<?php
	$_ = [];
	foreach ($sentence as $id => $v) {
		if (ISOP($v)) $_[]=$v;
		else $_[]=$v["value"];
	}
	echo serialize_sentence($_);
	//echo htmlentities(json_encode($sentence[9]));
?>
</p>
<?php
	foreach ($sentence as $id => $v) {
		if (ISOP($v)) continue;
?>
	<p class="description" id="desc-<?= $id ?>"><?= "<span class='word selected'>".$v["value"]."</span>" . $v["desc"] ?>.</p>
<?php
	}
?>
</div>
<br>




<!-- Loading script, enable hover events etc. -->
<script>
$(function(){
	var phrases_others = <?= json_encode($phrases_others); ?>;
	var hght = 0;
	$('.description').each(function() {
		var h = $(this).height();
		if (h > hght) hght = h;
	});
	$('.description-parent').height(hght);
	
	function clear() {
		timeout = undefined;
		$('.sentence > .word').removeClass('selected').removeClass('selected2');
		$('.tree').find('span').removeClass('selected').removeClass('selected2');
		select();
<?php
		foreach ($phrases as $id => $_) {
?>
			$('.sentence > .word').removeClass('phrase<?= $id ?>');
			$('.tree .word').removeClass('phrase<?= $id ?>');
<?php
		}
?>
	};
	function select(y) {
		if (y === undefined) y = "default";
		$('.description').hide();
		if (y !== undefined) {
			$('.description#desc-'+y).show();
			if (y in phrases_others) {
				$.each(phrases_others[y], function(k, phrases) {
					var phrase = phrases;
					//$.each(phrases, function(_,phrase) {
						$('#sentence-'+k).addClass('phrase'+phrase);
						$('#tree-'+k).addClass('phrase'+phrase);
					//});
				});
			}
			$('#tree-'+y).addClass('selected');
			$('#sentence-'+y).addClass('selected');
		}
	};
	function select2(y) {
		if (y !== undefined) {
			$('#tree-'+y).addClass('selected2');
			$('#sentence-'+y).addClass('selected2');
		}
	};
	function timed_cb(fn, wait) {
		return function() {
			setTimeout(fn, wait);
		}
	}
	clear();
	var timeout = undefined;
	var _out;
	$('.sentence').mouseleave((_out = function(){
		timeout = setTimeout(clear, 170);
	}));
<?php
	foreach ($sentence as $id => $v) {
		if (ISOP($v)) continue;
		$modifies = array_map(function($value) use ($sentence) {
			if (is_int($value)) return $value;
			return get_id($value,$sentence);
		}, array_key_exists("modifies",$v) ? $v["modifies"] : []);
?>
		$('#sentence-<?= $id ?>, #tree-<?= $id ?>').mouseover(function(){
			if (timeout !== undefined) clearTimeout(timeout);
			clear();
			select('<?= $id ?>');
			var modifies = <?= json_encode($modifies); ?>;
			modifies.forEach(select2);
		});
		$('#tree-<?= $id ?>').parent().mouseleave(_out);
<?php
	}
?>
});
</script>





<!--
We will create a family tree using just CSS(3)
The markup will be simple nested lists
-->
<?php
	$root = [
		9 => [
			0 => [
				1 => [
					4 => [
						2 => [3],
						100 => [101],
					]
				]
			],
			7 => [8],
			6 => [5],
		]
	];
	$recurse = function($key,$element) use($sentence,&$recurse,$phrases_others) {
		$name=NULL;$id=NULL;
		if (is_string($element)) {
			$name = $element;
			$id = get_id($name,$sentence);
		} elseif (is_int($element)) {
			$id = $element;
			$name = $sentence[$id]["value"];
		} elseif (is_string($key)) {
			$name = $key;
			$id = get_id($name,$sentence);
		} elseif ($key !== NULL) {
			$id = $key;
			$name = $sentence[$id]["value"];
		}
		if ($name !== NULL and $id !== NULL) {
			$phrase = safe_get($id, safe_get($id, $phrases_others));
			if ($phrase !== NULL) {
				$phrase = " phrase$phrase";
			}
			if (array_key_exists("link",$sentence[$id])) {
				$name = "<a href='{$sentence[$id]['link']}'>$name</a>";
			}
			if (array_key_exists("role", $sentence[$id]))
				$div = '<div class="role'.$phrase.'">('.$sentence[$id]["role"].')</div>';
			else $div = NULL;
			?><?= $div ?><span class="word" id="tree-<?= $id ?>"><?= $name ?></span><?php
		}
		if (is_array($element)) {
			?><ul><?php
			foreach ($element as $k => $v) {
				?><li><?php
				$recurse($k,$v);
				?></li><?php
			}
			?></ul><?php
		}
	};
	?><div class="scrollable"><div class="tree auto-width"><?php
	$recurse(NULL, $root);
	?></div></div><div class="scrollable" id="body"></div><?php
	print("<h1>JSON</h1><code>".htmlentities(json_encode(["sentence"=>$sentence,"phrases"=>$phrases,"tree"=>$root]))."</code>");
?>


<script>
var m = [10, 110, 10, 110],
	w = 1050 - m[1] - m[3],
	h = 450 - m[0] - m[2],
	i = 0,
	root,
	anim_duration = 200;
var depth_scale = 100;
var text_width = 80;

var tree = d3.layout.tree()
	.size([h, w]);

var diagonal = d3.svg.diagonal()
	.projection(function(d) {
		return [d.y, d.x];
	});

var vis = d3.select("#body").append("svg:svg")
	.attr("width", w + m[1] + m[3])
	.attr("height", h + m[0] + m[2])
	.append("svg:g")
	.attr("transform", "translate(" + m[3] + "," + m[0] + ")");


function update(source) {
	var duration = d3.event && d3.event.altKey ? anim_duration * 10 : anim_duration;

	// Compute the new tree layout.
	var nodes = tree.nodes(root).reverse();

	// Normalize for fixed-depth.
	nodes.forEach(function(d) {
		d.y = d.depth * depth_scale;
	});

	// Update the nodes…
	var node = vis.selectAll("g.node")
		.data(nodes, function(d) {
			return d.id || (d.id = ++i);
		});

	// Enter any new nodes at the parent's previous position.
	var nodeEnter = node.enter().append("svg:g")
		.attr("class", "node")
		.attr("transform", function(d) {
			return "translate(" + source.y0 + "," + source.x0 + ")";
		})
		.on("click", function(d) {
			toggle(d);
			update(d);
		});

	nodeEnter.append("svg:circle")
		.attr("r", 1e-6)
		.style("fill", function(d) {
			return d._children ? "lightsteelblue" : "#fff";
		});

	nodeEnter.append("svg:text")
		.attr("x", function(d) {
			return d.children || d._children ? -10 : 10;
		})
		.attr("dy", ".35em")
		.attr("text-anchor", function(d) {
			return d.children || d._children ? "end" : "start";
		})
		.style("fill-opacity", 1e-6);

	// Transition nodes to their new position.
	var nodeUpdate = node.transition()
		.duration(duration)
		.attr("transform", function(d) {
			return "translate(" + d.y + "," + d.x + ")";
		});

	nodeUpdate.select("circle")
		.attr("r", 4.5)
		.style("fill", function(d) {
			return d._children ? "lightsteelblue" : "#fff";
		});

	node.select("text")
		.style("fill-opacity", 1)
		.text(function(d){return d.name;})
		/*.each(function(d) {
			var text = d3.select(this),
				lineNumber = 0,
				lineHeight = 1, // ems
				dy = parseFloat(text.attr("dy")),
				_y = 0;
			var matches = [];
			//matches = d.name.split(/\s+/);
			var e, re = /[^\s]{1,5}\s+[^\s]{1,5}(?=\s|$)|[^\s]{1,3}\s+[^\s]{1,10}(?=\s|$)|[^\s]+/g,
				s = d.name;
			while (e = re.exec(s)) {
				matches.push(e[0]);
			}
			_y -= (matches.length > 1 ? matches.length : 0) / 2 * lineHeight;
			matches.forEach(function(e) {
				if (!lineNumber) var h = _y;
				else var h = lineHeight;
				lineNumber++;
				text.append("tspan").text(e)
					.attr("x", text.attr("x"))
					.attr("y", text.attr("y"))
					.attr("dy", h + dy + "em");
			});
		});*/
		.each(wrap);

	// Transition exiting nodes to the parent's new position.
	var nodeExit = node.exit().transition()
		.duration(duration)
		.attr("transform", function(d) {
			return "translate(" + source.y + "," + source.x + ")";
		})
		.remove();

	nodeExit.select("circle")
		.attr("r", 1e-6);

	nodeExit.select("text")
		.style("fill-opacity", 1e-6);

	// Update the links…
	var link = vis.selectAll("path.link")
		.data(tree.links(nodes), function(d) {
			return d.target.id;
		});

	// Enter any new links at the parent's previous position.
	link.enter().insert("svg:path", "g")
		.attr("class", "link")
		.attr("d", function(d) {
			var o = {
				x: source.x0,
				y: source.y0
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
				x: source.x,
				y: source.y
			};
			return diagonal({
				source: o,
				target: o
			});
		})
		.remove();

	// Stash the old positions for transition.
	nodes.forEach(function(d) {
		d.x0 = d.x;
		d.y0 = d.y;
	});
}


function wrap(text, i) {
	var width = text_width;
	//text.each(function() {
		var text = d3.select(this),
			words = text.text().split(/\s+/).reverse(),
			word,
			line = [],
			lineNumber = 0,
			lineHeight = 1.1, // ems
			x = text.attr("x"),
			y = text.attr("y"),
			dy = parseFloat(text.attr("dy")),
			tspan = text.text(null).append("tspan").attr("x", x).attr("y", y).attr("dy", dy + "em");
		if (words.length === 1 && words[0] == "") words = [];
		//if (words.length > 1) {console.log(words.slice());}
		while (word = words.pop()) {
			line.push(word);
			tspan.text(line.join(" "));
			if (line.length > 1 && tspan.node().getComputedTextLength() > width) {
				//console.log("overflow '" + word + "' " + tspan.node().getComputedTextLength());
				line.pop();
				tspan.text(line.join(" "));
				line = [word];
				tspan = text.append("tspan").attr("x", x).attr("y", y).attr("dy", (++lineNumber === 0 ? 0 : 1) * lineHeight + dy + "em").text(word);
			}
		}
		if (lineNumber) {
			var first = text.select("tspan");
			first.attr("dy", (parseFloat(first.attr("dy")) - lineHeight * lineNumber/2) + "em")
		}
	//});
}

var sentence = <?= json_encode(["sentence" => $sentence, "phrases" => $phrases, "tree" => $root]) ?> ;
var root = {};
var run = function(tree) {
	var ret = [];
	for (key in tree) {
		var v = tree[key];
		if (v instanceof Array) {
			var v2 = {};
			for (_ in v) {
				v2[v[_]] = {};
			}
			v = v2;
		}
		ret.push({
			"name": sentence.sentence[key].value,
			"children": run(v)
		});
	}
	return ret;
};
root = run(sentence.tree)[0];
console.log(root);
var recurse = function(e) {
	e._name = e.name;
	e._name2 = e.name;
	if (e.children) {
		e.children.forEach(recurse);
		if (e.children.length === 1) {
			e._name2 += " " + e.children[0]._name2;
		} else
			e.children.forEach(function(_) {
				e._name2 += " (" + _._name2 + ")";
				first = false;
			});
	}
};
recurse(root);
root.x0 = h / 2;
root.y0 = 0;

// Toggle children.
function toggle(d) {
	if (d.children) {
		d._children = d.children;
		d.children = null;
		d.name = d._name2;
	} else {
		d.children = d._children;
		d._children = null;
		d.name = d._name;
	}
}

function toggleAll(d) {
	if (d.children) {
		d.children.forEach(toggleAll);
		toggle(d);
	}
}

update(root);
</script>

</article>
