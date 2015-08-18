<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/quiz/common.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');

	global $sql_stmts;
	//requireRank(1);

	$langs = select2_getlangs();
?>
<style>
code {
	white-space: pre;
}
</style>
<header>
	<h1>Quiz Creator</h1>
</header>
<article id="quiz">
	<form action="#">
	<select id="lang" style="width: 150px"></select>
	<input class="autosizeable" id="id" required placeholder="Unique ID">
	<input class="autosizeable" id="name" required placeholder="Name">
	<input class="autosizeable" id="category" required placeholder="Category">
	<br>
	<input id="n_questions" placeholder="# of questions (or blank or 'auto')">
	<label><input type="checkbox" id="no_shuffle">Fixed order?</label>
	<br>
	<hr>
	
	<button type="button" id="add">Add question/option</button>
	<br>
	<button type="submit" id="save">Save quiz!</button>
	</form>
	<code id="output"></code>
</article>
<script>
$('#lang').select2({
	minimumResultsForSearch: Infinity,
	placeholder: "Type",
	data: <?= json_encode($langs) ?>
}).val('la').change();

// Types of elements (parts of a quiz page)
var types = {
	// Simple text
	"Text":{
		html:'<input class="autosizeable" required placeholder="Text">',
		word: function(text) JSON.stringify(text),
	},
	// Latin text
	"Latin":{
		html:'<input class="autosizeable" required placeholder="Latin">',
		word: function(text) {
			return "format_word("+JSON.stringify(text)+",'la')";
		}
	},
	// Raw HTML
	"HTML":{
		html:'<input class="autosizeable" required placeholder="HTML">',
		word: function(html) {
			return "HTML("+JSON.stringify(html)+")";
		}
	},
	// Image
	"Image":{
		html:'<input class="autosizeable" required placeholder="URL"><input class="autosizeable" placeholder="Alternate text"><input class="autosizeable" placeholder="Style">',
		word: function(url, alt, style) {
			return "HTML('<img"+attr("src", url)+attr("alt",alt)+attr("style",style)+">')";
		}
	},
	// Free-response answer, with fixed correct answers
	"Simple free-response":{
		html:'<select class="lang"></select><input class="autosizeable" required placeholder="Placeholder text"><input class="autosizeable" required placeholder="The correct answer"><button data-placeholder="Another answer" class="addanswer" type="button">+Answer</button>',
		word: function() "$OP_USER_INPUT",
		extra: function() {
			var args = Array.slice(arguments);
			var extras = args.shift();
			var lang = args.shift();
			var placeholder = args.shift();

			var n = extras.answerN;

			var answer = '["correct"=>[\n\t\t';
			answer += "\t"+$.map(args, JSON.stringify).join(",\n\t\t\t")+"\n\t\t";
			answer += ']]';

			extras["answer"+n] = answer;
			extras["answer"+n+"-tooltip"] = JSON.stringify(placeholder);
			extras["answer"+n+"-language"] = JSON.stringify(lang);
		},
		answer: true
	},
	// Free-response answer, with an expression matching and 1 correct answer
	"Expressive free-response":{
		html:'<select class="lang"></select><input class="autosizeable" required placeholder="Placeholder text"><input class="autosizeable" required placeholder="The correct answer"><input class="autosizeable" required placeholder="Correct answer expression">',
		word: function() "$OP_USER_INPUT",
		extra: function(extras, lang, placeholder, correct, expr) {
			var n = extras.answerN;
			var answer = '[\n\t\t\t"correct"=>['+JSON.stringify(correct)+'],\n\t\t\t"expr"=>'+JSON.stringify(expr)+'\n\t\t]';
			extras["answer"+n] = answer;
			extras["answer"+n+"-tooltip"] = JSON.stringify(placeholder);
			extras["answer"+n+"-language"] = JSON.stringify(lang);
		},
		answer: true
	},
	// Free-response paragraph, with an expression matching and 1 correct answer
	"Expressive free-paragraph":{
		html:'<select class="lang"></select><input class="autosizeable" required placeholder="Placeholder text"><input class="autosizeable" required placeholder="The correct answer"><input class="autosizeable" required placeholder="Correct answer expression">',
		word: function() "$OP_USER_PARAGRAPH",
		extra: function(extras, placeholder, correct, expr) {
			var n = extras.answerN;
			var answer = '[\n\t\t\t"correct"=>['+JSON.stringify(correct)+'],\n\t\t\t"expr"=>'+JSON.stringify(expr)+'\n\t\t]';
			extras["answer"+n] = answer;
			extras["answer"+n+"-tooltip"] = JSON.stringify(placeholder);
			extras["answer"+n+"-language"] = JSON.stringify(lang);
		},
		answer: true
	},
	"Multiple-choice":{
		html:'<input class="autosizeable" required placeholder="Answer 1"><button data-placeholder="Answer N" class="addanswer" type="button">+Answer</button><label><input class="hideshow" data-selector=".correct + *" type="checkbox">Fixed order</label> <select class="correct"><option>Answer 1</select>',
		js: function() {
			var $this = $(this);
			$this.find('.addanswer').on('click', function() {
				setTimeout(function() {
					$this.find('.correct').append('<option>'+$this.find('.addanswer').prev().attr('placeholder'));
				}, 1);
			});
			$this.find('select').next().hide();
		},
		word: function() "$OP_MULTIPLE_CHOICE",
		extra: function(extras) {
			var args = Array.slice(arguments);
			var extras = args.shift();
			var correct = args.pop();
			var fixed = args.pop();

			var n = extras.answerN;
			var $this = $(this);
			var $sel = $this.find('select');
			var no_shuffle = $sel.next().is(':visible');
			var i = no_shuffle ? correct.replace(/^\D+/g,'')-1 : 0;

			var answer = '[\n\t\t';
			$.each(args, function(j, val) {
				if (j == i)
					answer += '\t"correct"=>';
				else answer += "\t";
				answer += JSON.stringify(val) + ",\n\t\t";
			});
			answer += ']';

			extras["choices"+n] = answer;
			extras["choices"+n+"-no-shuffle"] = JSON.stringify(no_shuffle);
		},
		answer: true
	},
};

// Listeners
// Add option
$('#add').on('click', function() {
	$('hr:first, .option').last().after('<div class="option"><button type="button" class="del">&#x2212;</button><input class="autosizeable help" placeholder="Help text"><div><select class="type" style="width: 230px"></select><button type="button" class="add">Add element</button></div><hr></div>');
	$('.type:last').select2({
		minimumResultsForSearch: Infinity,
		placeholder: "Type",
		data: Object.keys(types)
	});
	$('input.autosizeable').autosizeInput();
}).trigger('click');
// Add element
$('#quiz').on('click', '.add', function() {
	var $this = $(this), type = $this.prev().prev().val(),
	    $insert = $(this).parent().parent().children('.help, div[data-type]').last();
	$insert.after('<div data-type="'+type+'"><button type="button" class="del">&#x2212;</button>'+types[type].html+'</div>');
	var $div = $('div[data-type]:last');
	$div.find('input.autosizeable').autosizeInput();
	$div.find('select.lang').html($('#lang').html());
	$div.find('select').select2({minimumResultsForSearch: Infinity});
	if (types[type]["js"]) types[type].js.apply($div[0]);
});
// Add answer
$('#quiz').on('click', 'div[data-type] button.addanswer', function() {
	var $this = $(this), $parent = $this.parent(), plc = $this.attr('data-placeholder');
	$input = $parent.find('input:not(:checkbox):not(:radio)');
	if (plc.charAt(plc.length-1) === "N")
		plc = plc.slice(0,plc.length-1) + -1*(-$input.length-1);
	$input.last().after('<input class="autosizeable" placeholder="'+plc+'">');
	$parent.find('input.autosizeable').autosizeInput();
});
// Hide-show
$('#quiz').on('click', '.hideshow', function() {
	var $this = $(this), $parent = $this.parents('div:first'), show = $this.is(':checked');
	$parent.find($this.attr('data-selector')).toggle(show);
});
// Delete
$('#quiz').on('click', '.del', function() {
	$(this).parent().remove();
})
// Save
$('#quiz form').on('submit', function(e) {
	e.preventDefault();
	$('#output').text(create());
});

// Helpers
function val(selector, s) {
	if (s) return JSON.stringify(val(selector));
	return $(selector).val();
}
function bool(selector, s) {
	if (s) return JSON.stringify(bool(selector));
	return $(selector).is(':checked');
}
function attr(attr, val) {
	if (val) return " "+attr+"="+JSON.stringify(""+val);
	return "";
}
function vals(selector) {
	var ret = [];
	$(selector).find('select, input').each(function() {
		var $this = $(this);
		if ($this.is(':checkbox'))
			ret.push($this.is(':checked'));
		else ret.push($this.val());
	})
	return ret;
}

// Glue!
function create() {
	var php = val('#id',true) + " => [\n\t";
	$.each(["name","lang","category","n_questions"], function(_,key) {
		if (val('#'+key))
			php += JSON.stringify(key) + " => " + val('#'+key,true) + ",\n\t";
	});
	$.each(["no_shuffle"], function(_,key) {
		php += JSON.stringify(key) + " => " + bool('#'+key,true) + ",\n\t";
	});
	php += "\"options\" => [";
	$('.option').each(function() {
		php += "[\n\t";

		var $this = $(this), $help = $this.find('.help');
		if (val($help)) php += "\t\"help\" => " + val($help,true) + ",\n\t";
		var extras = {}, selections = {};
		extras.answerN = 0;

		php += "\t\"sentence\" => [\n\t\t";
		$this.find('div[data-type]').each(function() {
			var $el = $(this), type = types[$el.attr('data-type')];

			if (type["word"]) php += "\t" + type.word.apply(this, vals($el)) + ",\n\t\t";

			if (type["extra"])     type.extra    .apply(this, [extras].concat(vals($el)));
			if (type["selection"]) type.selection.apply(this, [selections].concat(vals($el)));

			if (type["answer"]) extras.answerN += 1;
		});
		php += "],\n\t";

		extras.answerN = null;
		$.each(extras, function(key, value) {
			if (value === 0 || value === "" || value)
				php += "\t\""+key+"\" => " + value + ",\n\t";
		});

		php += "\t\"selections\" => [\n\t\t";
		$.each(selections, function(key, value) {
			php += "\t\""+key+"\" => " + value + ",\n\t\t";
		});
		php += "],\n\t";

		php += "],";
	});
	return php.slice(0,php.length-1) + "]\n],";
}
</script>
