<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');

function _do_ignore($l,$ignore) {
	if (!$ignore or !$l) return $l;
	$vec = is_vec($l);
	$ig = array_map(function($p) {
		if (ISPATH($p)) return (string)$p;
		return $p;
	}, $ignore);
	$ignore = [];
	foreach ($ig as $p) {
		if (strpos($p,"/") === FALSE)
			$ignore[] = $p;
	}
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

function _in_ignore($p,$ignore) {
	if (!$ignore) return FALSE;
	foreach ($ignore as $ig) {
		$ig = ISPATH($ig)?$ig:PATH($p, $ig);
		if ($p->issub($ig, TRUE))
			return TRUE;
	}
	return FALSE;
}

function _filter_ignore($values, $ignore, $p, $empty=TRUE, $prev=NULL) {
	$ret = [];
	if ($values !== NULL) {
		if ($prev) {
			$count = [];
			$ret["_"] = [];
			foreach ($prev[0] as $k) {
				$ret[$k] = _filter_ignore($values, $ignore, PATH($p,$p,$k), $empty, array_slice($prev, 1));
				// TODO: only works when count($prev) === 1
				foreach ($ret[$k] as $v) {
					$count[] = $v;
				}
			}
			foreach ($values as $v) {
				foreach ($count as $v2) {
					if ($v === $v2) {
						$ret["_"][] = $v;
						break;
					}
				}
			}
		} else {
			foreach ($values as $v) {
				if (!$v or !_in_ignore(PATH($p,$p,$v),$ignore))
					$ret[] = $v;
			}
		}
	}
	if ($empty or $ret)
		return $ret;
	return [FALSE];
}
function _filter_ignore2(&$values, $ignore, $p, $parallel, $prev=NULL) {
	foreach ($values as $key=>&$valuesz)
		$valuesz = _filter_ignore($valuesz, $ignore, PATH($p,$p,$key), TRUE, $prev?[$prev[$key]]:NULL);
}
function _fill($values, $parallel) {
	$ret = [];
	foreach ($parallel as $k) {
		$ret[$k] = $values;
	}
	return $ret;
}
function is_fillable($v) {
	if (!is_vec($v)) return FALSE;
	foreach ($v as $k) {
		if (is_array($k)) return FALSE;
	}
	return TRUE;
}

// Parse the depath into the necessary row/column values for the table
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
	// values2 : minor vertical
	// values3 : major horizontal
	// values4 : minor horizontal
	if ($lang === "la" or $lang === "grc") {





		if (($spart === "noun") or
			($spart === "adjective") or
			($spart === "pronoun")) {
			if ($spart === "adjective")
				$values1 = $w->path()->iterate("degree");
			else $values1 = [];
			$values2 = $w->path()->iterate("case");
			$values3 = $w->path()->iterate("number");
			$values4 = $w->path()->iterate("gender");
			$values0 = [];
		} elseif ($spart === "verb") {
			$values0 = $moods = $w->path()->iterate("mood");
			$values1 = [];
			$values2 = [];
			$values3 = [];
			$values4 = [];
			$hspan4 = [];
			$persons = PATH($w,"indicative")->iterate("person");
			$persons = _filter_ignore($persons, $ignore, PATH($w,"indicative"));
			foreach ($persons as $_) $hspan4[] = FALSE;

			foreach ($moods as $_0) {
				if ($ignore !== NULL and in_array($_0,$ignore))
					continue;
				$vals4 =
				$vals3 =
				$vals2 =
				$vals1 = NULL;
				$path = PATH($w,$_0);
				if ($_0 === "indicative" or
					$_0 === "subjunctive" or
					$_0 === "imperative") {
					$vals1 = $path->iterate("voice");
					$vals2 = $path->iterate("tense");
					$vals3 = $path->iterate("number");
					$vals4 = PATH($w,"indicative")->iterate("person");
					if (!$values1) $values1 = [FALSE];
				} else if ($_0 === "participle") {
					$vals1 = [""];
					$vals2 = $path->iterate("tense");
					$vals3 = $path->iterate("voice");
					$vals4 = $hspan4;
				} else if ($_0 === "infinitive") {
					$vals1 = [""];
					$vals2 = $path->iterate("tense");
					$vals3 = $path->iterate("voice");
					$vals4 = $hspan4;
				} else if ($_0 === "supine") {
					$vals1 = [""];
					$vals2 = [FALSE];
					$vals3 = $path->iterate("supine-type");
					$vals4 = $hspan4;
				}
				$values1[$_0] = $vals1;
				$values2[$_0] = $vals2;
				$values3[$_0] = $vals3;
				$values4[$_0] = $vals4;
			}
		} elseif ($spart === "adverb") {
			$values1 = $w->path()->iterate("degree");
		}






	} else
	if ($lang === "fr") {
		if (($spart === "noun") or
			($spart === "adjective") or
			($spart === "pronoun")) {
			if ($spart === "adjective")
				$values1 = $w->path()->iterate("degree");
			else $values1 = [];
			$values3 = $w->path()->iterate("number");
			$values4 = $w->path()->iterate("gender");
			$values0 = [];
		} elseif ($spart === "verb") {
			$values0 = $moods = $w->path()->iterate("mood");
			$values1 = [];
			$values2 = [];
			$values3 = [];
			$values4 = [];
			foreach ($moods as $_0) {
				if ($ignore !== NULL and in_array($_0,$ignore))
					continue;
				$vals4 =
				$vals3 =
				$vals2 =
				$vals1 = NULL;
				$path = PATH($w,$_0);
				if ($_0 === "indicative" or
					$_0 === "subjunctive") {
					if (!$vals1) $vals1 = [FALSE];
					$vals2 = $path->iterate("tense");
					$vals3 = $path->iterate("number");
					$vals4 = $path->iterate("person");
				} else if ($_0 === "infinitive" or
				           $_0 === "gerund") {
					$vals1 = [""];
					$vals2 = [""];
					$vals3 = $path->iterate("type");
					$vals4 = [FALSE,FALSE,FALSE];
				} else if ($_0 === "imperative") {
					$vals1 = [""];
					$vals2 = [""];
					$vals3 = $path->iterate("number");
					$vals4 = $path->iterate("person");
				} else if ($_0 === "participle") {
					$vals1 = [""];
					$vals2 = $path->iterate("gender");
					$vals3 = $path->iterate("tense");
					$vals4 = $path->iterate("number");
				}/**/
				$values1[$_0] = $vals1;
				$values2[$_0] = $vals2;
				$values3[$_0] = $vals3;
				$values4[$_0] = $vals4;
			}
		} elseif ($spart === "adverb") {
			$values1 = $w->path()->iterate("degree");
		}
	} else
	if ($lang === "es") {
		if (($spart === "noun") or
			($spart === "adjective") or
			($spart === "pronoun")) {
			if ($spart === "adjective")
				$values1 = $w->path()->iterate("degree");
			else $values1 = [];
			$values3 = $w->path()->iterate("number");
			$values4 = $w->path()->iterate("gender");
			$values0 = [];
		} elseif ($spart === "verb") {
			$moods = $w->path()->iterate("mood");
			$values0 = [];
			foreach ($moods as $_0) {
				if ($ignore !== NULL and in_array($_0,$ignore))
					continue;
				$path = PATH($w,$_0);
				if ($_0 === "indicative" or
					$_0 === "subjunctive") {
					if (!$values1) $values1 = [FALSE];
					$values2 = $path->iterate("tense");
					$values3 = $path->iterate("number");
					$values4 = $path->iterate("person");
				} else if ($_0 === "infinitive" or
				           $_0 === "gerund") {
					$values1 = [""];
					$values2 = [""];
					$values3 = [""];
					$values4 = [FALSE,FALSE,FALSE];
				} else if ($_0 === "imperative") {
					$values1 = [""];
					$values2 = $path->iterate("imperative-mood");
					$values3 = $path->iterate("number");
					$values4 = $path->iterate("person");
				} else if ($_0 === "past-participle") {
					$values1 = [""];
					$values2 = $path->iterate("gender");
					$values3 = $path->iterate("number");
					$values4 = [false,false,false];
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
			$values0 = [];
			$values1 = [];
			$values2 = $w->path()->iterate("case");
			$values3 = $w->path()->iterate("number");
			$values4 = [];
		} elseif ($spart === "verb") {
			$moods = $w->path()->iterate("mood");
			$values0 = [];
			$hacked = NULL;
			foreach ($moods as $_0) {
				if ($ignore !== NULL and in_array($_0,$ignore))
					continue;
				$path = PATH($w,$_0);
				$name = NULL;
				if ($_0 === "indicative") {
					$values1 = [""];
					$values2 = [""];
					$values3 = $path->iterate("tense");
					$values4 = [FALSE,FALSE];
				} else if ($_0 === "infinitive" or
				           $_0 === "conditional" or
				           $_0 === "imperative") {
					if ($hacked) {
						$values0[$hacked][1][] = $_0;
						continue;
					}
					$hacked = $_0;
					$name = "";
					$values1 = [""];
					$values2 = [""];
					$values3 = [$_0];
					$values4 = [FALSE,FALSE];
				} else if ($_0 === "adjectival-participle") {
					$values1 = $path->iterate("voice");
					$values2 = $path->iterate("case");
					$values3 = $path->iterate("tense");
					$values4 = $path->iterate("number");
				} else if ($_0 === "nominal-participle") {
					$values1 = $path->iterate("voice");
					$values2 = $path->iterate("case");
					$values3 = $path->iterate("tense");
					$values4 = $path->iterate("number");
				} else if ($_0 === "adverbial-participle") {
					$values1 = $path->iterate("voice");
					$values2 = [""];
					$values3 = $path->iterate("tense");
					$values4 = [FALSE,FALSE];
				}
				$values0[$_0] = [$values1,$values2,$values3,$values4];
				if ($name !== NULL)
					$values0[$_0][] = $name;
			}
		}
	} else
	if ($lang === "ith" && $spart === "root") {
		$values0 = $w->path()->iterate("complement");
		$values3 = $w->path()->iterate("formality");
		$values2 = $w->path()->iterate("stem");
	}
	// values0 : table name
	// values1 : major vertical
	// values2 : minor vertical
	// values3 : major horizontal
	// values4 : minor horizontal
	// #1,2,3,4 may depend on #0 (already done)
	// #2       may depend on #1
	// #4       may depend on #3
	$values0 = _do_ignore($values0,$ignore);
	if (is_vec($values1)) $values1 = _fill($values1, $values0);
	if (is_vec($values2)) $values2 = _fill($values2, $values0);
	if (is_vec($values3)) $values3 = _fill($values3, $values0);
	if (is_vec($values4)) $values4 = _fill($values4, $values0);
	_filter_ignore2($values1,$ignore,PATH($w),$values0);
	_filter_ignore2($values2,$ignore,PATH($w),$values0,$values1);
	_filter_ignore2($values3,$ignore,PATH($w),$values0);
	_filter_ignore2($values4,$ignore,PATH($w),$values0,$values3);
	/*var_dump($values0);
	var_dump($values1);
	var_dump($values2);
	var_dump($values3);
	var_dump($values4);*/
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
	if (!$values0 and !$values1 and !$values3
	and !$values4 and !$values2) {
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
		$w,$values0,$values1,$values2,$values3,$values4,NULL,
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

function do_table($w,$values0,$values1,$values2,$values3,$values4,$ignore,$format_value,$format_word,$get_link=NULL,$extras=NULL,$optimization=0) {
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
		?><table class="text-left inflection" id="word<?= $w->id() ?>_forms"><?php
		$first0=$last0=NULL; _get_first_last($values0,$first0,$last0);
		foreach ($values0 as $_key=>$_0) {
			$name0 = $_0; // FIXME
			if (!$values1[$_0]) $values1[$_0] = [FALSE];
			if (!$values2[$_0]) $values2[$_0] = [FALSE];
			if (!$values3[$_0]) $values3[$_0] = [FALSE];
			if (!$values4[$_0]) $values4[$_0] = [FALSE];
			$path = PATH($w, $_0);
			if ($_0 !== $first0) {
				// Blank row to separate sub-tables based on $values0
				?><tr><th>&nbsp;</th></tr><?php
			}
			// values0 : table name
			// values1 : major vertical
			// values2 : minor vertical
			// values3 : major horizontal
			// values4 : minor horizontal
			$_1 = (count($values1[$_0]) > 1 or $values1[$_0][0] !== FALSE);
			?><tr><?php
			$hspan1 = $_1 !== FALSE ? 2 : 1;
			if ($name0 === FALSE) {
				?><th colspan="<?= $hspan1 ?>">&nbsp;</th><?php
			} else {
				?><th colspan="<?= $hspan1 ?>" class="greatest"><?= $format_value($name0) ?></th><?php
			}
			if ($values3[$_0])
			foreach ($values3[$_0] as $_3) {
				?><th colspan="<?= count($values4[$_0][$_3]) ?>" class="major"><?php
				echo $format_value($_3);
				?></th><?php
			}
			?></tr><?php
			if ($values4[$_0]["_"] and $values4[$_0]["_"][0] !== FALSE) {
				?><tr><th colspan="<?= $hspan1 ?>">&nbsp;</th><?php
				foreach ($values3[$_0] as $_3) {
					if ($values4[$_0][$_3])
					foreach ($values4[$_0][$_3] as $_4) {
						?><th class="minor"><?php
						echo $format_value($_4);
						?></th><?php
					}
				}
				?></tr><?php
				$hspan4 = 1;
			} elseif ($values4[$_0]["_"]) {
				$hspan4 = count($values4[$_0]["_"]);
				foreach ($values4[$_0] as &$v)
					$v = [""];
			} else $hspan4 = 1;
			foreach ($values1[$_0] as $_1) {
				if ($_1 !== FALSE) {
					?><tr><?php
					?><th colspan="2" class="major"><?php
					echo $format_value($_1);
					?></th><?php
					?></tr><?php
				}
				// Previous row (directly above)
				$p_2 = NULL;
				foreach ($values2[$_0][$_1] as $_2) {
					?><tr><?php
					if ($_1 !== FALSE) {
						?><th>&nbsp;&nbsp;&nbsp;</th><?php
					}
					?><th class="minor"><?php
					echo $format_value($_2);
					?></th><?php
					$row = [];
					$last = NULL;
					foreach ($values3[$_0] as $_3) {
						$acc = []; $i=-1;
						foreach ($values4[$_0][$_3] as $_4) {
							$p = PATH($w, $_0,$_1,$_3,$_4,$_2);
							if ($i<0 or $p->get() != $last or !$last) {
								$acc[] = []; $last = $p->get(); $i+=1;
							}
							$acc[$i][] = [$p,2=>$_3,$_4];
						}
						if (!($optimization & 2) or (count($acc) != 1 and count($acc) != count($values4))) {
							$acc = [];
							foreach ($values4[$_0][$_3] as $_4) {
								$p = PATH($w, $_0,$_1,$_3,$_4,$_2);
								$acc[] = [[$p,2=>$_3,$_4]];
							}
						}
						$row = array_merge($row, $acc);
					}
					$first1=$last1=NULL; _get_first_last($values1[$_0],     $first1,$last1);
					$first2=$last2=NULL; _get_first_last($values2[$_0][$_1],$first2,$last2);
					$first3=$last3=NULL; _get_first_last($values3[$_0],     $first3,$last3);
					$first4=$last4=NULL; _get_first_last($values4[$_0][$_3],$first4,$last4);
					foreach ($row as $val_group) {
						$p = $val_group[0][0];
						$_ = count($val_group)-1;
						$_30 = $val_group[0][2];
						$_40 = $val_group[0][3];
						$_31 = $val_group[$_][2];
						$_41 = $val_group[$_][3];
						if ($_ === 0) {
							$_3 = $_30; $_4 = $_40;
							$ditto = ($p_2 and $p->get() and PATH($w, $_0,$_1,$_3,$_4,$p_2)->get() == $p->get());
						} else {
							$_3 = $_4 = NULL; $ditto = FALSE;
						}
						?><td colspan="<?= $hspan4*count($val_group) ?>" <?php
							$classes = "";
							if (!$first4 or ($_40 === $first4))
								$classes .= " leftline";
							if (!$last4 or ($_41 === $last4))
								$classes .= " rightline";
							if (!$first2 or ($_2 === $first2)) $classes .= " topline";
							if (!$last2 or ($_2 === $last2)) $classes .= " bottomline";
							if ((!$first4 or ($_40 === $first4)) and $_30 !== $first3)
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
						if (!_in_ignore($p, $ignore) or !$p->hasvalue())
							$val = $format_word($p->get(),$p);
						else $val = '<abbr class="symbolic" title="You\'ve not learned this yet">—</abbr>';
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
					$p_2 = $_2;
				}
			}
		}
	}
	?></table></div><?php
}

?>
