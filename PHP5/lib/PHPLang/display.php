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

function format_abbr($abbr, $desc) {
	return "<abbr title='$desc'>$abbr</abbr>";
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
	echo "<br>";
	display_inflection($w, $inflection_hidden);
}

function no_format($w) {
	$w->read_attrs();
	return safe_get("template",$w->attr_storage) === "true";
}
// Format word for displaying based upon replacements
// TODO: user settings, DB encoding
function format_word($w, $lang=NULL, $all=false) {
	if (!strlen($w)) return "—"; # em-dash
	if (!is_string($lang)) $lang = "la";
	if (!$all)
		$w = explode("\n", $w)[0];
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
function slugify($w) {
	$w = mb_strtolower(no_specials($w), "utf-8");
	$w = str_replace("j", "i", $w);
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
	if ($v === "adjectival-participle")
		return format_abbr("Adj. Participle","Adjectival Participle");
	if ($v === "nominal-participle")
		return format_abbr("Nom. Participle","Nominal Participle");
	if ($v === "adverbial-participle")
		return format_abbr("Adv. Participle","Adverbial Participle");
	return str_replace("-", " ", ucfirst($v));
}
function format_spart($spart) {
	if ($spart === "adverb")       return format_abbr("Adv.",   "Adverb");
	if ($spart === "verb")         return format_abbr("V.",     "Verb");
	if ($spart === "noun")         return format_abbr("N.",     "Noun");
	if ($spart === "adjective")    return format_abbr("Adj.",   "Adjective");
	if ($spart === "pronoun")      return format_abbr("Pro.",   "Pronoun");
	if ($spart === "preposition")  return format_abbr("Prep.",  "Preposition");
	if ($spart === "conjunction")  return format_abbr("Conj.",  "Conjunction");
	if ($spart === "interjection") return format_abbr("Interj.","Interjection");
	return ucfirst($spart);
}
function format_attr($tag,$value=NULL) {
	if ($tag === "transitive")
		if ($value === "true") return format_abbr("TR","transitive");
		elseif ($value === "false") return format_abbr("NTR","intransitive");
	if ($tag === "irregular")
		if ($value === "true") return "irregular";
		elseif ($value === "false") return "regular";
	if ($tag === "person")
		if ($value === "person-1") return "1st person";
		elseif ($value === "person-2") return "2nd person";
		elseif ($value === "person-3") return "3rd person";
	if ($tag === "case")
		if ($value === "ablative") return "+ABL";
		elseif ($value === "accusative") return "+ACC";
		elseif ($value === "dative") return "+DAT";
		elseif ($value === "dative-personal") return "+DAT (of persons)";
	if ($tag === "declension")
		if ($value === "decl-1") return "1st Declension";
		elseif ($value === "decl-2") return "2nd Declension";
		elseif ($value === "decl-3") return "3rd Declension";
		elseif ($value === "decl-4") return "4th Declension";
		elseif ($value === "decl-5") return "5th Declension";
		elseif ($value === "decl-3-i") return "3rd Declension i-stem";
		elseif ($value === "decl-2-neuter") return "2nd Declension Neuter";
		elseif ($value === "decl-3-neuter") return "3rd Declension Neuter";
		elseif ($value === "decl-3-i-neuter") return "3rd Declension Neuter i-stem";
		elseif ($value === "decl-4-neuter") return "4th Declension Neuter";
		elseif ($value === "adjective-12") return "1st/2nd Declension";
		elseif ($value === "adjective-3-3") return "3rd Declension";
	if ($tag === "conjugation")
		if ($value === "conj-1") return "1st Conjugation";
		elseif ($value === "conj-2") return "2nd Conjugation";
		elseif ($value === "conj-3") return "3rd Conjugation";
		elseif ($value === "conj-3-io") return "3rd Conjugation i-stem";
		elseif ($value === "conj-4") return "4th Conjugation";
		elseif ($value === "conj-1-deponent") return "1st Conjugation Deponent";
		elseif ($value === "conj-2-deponent") return "2nd Conjugation Deponent";
		elseif ($value === "conj-3-deponent") return "3rd Conjugation Deponent";
		elseif ($value === "conj-3-io-deponent") return "3rd Conjugation Deponent i-stem";
		elseif ($value === "conj-4-deponent") return "4th Conjugation Deponent";
	if ($tag === "clc-stage") {
		$sp = explode("-", $value);
		if (count($sp) === 1)
			return "Stage $value (CLC)";
		elseif (count($sp) === 2)
			return "Stages ".$sp[0]." and ".$sp[1]." (CLC)";
		$value = "";
		for ($i=0;$i<count($sp)-1;$i++) {
			if ($value) $value .= ", ";
			$value .= $sp[$i];
		}
		$value .= ", and ".$sp[$i];
		return "Stages $value (CLC)";
	}
	$abbrs = [
		"copulative"=>"COP",
	];
	if ($value === "true") {
		if (array_key_exists($tag, $abbrs))
			return format_abbr($abbrs[$tag], $tag);
	}
	return ($value !== NULL and $value !== "true") ? "$tag=$value" : "$tag";
}
function format_path($c) {
	return implode(" ", array_map("format_value", array_reverse(explode("/",$c))));
}
function format_lang($lang) {
	global $sql_stmts;
	if (ISWORD($lang)) $lang = $lang->lang();
	$name = $lang;
	sql_getone($sql_stmts["lang_id->lang_dispname"], $name, ["s", $lang]);
	return $name;
}

function select2_getlangs() {
	$langs = [];
	$db = defaultDB();
	foreach ($db->langs() as $l) {
		$langs[] = ["id"=>$l,"text"=>format_lang($l)];
	}
	return $langs;
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

function word_link2($w, $text=NULL, $no_format=false) {
	if ($text === NULL) $text = $w->name();
	if (!$no_format) $text = format_word($text,$w->lang(), true);
	return '<a href="dictionary.php?lang='.$w->lang().'&spart='.$w->speechpart().'&name='.$w->name().'">'.$text.'</a>';
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

function display_word_name($w) {
	$lang = $w->lang();
	$spart = $w->speechpart();
	$name = NULL;
	if ($lang === "la" and $spart === "noun") {
		if ($genders = $w->path()->iterate("gender")) {
			$name = [];
			if ($name !== NULL and in_array($g = "masculine",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else {
					$name[] = ($key->get());
					$key = PATH($w, "genitive/singular/$g");
					if (!$key->hasvalue()) $name = NULL;
					else $name[] = ($key->get());
				}
			}
			if ($name !== NULL and in_array($g = "feminine",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else {
					$name[] = ($key->get());
					$key = PATH($w, "genitive/singular/$g");
					if (!$key->hasvalue()) $name = NULL;
					else $name[] = ($key->get());
				}
			}
			if ($name !== NULL and in_array($g = "neuter",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else {
					$name[] = ($key->get());
					$key = PATH($w, "genitive/singular/$g");
					if (!$key->hasvalue()) $name = NULL;
					else $name[] = ($key->get());
				}
			}
		}
	} elseif ($lang === "la" and $spart === "pronoun") {
		if ($genders = $w->path()->iterate("gender")) {
			$name = [];
			if ($name !== NULL and in_array($g = "masculine",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else $name[] = $key->get();
			}
			if ($name !== NULL and in_array($g = "feminine",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else $name[] = $key->get();
			}
			if ($name !== NULL and in_array($g = "neuter",$genders)) {
				$key = PATH($w, "nominative/singular/$g");
				if (!$key->hasvalue()) $name = NULL;
				else $name[] = $key->get();
			}
			if ((count($name) == 2 and $name[0] === $name[1])
			 or (count($name) == 3 and $name[0] === $name[1] and $name[1] === $name[2])) {
				$name = [$name[0]];
				$key = PATH($w, "nominative/plural");
				if ($key->hasvalue())
					$name[] = $key->get();
			}
		}
	} elseif ($lang === "la" and $spart === "verb") {
		$name = [];
		foreach (["indicative/active/present/person-1/singular",
		          "infinitive/active/present",
		          "indicative/active/perfect/person-1/singular",
		          "supine/supine-1"] as $_=>$key) {
			$key = PATH($w,$key);
			if (!$key->hasvalue()) {
				if ($_ <= 1) {$name = NULL; break;}
				else continue;
			}
			$name[] = $key->get();
		}
	}
	if ($name === NULL) $name = $w->name();
	else $name = implode(", ", array_map("modify_options",$name));
	if (no_format($w)) {
		$name = $w->name();
	} else $name = format_word($name,$w->lang());
	return $name;
}

function display_word_info($w, $can_edit=FALSE) {
	$id = $w->id();
	$lang = $w->lang();
	$spart = $w->speechpart();
	$w->clear_connections();
	$connections = $w->connections();
	$w->read_paths();
	$w->read_attrs();
	display_lang($w);
	?><span class="word-name" id="word<?= $w->id() ?>_name"><?= display_word_name($w) ?></span>
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
	?>(<?php echo implode("; ", $infos); ?>)<?php
	if ($can_edit !== NULL and ($can_edit)) {
		$slug = slugify($w->name());
		$class = "word${id}_toolbox";
?>
		[<a href="javascript:void(0)" id="word<?= $w->id() ?>_tools">tools</a>]
		<script type="text/javascript">
			$(function() {
				var id = <?= $id ?>;
				$('#word'+id+'_tools').on("click.tools", function() {
					$('.<?= $class ?>').toggle();
				});
			});
		</script>
		<span class="<?= $class ?>" style="display: none;">
		[<a href="dictionary.php?id=<?= $id ?>">hardlink</a>]
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
		[<a href="javascript:void(0)" id="word<?= $w->id() ?>_change_POS">change POS</a>]
		<script type="text/javascript">
			$(function() {
				var id = <?= $id ?>;
				$('#word'+id+'_change_POS').on("click.change_POS", function() {
					var pos = prompt('What part of speech?', '<?= $spart ?>');
					if (pos) dict.word_change_POS(id, pos);
				});
			});
		</script>
		</span>
		(size: <?= count($w->paths()) ?>)
		<div style="display: none;" class="<?= $class ?>">
			&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="http://en.wiktionary.org/wiki/<?= $slug ?>#Latin" target="_blank">Wiktionary</a>,
			<a href="http://www.perseus.tufts.edu/hopper/text?doc=Perseus:text:1999.04.0059:entry=<?= $slug ?>" target="_blank">Lewis & Short</a>
			<br>&nbsp;&nbsp;&nbsp;&nbsp;
			Pronunciation: <input id="word<?= $id ?>_pronunciation_tool"> <span></span>
		</div>
		<script>
			$(function() {
				var transform = la_ipa.transforms["IPA transcription"];
				$('#word<?= $id ?>_pronunciation_tool').on('keyup', function() {
					var $this=$(this);
					$this.next().text(transform($this.val()));
				});
			});
		</script>
<?php
	}
	$made_div = FALSE;
	$first = TRUE;
	$last_type = NULL;
	$using = [];
	$from = [];
	$sep = "";
	foreach ($connections as $c) {
		if (!$made_div) {
			?><div class="word-more-info"><?php
			$made_div = TRUE;
		}
		if ($c->type() === "prefix")
			$using[] = $c->to();
		elseif ($c->type() === "etymon")
			$from[] = $c->to();
	}
	if ($from) {
		if (!$made_div) {
			?><div class="word-more-info"><?php
			$made_div = TRUE;
		}
		echo "From ";
		foreach ($using as $u) {
			word_link($u,$u->lang() === $w->lang());
			echo " + ";
		}
		$sep = "";
		foreach ($from as $u) {
			word_link($u,$u->lang() === $w->lang());
			echo $sep;
			$sep = ", from ";
		}
		?><br><?php
	}
	if ($sep===", from ") { ?><br><?php }
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
			$('#word'+id+'_value_attr').autocomplete(autocompletions['word-attributes'](id,'<?= $lang ?>','<?= $spart ?>'));
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
		if ($d->type())
			echo "{:".$d->type().":} ";
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
}

function _do_ignore($l,$ignore) {
	if (!$ignore or !$l) return $l;
	$vec = is_vec($l);
	foreach ($l as $k => $v) {
		if (is_array($v)) {
			$v = $l[$k] = _do_ignore($v,$ignore);
			if (!$v or in_array($k, $ignore, true))
				unset($l[$k]);
		} elseif (in_array($v, $ignore))
			unset($l[$k]);
	}
	if ($vec) $l = array_values($l);
	return $l;
}

function word_table_values($w,$ignore=NULL) {
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
	} else
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
				} else if ($_0 === "participle") {
					$values4 = $path->iterate("gender");
					$values3 = $path->iterate("number");
					$values2 = $path->iterate("tense");
					$values1 = [""];
				}/**/
				$values0[$_0] = [$values1,$values2,$values3,$values4];
			}
		} elseif ($spart === "adverb") {
			$values1 = $w->path()->iterate("degree");
		}
	} else
	if ($lang === "es") {
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
					$values2 = [""];
					$values3 = [FALSE,FALSE,FALSE];
					$values1 = [""];
				} else if ($_0 === "imperative") {
					$values4 = $path->iterate("imperative-mood");
					$values3 = $path->iterate("person");
					$values2 = $path->iterate("number");
					$values1 = [""];
				} else if ($_0 === "past-participle") {
					$values4 = $path->iterate("gender");
					$values3 = [false,false,false];
					$values2 = $path->iterate("number");
					$values1 = [""];
				}
				$values0[$_0] = [$values1,$values2,$values3,$values4];
			}
		} elseif ($spart === "adverb") {
			$values1 = $w->path()->iterate("degree");
		}
	} else
	if ($lang === "eo") {
		if (($spart === "noun") or
			($spart === "adjective") or
			($spart === "pronoun")) {
			$values4 = $w->path()->iterate("case");
			$values3 = [];
			$values2 = $w->path()->iterate("number");
			$values1 = [];
			$values0 = [];
		} elseif ($spart === "verb") {
			$moods = $w->path()->iterate("mood");
			$values0 = [];
			$hacked = NULL;
			foreach ($moods as $_0) {
				$path = PATH($w,$_0);
				$name = NULL;
				if ($_0 === "indicative") {
					$values4 = [""];
					$values3 = [FALSE,FALSE];
					$values2 = $path->iterate("tense");
					$values1 = [""];
				} else if ($_0 === "infinitive" or
				           $_0 === "conditional" or
				           $_0 === "imperative") {
					if ($hacked) {
						$values0[$hacked][1][] = $_0;
						continue;
					}
					$hacked = $_0;
					$name = "";
					$values4 = [""];
					$values3 = [FALSE,FALSE];
					$values2 = [$_0];
					$values1 = [""];
				} else if ($_0 === "adjectival-participle") {
					$values4 = $path->iterate("case");
					$values3 = $path->iterate("number");
					$values2 = $path->iterate("tense");
					$values1 = $path->iterate("voice");
				} else if ($_0 === "nominal-participle") {
					$values4 = $path->iterate("case");
					$values3 = $path->iterate("number");
					$values2 = $path->iterate("tense");
					$values1 = $path->iterate("voice");
				} else if ($_0 === "adverbial-participle") {
					$values4 = [""];
					$values3 = [FALSE,FALSE];
					$values2 = $path->iterate("tense");
					$values1 = $path->iterate("voice");
				}
				$values0[$_0] = [$values1,$values2,$values3,$values4];
				if ($name !== NULL)
					$values0[$_0][] = $name;
			}
		}
	} else
	if ($lang === "ith" && $spart === "root") {
		$values0 = $w->path()->iterate("complement");
		$values2 = $w->path()->iterate("formality");
		$values4 = $w->path()->iterate("stem");
	}
	$values0 = _do_ignore($values0,$ignore);
	$values1 = _do_ignore($values1,$ignore);
	$values2 = _do_ignore($values2,$ignore);
	$values3 = _do_ignore($values3,$ignore);
	$values4 = _do_ignore($values4,$ignore);
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
		function($v) use($w) {
			return format_word($v,$w->lang(),true);
		},
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
			echo format_word(PATH($w,$_1)->get(),$w->lang(),true);
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
				if (array_key_exists(4, $_values))
					$name0 = $_values[4];
				else $name0 = $_0;
			} else $name0 = $_0;
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
			if ($name0 === FALSE) {
				?><th colspan="<?= $hspan1 ?>">&nbsp;</th><?php
			} else {
				?><th colspan="<?= $hspan1 ?>" class="greatest"><?= $format_value($name0) ?></th><?php
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
			<?= format_path($c->type()) ?>: <?php word_link($c->to(),$c->to()->lang() === $w->lang());
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
		//alert(4);
		
		$('#word<?= $id ?>_connection_to, #word<?= $id ?>_connection_type').keypress(function(e){
			if (e.which == 13) dict.word_add_connect(<?= $id ?>);
		});
		//alert(45);
		$('#word<?= $id ?>_connection_to').autocomplete({
			//lookup: names,
			serviceUrl: '/PHP5/dictionary/get-info-json.php',
			params: {
				"lang":"la",
			},
			paramName: "name",
			deferRequestBy: 150,
			noCache: true,
			transformResult: function(response) {
				response = JSON.parse(response);
				return {suggestions: response};
			},
			minChars: 1,
		});
		//alert(5);
	});
	</script><br>
	<?php
}

?>
