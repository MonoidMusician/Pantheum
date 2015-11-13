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
	if (!is_array($list)) return $list;
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
	return array_map("trim", preg_split("/[,;\n]/", $defs));
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

	$verb = ($spart === "verb");
	$noun = ($spart === "noun");
	if ($verb) {
		$mood = $path->key_value("mood");
		$tense = $path->key_value("tense");
		$voice = $path->key_value("voice");
		$person = $path->key_value("person");
		$number = $path->key_value("number");

		$psv = ($voice === "passive"); // passive voice
		$_p = $person[strlen($person)-1]; // person {1,2,3}
		$pl = $number === "singular" ? 0 : 1; // plural number
		$st  = ($_p == 2 and !$pl) ? "st" : NULL; // singular/person-2
		$eth = ($_p == 3 and !$pl) ? "eth": NULL; // singular/person-3
		if (!$o and $st) $st = "[st]";
	}

	$d0 = []; // present
	$d1 = []; // preterite
	$d2 = []; // past participle
	$d3 = []; // present participle
	$d4 = []; // 3s present / 2s present
	$d5 = []; // 2s perfect (archaic)
	$be = [];

	foreach (split_definitions(cull_definitions($definitions,$path)) as $def) {
		if ($o and $d0) break;
		$matches = [];
		if (preg_match("/^be\s+/", $def)) {
			if ($o and $be) continue;
			$be[] = preg_replace("", "", $def);
			continue;
		}
		if (preg_match("/^([a-zA-Z-]+)((?:[^a-zA-Z-].*)?)$/", $def, $matches)) {
			$a = $matches[1];
			$b = $matches[2];
			$d0[] = $def;
			if ($noun) $d1[] = _make_expr(InflectN::pluralize($a)).$b;
			if (!$verb) continue;
			$d1[] = _make_expr(InflectV::preterite($a,$o)).$b;
			$d2[] = _make_expr(InflectV::pastparticiple($a,$o)).$b;
			$d3[] = _make_expr(InflectV::presentparticiple($a,$o)).$b;
			if (!$pl and $_p != 1)
				if ($_p == 2)
					$d4[] = _make_expr(InflectV::secondsingular($a,$o)).$b;
				else
					$d4[] = _make_expr(InflectV::thirdsingular($a,$o)).$b;
			else
				$d4[] = $def;
			$d5[] = _make_expr(
				InflectV::secondsingular(
					InflectV::preterite($a,$o),$o
				)
			).$b;
		}
	}
	if ($o) {
		if ($d0) $d0 = $d0[0];
		if ($d1) $d1 = $d1[0];
		if ($d2) $d2 = $d2[0];
		if ($d3) $d3 = $d3[0];
		if ($d4) $d4 = $d4[0];
	}
	$d0 = make_expr($d0);
	$d1 = make_expr($d1);
	$d2 = make_expr($d2);
	$d3 = make_expr($d3);
	$d4 = make_expr($d4);
	$d5 = make_expr($d5);
	$be = make_expr($be);

	$d = $d0;
	$D = $d3;

	if ($spart === "verb") {

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
				$d3 = $d;
			}
			elseif ($tense === "perfect") {
				$t = "having";
				$d3 = $d1;
			}
			if ($voice === "passive") {
				if ($tense === "perfect") $v = "been";
				else $v = "be";
				$d3 = $d2;
			}
			return "$t $v $d3";
		} elseif ($mood === "indicative" || $mood === "subjunctive") {
			$M = "";

			$p = [
				"I", "we",
				$o?"thou":"(you|thou) [\(sg.\)]",
				$o?"you":"[all] (you|ye|y{$OP_APOS}all) [\(pl.\)]",
				$o?"She/he/it":"(he|she|it)",
				"they",
			];
			$p = safe_get(2*($_p-1)+$pl, $p); // ignore errors if person isn't provided


			// am are is ...
			$is = [
				$o?"am":"(am|${OP_APOS}m)",
				$o?"are":"(are|art|${OP_APOS}rt|${OP_APOS}re)",
				$o?"is":"(is|${OP_APOS}s)",
			];
			if ($pl) $is = "are"; else $is = $is[$_p-1];

			// was were wast ...
			$was = [
				"was",
				$o?"wast":"(were|wast)",
				"was",
			];
			if ($pl) $was = "were"; else $was = $was[$_p-1];

			// shall will wilt ...
			$will = $o?"will":"(will|${OP_APOS}ll)";
			if ($_p == 1) $will = $o?"shall":"(shall|will|${OP_APOS}ll)";
			elseif ($_p == 2 and !$pl) $will = $o?"wilt":"(wilt|will|shall|${OP_APOS}ll)";

			// has have hast ...
			$has = "have";
			if ($_p == 3 and !$pl) $has = $o?"hath":"(hath|has)";
			elseif ($_p == 2 and !$pl) $has = $o?"hast":"(hast|havest|have)";

			if ($psv) $d = $D = $d2;



			if ($tense === "present") {
				$b = $is;
				if ($psv) list($m,$b) = [$b,$b." being"];
				else $m = " ";
				if (!$psv and $_p != 1 and !$pl) $d = ($o or $_p==3)?$d4:"($d|$d4)";
			} elseif ($tense === "imperfect") {
				$b = $was;
				if ($psv) list($m,$b) = [$b,$b." being"];
			} elseif ($tense === "future") {
				$m = $will;
				if ($psv) $m .= " be";
				else $b = "$m be";
			} else {
				$d = $d2;
				$D = $d2;
				if ($tense === "perfect")
					if ($psv) list($b, $m) = [$was, "$has been"];
					else list($b, $m, $d) = ["$has", " ", $st?($o?$d5:"($d5|$d1)"):$d1];
				elseif ($tense === "pluperfect")
					$m = "had$st" . ($psv?" been":"");
				elseif ($tense === "future-perfect")
					$m = $will . " have" . ($psv?" been":"");
			}

			if ($be and !$o and !$psv) {
				$D = $D?"($D|$be)":$be;
				$d = $d?"($d|be $be)":$be;
			}

			if (!$d and !$D) return NULL;
			if (!$D) $b = NULL;
			if (!$d) $m = NULL;
			if ($b and (!$o or $m === NULL))
				if ($o or $m === NULL)
					return "$p $b $D";
				else return "$p ($m $M $d|$b $D)";
			elseif ($d) return "$p $m $M $d";
			return NULL;
		}
	} else if ($noun) {
		if ($path->key_value("number") == "plural") $d = $d1;
		$t = $o?"the":"(a|an|the|some)";
		$c = [
			0 => "",
			"vocative"   => "",
			"nominative" => "",
			"accusative" => "",
			"ablative"   => $o?"from/by":"(from|by)",
			"dative"     => $o?"to/for" :"(to|for)",
			"genitive"   => "of",
			"locative"   => "at",
		];
		$c = $c[$path->key_value("case")];
		return "$c $t $d";
	}
	return $d;
}
?>
