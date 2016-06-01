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
	$editor = hasACL('add_words', 'R', 'S');
	if (count($_GET)) {
		if (!array_key_exists("id", $_GET) or !(
			$ids = vec_norm(explode(",", $_GET["id"]), "intval")
			))
			{ $ids = []; }

		if ($ids) {
			$langs = array_unique(array_map(function($w) {
				$word = WORD($w);
				return $word->lang();
			}, $ids));
		} else if (!array_key_exists("lang", $_GET) or !(
			$langs = vec_norm(explode(",", $_GET["lang"]), "trim")
			))
			{ $langs = ['la']; }

		if ($ids or !array_key_exists("name", $_GET) or !(
			$names = vec_norm(explode(",", $_GET["name"]), "trim")
			))
			{ $names = NULL; }

		if ($ids or !array_key_exists("spart", $_GET) or !(
			$sparts = vec_norm(explode(",", $_GET["spart"]), "trim")
			))
			{ $sparts = NULL; }

		if ($ids or !array_key_exists("attr", $_GET) or !(
			$attrs = vec_norm(explode(",", $_GET["attr"]), "trim")
			))
			{ $attrs = []; }

		if ($ids or !array_key_exists("def", $_GET) or !(
			$defs = vec_norm(explode(",", $_GET["def"]), "trim")
			))
			{ $defs = []; }
	} else {
		$langs = ["la"]; $names = $sparts = $ids = [];
	}
	$start = intval(safe_get("start",$_GET));
	$limit = intval(safe_get("limit",$_GET));
	if ($limit <= 0) $limit = 5;
	if ($limit > 50) $limit = 50;
?>
<header>
	<h1 data-i18n>Dictionary</h1>
	<h4 data-i18n="dictionary.description">Find words by name, attributes, language, and/or part of speech</h4>
</header>
	<div id="search-form">
	<?php $noinfl = (!array_key_exists("no_inflections",$_GET) or $_GET["no_inflections"] !== "true") ? "" : "checked"; ?>
	<?php $nodefs = (!array_key_exists("no_definitions",$_GET) or $_GET["no_definitions"] !== "true") ? "" : "checked"; ?>
	<?php $showtmpls = (!array_key_exists("show_templates",$_GET) or $_GET["show_templates"] !== "true") ? "" : "checked"; ?>
	<label><input name="no-inflections" type="checkbox" <?= $noinfl ?>><span data-i18n="dictionary.hide_inflection">Hide inflection</span></label>
	<label><input name="show-templates" type="checkbox" <?= $showtmpls ?>><span data-i18n="dictionary.show_decl_conj">Show declensions/conjugations</span></label>
	<?php if (requireRank(3, FALSE)) { ?>
		<label><input name="no-definitions" type="checkbox" <?= $nodefs ?>><span data-i18n="dictionary.show_no_defs">Show only words without definitions</span></label>
	<?php } ?>
	<br>
	<br><span data-i18n="dictionary.names">Name(s)</span>:
	<input id="enter-names" type="text" value="<?= safe_get('name', $_GET) ?>" placeholder="name, ...">
	<br><span data-i18n="dictionary.forms">Form(s)</span>:
	<input id="enter-forms" type="text" value="<?= safe_get('form', $_GET) ?>" placeholder="form, ...">
	<br><span data-i18n="dictionary.languages">Languages(s)</span>:
	<select id="enter-langs" style="width: 300px;"></select>
	<br><span data-i18n="dictionary.parts_of_speech">Part(s) of speech</span>:
	<select id="enter-sparts" style="width: 300px;"></select>
	<br><span data-i18n="dictionary.definitions">Definition(s)</span>:
	<input id="enter-defs" type="text" value="<?= safe_get('def', $_GET) ?>" placeholder="definition_part; definition1, definition2,; ...">
	<br><span data-i18n="dictionary.attributes">Attribute(s)</span>:
	<input id="enter-attrs" type="text" value="<?= safe_get('attr', $_GET) ?>" placeholder="[!]attr[=value], ...">

	<br>
	ID: <input id="enter-ids" style="width: 100px;" type="text" value="<?= safe_get('id', $_GET) ?>" placeholder="id, ...">
	<button onclick="dict.refreshEntries();" data-i18n="ui.search">Search</button>
	<?php if ($editor) { ?>
	<button onclick="dict.addEntry(function(){dict.refreshEntries();});" data-i18n="ui.add">Add</button>
	<?php } ?>
	<button onclick="$('#enter-attrs,#enter-ids,#enter-names,#enter-forms,#enter-langs,#enter-sparts').val('').change();" data-i18n="ui.clear_fields">Clear fields</button>

	<div class="navigation">
		<span data-i18n="dictionary.show">Show</span> <select id="limit">
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
		</select> <span data-i18n="dictionary.results_from">results from</span>
			<?php display_icon("&lt;&lt;", "First", "goto-first"); ?>
			<?php display_icon("&lt;", "Previous", "goto-prev"); ?>
			<input placeholder="<?= $start; ?>" type="number" value="<?= $start; ?>" min="0" id="start-at" style="width: 80px;">
			<?php display_icon("&gt;", "Next", "goto-next"); ?>
			<?php display_icon("&gt;&gt;", "Last", "goto-last"); ?>
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
		</script> (<span data-i18n="ui.out_of">out of</span> <span id="number-entries">0</span>).
		</div>
	</div>

<article id="dictionary"/>
<script type="text/javascript">
	$(document).on('keyup', '#enter-names, #enter-attrs, #enter-ids, #enter-forms, #enter-defs', function(event) {
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
