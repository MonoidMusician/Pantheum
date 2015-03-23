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
<input id="enter-sentence" type="text" value="<?= safe_get('sentence', $_GET) ?>" placeholder="Enter the sentence">
<button onclick="get_sentence();">Search</button>
<button onclick="$('#enter-sentence').val('');">Clear</button>
<article>
</article>
<script>
	function handleResponse(data) {
		//messageTip("Response succeeded");
		$('article').html(data);
	}
	var last_loc = "";
	function get_sentence() {
		//messageTip("Loading sentence...");
		var my = this;
		if ($('#enter-sentence').val())
			var loc = "sentence="+encodeURIComponent($('#enter-sentence').val());
		else var loc = "";
		if (loc != last_loc) {
			window.history.pushState(loc, "", 'sentence.php?'+loc);
		}
		$.get('/PHP5/sentence/get_sentence.php', loc)
		.done(handleResponse)
		.fail(function(data) {
			messageTip('Query failed! The server returned status '+data.status+": "+data.statusText)
		});
	};
	$(document).on('keyup', '#enter-sentence', function(event) {
		if (event.which == 13) {
			$(this).blur();
			get_sentence();
		}
	});
	$(document).on('click', '#translation_hide', function(event) {
		var vis = !$('#translation').is(':visible');
		$('#translation_hide').text(vis ? 'hide' : 'show translation');
		$('#translation').toggle();
	});
	$(function(){
		function getcheckbox(name) {
			var ret=[];
			$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
				ret.push($(this).val());
			});
			return ret.join();
		}
		$('#enter-sentence').autocomplete({
			serviceUrl: '/PHP5/sentence/get_sentence_names.php',
			params: {},
			paramName: "sentence",
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 0,
			onSelect: function() {
				get_sentence();
			},
		});
		if ($('#enter-sentence').val()) get_sentence();
	});
</script>

