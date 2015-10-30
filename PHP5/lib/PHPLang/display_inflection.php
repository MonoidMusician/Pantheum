<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');

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
	?><span id="toggle-quizzing<?= $w->id() ?>_outer">
		[<a href="javascript:void(0)" id="toggle-quizzing<?= $w->id() ?>">cover forms</a>]<br><br>
	</span><?php
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
			var selector = $('#word'+c+'_forms, #toggle-pronunciations'+c+'_outer, #toggle-quizzing'+c+'_outer');
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
			$('#toggle-quizzing'+c).click(function () {
				$('#word'+c+' td').addClass('hidden').on('click', function() {$(this).removeClass('hidden').off('click')});
			});
			$('.word'+c+'_pronunciation, #toggle-pronunciations').hide();
		});
	</script>
	<?php
	$w->set_cached(ob_get_contents());
	ob_end_flush();
}

function do_table($w,$values0,$values1,$values2,$values3,$values4,$format_value,$format_word,$get_link=NULL,$extras=NULL,$optimization=0) {
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
							$val = "&nbsp;&#x2044;&nbsp;&#x2044;";
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

?>
