<?php
	require_once('/var/www/latin/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/common.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');

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
?>
<header>
	<h1>Dictionary</h1>
	<h4>Find words by name, attributes, language, and/or part of speech</h4>
</header>
	Find words:
	<input id="enter-names" type="text" value="<?= safe_get('name', $_GET) ?>" placeholder="name, ...">
	<span class="select">
<?php
	global $sql_stmts;
	foreach ($db->langs() as $l) {
		$name = $l;
		sql_getone($sql_stmts["lang_id->lang_dispname"], $name, ["s", $l]);
		?><div><label><input type="checkbox" name="enter-lang" <?php
		if ($langs !== NULL and in_array($l, $langs)) {
			?>checked<?php
		}
		?> value="<?= $l ?>" ><?= $name ?></label></div><?php
	}
?>
	</span>
	<span class="select">
<?php
	foreach ($db->sparts() as $s) {
		?><div><label><input type="checkbox" name="enter-spart" <?php
		if ($sparts !== NULL and in_array($s, $sparts)) {
			?>checked<?php
		}
		?> value="<?= $s ?>" ><?= format_spart($s) ?></label></div><?php
	}
?>
	</span>
	<input id="enter-attrs" type="text" value="<?= safe_get('attr', $_GET) ?>" placeholder="[!]attr[=value], ...">
	<input id="enter-ids" style="width: 100px;" type="text" value="<?= safe_get('id', $_GET) ?>" placeholder="id, ...">
	<?php $noinfl = (!array_key_exists("no_inflection",$_GET) or $_GET["no_inflection"] !== "true") ? "" : "checked"; ?>
	<label><input name="to-hide-inflection" type="checkbox" <?= $noinfl ?>>Hide inflection</label>
	<button onclick="dict.refreshEntries();">Search</button>
	<?php if ($editor) { ?>
	<button onclick="dict.addEntry(function(){dict.refreshEntries();});">Add</button>
	<?php } ?>
	<button onclick="$('#enter-attrs,#enter-ids,#enter-names').val('');$('[name=enter-spart], [name=enter-lang]').prop('checked', false);">Clear fields</button>

<article id="dictionary"/>
<script type="text/javascript">
	$(document).on('keyup', '#enter-names, #enter-attrs', function(event) {
		if (event.which == 13) {
			$(this).blur();
			dict.refreshEntries();
		}
	});
	$(function(){
		var lock=false;
		var splitter = /,\s*/;
		var last = $('#enter-names').val().split(splitter);
		function getcheckbox(name) {
			var ret=[];
			$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
				ret.push($(this).val());
			});
			return ret.join();
		}
		$('#enter-names').autocomplete({
			//lookup: names,
			serviceUrl: '/latin/PHP5/dictionary/get-names-json.php',
			params: {},
			delimiter: splitter,
			onSelect: function(selection) {
				if (lock) return; lock=true;
				var el = $('#enter-names');
				if ($.inArray(selection.value, last) === -1) {
					el.val(el.val()+", ");
				}
				last = el.val().split(splitter);
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
			minChars: 0,
		});
	});
	var dict = new jWord();
	dict.init('dictionary', '/latin/PHP5/quiz/get-entries.php', '/latin/PHP5/quiz/set-path.php', '#enter-names');
	<?php
		if ($_GET) {
	?>
			$(function(){dict.refreshEntries();});
	<?php
		}
	?>
</script>
