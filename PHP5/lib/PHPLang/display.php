<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');

function _get_first_last($arr, &$first, &$last) {
	if (!$arr) return;
	if (is_vec($arr)) {
		$first = $arr[0];
		$last = $arr[count($arr)-1];
	} else {
		$first = array_keys($arr)[0];
		$last = array_keys($arr)[count($arr)-1];
	}
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

function no_format($w) {
	$w->read_attrs();
	return safe_get("template",$w->attr_storage) === "true";
}
// Format word for displaying based upon replacements
// TODO: user settings, DB encoding
function format_word($w, $lang=NULL) {
	if (!strlen($w)) return "—"; # em-dash
	if (!is_string($lang)) $lang = "la";
	if ($lang)
		return "<span class='format-word-$lang'>$w</span>";
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

function no_specials($w,$extras="1-9/; ,\\n") {
	$w = normalizer_normalize($w, Normalizer::FORM_D);
	$w = str_replace("æ", "ae", $w);
	$w = str_replace("œ", "oe", $w);
	$w = str_replace("Æ", "ae", $w);
	$w = str_replace("Œ", "oe", $w);
	$w = preg_replace("#[^A-Za-z$extras]#","", $w);
	return $w;
}

function strip_html($w) {
	return preg_replace('/<[^>]*>/', '', $w);
}

// Normalize a word to check for basic equality
function unformat_word($w) {
	$w = mb_strtolower(no_specials($w), "utf-8");
	$w = str_replace("j", "i", $w);
	$w = str_replace("u", "v", $w);
	$w = str_replace("aa", "a", $w);
	$w = str_replace("ee", "e", $w);
	$w = str_replace("ii", "i", $w);
	$w = str_replace("oo", "o", $w);
	$w = str_replace("uu", "u", $w);
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
	if ($v === "complementary-1")
		return "Comp. L";
	if ($v === "complementary-2")
		return "Comp. R";
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
	if ($tag === "clc-stage") {
		$sp = explode("-", $value);
		if (count($sp) === 1)
			return "Stage $value (CLC)";
		elseif (count($sp) === 1)
			return "Stages ".$sp[0]." and ".$sp[1]." (CLC)";
		$value = "";
		for ($i=0;$i<count($sp)-1;$i++) {
			if ($value) $value .= ", ";
			$value .= $sp[$i];
		}
		$value .= ", and ".$sp[$i];
		return "Stages $value (CLC)";
	}
	return $value !== NULL ? "$tag=$value" : "$tag";
}
function format_path($c) {
	return implode(" ", array_map("format_value", array_reverse(explode("/",$c))));
}

function word_link($w,$hide_lang=false) {
	if (!$hide_lang) display_lang($w);
	$lang = $w->lang();
	echo $w->info();
	/*?><a class="word-ref format-word-<?=$lang?>" href="dictionary.php?id=<?= $w->id() ?>"><?= $w->name() ?></a><?php*/
	?><script type="text/javascript">
		$(function() {
			$('a[href="dictionary.php?id=<?= $w->id() ?>"]').on("click", function() {
				$('#enter-attrs,#enter-names').val('');$('[name=enter-spart], [name=enter-lang]').prop('checked', false);
				$('#enter-ids').val(<?= $w->id() ?>);
				$('[name=enter-lang][value=<?= $w->lang() ?>]').prop('checked', true);
				dict.refreshEntries();
				return false;
			});
		});
	</script><?php
}

function display_lang($lang) {
	if (ISWORD($lang)) $lang = $lang->lang();
	?><sup>[<?= $lang ?>]</sup><?php
}

function modify_options($w) {
	$s = explode("\n", $w);
	if (count($s) <= 1) return $w;
	return $s[0]." (".implode(", ", array_slice($s,1)).")";
}

function display_word_info($w, $can_edit=FALSE) {
	$id = $w->id();
	$lang = $w->lang();
	$spart = $w->speechpart();
	$w->clear_connections();
	$connections = $w->connections();
	$w->read_paths();
	$w->read_attrs();
	$name = NULL;
	if ($lang === "la" and $spart === "noun") {
		if ($genders = $w->path()->iterate("gender")) {
			$name = "";
			if ($name !== NULL and in_array($g = "masculine",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else {
					$name .= ($name?", ":"") . modify_options($key->get());
					$key = PATH($w, "genitive/singular/$g");
					if (!$key->hasvalue()) $name = NULL;
					else $name .= ($name?", ":"") . modify_options($key->get());
				}
			}
			if ($name !== NULL and in_array($g = "feminine",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else {
					$name .= ($name?", ":"") . modify_options($key->get());
					$key = PATH($w, "genitive/singular/$g");
					if (!$key->hasvalue()) $name = NULL;
					else $name .= ($name?", ":"") . modify_options($key->get());
				}
			}
			if ($name !== NULL and in_array($g = "neuter",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else {
					$name .= ($name?", ":"") . modify_options($key->get());
					$key = PATH($w, "genitive/singular/$g");
					if (!$key->hasvalue()) $name = NULL;
					else $name .= ($name?", ":"") . modify_options($key->get());
				}
			}
		}
	} elseif ($lang === "la" and $spart === "verb") {
		$name = "";
		foreach (["indicative/active/present/person-1/singular",
		          "infinitive/active/present",
		          "indicative/active/perfect/person-1/singular",
		          "supine/supine-1"] as $_=>$key) {
			$key = PATH($w,$key);
			if (!$key->hasvalue()) {
				if ($_ <= 1) {$name = NULL; break;}
				else continue;
			}
			$name .= ($name?", ":"") . modify_options($key->get());
		}
	}
	if ($name === NULL) $name = $w->name();
	if (no_format($w)) {
		$name = $w->name();
	} else $name = format_word($name);
	display_lang($w);
	?><span class="word-name" id="word<?= $w->id() ?>_name"><?= $name ?></span>
	<?php
	$infos = [];
	if ($lang === "la" and $spart === "noun" and ($genders = $w->path()->iterate("gender"))) {
		$genders = array_map(function($a){return $a[0];}, $genders);
		echo implode(".", $genders).". ";
	}
	$stem = $w->path();
	/*if ($stem->hasvalue())
		$infos[] = format_word(str_replace("\n", ", ", $stem->get()));
	*/
	$infos[] = $spart;
	foreach ($w->read_attrs() as $attr) {
		$infos[] = format_attr($attr->tag(), $attr->value());
	}
	?>(<?php echo implode("; ", $infos); ?>) [<a href="dictionary.php?id=<?= $id ?>">hardlink</a>]<?php
	if (1 or $can_edit) {
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
					dict.word_rename(id, "<?= $w->name() ?>");
				});
			});
		</script>
		[<a href="javascript:void(0)" id="word<?= $w->id() ?>_refresh">refresh</a>]
		<script type="text/javascript">
			$(function() {
				var id = <?= $id ?>;
				$('#word'+id+'_refresh').on("click.refresh", function() {
					dict.word_refresh(id);
				});
			});
		</script>
		(size: <?= count($w->paths()) ?>)
<?php
	}
	$made_div = FALSE;
	$first = TRUE;
	$last_type = NULL;
	$r = "From ";
	$using = [];
	$from = [];
	foreach ($connections as $c) {
		if (!$made_div) {
			?><div class="word-more-info"><?php
			$made_div = TRUE;
		}
		if ($c->type() === "etymon")
		{echo$r;word_link($c->to());$r=", from ";}
		elseif ($c->type() === "derived using")
			$using[] = $c->to();
		elseif ($c->type() === "derived from")
			$from[] = $c->to();
	}
	if ($from) {
		if (!$made_div) {
			?><div class="word-more-info"><?php
			$made_div = TRUE;
		}
		echo "From ";
		foreach ($using as $u) {
			word_link($u,true);
			echo " + ";
		}
		$sep = "";
		foreach ($from as $u) {
			word_link($u,true);
			echo $sep;
			$sep = " and ";
		}
		?><br><?php
	}
	if ($r===", from ") { ?><br><?php }
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
		?>[<?=
		format_pron($pron->value())
		?>]<?php
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
			var lock=false;
			var splitter = /,\s*/;
			var last1 = $('#enter-names').val().split(splitter);
			var last2 = $('#enter-attrs').val().split(splitter);
			function getcheckbox(name) {
				var ret=[];
				$('input:checkbox[name="'+name+'"]:checked:visible').each(function() {
					ret.push($(this).val());
				});
				return ret.join();
			}
			$('#word'+id+'_value_attr').autocomplete({
				serviceUrl: '/PHP5/dictionary/get-attributes-json.php',
				params: {
					"lang": '<?= $lang ?>',
					"spart": '<?= $spart ?>',
				},
				delimiter: splitter,
				onSelect: function(selection) {
					if (lock) return; lock=true;
					var el = $('#word'+id+'_value_attr');
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
				transformResult: function(response) {
					response = JSON.parse(response);
					return {suggestions: response};
				},
				minChars: 0,
			});
		});
		</script>
<?php
	}
}

function display_definitions($w, $can_edit=FALSE) {
	?><ol id="word<?= $w->id() ?>_definitions"><?php
	foreach ($w->definitions() as $d) {
		?><li><?php
		$value = $d->value();
		$value = str_replace("\n", ", ", $value);
		?><sup>[<?= $d->lang() ?>]</sup><?php
		if ($d->src()) {
			?><sup>[<a href="<?= $d->src() ?>">src</a>]</sup><?php
		}
		if ((string)$d->path()) {
			if ($d->path()->get()) {
				?>(<?= format_path($d->path()) ?> “<?= $d->path()->get() ?>”) <?php
			} else {
				?>(<?= format_path($d->path()) ?>) <?php
			}
		}
		echo $value;
		/*/
		if ((string)$d->path()) {
			?>(<?= $d->path() ?>) <?php
		}
		if ($d->src()) {
			$value = "<a href='".$d->src()."'>$value</a>";
		}
		echo $value;
		/**/
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
	?></ol><?php
	echo "<br>";
}

function word_table_values($w) {
	$w->read_paths();
	$lang = $w->lang();
	$spart = $w->speechpart();
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
	if ($lang === "la" or $lang === "grc") {
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
			$moods = $w->path()->iterate("mood");
			$values0 = [];
			foreach ($moods as $_0) {
				$path = PATH($w,$_0);
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
				$values0[$_0] = [$values1,$values2,$values3,$values4];
			}
		} elseif ($spart === "adverb") {
			$values1 = $w->path()->iterate("degree");
		}
	}
	if ($lang === "fr") {
		if (($spart === "noun") or
			($spart === "adjective") or
			($spart === "pronoun")) {
			$values3 = $w->path()->iterate("gender");
			$values2 = $w->path()->iterate("number");
			if ($spart === "adjective")
				$values1 = $w->path()->iterate("degree");
			else $values1 = [];
			$values0 = [];
		} elseif ($spart === "verb") {
			$moods = $w->path()->iterate("mood");
			$values0 = [];
			foreach ($moods as $_0) {
				$path = PATH($w,$_0);
				if ($_0 === "indicative" or
					$_0 === "subjunctive") {
					$values4 = $path->iterate("tense");
					$values3 = $path->iterate("person");
					$values2 = $path->iterate("number");
					if (!$values1) $values1 = [FALSE];
				} else if ($_0 === "infinitive" or
				           $_0 === "gerund") {
					$values4 = [""];
					$values2 = $path->iterate("type");
					$values3 = [FALSE,FALSE,FALSE];
					$values1 = [""];
				} else if ($_0 === "imperative") {
					$values4 = [""];
					$values3 = $path->iterate("person");
					$values2 = $path->iterate("number");
					$values1 = [""];
				}
				$values0[$_0] = [$values1,$values2,$values3,$values4];
			}
		} elseif ($spart === "adverb") {
			$values1 = $w->path()->iterate("degree");
		}
	}
	if ($lang === "ith" && $spart === "root") {
		$values0 = $w->path()->iterate("complement");
		$values2 = $w->path()->iterate("formality");
		$values4 = $w->path()->iterate("stem");
	}
	return [$values0, $values1, $values2, $values3, $values4];
}

function display_inflection($w, $hidden=TRUE) {
	if ($c = $w->cached()) {
		if (($_ = json_decode($c,true)) === NULL) {echo$c;return;}
		else $c = $_;
		for ($i=0;$i<count($c);$i++) {
			if($i===0)echo$c[$i];
			else echo format_word($c[$i]);
		}
		return;
	} else
	ob_start();
	$pronunciations = $w->pronunciations();
	//error_log($pronunciations);
	$w->clear_connections();
	$connections = $w->connections();
	list ($values0, $values1, $values2, $values3, $values4) = word_table_values($w);
	if (!$values0 and !$values1 and !$values2
	and !$values3 and !$values4) {
		?><span id="word<?= $w->id() ?>_forms">(No inflection for this word)</span><?php
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
			$p = (string)$p;
			foreach ($connections as $connect) {
				if ($connect->type() === $p)
					return $connect->to();
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
				?>[<?=
				format_pron($pron->value())
				?>]<?php
				$first = FALSE;
			}
			if ($made_div) {
				?></span><?php
			}
		}
	);
	?>
	<script type="text/javascript">
		$(function(){
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
		});
	</script>
	<?php
	$w->set_cached(ob_get_contents());
	ob_end_flush();
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
		foreach ($values0 as $_key=>$_0) {
			if (is_array($_0)) {
				$_values = $_0;
				$values1 = $_values[0];
				$values2 = $_values[1];
				$values3 = $_values[2];
				$values4 = $_values[3];
				$_0 = $_key;
			}
			$path = PATH($w, $_0);
			if ($_0 !== $first0) {
				// Blank row to separate sub-tables based on $values0
				?><tr><th>&nbsp;</th></tr><?php
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
					?><th colspan="2" class="major"><?php
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
						if (ISWORD($link)) {
							$link = "dictionary.php?id=".$link->id();
						}
						if ($link) { ?><a class="word-ref" href="<?= $link ?>"><?php }
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
	?>Related words:
	<ul><?php
		$c_id = 0;
		foreach ($connections as $c) {
			?><li>
			<?= format_path($c->type()) ?>: <?php word_link($c->to());
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
	?></ul><?php
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
