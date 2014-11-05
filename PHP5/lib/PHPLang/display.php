<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');

function _get_first_last($arr, &$first, &$last) {
	if (!$arr) return;
	$first = $arr[0];
	$last = $arr[count($arr)-1];
}

function display_word_entries($list) {
	foreach ($list as $w) {
		display_word_entry($w);
		?><hr><?php
	}
}

function display_word_entry($w, $inflection_hidden=TRUE) {
	display_word_info($w);
	display_definitions($w);
	display_inflection($w, $inflection_hidden);
}

// Format word for displaying based upon replacements
// TODO: user settings, DB encoding
function format_word($w, $replacements=NULL) {
	if (!strlen($w)) return "—"; # em-dash
	if ($replacements === NULL)
		$replacements = [
			"ae" => "aë","oe" => "oë","Ae" => "Aë","Oe" => "Oë",
			#"æ" => "ae","œ" => "oe","Æ" => "Ae","Œ" => "Oe",
			#"æ" => "ai","œ" => "oi","Æ" => "Ai","Œ" => "Oi",
			#"x"=>"cs",
			#"no_specials",
			#function($s) {return mb_strtoupper($s,"utf-8");},
			#"ī"=>"ii","ā"=>"aa","ē"=>"ee","ō"=>"oo","ū"=>"uu",
			"j" => "i", "J" => "I",
			#"v" => "u",#"U" => "V",
			#"<" => "&lt;",">" => "&gt;", "{HTMLLT}"=>"<","{HTMLGT}"=>">",
			"\n" => "<br>",
		];
	foreach ($replacements as $old => $new) {
		if (is_int($old) and is_callable($new))
			$w = $new($w);
		else
			$w = str_replace($old, $new, $w);
	}
	return $w;
}
function format_word1($w) { return format_word($w); }
function format_pron($w, $replacements=NULL) {
	if ($replacements === NULL)
		$replacements = [
			"'" => "ˈ",
			":" => "ː",
		];
	foreach ($replacements as $old => $new) {
		if (is_int($old) and is_callable($new))
			$w = $new($w);
		else
			$w = str_replace($old, $new, $w);
	}
	return $w;
}

function no_specials($w) {
	$w = normalizer_normalize($w, Normalizer::FORM_D);
	$w = str_replace("æ", "ae", $w);
	$w = str_replace("œ", "oe", $w);
	$w = str_replace("Æ", "ae", $w);
	$w = str_replace("Œ", "oe", $w);
	$w = preg_replace("#[^A-Za-z1-9 \\n]#","", $w);
	return $w;
}

// Normalize a word to check for basic equality
function unformat_word($w) {
	$w = mb_strtolower(no_specials($w), "utf-8");
	$w = str_replace("j", "i", $w);
	$w = str_replace("u", "v", $w);
	return normalize_spaces($w);
}

function format_key ($k) {
	return str_replace("-", " ", ucfirst($k));
}
function format_value ($v) {
	if ($v === "person-1")
		return "1st person";
	if ($v === "person-2")
		return "2nd person";
	if ($v === "person-3")
		return "3rd person";
	if ($v === "supine-1")
		return "Supine I";
	if ($v === "supine-2")
		return "Supine II";
	return str_replace("-", " ", ucfirst($v));
}
function format_spart($spart) {
	if ($spart === "adverb") return "Adv.";
	if ($spart === "verb") return "V.";
	if ($spart === "noun") return "N.";
	if ($spart === "adjective") return "Adj.";
	if ($spart === "pronoun") return "Pro.";
	if ($spart === "preposition") return "Prep.";
	return ucfirst($spart);
}
function format_attr($tag,$value=NULL) {
	if ($tag === "transitive")
		if ($value === "true") return "transitive";
		elseif ($value === "false") return "intransitive";
	if ($tag === "irregular")
		if ($value === "true") return "irregular";
		elseif ($value === "false") return "regular";
	if ($tag === "person")
		if ($value === "person-1") return "1st person";
		elseif ($value === "person-2") return "2nd person";
		elseif ($value === "person-3") return "3rd person";
	if ($tag === "declension")
		if ($value === "decl-1") return "1st Declension";
		elseif ($value === "decl-2") return "2nd Declension";
		elseif ($value === "decl-3") return "3rd Declension";
	if ($tag === "conjugation")
		if ($value === "conj-1") return "1st Conjugation";
		elseif ($value === "conj-2") return "2nd Conjugation";
		elseif ($value === "conj-3") return "3rd Conjugation";
		elseif ($value === "conj-4") return "4th Conjugation";
	return $value !== NULL ? "$tag=$value" : "$tag";
}

function display_word_info($w, $can_edit=FALSE) {
	$id = $w->id();
	?>
	<sup>[<?= $w->lang() ?>]</sup><span class="word-name" id="word<?= $w->id() ?>_name"><?= format_word($w->name()) ?></span>
	<?php
	$infos = [];
	$w->read_paths();
	$stem = $w->path();
	if ($stem->hasvalue())
		$infos[] = implode(", ", explode("\n", $stem->get()));
	$infos[] = $w->speechpart();
	foreach ($w->read_attrs() as $attr) {
		$infos[] = format_attr($attr->tag(), $attr->value());
	}
	?>(<?php echo implode("; ", $infos); ?>) <?php
	?>[<a href="dictionary2.php?id=<?= $id ?>">hardlink</a>]<?php
	if ($can_edit) {
?>
		[<a href="javascript:void(0)" id="word<?= $w->id() ?>_delete">del</a>]
		<script type="text/javascript">
			$(function() {
				var id = <?= $id ?>;
				$('#word'+id+'_delete').on("click", function() {
					dict.word_delete(id);
				});
			});
		</script>
		[<a href="javascript:void(0)" id="word<?= $w->id() ?>_rename">rename</a>]
		<script type="text/javascript">
			$(function() {
				var id = <?= $id ?>;
				$('#word'+id+'_rename').on("click.rename", function() {
					dict.word_rename(id);
				});
			});
		</script>
<?php
	}
	$made_div = FALSE;
	$first = TRUE;
	$last_type = NULL;
	foreach ($w->pronunciations() as $pron) {
		if ((string)$pron->path()) continue;
		if (!$pron->value()) continue;
		if (!$made_div) {
			?><div class="word-more-info"><?php
			$made_div = TRUE;
		}
		if (!$first and $pron->type() === $last_type) { ?>; <?php }
		else {
			if (!$first) { ?><br><?php }
			echo $pron->type().": ";
		}
		$last_type = $pron->type();
		if ($pron->sublang()) {
			?><sup>[<?= $pron->sublang() ?>]</sup><?php
		}
		?>/<?=
		format_pron($pron->value())
		?>/<?php
		$first = FALSE;
	}
	if ($made_div) {
		?></div><?php
	}
	if ($can_edit) {
?>
		<div>
		<input id="word<?= $id ?>_value_attr" type="text" placeholder="[!]attr[=value]; ..." required>
		<button id="word<?= $id ?>_button_enter_attr" onclick="dict.word_add_attr(<?= $id ?>)">Add</button>
		<button id="word<?= $id ?>_button_clear_attr" onclick="$('#word<?= $id ?>_value_attr').val('')">Clear</button>
		</div>
		<script type="text/javascript">
		$(function() {
			var id = <?= $id ?>;
			$('#word'+id+'_value_attr').keypress(function(e){if (e.which == 13)dict.word_add_attr(<?= $id ?>)});
		});
		</script>
<?php
	}
}

function display_definitions($w, $can_edit=FALSE) {
	?>
	<ol id="word<?= $w->id() ?>_definitions">
<?php
	foreach ($w->definitions() as $d) {
		?><li><?php
		$value = $d->value();
		$value = str_replace("\n", ", ", $value);
		if ((string)$d->path()) {
?>
			<sup>[<?= $d->lang() ?>]</sup>(<?= $d->path() ?>) <?= $value ?>
<?php
		} else {
?>
			<sup>[<?= $d->lang() ?>]</sup><?= $value ?>
<?php
		}
		if ($can_edit) {
?>
			[<a href="javascript:void(0)" id="definition<?= $d->id() ?>_delete">del</a>]
			<script type="text/javascript">
				$(function() {
					var w_id = <?= $w->id() ?>;
					var id = <?= $d->id() ?>;
					$('#definition'+id+'_delete').on("click", function() {
						dict.definition_delete(id, w_id);
					});
				});
			</script>
<?php
		}
		?></li><?php
	}
?>
	</ol>
<?php
	echo "<br>";
}

function display_inflection($w, $hidden=TRUE) {
	$w->read_paths();
	$spart = $w->speechpart();
	$pronunciations = $w->pronunciations();
	$w->clear_connections();
	$connections = $w->connections();
	$values4 =
	$values3 =
	$values2 =
	$values1 =
	$values0 = NULL;
	// values0 : table name
	// values1 : major vertical
	// values2 : major horizontal
	// values3 : minor horizontal
	// values4 : minor vertical
	if (($spart === "noun") or
		($spart === "adjective") or
		($spart === "pronoun")) {
		$values4 = $w->path()->iterate("case");
		$values3 = $w->path()->iterate("gender");
		$values2 = $w->path()->iterate("number");
		if ($spart === "adjective")
			$values1 = $w->path()->iterate("degree");
		else $values1 = [];
		$values0 = [];
	} elseif ($spart === "verb") {
		$values0 = $w->path()->iterate("mood");
	} elseif ($spart === "adverb") {
		$values1 = $w->path()->iterate("degree");
	}
	if (!$values0 and !$values1 and !$values2
	and !$values3 and !$values4) {
		?><p id="word<?= $w->id() ?>_forms">(No inflection for this word)</p><?php
		return;
	}
	?>
	Inflection [<a href="javascript:void(0)" id="toggle-forms<?= $w->id() ?>"><?php
	if ($hidden) echo "show"; else echo "hide";
	?></a>]<?php
	if ($pronunciations) {
		?><span id="toggle-pronunciations<?= $w->id() ?>_outer">
			[<a href="javascript:void(0)" id="toggle-pronunciations<?= $w->id() ?>">show IPA</a>]<br><br>
		</span><?php
	}
	do_table(
		$w,$values0,$values1,$values2,$values3,$values4,
		"format_value",
		"format_word1",
		function($p) use($connections) {
			$m = $p->mgr();
			if ($m->is_key("mood") and $m->is_key("tense") and $m->is_key("voice")) {
				$_0 = $p->key_value("mood");
				$_4 = $p->key_value("tense");
				$_2 = $p->key_value("voice");
				if ($_0 == "participle")
					foreach ($connections as $connect)
						if ($connect->type() === "Participle: $_4/$_2")
							return "dictionary2.php?id=".$connect->to()->id();
			}
		},
		function($p) use($pronunciations,$w) {
			$p = (string)$p;
			$made_div = FALSE;
			$last_type = NULL;
			$first = TRUE;
			foreach ($pronunciations as $pron) {
				if ((string)$pron->path() !== $p) continue;
				if ($pron->type() !== "IPA") continue;
				if (!$made_div) {
					?><span class="word<?= $w->id() ?>_pronunciation"><?php
					$made_div = TRUE;
				}
				?><br><?php
				$last_type = $pron->type();
				if ($pron->sublang()) {
					?><sup>[<?= $pron->sublang() ?>]</sup><?php
				}
				?>/<?=
				format_pron($pron->value())
				?>/<?php
				$first = FALSE;
			}
			if ($made_div) {
				?></span><?php
			}
		}
	);
	?>
	<script type="text/javascript">
		(function(){
			var c = "<?= $w->id() ?>";
			var selector = $('#word'+c+'_forms, #toggle-pronunciations'+c+'_outer');
			$('#toggle-forms'+c).click(function () {
				selector.toggle();
				var vis = $('#word'+c+'_forms').is(':visible');
				$('#toggle-forms'+c).text(vis ? 'hide' : 'show');
				if (!vis)
					$('.word'+c+'_pronunciation').hide();
			});
		<?php if (!$hidden) { ?>
			selector.hide();
		<?php } ?>
			$('#toggle-forms'+c).trigger("click");
			$('#toggle-pronunciations'+c).click(function () {
				$('.word'+c+'_pronunciation').toggle();
				$('#toggle-pronunciations'+c).text($('.word'+c+'_pronunciation').is(':visible') ? 'hide IPA' : 'show IPA');
			});
			$('.word'+c+'_pronunciation, #toggle-pronunciations').hide();
		})();
	</script>
	<?php
}

function do_table($w,$values0,$values1,$values2,$values3,$values4,$format_value,$format_word,$get_link=NULL,$extras=NULL,$optimization=3) {
	?><div class="scrollable"><?php
	if ($values1 and
       !$values2 and
       !$values3 and
       !$values4 and
       !$values0) {
		?><table class="text-center inflection inflection-small" id="word<?= $w->id() ?>_forms"><?php
		foreach ($values1 as $_1) {
			?><tr><th><?php
			echo format_value($_1);
			?></th></tr><tr><td><?php
			echo format_word(PATH($w,$_1)->get());
			?></td></tr><?php
		}
	} else {
		if (!$values4) $values4 = [FALSE];
		if (!$values3) $values3 = [FALSE];
		if (!$values2) $values2 = [FALSE];
		if (!$values1) $values1 = [FALSE];
		if (!$values0) $values0 = [FALSE];
		?><table class="text-left inflection" id="word<?= $w->id() ?>_forms"><?php
		$first0=$last0=NULL; _get_first_last($values0,$first0,$last0);
		foreach ($values0 as $_0) {
			$path = PATH($w, $_0);
			if ($_0 !== $first0) {
				// Blank row to separate sub-tables based on $values0
				?><tr><th>&nbsp;</th></tr><?php
			}
			if ($_0 === "indicative" or
				$_0 === "subjunctive" or
				$_0 === "imperative") {
				$values4 = $path->iterate("tense");
				$values3 = $path->iterate("person");
				$values2 = $path->iterate("number");
				$values1 = $path->iterate("voice");
				if (!$values1) $values1 = [FALSE];
			} else if ($_0 === "participle") {
				$values4 = $path->iterate("tense");
				$values2 = $path->iterate("voice");
				$values3 = [FALSE,FALSE,FALSE];
				$values1 = [""];
			} else if ($_0 === "infinitive") {
				$values4 = $path->iterate("tense");
				$values2 = $path->iterate("voice");
				$values3 = [FALSE,FALSE,FALSE];
				$values1 = [""];
			} else if ($_0 === "supine") {
				$values4 = [FALSE];
				$values2 = $path->iterate("supine-type");
				$values3 = [FALSE,FALSE,FALSE];
				$values1 = [""];
			}
			$first1=$last1=NULL; _get_first_last($values1,$first1,$last1);
			$first2=$last2=NULL; _get_first_last($values2,$first2,$last2);
			$first3=$last3=NULL; _get_first_last($values3,$first3,$last3);
			$first4=$last4=NULL; _get_first_last($values4,$first4,$last4);
			// values0 : table name
			// values1 : major vertical
			// values2 : major horizontal
			// values3 : minor horizontal
			// values4 : minor vertical
			$_1 = (count($values1) > 1 or $values1[0] !== FALSE);
			?><tr><?php
			$hspan1 = $_1 !== FALSE ? 2 : 1;
			if ($_0 === FALSE) {
				?><th colspan="<?= $hspan1 ?>">&nbsp;</th><?php
			} else {
				?><th colspan="<?= $hspan1 ?>" class="greatest"><?= $format_value($_0) ?></th><?php
			}
			if ($values2)
			foreach ($values2 as $_2) {
				?><th colspan="<?= count($values3) ?>" class="major"><?php
				echo $format_value($_2);
				?></th><?php
			}
			?></tr><?php
			if ($values3 and $values3[0] !== FALSE) {
				?><tr><th colspan="<?= $hspan1 ?>">&nbsp;</th><?php
				foreach ($values2 as $_2) {
					if ($values3)
					foreach ($values3 as $_3) {
						?><th class="minor"><?php
						echo $format_value($_3);
						?></th><?php
					}
				}
				?></tr><?php
				$hspan3 = 1;
			} elseif ($values3) {
				$hspan3 = count($values3);
				$values3 = [""];
			} else $hspan3 = 1;
			foreach ($values1 as $_1) {
				if ($_1 !== FALSE) {
					?><tr><?php
					?><th colspan="0" class="major"><?php
					echo $format_value($_1);
					?></th><?php
					?></tr><?php
				}
				$p_4 = NULL;
				foreach ($values4 as $_4) {
					?><tr><?php
					if ($_1 !== FALSE) {
						?><th>&nbsp;&nbsp;&nbsp;</th><?php
					}
					?><th class="minor"><?php
					echo $format_value($_4);
					?></th><?php
					$row = [];
					$last = NULL;
					foreach ($values2 as $_2) {
						$acc = []; $i=-1;
						foreach ($values3 as $_3) {
							$p = PATH($w, $_0,$_1,$_2,$_3,$_4);
							if ($i<0 or $p->get() != $last or !$last) {
								$acc[] = []; $last = $p->get(); $i+=1;
							}
							$acc[$i][] = [$p,2=>$_2,$_3];
						}
						if (!($optimization & 2) or (count($acc) != 1 and count($acc) != count($values3))) {
							$acc = [];
							foreach ($values3 as $_3) {
								$p = PATH($w, $_0,$_1,$_2,$_3,$_4);
								$acc[] = [[$p,2=>$_2,$_3]];
							}
						}
						$row = array_merge($row, $acc);
					}
					foreach ($row as $val_group) {
						$p = $val_group[0][0];
						$_ = count($val_group)-1;
						$_20 = $val_group[0][2];
						$_30 = $val_group[0][3];
						$_21 = $val_group[$_][2];
						$_31 = $val_group[$_][3];
						if ($_ === 0) {
							$_2 = $_20; $_3 = $_30;
							$ditto = ($p_4 and $p->get() and PATH($w, $_0,$_1,$_2,$_3,$p_4)->get() == $p->get());
						} else {
							$_2 = $_3 = NULL; $ditto = FALSE;
						}
						?><td colspan="<?= $hspan3*count($val_group) ?>" <?php
							$classes = "";
							if (!$first3 or ($_30 === $first3))
								$classes .= " leftline";
							if (!$last3 or ($_31 === $last3))
								$classes .= " rightline";
							if (!$first4 or ($_4 === $first4)) $classes .= " topline";
							if (!$last4 or ($_4 === $last4)) $classes .= " bottomline";
							if ((!$first3 or ($_30 === $first3)) and $_20 !== $first2)
								$classes .= " leftline";
							echo " class='$classes' ";
							if (count($val_group) > 1) echo " style='text-align: center;'";
						?>><?php
						if ($get_link !== NULL)
							$link = $get_link($p);
						else $link = NULL;
						if ($link) { ?><a href="<?= $link ?>"><?php }
						$val = $format_word($p->get(),$p);
						if (count($val_group) > 1) {
							$val = ""
								. "<span style='float: right;'>→</span>"
								. "<span style='float: left;'>←</span>"
								. $val;
						} elseif ($ditto and $optimization & 1) {
							$val = "&nbsp;&nbsp;&nbsp;&nbsp;&#8243;"; # ditto mark
							#echo "↓";
						}
						echo $val;
						if ($link) { ?></a><?php }
						if ($extras !== NULL) $extras($p);
						?></td><?php
					}
					?></tr><?php
					$p_4 = $_4;
				}
			}
		}
	}
	?></table></div><?php
}

function display_connections($w, $can_edit) {
	$id = $w->id();
	$w->clear_connections();
	$connections = $w->connections();
	if (!$connections and !$can_edit) return;
	?>
	Related words:
	<ul>
	<?php
		$c_id = 0;
		foreach ($connections as $c) {
			?><li>
			<a href="dictionary2.php?id=<?= $c->to()->id() ?>">
			<?= $c->to()->name() ?>
			</a> (<?= $c->type() ?>)
			<script type="text/javascript">
				$(function() {
					$('a[href="dictionary2.php?id=<?= $c->to()->id() ?>"]').on("click", function() {
						$('#enter-attrs,#enter-names').val('');$('[name=enter-spart], [name=enter-lang]').prop('checked', false);
						$('#enter-ids').val(<?= $c->to()->id() ?>);
						$('[name=enter-lang][value=<?= $c->to()->lang() ?>]').prop('checked', true);
						dict.refreshEntries();
						return false;
					});
				});
			</script>
			<?php
			if ($can_edit) {
?>
				[<a href="javascript:void(0)" id="connection<?= $id.'_'.$c_id ?>_delete">del</a>]
				<script type="text/javascript">
					$(function() {
						var w_id = <?= $w->id() ?>;
						var id = <?= $c_id ?>;
						var to = <?= $c->to()->id() ?>;
						var type = '<?= $c->type() ?>';
						$('#connection'+w_id+'_'+id+'_delete').on("click", function() {
							dict.connection_delete(w_id, to, type);
						});
					});
				</script>
<?php
			$c_id += 1;
			}
		}
	?>
	</ul>
	<?php
	if (!$can_edit) return;
	?>
	<input id="word<?= $id ?>_connection_to" type="text" placeholder="link" required>
	<input id="word<?= $id ?>_connection_type" type="text" placeholder="type" required>
	<label><input id="word<?= $id ?>_connection_ismutual" type="checkbox">Mutual</label>
	<button id="word<?= $id ?>_button_add_connect" onclick="dict.word_add_connect(<?= $id ?>)">Enter</button>
	<button id="word<?= $id ?>_button_clear_connect" onclick="$('#word<?= $id ?>_connection_to, #word<?= $id ?>_connection_type').val('')">Clear</button>
	<script type="text/javascript">
	$(function() {
		$('#word<?= $id ?>_connection_to, #word<?= $id ?>_connection_type').keypress(function(e){
			if (e.which == 13) dict.word_add_connect(<?= $id ?>);
		});
	});
	</script><br>
	<?php
}

?>
