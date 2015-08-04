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
		text = titlecase(text).trimLeft(deco[0]).trimRight(deco[1]);
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
		$(this).parents('tbody').before('<tbody><tr class="pos"><td colspan="2" class="title">'+pos+'</td></tr>'+$('#dict tr.new:first')[0].outerHTML+'</tbody>');
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
			if ($this.find('td:first').text() == word)
				row = $this;
		});
		if (row) {
			row.addClass('error');
			setTimeout(function() {
				row.removeClass('error');
			}, 1000);
			return;
		}
		tr.before('<tr><td>'+word+'</td><td>'+def+'</td></tr>');
	}
	$('#dict tr#pos input').on('keyup', function(e) {
		if (e.which === 13) return addpos.apply(this);
	});

	update();

	$('#save').on('click', function() {
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
});
/*$/**/(function() {
	var drake = dragula({
		isContainer: function (el) {
			return el.classList.contains('container');
			return el.nodeName === "TBODY" || el.nodeName === "TABLE";
		},
/*
		moves: function (el, container, handle) {
			console.log(handle, el, container);
			return container.nodeName === "TBODY" ? handle.nodeName === "TD" : handle.className === 'title';
		},
*/
		//removeOnSpill: true,
		delay: 200,
		direction: 'horizontal',
	}).on('over', function (el, container) {
		container.className += ' ex-over';
	}).on('out', function (el, container) {
		container.className = container.className.replace('ex-over', '').trim();
	});
});
$/**/(function() {
	//Helper function to keep table row from collapsing when being sorted
	var fixHelperModified = function(e, tr) {
		var $originals = tr.children();
		var $helper = tr.clone();
		$helper.children().each(function(index) {
			$(this).width($originals.eq(index).width())
		});
		return $helper;
	};

	$("#dict tbody").sortable({
		helper: fixHelperModified,
		stop: function(event,ui) {
			
		},
		items: "tr:not(.new):not(.pos)",
	}).disableSelection();/**/
	$("#dict table").sortable({
		handle: "tr.pos",
		forcePlaceholderSize: true,
		placeholder: 'group_move_placeholder'
	}).disableSelection();

	//Delete button in table rows
	$('table').on('click','.btn-delete',function() {
		tableID = '#' + $(this).closest('table').attr('id');
		r = confirm('Delete this item?');
		if(r) {
			$(this).closest('tr').remove();
		}
	});
});
