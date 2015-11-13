<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/definition.php');
sro('/PHP5/lib/PHPLang/pronunciation.php');
sro('/PHP5/lib/PHPLang/connection.php');
sro('/PHP5/lib/PHPLang/path.php');
sro('/PHP5/lib/InflectN.php');
sro('/PHP5/lib/InflectV.php');

function make_expr($list) {
	if (!$list) return NULL;
	if (!is_array($list)) {
		if (is_string($list)) {
			$list = str_replace("(","[",$list);
			$list = str_replace(")","]",$list);
		}
		return $list;
	}
	if (count($list) === 1) return $list[0];
	return "(".implode("|",array_map("make_expr",$list)).")";
}

function _make_expr($str) {
	if (is_string($str) and $str[0] != "(")
		$str = explode("|", $str);
	return make_expr($str);
}

function split_definitions($defs) {
	if (is_array($defs)) return flatten(array_map("split_definitions", $defs));
	return preg_split("/[,;\n]/", $defs);
}
function cull_definitions($defs) {
	return array_map(function($d) {
		return $d->value();
	}, array_filter($defs, function($d) {
		return !(string)$d->path();
	}));
}

function la_en($path, $only_one=false) {
	global $OP_APOS;

	$o = $only_one;

	$word = $path->word();
	$spart = $word->speechpart();
	$definitions = $word->definitions();

	$d0 = []; // present
	$d1 = []; // preterite
	$d2 = []; // past participle
	$d3 = []; // 3s present
	$d4 = []; // present participle
	$d5 = []; // 2s present
	$be = [];

	foreach (split_definitions(cull_definitions($definitions,$path)) as $def) {
		$matches = [];
		if (preg_match("/^be\s+/", $def)) {
			$be[] = preg_replace("", "", $def);
			continue;
		}
		if (preg_match("/^([a-zA-Z-]+)((?:[^a-zA-Z-].*)?)$/", $def, $matches)) {
			$a = $matches[1];
			$b = $matches[2];
			$d0[] = $def;
			$d1[] = _make_expr(InflectV::preterite($a,$o)).$b;
			$d2[] = _make_expr(InflectV::pastparticiple($a,$o)).$b;
			$d3[] = _make_expr(InflectV::thirdsingular($a,$o)).$b;
			$d4[] = _make_expr(InflectV::presentparticiple($a,$o)).$b;
			$d5[] = _make_expr(InflectV::secondsingular($a,$o)).$b;
		}
	}
	if ($o) {
		$d0 = $d0[0];
		$d1 = $d1[0];
		$d2 = $d2[0];
		$d3 = $d3[0];
		$d4 = $d4[0];
	}
	$d0 = make_expr($d0);
	$d1 = make_expr($d1);
	$d2 = make_expr($d2);
	$d3 = make_expr($d3);
	$d4 = make_expr($d4);
	$d5 = make_expr($d5);
	$be = make_expr($be);
	error_log($d0.','.$d1.','.$d2.','.$d3.','.$d4);

	$d = $d0;
	$D = $d4;

	if ($spart === "verb") {
		$mood = $path->key_value("mood");
		$tense = $path->key_value("tense");
		$voice = $path->key_value("voice");
		$person = $path->key_value("person");
		$number = $path->key_value("number");
		$psv = ($voice === "passive");
		$_p = $person[strlen($person)-1];
		$pl = $number === "singular" ? 0 : 1;

		$t = $v = $p = $b = $m = NULL;

		if ($mood === "infinitive") {
			if ($tense === "future") $t = "be about to";
			elseif ($tense === "perfect") {
				$t = "have";
				$d = $d1;
			}
			if ($voice === "passive") {
				if ($tense === "perfect") $v = "been";
				else $v = "be";
				$d = $d2;
			}
			return "to $t $v $d";
		} elseif ($mood === "participle") {
			if ($tense === "future") {
				$t = "about to";
				$d4 = $d;
			}
			elseif ($tense === "perfect") {
				$t = "having";
				$d4 = $d1;
			}
			if ($voice === "passive") {
				if ($tense === "perfect") $v = "been";
				else $v = "be";
				$d4 = $d2;
			}
			return "$t $v $d4";
		} elseif ($mood === "indicative" || $mood === "subjunctive") {
			$p = [
				"I", "we",
				$o?"thou":"(you|thou) [\(sg.\)]",
				$o?"you":"[all] (you|ye|y{$OP_APOS}all) [\(pl.\)]",
				$o?"She/he/it":"(he|she|it)",
				"they",
			];
			$p = $p[2*($_p-1)+$pl];
			if ($psv) $d = $D = $d2;
			if ($tense === "present") {
				$b = $o?"are":"(are|${OP_APOS}re)";
				if ($p === "I") $b = $o?"am":"(am|${OP_APOS}m)";
				elseif ($_p == 3 and !$pl) $b = $o?"is":"(is|${OP_APOS}s)";
				elseif ($_p == 2 and !$pl) $b = $o?"art":"(are|art|${OP_APOS}rt|${OP_APOS}re)";
				if ($psv) $b .= " being";
				else $m = " ";
				if (!$psv and $_p == 3 and !$pl) $d = $d3;
				elseif (!$psv and $_p == 2 and !$pl) $d = $o?$d5:"($d|$d5)";
			} elseif ($tense === "imperfect") {
				$b = "were";
				if ($p === "I" or ($_p == 3 and !$pl)) $b = "was";
				elseif (!$o and $_p == 2 and !$pl) $b = "(were|wast)";
				if ($psv) $b .= " being";
			} elseif ($tense === "future") {
				$m = $o?"will":"(will|${OP_APOS}ll)";
				if ($_p == 1) $m = $o?"shall":"(shall|will|${OP_APOS}ll)";
				elseif ($_p == 2 and !$pl) $m = $o?"wilt":"(wilt|will|${OP_APOS}ll)";
				if ($psv) $m .= " be";
				else $b = "$m be";
			} else {
				$d = $d2;
				$D = $d2;
				if ($tense === "perfect")
					if ($psv) list($b, $m) = ["was", (($_p == "3" and !$pl)?"has":"have")." been"];
					else list($m, $d) = ["", $d1];
				elseif ($tense === "pluperfect")
					$m = "had".($psv?" been":"");
				elseif ($tense === "future-perfect") {
					$m = $o?"will":"(will|${OP_APOS}ll)";
					if ($_p == 1) $m = $o?"shall":"(shall|will|${OP_APOS}ll)";
					elseif ($_p == 2 and !$pl) $m = $o?"wilt":"(wilt|will|${OP_APOS}ll)";
					$m .= " have".($psv?" been":"");
				}
			}
			if ($be and !$psv) {
				$D = $D?"($D|$be)":$be;
				$d = $d?"($d|be $be)":$be;
			}
			if (!$d and !$D) return NULL;
			if (!$D) $b = NULL;
			if (!$d) $m = NULL;
			if ($b and (!$only_one or !$m))
				if ($m !== NULL and !$only_one)
					return "$p ($m $d|$b $D)";
				else return "$p $b $D";
			elseif ($d) return "$p $m $d";
			return NULL;
		}
		return $d;
	}
}
?>
