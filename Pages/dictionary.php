<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/common.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');

	global $sql_stmts;
	$db = defaultDB();
	$editor = requireRank(3, FALSE);
	if (count($_GET)) {
		if (!array_key_exists("lang", $_GET) or !(
			$langs = vec_norm(explode(",", $_GET["lang"]), "trim")
			))
			{ $langs = ['la']; }

		if (!array_key_exists("name", $_GET) or !(
			$names = vec_norm(explode(",", $_GET["name"]), "trim")
			))
			{ $names = NULL; }

		if (!array_key_exists("spart", $_GET) or !(
			$sparts = vec_norm(explode(",", $_GET["spart"]), "trim")
			))
			{ $sparts = NULL; }

		if (!array_key_exists("attr", $_GET) or !(
			$attrs = vec_norm(explode(",", $_GET["attr"]), "trim")
			))
			{ $attrs = []; }

		if (!array_key_exists("id", $_GET) or !(
			$ids = vec_norm(explode(",", $_GET["id"]), "trim")
			))
			{ $ids = []; }
	} else {
		$langs = ["la"]; $names = $sparts = $ids = [];
	}
	$start = intval(safe_get("start",$_GET));
	$limit = intval(safe_get("limit",$_GET));
	if ($limit <= 0) $limit = 5;
	if ($limit > 50) $limit = 50;
?>
<header>
	<h1>Dictionary</h1>
	<h4>Find words by name, attributes, language, and/or part of speech</h4>
</header>
	<div id="search-form">
	<!--<span class="select">-->
	<?php $noinfl = (!array_key_exists("no_inflections",$_GET) or $_GET["no_inflections"] !== "true") ? "" : "checked"; ?>
	<?php $nodefs = (!array_key_exists("no_definitions",$_GET) or $_GET["no_definitions"] !== "true") ? "" : "checked"; ?>
	<?php $showtmpls = (!array_key_exists("show_templates",$_GET) or $_GET["show_templates"] !== "true") ? "" : "checked"; ?>
	<!--<div>--><label><input name="no-inflections" type="checkbox" <?= $noinfl ?>>Hide inflection</label><!--</div>-->
	<!--<div>--><label><input name="show-templates" type="checkbox" <?= $showtmpls ?>>Show declensions/conjugations</label><!--</div>-->
	<?php if (requireRank(3, FALSE)) { ?>
		<!--<div>--><label><input name="no-definitions" type="checkbox" <?= $nodefs ?>>Show only words without definitions</label><!--</div>-->
	<?php } ?>
	<!--</span>-->
	<br>
	<br>Name(s):
	<input id="enter-names" type="text" value="<?= safe_get('name', $_GET) ?>" placeholder="name, ...">
	<br>Form(s):
	<input id="enter-forms" type="text" value="<?= safe_get('form', $_GET) ?>" placeholder="form, ...">
	<br>Language(s):
	<select id="enter-langs" style="width: 300px;"></select>
	<br>Attributes:
	<input id="enter-attrs" type="text" value="<?= safe_get('attr', $_GET) ?>" placeholder="[!]attr[=value], ...">
	<br>Part(s) of speech:
	<select id="enter-sparts" style="width: 300px;"></select>
	
	<br>
	<input id="enter-ids" style="width: 100px;" type="text" value="<?= safe_get('id', $_GET) ?>" placeholder="id, ...">
	<button onclick="dict.refreshEntries();">Search</button>
	<?php if ($editor) { ?>
	<button onclick="dict.addEntry(function(){dict.refreshEntries();});">Add</button>
	<?php } ?>
	<button onclick="$('#enter-attrs,#enter-ids,#enter-names,#enter-forms').val('');$('[name=enter-spart], [name=enter-lang]').prop('checked', false);">Clear fields</button>

	<div class="navigation">
		Show <select id="limit">
		<?php
			$_ = [5,10,20,50];
			if (!in_array($limit,$_)) {
				$_[] = $limit;
				sort($_);
			}
			foreach ($_ as $__) {
				?><option <?= ($__===$limit?"selected":"") ?>><?= $__ ?></option><?php
			}
		?>
		</select> results from
			<span class="actionable" id="goto-first" title="First results">&lt;&lt;</span>
			<span class="actionable" id="goto-prev"  title="Previous results">&lt;</span>
			<input placeholder="<?= $start; ?>" type="number" value="<?= $start; ?>" min="0" id="start-at" style="width: 80px;">
			<span class="actionable" id="goto-next"  title="Next results">&gt;</span>
			<span class="actionable" id="goto-last"  title="Last results">&gt;&gt;</span>
		<script>
			$('#start-at').on("keypress", function(event) {
				if (event.which == 13) {
					$(this).blur();
					dict.refreshEntries();
				}
			});
			$('#start-at').on("change", function(event) {
				$(this).blur();
				dict.refreshEntries();
			});
			$('#goto-first').on('click', function() {
				$('#start-at').val(0);
				dict.refreshEntries();
			});
			$('#goto-last').on('click', function() {
				$('#start-at').val(dict.number_entries - $('#limit').val());
				dict.refreshEntries();
			});
			$('#goto-prev').on('click', function() {
				var next = $('#start-at').val() - $('#limit').val();
				if (next < 0) next = 0;
				$('#start-at').val(next);
				dict.refreshEntries();
			});
			$('#goto-next').on('click', function() {
				var limit = (-$('#limit').val());
				var next = $('#start-at').val() - limit;
				if (next > dict.number_entries + limit)
					next = dict.number_entries + limit;
				$('#start-at').val(next);
				dict.refreshEntries();
			});
		</script> (out of <span id="number-entries">0</span>).
		</div>
	</div>

<article id="dictionary"/>
<script type="text/javascript">
	$(document).on('keyup', '#enter-names, #enter-attrs, #enter-ids, #enter-forms', function(event) {
		var val = $('#enter-attrs').val();
		if (event.which == 13 && val[val.length-1] !== "=") {
			$(this).blur();
			dict.refreshEntries();
		}
	});
	$(function(){
		var lock=false;
		var splitter = /(?:,\s*)/;
		var last1 = $('#enter-names').val().split(splitter);
		function getcheckbox(name) {
			var ret=[];
			$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
				ret.push($(this).val());
			});
			return ret.join();
		}
		$('#enter-sparts').select2({
			multiple: "multiple"
		});
		$('#enter-langs').select2({
			data: <?= json_encode(select2_getlangs()) ?>,
			multiple: "multiple"
		}).on('change', function() {
			var sparts = {}, langs = $('#enter-langs').val() || <?= json_encode($db->langs()) ?>;
			<?php foreach ($db->sparts_by_lang as $l=>$_sparts) { ?>
				if ($.inArray("<?= $l ?>",langs) > -1) {
					<?php foreach ($_sparts as $s) { ?>
						sparts["<?= $s ?>"] = true;
					<?php } ?>
				}
			<?php } ?>
			$('#enter-sparts').html('');
			$.each(sparts, function(spart) {
				$('#enter-sparts').append('<option value="'+spart+'">'+spart[0].toUpperCase()+spart.slice(1));
			});
		}).val(<?= json_encode($langs) ?>).trigger('change');
		$('#enter-sparts').val(<?= json_encode($sparts) ?>).trigger('change');
		$('#enter-names').autocomplete(autocompletions['dictionary-names']);
		$('#enter-forms').autocomplete(autocompletions['dictionary-forms']);
		$('#enter-attrs').autocomplete(autocompletions['dictionary-attributes']);
		var number_entries = function(e) {
			dict.number_entries = e.max_length;
			$('#number-entries').text(e.max_length); // hint: set in PHP
			$('#start-at').attr('max', Math.max(e.max_length - $('#limit').val(),0));
		};
		dict = new jWord();//http://52.3.75.179/dictionary.php?lang=la&spart=verb&start=0&limit=5
		dict.init('dictionary', '/PHP5/quiz/get-entries.php', '/PHP5/quiz/set-path.php', '#enter-names');
		dict.bindEvents();
		$('#search-form input, #search-form select').on('change', function() {
			dict.previewEntries(number_entries);
		});
		dict.previewEntries(number_entries);
		$('[name=no-inflections]').on('change', function() {
			if ($.jStorage) $.jStorage.flush();
		});
	});
	<?php
		if (!$langs or $langs == ['la']) {
			?><?php
		}
		if ($_GET) {
	?>
			$(function(){dict.refreshEntries();});
	<?php
		}
	?>
</script>
