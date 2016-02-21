<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/dictionary/search.php');

	$_start_t = microtime(true);
	$editor = requireRank(3, FALSE);
	$id2vals = "{"; # for JS
	$dependencies = "{";

	$inflection = !(safe_get("no_inflections",$_GET) === "true");
	$start = intval(safe_get("start",$_GET));
	$limit = intval(safe_get("limit",$_GET));
	if ($limit <= 0) $limit = 5;
	if ($limit > 50) $limit = 50;

	if (count($_GET)) {
		$max_size = NULL;
		$list = search_GET($limit,$max_size);
		foreach ($list as $w) {
			$id = $w->id();
			?><hr><section id="word<?= $id ?>"><?php
			$id2val = "";
			$dependency = "";
			display_word_info($w, $editor);
			display_definitions($w, $editor);
			if ($inflection) display_inflection($w);

			if ($editor) {
				?><div id="word<?= $id ?>_edit_button"><br>
				<?php display_icon("edit", "Edit"); ?>
				</div>
				<div id="word<?= $id ?>_edit"><?php
				$_level = array_merge($w->mgr()->simple_keys, $w->mgr()->recursive_keys);
				foreach ($w->mgr()->all_sub_keys as $k) {
					$first_level = in_array($k, $_level);
					$isselector = in_array($k, $w->mgr()->recursive_keys);
					if ($isselector) {
						$dependency .= "'$k':{";
						$depaths = $w->mgr()->level[$k];
						foreach ($depaths as $_k => $depath) {
							$dependency .= "'$_k':{";
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
								$dependency .= implode(",", $vec);
							}
							$dependency .= "},";
						}
						$dependency .= "},";
					}
					$id2val .= "'$k',";
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
				?> <span class="select">
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
						var load = function() {
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
							});
							var id2vals = <?= "{".$id.":[".$id2val."]}" ?>;
							var dependencies = <?= "{".$id.":{".$dependency."}}" ?>;
							dict.register(id2vals, dependencies);
						};
						$('#word'+id+"_edit").hide();
						$('#word'+id+"_edit_button a").on('click', function() {
							$('#word'+id+"_edit_button").remove();
							$('#word'+id+"_edit").show();
							load();
						});
					});
					</script>
				</span>
				</div><?php
			}
			$id2vals .= "$id:[$id2val],";
			$dependencies .= "$id:{".$dependency."},";
			if ($inflection) { ?><br><?php }
			display_connections($w,$editor);
			?></section><?php
		}
	}

	$id2vals .= "}";
	$dependencies .= "}";
	#echo($id2vals);
	#echo("<br>");
	#echo($dependencies);
	$time = microtime(true) - $_start_t;
	$time = round($time, 1);
?>
