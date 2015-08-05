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
		text = titlecase(text).replace(deco[0],"").replace(deco[1],"");
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
	function update() {
		// Make fancy Promocyja titles
		$('.title').each(processtext(title));
		$('.titlecase').each(processtext(titlecase));

		//
		$('#dict tr.new input').off('keyup.addnew');
		$('#dict tr.new input').on('keyup.addnew', function(e) {
			if (e.which === 13) return addnew.apply(this);
		});
		$('#dict tr.new td:first-child input').width(0);
		var width = $('#dict thead td:first-child').width();
		$('#dict tr.new td:first-child input').each(function() {
			var $this = $(this);
			$this.width(width-16);
		});
	}

	function addpos() {
		var pos = $('#dict tr#pos input').val().trim(),
			row;
		if (!pos) return;
		$('#dict tr#pos input').val('');
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
		$(this).parents('tbody').before('<tbody><tr class="pos"><td colspan="4" class="title">'+pos+'</td></tr>'+$('#dict tr.new:first')[0].outerHTML+'</tbody>');
		update();
	}
	function addnew() {
		var tr = $(this).parents('tr'),
			word = tr.find('input:first').val().trim(),
			def  = tr.find('input:last').val().trim(),
			row;
		if (!word || !def) return;
		tr.find('input').val('');
		tr.find('input:first').focus();
		$('#dict tr').each(function() {
			if (row) return;
			var $this = $(this);
			if ($this.find('td.word').text() == word)
				row = $this;
		});
		if (row) {
			row.addClass('error');
			setTimeout(function() {
				row.removeClass('error');
			}, 1000);
			return;
		}
		tr.before($(this).parents('table').find('tbody:not(:first) tr:not(.pos):not(.new):first').clone());
		tr.prev().find('.word').text(word).parent().find('.def').text(def);
	}
	$('#dict tr#pos input').on('keyup', function(e) {
		if (e.which === 13) return addpos.apply(this);
	});

	update();

	$('#save').on('click', function() {
		$('#dict .word input, #dict .def input').trigger('change');
		$('input').val('');
		var data = $('#dict').html();
		$.post(window.location.href, {"data":data})
		.done(function(d) {
			$('#dict').addClass('success');
			setTimeout(function() {
				$('#dict').removeClass('success');
			}, 1000);
		})
		.fail(function() {
			alert("Save failed!");
		});
	});

	//Helper function to keep table row from collapsing when being sorted
	var fixHelperModified = function(e, tr) {
		var $originals = tr.width(tr.width()).children();
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

	var sortables;
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

		$("#dict").sortable({
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

		sortables = $("#dict tbody:not(:first):not(:last)").sortable({
			helper: fixHelperModified,
			axis: "y",
			delay: 150,
			handle: ".edit",
			connectWith: '#dict tbody',
			items: "tr:not(.new):not(.pos)",
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

	$('#dict').on('click','.word, .def',function() {
		var $this = $(this), val = $this.text(), width = $this.width();
		if ($this.find('input').length) return;
		var s = getSelectionRel(this, val);
		//if (s[2]) return;
		$this.html('<input style="width: '+width+'px" value="'+val+'" placeholder="'+val+'">');
		$this.children('input').on('change', function() {
			$this.text($(this).val() || $(this).attr('placeholder'));
			sortables.sortable('enable');
		}).on('keyup', function(e) {
			if (e.which === 13) $(this).trigger('change');
		}).on('blur', function(e) {
			$(this).trigger('change');
		}).trigger('focus')[0].setSelectionRange(s[0], s[1]);
		sortables.sortable('disable');
	});

	// Search
	var ins = ["",""];
	$('.search input').on('keyup', function() {
		var $this = $(this), val = $this.val();
		if ($this.parent().is(':first-child'))
			ins[0] = val;
		else ins[1] = val;
		searching();
	});
	function searching() {
		var region = $('tbody:not(:first):not(:last)').show();
		if (!ins[0] && !ins[1]) {
			region.find('tr').show();
			return;
		}
		region.find('.new').hide();
		region.find('tr:not(.pos)').hide();
		var l = region.find('tr:not(.pos):not(.new)');
		console.log(ins);
		if (ins[0])
			l = l.find('td.word:contains("'+ins[0]+'")').parent();
		if (ins[1])
			l = l.find('td.def:contains("'+ins[1]+'")').parent();
		l.show();
		region.each(function() {
			var $tbody = $(this);
			console.log($tbody.find('tr:not(.pos):visible').length);
			if (!$tbody.find('tr:not(.pos):visible').length)
				$tbody.hide();
		});
	}

});





