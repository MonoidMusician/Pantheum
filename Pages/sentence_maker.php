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
	<h1>Sentence maker</h1>
</header>
<input id="enter-sentence" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" type="text" value="Salvius servum torsit ut eum vērum patefacere cōgeret." placeholder="Enter the sentence"><br>
<input id="enter-translation" type="text" value="Salvius tortured the slave in order that he might force him to reveal the truth." placeholder="Enter the translation"><br>
<button onclick="$('#enter-sentence').val('').next().next().val('');prepare();">Clear</button>
<button onclick="prepare();">Prepare</button>
<button onclick="save();">Save</button>
<br>
<code></code>
<br><br><br>
<code></code>
<article>
</article>
<script>
function make_ordered_hash() {
    var keys = [];
    var vals = {};
    return {
        push: function(k,v) {
            if (!(k in vals)) keys.push(k);
            vals[k] = v;
        },
        insert: function(pos,k,v) {
            if (!vals[k]) {
                keys.splice(pos,0,k);
                vals[k] = v;
            }
        },
        val: function(k) {return vals[k]},
        length: function(){return keys.length},
        keys: function(){return keys},
        values: function(){return vals},
        jsonify: function(){
            var ret="{";
            $.each(keys, function(i,k) {
                if (i) ret += ",";
                ret += JSON.stringify(k)+":"+JSON.stringify(vals[k]);
            });
            return ret+"}";
        },
    };
};
	$(document).on('keyup', '#enter-sentence', function(event) {
		if (event.which == 13) {
			$(this).blur();
			prepare();
		}
	});

	var last_active_element;
	$(document).on('blur', 'input', function () {
		last_active_element = this;
	});

	function insertAtCaret(txtarea, text) {
		var scrollPos = txtarea.scrollTop;
		var caretPos = txtarea.selectionStart;

		var front = (txtarea.value).substring(0, caretPos);
		var back = (txtarea.value).substring(txtarea.selectionEnd, txtarea.value.length);
		txtarea.value = front + text + back;
		caretPos = caretPos + text.length;
		txtarea.selectionStart = caretPos;
		txtarea.selectionEnd = caretPos;
		txtarea.focus();
		txtarea.scrollTop = scrollPos;
		$(txtarea).trigger("keyup");
	}

	function isPunct(a) {
		return a.match(/^[\.,-\/#!$%\^&\*;:{}=\-_`~()]$/) != null;
	};
	function process(r) {
		if (r[r.length-1] == ".") return r.slice(0,-1);
		return r;
	}
	function punctsplit(a) {
		return process(a.split(/\s+|(?=[\.,-\/#!$%\^&\*;:{}=\-_`~()])/g));
	}
	function wrapli(a,id) {
		if (!a) return ''; 
		var html = '<li id="word-'+id+'">';
		html += '<span data-value="'+a+'" data-id="'+id+'" onclick="insertAtCaret(last_active_element,&quot;<i>'+a+'</i>&quot;);">'+a+'</span>';
		html += '<ul>';
			html += '<li>Part of speech: <input>';
			html += '<li>Role: <input>';
			html += '<li>Description: <input>';
			html += '<li>Path: <input>';
			html += '<li>Parent(s): <input type="text">';
			html += '<li>Translation <input>';
		html += '</ul>';
		return html;
	}
	function wrapol(words) {
		var index = null;
		return '<ol>' + $.map(words, function(a) {
			if (index===null) {
				index=a;
				return null;
			} else {
				var ret = wrapli(a,index);
				index=null;
				return ret;
			}
		}).join('') + '</ol>';
	};
	function swrapol(swords) {
		var index = null;
		return '<ol>' + $.map(swords, wrapli).join('') + '</ol>';
	};
	var words, nonpunct, swords, trans, strans;
	function prepare() {
		$('code').text('');
		words = punctsplit($('#enter-sentence').val());
		nonpunct = $.map(words, function(a,i) {
			if (!isPunct(a)) return [i,a];
		});
		swords = $.map(words, function(a,i) {
			if (!isPunct(a)) return a;
		});
		trans = punctsplit($('#enter-translation').val());
		strans = $.map(trans, function(a,i) {
			if (!isPunct(a)) return a;
		});
		if (!words) return $('article').html('');
		var html = swrapol(swords);
		$('article').html(html);
		$('article ul li:nth-child(1) input').each(function() {
			var $this = $(this);
			$this.autocomplete({
				lookup: ['pro','n','v','adj','adv','conj','prep','interj'],
				minChars: 0,
			});
		});
		$('article ul li:nth-child(2) input').each(function() {
			var $this = $(this);
			$this.autocomplete({
				lookup: ['verb/root','verb', 'subject', 'direct object', 'indirect object','purpose','location','conjunction','modifier'],
				minChars: 0,
				onSelect: function(suggestion) {
					var next = $this.parent().next().find('input');
					if (!next.val())
						if (suggestion.value == 'verb/root')
							next.val('main verb');
						else
							next.val(suggestion.value + ' of ');
				},
			});
		});
		$('article ul li:nth-child(3) input').each(function() {
			var $this = $(this);
			$this.autocomplete({
				lookup: ['main verb', 'purpose word', 'interrogative word', 'subject of ', 'object of ', 'direct object of ', 'indirect object of ','modifier of '],
				minChars: 0,
			});
		});
		$('article ul li:nth-last-child(2) input').each(function() {
			var $this = $(this);
			$this.autocomplete({
				lookup: swords,
				delimiter: /,\s*/g,
				minChars: 0,
			});
		});
	};
	var puncts = {
		',': {
			"space_after":1,
			"text":",",
			"space_before":0
		},
		':': {
			"space_after":1,
			"text":":",
			"space_before":0
		},
		'.': {
			"space_after":1,
			"text":".",
			"space_before":0
		},
		'!': {
			"space_after":1,
			"text":"!",
			"space_before":0
		},
		'?': {
			"space_after":1,
			"text":"?",
			"space_before":0
		},
		'(': {
			"space_after":0,
			"text":"(",
			"space_before":1
		},
		')': {
			"space_after":1,
			"text":")",
			"space_before":0
		},
	};
	function save() {
		var values = [];
		$('article input').each(function() {
			values.push($(this).val());
		});
		$('code:first').text(JSON.stringify(values));
		var negative = 0;
		var json = {};
		json.sentence = make_ordered_hash();
		json.translation = make_ordered_hash();
		//alert(json.translation.push("a","b"));
		json.phrases = [[]];
		json.tree = {};

		var translation = [];
		var children = {"top":[]};
		var parents = {};
		var findid = function(word) {
			return $('[data-value="'+word+'"]').attr('data-id');
		};
		var sentence = $.map(words, function(a) {
			var li = $('span[data-value="'+a+'"]').parent();
			if (!li.length) return [''+(negative-=1),puncts[a]];
			var id = li.find('span').attr('data-id');
			var input = li.find('input').map(function(){return $(this).val()});
			var word = {
				value: a,
				role: input[2],
				desc: ' ('+input[0]+'): '+input[2],
				modifies: input[4].split(/,\s*/g)
			};
			if (input[3]) word.desc += ', '+input[3];
			var top = true;
			$.each(word.modifies, function(i,p) {
				if (!p) return;
				var p0=p,p = findid(p); if (!p) {
					alert("could not find reference to "+p0);
					return;
				}
				top = false;
				if (!(p in children)) children[p] = [];
				children[p].push(id);
				if (!(id in parents)) parents[id] = [];
				parents[id].push(p);
			});
			if (top) {
				children["top"].push(id);
			}
			translation.push([punctsplit(input[5]),id,input[5]]);
			return [id,word];
		});
		var index = null;
		$.each(sentence, function(i,a) {
			if (index===null) {
				index=a;
			} else {
				json.sentence.push(index,a);
				index=null;
			}
		});

		$('code:last').text(JSON.stringify(json));

		var trans_cpy = trans.slice();
		negative = 0;
		var add = function(key,value) {json.translation.push(key,value);}
		while (trans_cpy.length) {
			var T = trans_cpy[0];
			var t = trans_cpy.shift().toLowerCase();
			var id = null, match, orig;
			$.each(translation, function(i,a) {
				if (id!==null || !a[0].length) return;
				if (t == a[0][0].toLowerCase())
				{match=a[0];id = a[1];orig=a[2];}
			});
			//alert(id);
			if (id === null) {
				if (T in puncts)
					add(''+(negative-=1), puncts[T]);
				else
					add(''+(negative-=1), {value:T});
			} else {
				var s = [T];
				match.shift();
				while (match.length && trans_cpy[0].toLowerCase() == match.shift().toLowerCase()) {
					s.push(trans_cpy.shift());
				}
				add(id, {value:orig});
				//alert(orig);
			}
		}
		alert(json);


		function getchildren(child,ret) {
			if (ret === undefined) {
				ret = [child];
				if (child in parents) {
					var early = true;
					$.each(parents[child], function(i,parens) {
						early = early && children[parens].length == 1;
					});
					//if (early) alert(parents[child]+"/"+child);
					if (early) return [];
				} else return [];
			} else ret.push(child);
			if (child in children)
				$.each(children[child], function(i,child) {
					if (ret.indexOf(child) != -1) return;
					getchildren(child,ret);
				});
			return ret;
		};
		$.each(json.sentence.keys(), function(i,k) {
			if (k < 0) return;
			json.phrases[0].push(k);
			var childs = getchildren(k);
			if (childs.length > 1)
				json.phrases.push(childs);
		});
		var treechildren = function(tree,parens) {
			$.each(children[parens], function(i,child) {
				tree[child] = {};
				if (child in children)
					treechildren(tree[child],child);
				if ($.isEmptyObject(tree[child])) tree[child]=null;
			});
		};
		alert("top: "+children["top"]);
		treechildren(json.tree,"top");


		console.log(json.translation.jsonify(),json.translation.keys(),json.translation.values());
		var translation = json.translation.jsonify();
		var sentence = json.sentence.jsonify();
		json = JSON.stringify(json);
		json = json.replace('"translation":{}','"translation":'+translation);
		json = json.replace('"sentence":{}','"sentence":'+sentence);
		$('code:last').text('"'+$('#enter-sentence').val()+'":'+json);
		return json;
	};
	$(prepare);
	$(function() {
		if ($('#enter-sentence').val() == "servī Barbillum, quī vulnus habēbat, portābant. l")
			var values = ["n","subject","subject of <i>portābant</i>","masculine nominative plural","portābant","The slaves","n","direct object","direct object of <i>portābant</i>","masculine accusative","portābant","Barbillus","pro","modifier","modifier of <i>Barbillum</i>","masculine nominative singular","Barbillum","who","n","direct object","direct object of <i>habēbat</i>","neuter accusative singular","habēbat","a wound","v","verb","verb of <i>quī</i>","3rd person singular imperfect active indicative","quī","had","v","verb/root","main verb","3rd person plural imperfect active indicative","","were carrying"];
		else if ($('#enter-sentence').val() == "servī Barbillum aegrum, quī vulnus habēbat, portābant.")
			var values = ["n","subject","subject of <i>portābant</i>","masculine nominative plural","portābant","The slaves","n","direct object","direct object of <i>portābant</i>","masculine accusative","portābant","Barbillus","adj","modifier","modifier of <i>Barbillum</i>","masuline accusative singular","Barbillum","a sick","pro","modifier","modifier of <i>Barbillum</i>","masculine nominative singular","Barbillum","who","n","direct object","direct object of <i>habēbat</i>","neuter accusative singular","habēbat","a wound","v","verb","verb of <i>quī</i>","3rd person singular imperfect active indicative","quī","had","v","verb/root","main verb","3rd person plural imperfect active indicative","","were carrying"];
		else if ($('#enter-sentence').val() == "Salvius servum torsit ut eum vērum patefacere cōgeret.")
			var values = ["n","subject","subject of <i>torsit</i> and <i>cōgeret</i>","masculine nominative","torsit, cōgeret","Salvius","n","direct object","direct object of <i>torsit</i>","masculine accusative singular","torsit","the slave","v","verb/root","main verb","3rd person singular perfect active indicative","","tortured","conj","purpose","purpose of <i>torsit</i>","","torsit","in order that","pro","direct object/subject","direct object of <i>cōgeret</i>, subject of <i>patefacere</i>","masculine accusative singular","cōgeret, patefacere","him","n","direct object","direct object of <i>patefacere</i>","neuter accusative singular","patefacere","the truth","v","complement","complement of <i>cōgeret</i>","present active infinitive","cōgeret","to reveal","v","verb","verb of <i>ut</i>","3rd person singular imperfect active subjunctive","ut","he might force"];
		else return;
		$('article input').each(function() {
			$(this).val(values.shift());
		});
	});
	$(save);
</script>
