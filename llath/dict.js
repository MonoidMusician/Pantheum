function loginSubmit(username, password, error) {
	console.log(error);
	if ((username != '') && (password != '')) {
		password = loginHash(username, password);
		$.post("/PHP5/login.php", { u: username, p: password }, function(data) {
			if (data == 'success') {
				$('#login').hide();
			} else {
				if (data == '1') {
					$(error).html('Already logged in.');
				} else if (data == '2') {
					$(error).html('Error logging in.');
				} else if (data == '3') {
					$(error).html('User is banned.');
				} else if (data == '4') {
					$(error).html('Missing values.');
				} else {
					$(error).html('Error logging in (' + data + ').');
				}
			}
		});
	} else {
		if (username == '') {
			$(error).html('Missing username.');
		} else {
			$(error).html('Missing password.');
		}
	}
}


$(function() {
	function processtext(fn) {
		return function() {
			var $this = $(this);
			if (fn == title && $this.is('tr.pos td')) {
				$this.text(title($this.text(), "\u2014"))
			}
			$this.text(fn($this.text()));
		};
	}
	function title(text, deco) {
		if (deco === null || deco === undefined) deco  = "";
		if (typeof deco === "string") deco = [deco, deco];
		text = titlecase(text.replace(deco[0],"").replace(deco[1],""));
		var first = text.codePointAt(0),
			last = text.codePointAt(text.length - 1);
		var adj = 57759; // distance from a to decorated a
		var range = ["a".codePointAt(), "z".codePointAt()];
		if (last >= range[0] && last <= range[1])
			text = text.substr(0, text.length - 1) + String.fromCodePoint(last + adj);
		return deco[0] + text + deco[1];
	}
	function titlecase(text) {
		var first = text.codePointAt(0);
		var adj = 57817; // distance from A to decorated A
		var range = ["A".codePointAt(), "Z".codePointAt()];
		if (first >= range[0] && first <= range[1])
			text = String.fromCodePoint(first + adj) + text.substr(1);
		return text;
	}

	$(document).on('copy',function() {
		var body_element = document.getElementsByTagName('body')[0];
		var selection;
		selection = window.getSelection();
		var prev = selection.getRangeAt(0).cloneRange();
		var copytext = selection.toString().replace(/[-]/g, function(c) {
			return String.fromCodePoint(c.codePointAt() - 57817);
		}).replace(/[-]/g, function(c) {
			return String.fromCodePoint(c.codePointAt() - 57759);
		});
		if (copytext === selection.toString()) return;
		var newdiv = document.createElement('div');
		newdiv.style.position='absolute';
		newdiv.style.left='-99999px';
		body_element.appendChild(newdiv);
		newdiv.innerHTML = copytext;
		selection.selectAllChildren(newdiv);
		window.setTimeout(function() {
			body_element.removeChild(newdiv);
			// thanks to http://stackoverflow.com/a/21302196
			selection.removeAllRanges();
			selection.addRange(prev);
		},0);
	});


	function update() {
		// Make fancy Promocyja titles
		$('.title').each(processtext(title));
		$('.titlecase').each(processtext(titlecase));

		//
		$('#dict tr.new td').off('keyup.addnew');
		$('#dict tr.new td').on('keyup.addnew', function(e) {
			var w = e.which;
			if (w === 27) $(this).blur();
			if (w === 13 && !e.shiftKey) {
				e.preventDefault();
				return addnew.apply(this);
			}
		});

		count();
	}
	function count() {
		$('h1:first').attr('title', $('#dict tr:not(.new) .word').length + ' words/phrases');
		$('tbody .pos').each(function() {
			var l =$(this).parent().find('tr:not(.new) .word').length;
			$(this).attr('title', l + (l!==1?' words/phrases':' word/phrase'));
		});
	}

	function addpos() {
		var pos = $('#dict tr#pos td').text().trim(),
			row;
		if (!pos) return;
		$('#dict tr#pos td').html('');
		pos = title(pos);
		$('#dict tr').each(function() {
			if (row) return;
			var $this = $(this);
			if ($this.find('td').text().trim() == pos)
				row = $this;
		});
		if (row) {
			row.addClass('error');
			setTimeout(function() {
				row.removeClass('error');
			}, 1000);
			return;
		}
		var new_tbody = '<tbody><tr class="pos"><td colspan="4" class="title">'+pos+'</td></tr>'+$('#dict tr.new:first')[0].outerHTML+'</tbody>';
		var $new_tbody = $(new_tbody).sortable(tbody_sort_opts);
		$(this).parents('tbody').before($new_tbody);
		update();
	}
	function addnew() {
		var tr = $(this).parents('tr'),
			word = tr.find('td:first'),
			def  = tr.find('td:last' ),
			row;
		word = word.is('.empty') ? '' : word.html().trim();
		def  = def .is('.empty') ? '' : def .html().trim();
		if (!word || !def) return;
		tr.parents('tbody').find('tr:not(.new)').each(function() {
			if (row) return;
			var $this = $(this);
			if ($this.find('td.word').text() == word)
				row = $this;
		});
		if (row) {
			$('html, body').animate({
				scrollTop: row.offset().top - /* ~center it on screen: */screen.height/3
			}, 200);
			row.addClass('error');
			setTimeout(function() {
				row.removeClass('error');
			}, 1000);
			return;
		}
		tr.find('td').text('');
		tr.find('td:first').focus();
		tr.before($(this).parents('table').find('tbody:not(:first) tr:not(.pos):not(.new):not(.formula):first').clone());
		tr.prev().find('.word').html(word).parent().find('.def').html(def);
		count();
	}
	$('#dict tr#pos td').on('keyup', function(e) {
		var w = e.which;
		if (w === 27) $(this).blur();
		if (w === 13 && !e.shiftKey) {
			e.preventDefault();
			return addpos.apply(this);
		}
	});

	update();

	// Helper function to keep table row from collapsing when being sorted
	var fixHelperModified = function(e, tr) {
		var $originals = tr.children();
		var $helper = tr.clone();
		var recurse = function($helper, $originals) {
			$helper.children('table, thead, tbody, tr, td').each(function(index) {
				var $o = $originals.eq(index);
				$(this).width($o.width());
				recurse($(this), $o.children());
			});
			return $helper;
		};
		return recurse($helper, $originals);
	};

	var sortables, table_sort_opts, tbody_sort_opts;
	(function() {
		var stop = false;
		var scroller = null;
		var $w = $(window);
		mouseheight = null;
		$(document).mousemove(function(event) {
			mouseheight = (event.pageY - $w.scrollTop());
		});

		function scrolling(){
			if (stop) { stop = false; return; }
			var speed = 24;
			var q = screen.height / 4;
			if (mouseheight < q || mouseheight > screen.height*3/4) {
				var norm = mouseheight < q ? 1-mouseheight/q : -1-(mouseheight - screen.height)/q;
				$w.scrollTop($w.scrollTop()-speed*norm);
				scroller.sortable('refreshPositions');
			}
			setTimeout(scrolling,10);
		}

		$("#dict").sortable(table_sort_opts = {
			helper: fixHelperModified,
			axis: "y",
			delay: 150,
			handle: "tr.pos",
			forcePlaceholderSize: true,
			scroll: false,
			start: function( event, ui ) {
				if (!ui.helper) return;
				scroller = $(this);
				scrolling();
			},
			deactivate: function(event, ui) {
				stop = true;
			}
		});

		sortables = $("#dict tbody:not(:first):not(:last)").sortable(tbody_sort_opts = {
			helper: fixHelperModified,
			axis: "y",
			delay: 150,
			handle: ".edit",
			connectWith: '#dict tbody',
			items: "tr:not(.new):not(.pos):not(.formula)",
			scroll: false,
			start: function( event, ui ) {
				if (!ui.helper) return;
				scroller = $(this);
				scrolling();
			},
			deactivate: function(event, ui) {
				stop = true;
			}
		})/*.find(':not(:input)').disableSelection();/**/
	})();

	//Delete button in table rows
	$('#dict').on('click','.del',function() {
		tableID = '#' + $(this).closest('table').attr('id');
		r = confirm('Delete this word?');
		if(r)
			$(this).closest('tr').remove();
	});

	function getSelectionRel(node, val) {
		var s = window.getSelection();
		if (!val) val = node.textContent;
		//console.log(s);
		if (s.anchorNode === s.focusNode &&
		    s.anchorNode.textContent === node.textContent)
			return [s.anchorOffset, s.focusOffset, true];
		return [val.length, val.length, false];
	}

	// Save
	$(document).on('click', '#save', function() {
		$('[data-placeholder]').html('&nbsp;<br>').trigger('blur');
		ins = ["",""];
		searching();
		var data = $('#dict').html();
		$.post(window.location.href, {"data":data,"readback":"false"})
		.done(function(d) {
			var $d = $(d);
			var status = $d.filter('#status');
			if (status.text() !== "success") {
				$('#status').show().text(status.text()).attr('class', status.attr('class'));
				if (status.text() === "not logged in")
					$('#login').show();
			} else {
				$('#status').hide().text('success').attr('class', 'success');
				$('#dict').addClass('success');
				setTimeout(function() {
					$('#dict').removeClass('success');
				}, 1000);
			}
		})
		.fail(function() {
			alert("Save failed!");
		});
	});
	$(document).on('click', '#showlogin', function() {
		$('#login').show();
		$('#showlogin').attr('id', 'save').text('Save');
	});
	// from http://stackoverflow.com/a/10273585
	$(document).keyup(function(event) {
		//19 for Mac Command+S
		if (!( String.fromCharCode(event.which).toLowerCase() == 's' && event.ctrlKey) && !(event.which == 19)) return true;

		$('#save').click();

		event.preventDefault();
		return false;
	});

	// Search
	var ins = ["",""];
	$('.search td').on('keyup', function() {
		var $this = $(this),
		    val = $this.is('.empty') ? "" : $this.text();
		if ($this.is(':first-child'))
			ins[0] = val.trim();
		else ins[1] = val.trim();
		searching();
	});
	function searching() {
		var region = $('#dict tbody.ui-sortable').show();
		if (!ins[0] && !ins[1]) {
			region.find('tr').show();
			return;
		}
		region.find('.new, #pos').hide();
		region.find('tr:not(.pos)').hide();
		var l = region.find('tr:not(.pos):not(.new)');
		function trim(v) {
			var ret = [];
			$.each(v, function(_,e) {
				if (e.trim()) ret.push(e.trim());
			});
			return ret;
		}
		var srch = [
			trim(ins[0].split('|')).join('"), :contains("'),
			trim(ins[1].split('|')).join('"), :contains("')
		];
		if (ins[0])
			l = l.find('td.word').filter(':contains("'+srch[0]+'")').parent();
		if (ins[1])
			l = l.find('td.def' ).filter(':contains("'+srch[1]+'")').parent();
		l.show();
		region.each(function() {
			var $tbody = $(this);
			if (!$tbody.find('tr:not(.pos):visible').length)
				$tbody.hide();
		});
	}

	// from http://stackoverflow.com/a/2587356
	jQuery.fn.cleanWhitespace = function() {
		this.contents().filter(function() { return (this.nodeType == 3 && !/\S/.test(this.nodeValue)); })
			.remove();
		return this;
	}
	jQuery.fn.trimWhitespace = function() {
		var prev = null;
		this.contents().each(function() {
			if (this.nodeType === 3) {
				this.textContent = this.textContent.replace(/\s+/,' ');
				if (prev && prev.nodeName === "BR")
					this.textContent = this.textContent.trimLeft();
			} else if (prev && this.nodeName === "BR" && prev.nodeType === 3) {
				prev.textContent = prev.textContent.trimRight();
			}
			prev = this;
		});
		return this;
	}

	// contenteditable input boxes (table cells)
	var plch = 'data-placeholder',
	    prev = 'data-prev-value';
	var isFirefox = typeof InstallTrigger !== 'undefined'; // from http://stackoverflow.com/a/9851769
	var empty_input = isFirefox ? '&#xFEFF;<br>' : '';
	var onfucs, onblur;
	$('table').on('focus','td[contenteditable=true]',onfocus=function() {
		var $this = $(this);
		//console.log(this,$this);
		if ($this.is('.empty')) {
			$this.attr(plch, $this.html());
			$this.html(empty_input);
			$this.removeClass('empty');
		} else $this.attr(prev, $this.html());
	}).on('blur','td[contenteditable=true]',onblur=function() {
		var $this = $(this).cleanWhitespace().trimWhitespace();
		//console.log($this.html(),$this.text());
		if (!$this.text().trim() && $this.attr(plch)) {
			$this.html($this.attr(plch));
			$this.removeAttr(plch);
			$this.addClass('empty');
		} else if (!$this.text().trim()) $this.html($this.attr(prev));
		$this.removeAttr(prev);
	}).on('keyup','td[contenteditable=true]',function(e) {
		var $this = $(this), w = e.which;
		if ((String.fromCharCode(w).toLowerCase() == 's' && event.ctrlKey) || (w == 19)) {
			$this.blur();
			return true;
		}
		if (w === 27) {
			if ($this.attr(prev))
				$this.html($this.attr(prev))
			w = 13; // fall through
		}
		if (w === 13 && !e.shiftKey) {
			e.preventDefault();
			$this.blur();
		}
		if (!$this.text().trim()) {
			$this.html(empty_input);
		}
	}).on('keydown','td[contenteditable=true]',function(e) {
		var $this = $(this);
		if (e.which === 13 && !e.shiftKey) e.preventDefault();
		if (!$this.text().trim()) {
			$this.html(empty_input);
		}
	});

	$('#conv').on('keyup','td',function() {
		var table = [
			["ẇ","ʍ","wh"],
			["ȑ","ʀ","rch"],
			["ř","r̝","rzh"],
			["č","ʧ","tsh"],
			["ǰ","ʤ","dzh"],
			["ŝ","ʦ","ts"],
			["ẑ","ʣ","dz"],
			["ś","ʃ","sh"],
			["ź","ʒ","zh"],
			["ṗ","φ","ph"],
			["ḃ","β","bh"],
			["ð","ð","dh"],
			["þ","θ","th"],
			["ļ","ɬ","ll"],
			["ñ","ɲ","gn"],
			["y","y","ü"],
			["ø","ø","ö"],
			["a","a","a"],
			["ă","ə","uh"],
			["j","i","y"],
			["ē","e","ē"],
			["ō","o","ō"],
			["w","u","w"],
			["ı","ɪ","i"],
			["e","ɛ","e"],
			["o","ɔ","o"],
			["u","ʊ","u"],
			["rr","r","rr"],
			["ṟ","ɹ","rh"],
			["r","ɾ","r"],
			["ḩ","χ","xh"],
			["ç","ç","ch"],
			["ǥ","ʝ","gh"],
		], $this = $(this), i = $this.index(),
		j = i === 0 ? 1 : 0,
		k = i === 2 ? 1 : 2,
		v = $this.html(),
		ch = $this.parent().children();
		function map(v,f) {
			var r = [];
			$.each(v, function(_,vv) {
				r.push(f(vv));
			}); return r;
		}
		function splitter(b,z) { var split = function(a) {
			if (a === undefined) a = _;
			if (typeof a === "string") return a.split(b);
			return map(a,split);
		}; return z === undefined ? split : split(z) }
		function joiner(b,z) { var join = function(a) {
			if (a === undefined) a = _;
			if (typeof a[0] === "string") return a.join(b);
			return map(a,join);
		}; return z === undefined ? join : join(z) }
		if (!$this.text()) {
			ch.filter('['+plch+']:not(:nth-child('+(i+1)+'))').text('').each(onblur);
			return;
		} else ch.filter('.empty').each(onfocus);
		//console.log(i,j,k);
		var rev = [];
		$.each(table, function(_,r) {
			v = splitter(r[i],v);
			rev.unshift(r);
		});
		v = [v,v];
		$.each(rev, function(_,r) {
			v[0] = joiner(r[j],v[0]);
			v[1] = joiner(r[k],v[1]);
		});
		ch[j].innerHTML = v[0];
		ch[k].innerHTML = v[1];
	});
});

