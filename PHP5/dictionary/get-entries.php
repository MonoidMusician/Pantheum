<?php
	require_once('/var/www/latin/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');

	$start = microtime(true);
	$editor = requireRank(3, FALSE);
	$db = defaultDB();
	$id2vals = "{"; # for JS
	$dependencies = "{";
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
			{ $ids = NULL; }

		$inflection = !(safe_get("no_inflection",$_GET) === "true");

		if ($ids === NULL) {
			$searcher = $db->searcher();
			if ($names)
				$searcher = $searcher->name($names);
			if ($langs)
				$searcher = $searcher->lang($langs);
			if ($sparts)
				$searcher = $searcher->spart($sparts);
			foreach ($attrs as $attr) {
				if (!$attr) continue;
				$a = NULL;
				if ($reverse = (substr($attr, 0, 1) === "!")) {
					$attr = substr($attr, 1);
				}
				if (strpos($attr,"=") === FALSE)
					$a = ATTR($attr);
				else {
					list ($name,$value) = explode("=",$attr,2);
					$a = ATTR($name,$value);
				}
				if ($a !== NULL) {
					if (!$reverse)
						$searcher = $searcher->only_with_attr($a);
					else
						$searcher = $searcher->only_without_attr($a);
				}
			}
			$list = $searcher->all("name");
		} else {
			$list = [];
			foreach ($ids as $id)
				$list[] = WORD(defaultDB(), intval($id));
		}
		$pl = count($list) == 1 ? "" : "s";
		echo "<h4>Found ".count($list)." result$pl:</h4>";
		foreach ($list as $w) {
			$id = $w->id();
			?><section id="word<?= $id ?>"><?php
			$id2vals .= "$id:[";
			$dependencies .= "$id:{";
			display_word_info($w, $editor);
			display_definitions($w, $editor);
			if ($inflection) display_inflection($w);

			if ($editor) {
				$_level = array_merge($w->mgr()->simple_keys, $w->mgr()->recursive_keys);
				foreach ($w->mgr()->all_sub_keys as $k) {
					$first_level = in_array($k, $_level);
					$isselector = in_array($k, $w->mgr()->recursive_keys);
					if ($isselector) {
						$dependencies .= "'$k':{";
						$depaths = $w->mgr()->level[$k];
						foreach ($depaths as $_k => $depath) {
							$dependencies .= "'$_k':{";
							#var_dump($depath->key2values);
							$vec = $depath->all_sub_keys;
							if (!empty($vec)) {
								$vec = array_map(function($k) use($depath,$w) {
									$v = $w->mgr()->key2values[$k];
									$v2 = array_map(function($v) use($depath) {
										return "'$v', ".(array_key_exists($v, $depath->value2key) ? "true":"false");
									}, $v);
									return "'$k':[[".implode("],[", $v2)."]]";
								}, $vec);
								$dependencies .= implode(",", $vec);
							}
							$dependencies .= "},";
						}
						$dependencies .= "},";
					}
					$id2vals .= "'$k',";
					?><span id="word<?= $id ?>-<?= $k ?>" class="select"><?php
						$values = [NULL];
						$values = array_merge($values, $w->mgr()->key2values[$k]);
						#var_dump($values);
						foreach ($values as $v) {
							?><div id="word<?= $id ?>-div-<?= $v ?>"><label>
							<input class="inputlabel" type="radio"
								   name="word<?= $id ?>-<?= $k ?>"
								   id="word<?= $id ?>-div-<?= $v ?>"
								   value="<?= $v ?>" ><?= format_value($v) ?></label></div><?php
						}
					?></span><?php
				}
				?><span class="select">
					<div>
					<input id="word<?= $id ?>_value" type="text" placeholder="form, ..." required>
					<button id="word<?= $id ?>_button_enter" onclick="dict.word_set_val(<?= $id ?>)">Enter</button>
					<button id="word<?= $id ?>_button_delete" onclick="dict.word_del_val(<?= $id ?>)">Delete</button>
					<button id="word<?= $id ?>_button_clear" onclick="$('#word<?= $id ?>_value').val('')">Clear</button>
					</div>
					<div>
					<input id="word<?= $id ?>_value_def" type="text" placeholder="definition; ..." required>
					<button id="word<?= $id ?>_button_enter_def" onclick="dict.word_add_def(<?= $id ?>)">Add</button>
					<button id="word<?= $id ?>_button_clear_def" onclick="$('#word<?= $id ?>_value_def').val('')">Clear</button>
					</div>
					<div>
					<input id="word<?= $id ?>_value_pron" type="text" placeholder="pronunciation; ..." required>
					<button id="word<?= $id ?>_button_enter_pron" onclick="dict.word_add_pron(<?= $id ?>)">Add</button>
					<button id="word<?= $id ?>_button_clear_pron" onclick="$('#word<?= $id ?>_value_pron').val('')">Clear</button>
					</div>
					<div>
					<input id="word<?= $id ?>_value_templ" type="text" placeholder="template: arg; ..." required>
					<button id="word<?= $id ?>_button_enter_templ" onclick="dict.word_run_templ(<?= $id ?>)">Run</button>
					<button id="word<?= $id ?>_button_clear_templ" onclick="$('#word<?= $id ?>_value_templ').val('')">Clear</button>
					</div>
					<script type="text/javascript">
					$(function() {
						var id = <?= $id ?>;
						$('#word'+id+'_value'      ).keypress(function(e){if (e.which == 13)dict.word_set_val(<?= $id ?>)});
						$('#word'+id+'_value_def'  ).keypress(function(e){if (e.which == 13)dict.word_add_def(<?= $id ?>)});
						$('#word'+id+'_value_pron' ).keypress(function(e){if (e.which == 13)dict.word_add_pron(<?= $id ?>)});
						$('#word'+id+'_value_templ').keypress(function(e){if (e.which == 13)dict.word_run_templ(<?= $id ?>)});
						var names = <?php
							global $mysqli;

							$stmt = $mysqli->prepare("
								SELECT word_name FROM words
								WHERE word_id in (
									SELECT word_id FROM attributes
									WHERE attr_tag = 'template' AND attr_value = 'true'
								) AND word_spart = (?)
							");
							$res = [];
							sql_getmany($stmt, $res, ["s", $w->speechpart()]);
							echo json_encode($res);
						?>;
						var lock=false;
						var last = [$('#word'+id+'_value_templ').val()];
						$('#word'+id+'_value_templ').autocomplete({
							lookup: names,
							onSelect: function(selection) {
								if (lock) return; lock=true;
								var el = $('#word'+id+'_value_templ');
								if ($.inArray(selection.value, last) === -1) {
									el.val(el.val()+": ");
								}
								last = [el.val().split(":")[0]];
								el.focus();
								lock=false;
							},
						});
					});
					</script>
				</span><?php
			}
			$id2vals .= "],";
			$dependencies .= "},";
			if ($inflection) { ?><br><?php }
			display_connections($w,$editor);
			?></section><hr><?php
		}
	}

	$id2vals .= "}";
	$dependencies .= "}";
	#echo($id2vals);
	#echo("<br>");
	#echo($dependencies);
	$time = microtime(true) - $start;
	$time = round($time, 1);
	?>
		<script type="text/javascript">
		messageTip("Found <?= count($list) ?> result<?= $pl ?> in <?= $time ?> seconds!");
		</script>
	<?php
?>
<script type="text/javascript">
	$(function(){
		var id2vals = <?= $id2vals ?>;
		var dependencies = <?= $dependencies ?>;
		dict.register(id2vals, dependencies);
	});
</script>
