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
	<span class="select" id="select-langs">
<?php
	$show_more = FALSE;
	foreach ($db->langs() as $l) {
		$name = $l;
		sql_getone($sql_stmts["lang_id->lang_dispname"], $name, ["s", $l]);
		?><div <?php
		if (!in_array($l, $langs)) {
			$show_more = TRUE;
			?>style="display: none"<?php
		}
		?>><label><input type="checkbox" id="lang-<?= $l ?>" name="enter-lang" <?php
		if (in_array($l, $langs)) {
			?>checked<?php
		}
		?> value="<?= $l ?>" ><?php display_lang($l);echo$name ?></label></div><?php
	}
?>
	</span>
<?php
	if ($show_more) {
		?><a href="javascript:void(0)" onclick="$(this).remove();$('#select-langs div').show();">(show all)</a><?php
	}
?>
	<br>Attributes:
	<input id="enter-attrs" type="text" value="<?= safe_get('attr', $_GET) ?>" placeholder="[!]attr[=value], ...">
	<br>Part(s) of speech:
	<span class="select" id="select-sparts">
<?php
	foreach ($db->sparts() as $s) {
		?><span><label title="<?= ucfirst($s) ?>"><input type="checkbox" id="spart-<?= $s ?>" name="enter-spart" <?php
		if ($sparts !== NULL and in_array($s, $sparts)) {
			?>checked<?php
		}
		?> value="<?= $s ?>" ><?= format_spart($s) ?></label></span><?php
	}
?>
	</span>
	<br>Show <select id="limit">
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
	</select> results from <input placeholder="<?= $start; ?>" value="<?= $start; ?>" id="start-at" style="width: 50px;">
	<script>$('#start-at').on("keypress", function(event) {
		if (event.which == 13) {
			$(this).blur();
			dict.refreshEntries();
		}
	});</script> (out of <span id="number-entries">0</span>).
	<input id="enter-ids" style="width: 100px;" type="text" value="<?= safe_get('id', $_GET) ?>" placeholder="id, ...">
	<button onclick="dict.refreshEntries();">Search</button>
	<?php if ($editor) { ?>
	<button onclick="dict.addEntry(function(){dict.refreshEntries();});">Add</button>
	<?php } ?>
	<button onclick="$('#enter-attrs,#enter-ids,#enter-names,#enter-forms').val('');$('[name=enter-spart], [name=enter-lang]').prop('checked', false);">Clear fields</button>
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
		var last2 = $('#enter-attrs').val().split(splitter);
		function getcheckbox(name) {
			var ret=[];
			$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
				ret.push($(this).val());
			});
			return ret.join();
		}
		$('#select-langs input').on('change', function() {
			$('#select-sparts > *').hide();
			var all = !$('#select-langs input:checked:visible').length;
			<?php foreach ($db->sparts_by_lang as $l=>$sparts) { ?>
				if (all || $('#select-langs input#lang-<?= $l ?>:checked:visible').length) {
					$('#select-sparts').find(
						'<?php $sep=""; foreach ($sparts as $s) { echo "$sep#spart-$s";$sep=",";} ?>'
					).parent().parent().show();
				}
			<?php } ?>
		}).first().trigger('change');
		$('#enter-names').autocomplete({
			//lookup: names,
			serviceUrl: '/PHP5/dictionary/get-names-json.php',
			params: {},
			delimiter: splitter,
			onSelect: function(selection) {
				return;
				if (lock) return; lock=true;
				var el = $('#enter-names');
				if ($.inArray(selection.value, last1) === -1) {
					el.val(el.val()+", ");
				}
				last1 = el.val().split(splitter);
				el.focus();
				lock=false;
			},
			paramName: "name",
			deferRequestBy: 150,
			onSearchStart: function(query) {
				$(this).autocomplete().options.params["attr"] = $('#enter-attrs').val();
				$(this).autocomplete().options.params["lang"] = getcheckbox('enter-lang');
				$(this).autocomplete().options.params["spart"] = getcheckbox('enter-spart');
			},
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 2,
		});
		$('#enter-forms').autocomplete({
			//lookup: names,
			serviceUrl: '/PHP5/dictionary/get-forms-json.php',
			params: {},
			delimiter: splitter,
			onSelect: function(selection) {
				return;
				if (lock) return; lock=true;
				var el = $('#enter-forms');
				if ($.inArray(selection.value, last1) === -1) {
					el.val(el.val()+", ");
				}
				last1 = el.val().split(splitter);
				el.focus();
				lock=false;
			},
			paramName: "form",
			deferRequestBy: 150,
			onSearchStart: function(query) {
				$(this).autocomplete().options.params["attr"] = $('#enter-attrs').val();
				$(this).autocomplete().options.params["lang"] = getcheckbox('enter-lang');
				$(this).autocomplete().options.params["spart"] = getcheckbox('enter-spart');
			},
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 5,
		});
		$('#enter-attrs').autocomplete({
			//lookup: names,
			serviceUrl: '/PHP5/dictionary/get-attributes-json.php',
			params: {},
			delimiter: splitter,
			onSelect: function(selection) {
				return;
				if (lock) return; lock=true;
				var el = $('#enter-attrs');
				if ($.inArray(selection.value, last2) === -1) {
					if (selection.value.indexOf("={") === -1) {
						el.val(el.val()+", ");
					} else {
						var prev = /^(.*?,?)(?:[^{,}]|\{[^{}]*\})+$/.exec(el.val())[1];
						console.log(prev);
						var re = /\{([^,]+)\}$/;
						var matched = re.exec(selection.value);
						console.log(matched);
						if (matched !== null)
							el.val(prev+selection.value.split("=")[0]+"="+matched[1]);
						else el.val(prev+selection.value.split("=")[0]+"=");
					}
				}
				last2 = el.val().split(splitter);
				el.focus();
				lock=false;
			},
			paramName: "attr",
			deferRequestBy: 150,
			onSearchStart: function(query) {
				$(this).autocomplete().options.params["name"] = $('#enter-names').val();
				$(this).autocomplete().options.params["lang"] = getcheckbox('enter-lang');
				$(this).autocomplete().options.params["spart"] = getcheckbox('enter-spart');
			},
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 0,
		});
		var number_entries = function(e) {
			$('#number-entries').text(e.max_length); // hint: set in PHP
		};
		$('#search-form input, #search-form select').on('change', function() {
			dict.previewEntries(number_entries);
		}).trigger('change');
	});
	var dict = new jWord();
	dict.init('dictionary', '/PHP5/quiz/get-entries.php', '/PHP5/quiz/set-path.php', '#enter-names');
	dict.bindEvents();
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
