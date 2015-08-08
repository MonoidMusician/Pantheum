<?php
// String utilites/languages/parsers
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/misc.php');
sro('/PHP5/lib/PHPLang/make_example.php');



global $DEBUG_STRING_PHP;
$DEBUG_STRING_PHP = /*true/*/false/**/;


// $s = "{*test (this|that [system])}, {computer|machine}!";
// $s = "{*now} {[the|a] cloud was|[the] clouds were} {coming down|descending} {to [the] (earth|ground)}";
// $s = "{*now} {the (very dense|densest|thickest) ash} {was burning}";
// $s = "{*now} {(the majority of|most [of]) [the] Pompeian(s|i)} {were} {despairing|disparaging} {about (the[ir [own]] city|Pompeii)}";
function permute_syntax($s) {
	$ll = [];
	$r = [];
	$j = strlen($s);
	for ($i=0; $i++; $i<$j) {
		if ($c == ")") {
			array_pop($ll);
			continue;
		}
		$c = $s[$l];
		$r2 = &$r;
		foreach ($ll as $l) {
			$r2 = &$r2[$l];
		}
		if ($c == "(" or $c == "[") {
			$ll[] = count($r2);
			$r2[] = [];
		} else if ($c == "]") {
			$r2[] = "";
			array_pop($ll);
		} else if ($c == "|") {
			$r2[] = "";
		} else $r2[count($r2)-1].=$c;
	}
}

function swap($s,$s1,$s2) {
	return implode(
		$s2, array_map(function($a)use($s1,$s2){
			return str_replace($s2,$s1,$a);
		}, explode($s1,$s))
	);
}
function swap2($s,$rr) {
	foreach ($ss as $r) $s = swap($s,$r[0],$r[1]);
	return $s;
}
function swap3($s,$rr) {
	$s = explode("\\\\", $s);
	foreach ($rr as $r)
		$s = array_map(function($s)use($r){return swap($s,"\\$r",$r);},$s);
	return implode("\\\\", $s);
}
function mb_str_split($string) {
	return preg_split('/(?<!^)(?!$)/u', $string); 
}
function split1($s) {
	$l = 0; $r = [""];
	$j = strlen($s);
	$m = false;
	foreach (mb_str_split($s) as $c) {
		/* TODO optimize */
		if ($c == "\\" and !$m)
		{$m=true;continue;}
		if ($m and $c != "\\") {
			$m=false;
			if ($c == "|" and $l==0)
			{ $r[]="";continue;}
			if ($c == "(" or $c == "[" or $c == "{")
				$l += 1;
			else if ($c == "}" or $c == "]" or $c == ")")
				$l -= 1;
			$c="\\$c";
		}
			array_push($r,array_pop($r).$c);
			//var_dump($r[count($r)-1]);
			//var_dump($c);
	}
	return $r;
}
function str_between($ls,$rs,$s,&$offset=0) {
	global $DEBUG_STRING_PHP;
	$lens = strlen($s);
	$l = strpos($s,$ls,$offset);
	if ($l === FALSE) return null;
	$r = strpos($s,$rs,$l+strlen($ls));
	if ($r === false) $r = $lens;
	$l2 = strpos($s,$ls,$l+1);
	//if ($DEBUG_STRING_PHP) echo "Initial: $l $r ".var_export($l2,1)."<br>";
	while ($l2 and $l2 < $r and $r < $lens) {
		$r2 = strpos($s,$rs,$r+1);
		if (!$r2) {
			$r = $lens;
			break;
		}
		$r = $r2;
		$l2 = strpos($s,$ls,$l2+1);
	}
	$offset = $l;
	//if ($DEBUG_STRING_PHP) echo "Final: $l $r<br>";
	return substr($s,$l+strlen($ls),$r-($l+strlen($ls)));
}
function no_specials2($w,$extras="1-9/; ,\\n") {
	$w = normalizer_normalize($w, Normalizer::FORM_D);
	$w = str_replace("æ", "ae", $w);
	$w = str_replace("œ", "oe", $w);
	$w = str_replace("Æ", "ae", $w);
	$w = str_replace("Œ", "oe", $w);
	$w = preg_replace("#[^A-Za-z$extras]#","", $w);
	return $w;
}

function unformat_word2($w,$lang) {
	$w = mb_strtolower(no_specials2($w,'1-9'), "utf-8");
	$w = str_replace("j", "i", $w);
	$w = str_replace("u", "v", $w);
	/*$w = str_replace("aa", "a", $w);
	$w = str_replace("ee", "e", $w);
	$w = str_replace("ii", "i", $w);
	$w = str_replace("oo", "o", $w);
	$w = str_replace("uu", "u", $w);/**/
	return normalize_spaces($w);
}
function sanitize($s,$flags) {
	if (!safe_get("keephtml",$flags)) $s = strip_tags($s);
	$s = unformat_word2($s,safe_get("lang",$flags));
	if (safe_get("stripspaces",$flags))
		$s = preg_replace('/\s+/ig',"",$s);
	else $s = normalize_spaces($s);
	return $s;
}
function sanitize2($s) {
	if (is_string($s))
		return sanitize($s,[]);
	elseif (is_array($s))
		return array_map("sanitize2", $s);
	else return $s;
}
function _strtoupper($matches) {
	return mb_strtoupper($matches[0]);
}
function capitalize($str) {
	return preg_replace_callback('/\w/',"_strtoupper",$str,1);
}
function compare_strings($l,$r,$flags) {
	return $l === $r or sanitize($l,$flags) === sanitize($r,$flags);
}
function any_match_quoted($char) {
	$quot = preg_quote($char);
	$other = NULL;
	if ($char === "v")
		$other = "u";
	else if ($char === "i")
		$other = "j";
	if ($other !== NULL)
		return "[$quot".any_match_quoted($other)."]";
	return $quot;
}
function match($l,$r,$flags,$silent=false) {
	global $DEBUG_STRING_PHP;
	$ls = sanitize($l,$flags);
	$rs = sanitize($r,$flags);
	if (!$DEBUG_STRING_PHP) $silent = true;
	if (!$rs) {
		if (!$silent) echo "Null string<br>";
		return $l;
	}
	if (!$silent) echo "Try match:<ol>";
	if (!$silent) var_dump($ls,$rs);
	if (strncmp($ls,$rs,strlen($rs))) {
		if (!$silent) var_dump(strncmp($ls,$rs,strlen($rs)));
		if (!$silent) echo "</ol>No match";
		return null;
	}
	$ignore = "[^\\w]";
	if (!safe_get("keephtml",$flags)) $ignore .= "|<[^>]*>";
	$regex = implode("($ignore)*", array_map("any_match_quoted",str_split($rs)));
	$regex = "/^($ignore)*$regex\s*/i";
	if (!$silent) var_dump($regex);
	$ret = preg_replace($regex,"",$l,1);
	if (!$silent) echo "</ol>Result remaining:";
	if (!$silent) var_dump($ret);
	return $ret;
}
function compare_part($s, $i, $flags) {
	global $DEBUG_STRING_PHP;
	if (strpos($s, "\\") === false) {
		if ($DEBUG_STRING_PHP) echo "Simple:<ol>";
		if (match($i,$s,$flags) === null)
		{if ($DEBUG_STRING_PHP) echo "</ol>False";return null;}
		else {if ($DEBUG_STRING_PHP) echo "</ol>True ('$s')";return $s;}
	}
	if ($DEBUG_STRING_PHP) echo "compare part: '$s' and '$i'<ol>";
	$opts=[];$l=0;$arr=[];$lens=strlen($s);$capitals=[];
	// Pre-scan for (first-level) curly brace expressions
	while ($l < $lens) {
		$j2 = $j1 = $l;
		$r0 = str_between("\\{","\\}",$s,$l);
		$r1 = str_between("\\(","\\)",$s,$j1);
		$r2 = str_between("\\[","\\]",$s,$j2);
		if ($r0 === null) break;
		if ($r1 and $j1 < $l) {
			//if ($DEBUG_STRING_PHP) echo "Parentheses at $j1 beat $l<br>";
			$l = $j1 + strlen($r1) + 4;
			continue;
		}
		if ($r2 and $j2 < $l) {
			//if ($DEBUG_STRING_PHP) echo "Brackets at $j2 beat $l<br>";
			$l = $j2 + strlen($r2) + 4;
			continue;
		}
		if ($r0[0] === "*" or ($r0[0] === "\\" and $r0[1] === "*")) {
			$capitalize = ($r0[0] === "*");
			$r0 = substr($r0,1);
		} else $capitalize = false;
		$capitals[]=$capitalize;
		$opts[] = $r0;
		$arr[] = [$r0,$l]; // cache for later
		$l+=strlen($r0)+4;
	}
	if ($DEBUG_STRING_PHP and $arr) var_dump($arr);
	if ($DEBUG_STRING_PHP and $capitals) var_dump($capitals);
	//if ($DEBUG_STRING_PHP and $opts) var_dump($opts);
	$r = ""; $l = 0;
	$backtrack = []; $failed_backtrack = [];
	$back = function($forwards=false) use(
		$s,&$i,&$r,&$l,&$next,&$arr,&$opts,&$capitals,
		&$backtrack,&$failed_backtrack,$DEBUG_STRING_PHP
	) {
		if (!$forwards) {
			while ($backtrack and in_array(sanitize2($backtrack[count($backtrack)-1]),$failed_backtrack))
				array_pop($backtrack);
			if (!$backtrack) return false;
			$failed_backtrack[] = sanitize2($backtrack[count($backtrack)-1]);
			if (count($failed_backtrack) > 6) return false;
			list($i, $r, $l, $arr, $opts, $capitals) = array_pop($backtrack);
			if ($DEBUG_STRING_PHP) {
				$remaining = substr($s,$l);
				if ($l) $remaining = "...$remaining";
				echo "<hr style='height: 6px'>!! Backtracking to $l '$remaining' (match so far '$r', trying to match '$i') !!<hr style='height: 6px'>";
				var_dump($arr);var_dump($capitals);var_dump($opts);
				foreach ($failed_backtrack as $f)
					var_dump($f);
			}
			return true;
		} else {
			$backtrack[] = [$i, $r, $next, $arr, $opts, $capitals];
		}
	};
	while ($l < $lens) {
		$capitalize = false;
		$j0=$j1=$j2=$l; /* set to indices of opening elements */
		if ($arr)
			list($r0,$j0) = $arr[0];
		else $r0 = null;
		$r1 = str_between("\\(","\\)",$s,$j1);
		$r2 = str_between("\\[","\\]",$s,$j2);
		if ($r0 === null) $r0 = $j0 = $lens;
		if ($r1 === null) $r1 = $j1 = $lens;
		if ($r2 === null) $r2 = $j2 = $lens;
		$t = null;
		if ($j0 < $j1 and $j0 < $j2) {
			$j=$j0;$rn=$r0;$t=0;
			$capitalize = array_shift($capitals) ? 1 : 0;
		} else if ($j1 < $j0 and $j1 < $j2) {
			$j=$j1;$rn=$r1;$t=1;
		} else if ($j2 < $j0 and $j2 < $j1) {
			$j=$j2;$rn=$r2;$t=2;
		} else {$j=$lens;}
		$rr = substr($s,$l,$j-$l);
		if ($DEBUG_STRING_PHP) echo "<br>Fixed ($l to $j):";
		if ($DEBUG_STRING_PHP) var_dump($rr);
		if ($rr) {
			$i = match($i,$rr,$flags);
			if ($i === null) {
				if ($back()) continue; else {
					if ($DEBUG_STRING_PHP) echo "</ol>No match";
					return null;
				}
			} else
			$r .= $rr;
		}
		if ($j === $lens) break; else
		if ($DEBUG_STRING_PHP) echo "<br>Variable (".($j+2)." to ".($j+2+strlen($rn)).", type $t):";
		if ($DEBUG_STRING_PHP) var_dump($rn);
		$next = $j+strlen($rn)+4+$capitalize;
		$other = [];
		if ($t) {
			// Parentheses and brackets
			$rr = compare_syntax($rn, $i, $flags, $other);
		} else {
			// Curly braces: try each pair
			$rr = null;
			$match_idx = null;
			foreach ($opts as $idx => $opt) {
				$rrr = compare_syntax($opt, $i, $flags, $other);
				if ($DEBUG_STRING_PHP) echo "<hr>";
				if ($rrr === null) continue; else
				if ($rr === null) {
					// First result: use it
					$rr = $rrr;
					$match_idx = $idx;
					unset($opts[$idx]);
				} else {
					$_opts = $opts;
					$_capitals = $capitals;
					$opts[$match_idx] = $rr;
					$capitals[] = !!$capitalize;
					// Extra result
					$back(true);
					$opts = $_opts;
					$capitals = $_capitals;
				}
			}
			array_shift($arr);
		}
		if ($DEBUG_STRING_PHP and $rr===null) echo "<br>No match ($t)";
		if ($rr === null)
			if ($t === 2) {$l=$next;continue;}
			else {
				if ($back()) continue; else {
					if ($DEBUG_STRING_PHP) echo "</ol>No match";
					return null;
				}
			}
		else
		$l=$next;
		if ($t === 2) $back(true);
		if ($other and $DEBUG_STRING_PHP) {
			echo "<br>Some other results:";
			var_dump($other);
		}
		foreach ($other as $aliud) {
			$_r = $r;
			$_i = $i;
			$r .= $aliud;
			$i = match($i,$aliud,$flags,true);
			$back(true);
			$r = $_r;
			$i = $_i;
		}
		if ($capitalize) $rr = capitalize($rr);
		$r .= $rr;
		$i = match($i,$rr,$flags,true); // XXX: ugly hack?
		if ($i === null) /*go ballastic*/ die("internal error");
		if ($DEBUG_STRING_PHP) echo "<br>Remaining:";
		if ($DEBUG_STRING_PHP) var_dump($i);
	}
	if ($DEBUG_STRING_PHP) echo "</ol>";
	return $r;
}

function compare_syntax($ss, $i, $flags, &$backtrack=NULL) {
	global $DEBUG_STRING_PHP;
	if (safe_get("unescaped",$flags))
		$ss = swap3($ss, [
			"(",")","[","]",
			"{","}","|"
		]);/**/
	$matchall = !!safe_get("matchall", $flags);
	unset($flags["matchall"]);
	if ($DEBUG_STRING_PHP) echo "compare_syntax '$ss' and '$i'<ol>";
	//echo " -> '$ss'";
	$flags["unescaped"] = false;
	if (strpos($ss, "\\") === false) {
		$m = match($i,$ss,$flags);
		if ($DEBUG_STRING_PHP) echo "</ol>";
		if ($m === null or ($matchall and $m))
			return null;
		else return $ss;
	}
	//var_dump(split1($ss));
	$rr = null;
	foreach (split1($ss) as $s) {
		$r = compare_part($s, $i, $flags);
		if ($r !== null and $backtrack !== null and !in_array($r, $backtrack)) {
			$backtrack[] = $r;
		}
		if ($r !== null and ($rr === null or strlen($r) > strlen($rr) /* greediness */)) {
			$rr = $r;
		}
		if ($DEBUG_STRING_PHP) echo "<hr>";
	}
	if ($backtrack !== null) $backtrack = array_values(array_diff($backtrack, [$rr]));
	if ($DEBUG_STRING_PHP) var_dump($rr);
	if ($DEBUG_STRING_PHP) echo "</ol>";
	if ($matchall) {
		if ($DEBUG_STRING_PHP) echo compare_strings($rr, $i, $flags) ? "$rr ≈ $i" : "$rr !≈ $i";
		if (!compare_strings($rr, $i, $flags)) return NULL;
	}
	return $rr;
}


// Nano Macro replacer
// Utility: remove an entry from the dictionary, used to prevent infinite macro recursion.
function remove1($dict, $key) {
	$new = [];
	foreach ($dict as $k=>$v) {
		if ($k !== $key) $new[$k] = $v;
	}
	return $new;
}
function nanolexify($syntax) {
	$ret = [];
	$l = 0; $extra = "";
	$lens = strlen($syntax);
	while ($l < $lens) {
		$i = strpos($syntax,"\\\$",$l);
		$j = strpos($syntax,"\\_",$l);
		if ($i === FALSE) $i = $lens;
		if ($j === FALSE) $j = $lens;
		$o = $i<$j ? $i : $j;
		if ($o === $lens) {
			$ret[] = $extra.substr($syntax,$l,$o-$l);
			break;
		}
		if ($j < $i) {
			$matches = [];
			preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*/',substr($syntax,$j+2),$matches);
			$m = $matches[0];
			$ret[] = $extra.substr($syntax,$l,$o-$l);
			$extra = "";
			$ret[] = [0, $m];
			$l = $j + 2 + strlen($m);
		} else {
			$j1 = $j2 = $i+2;
			$r1 = str_between("\\(","\\)",$syntax,$j1);
			$r2 = str_between("\\[","\\]",$syntax,$j2);
			if ($r1 === null) $r1 = $j1 = $lens;
			if ($r2 === null) $r2 = $j2 = $lens;
			$t = null;
			if ($j1 === $i+2) {
				$j=$j1;$rn=$r1;$t=1;
			} else if ($j2 === $i+2) {
				$j=$j2;$rn=$r2;$t=2;
			} else {
				$extra.=substr($syntax,$l,$o+2-$l);
				$l=$o+2;
				continue;
			}
			$ret[] = $extra.substr($syntax,$l,$o-$l);
			$extra = "";
			$ret[] = [$t, $rn];
			$l = $i + 6 + strlen($rn);
		}
	}
	return $ret;
}
function nanolexify_replacements($syntax) {
	$ret = [];
	$l = 0;
	while ($l < strlen($syntax)) {
		$i = strpos($syntax,"\\\$\\{",$l);
		if ($i === FALSE) $i = strlen($syntax);
		$ret[] = substr($syntax,$l,$i-$l);
		if ($i === strlen($syntax)) break;
		$j = strpos($syntax,"\\}",$i);
		if ($j === FALSE) $j = strlen($syntax);
		$ret[] = substr($syntax,$i+4,$j-($i+4));
		$l = $j+2;
	}
	return $ret;
}
function get_map($expr) {
	if (startswith($expr,"+=") || startswith($expr,"-=")) {
		$i = intval(trim(substr($expr,2)));
		if ($expr[0] === "-") $i = -$i;
		return function($j)use($i){
			return $i+$j;
		};
	} else {
		$map = [];
		foreach (explode(", ",$expr) as $kv) {
			list($k,$v) = array_map("intval", array_map("trim",explode("=>",$k,$v)));
			$map[$k] = $v;
		}
		return function($j)use($map){
			return array_key_exists($j,$map) ? $map[$j] : $j;
		};
	}
}
function run_map($result, $expr) {
	$res = "";
	$odd = false;
	$map = get_map($expr);
	foreach (nanolexify_replacements($result) as $i) {
		$odd = !$odd;
		if ($odd) {$res.=$i;continue;}
		$res .= "\\\$\\{".$map($i)."\\}";
	}
	return $res;
}
function nanoescape($str) {
	return swap3($str, [
		"(",")","[","]",
		"{","}","$","_"
	]);
}
function nanomacro($syntax, $dictionary, $escape=false) {
	$result = "";
	$odd = false;
	if ($escape) $syntax = nanoescape($syntax);
	if ($escape === 3 || $escape === 4)
		$dictionary = array_map("nanoescape", $dictionary);
	foreach (nanolexify($syntax) as $op) {
		$odd = !$odd;
		if ($odd) {$result.=$op;continue;}
		list($type,$expr) = $op;
		if ($type === 0)
			if (array_key_exists($expr,$dictionary))
				$result .= nanomacro($dictionary[$expr], remove1($dictionary,$expr));
			else $result .= "_$expr";
		if ($type === 1)
			{$result = str_replace("\\\$\\{0\\}", nanomacro($expr, $dictionary), $result);$expr="-=1";}
		if ($type) $result = run_map($result,$expr);
	}
	if ($escape === 2 || $escape === 4)
		$result = nanoescape($result);
	return $result;
}
// Return the default/shared dictionary.
function nano_dfdict() {
	return [
		'a' => '[a[n]|the]',
		'test' => 'This is argument 1: ${0}, and 2: ${1}, and 3: ${2}',
		'quot' => ', “(${0})”,',
		'perfactv' => ', (having (${0}) (${1})|who had (${0}) (${1})),',
		'cum' => ', when ${0},',
		'phrase' => ', ${0},',
		'appos' => '{ {,${1},} {${0}}}',
		'Appos' => '{*{,${1},} {${0}}}',
		'opts' => '({${0}} {${1}}|{${0}} {${2}})',
	];
}


// Punctuation normalization
// TODO: parentheses
function lexify_punctuation($str) {
	$OPS = array_map(function($o){return $GLOBALS["OP_$o"];}, [
		"PERIOD","COMMA","COLON","APOS",
		"QUEST","EXCL","MDASH","NDASH",
		"LPAREN","RPAREN","LQUOTE","RQUOTE",
	]);
	$result = [""];
	$i = 0; $lens = strlen($str);
	while ($i < $lens) {
		$match = NULL;
		foreach ($OPS as $op) {
			if (startswith($str,$op->text,$i))
			{ $match = $op; break; }
		}
		if ($match !== NULL) {
			$result[] = $match;
			$i += strlen($match->text);
		} else {
			if (!is_string($result[count($result)-1]))
				$result[] = "";
			$result[count($result)-1] .= $str[$i];
			$i += 1;
		}
	}
	$result = vec_norm(array_map(function($o) {
		if (is_string($o)) return trim($o);
		return $o;
	}, $result));
	return $result;
}
// N.B. Assumes it receives a practically complete sentence
function simplify_punctuation($ops) {
	global $DEBUG_STRING_PHP;
	global $OP_COMMA, $OP_PERIOD, $OP_COLON, $OP_APOS,
	       $OP_QUEST, $OP_EXCL, $OP_MDASH, $OP_NDASH,
	       $OP_LPAREN, $OP_RPAREN, $OP_LQUOTE, $OP_RQUOTE;
	if (!$ops) return $ops;
	// Remove punctuation that doesn't belong at start of a sentence
	while (in_array($ops[0],[
		$OP_COMMA, $OP_PERIOD, $OP_COLON, $OP_APOS,
		$OP_QUEST, $OP_EXCL
	])) array_shift($ops);
	$precedence = [
		[$OP_QUEST,$OP_EXCL,$OP_MDASH,$OP_NDASH],
		[$OP_PERIOD],
		[$OP_COMMA]
	];
	$get_precedence = function($o) use($precedence) {
		$prec = NULL;
		foreach ($precedence as $p=>$ops) {
			foreach ($ops as $op) {
				if ($o === $op) {
					$prec=count($precedence)-$p; break;
				}
			}
			if ($prec !== NULL) break;
		}
		return $prec;
	};
	// Question marks, etc., can override periods, which can override commas.
	// Also remove straight duplicates, keeping the first one.
	$i = 0;
	while ($i < count($ops)-1) {
		if (!ISOP($op=$ops[$i]) or ($prec=$get_precedence($op)) === NULL)
		{ $i+=1;continue; }
		$j = $i+1;
		while ($j < count($ops)) {
			if (!ISOP($ops[$j])) break;
			$p = $get_precedence($o=$ops[$j]);
			if ($p === NULL) {
				$j+=1;
			} else if ($p < $prec) {
				if ($DEBUG_STRING_PHP) echo "Remove $o (j=$j) vs $op (i=$i)<br>";
				array_splice($ops, $j, 1);
			} else if ($p > $prec || $op === $o) {
				if ($DEBUG_STRING_PHP) echo "Remove $op (i=$i) vs $o (j=$j)<br>";
				array_splice($ops, $i, 1);
				$i = $j-1;
			} else $j += 1;
		}
		$i = $j;
	}
	// Follow American quotation rules for commas and periods
	$i = 0;
	while ($i < count($ops)-1) {
		$op = $ops[$i];
		if (!in_array($op,[$OP_LQUOTE,$OP_RQUOTE]))
		{ $i+=1;continue; }
		$j = $i+1;
		$o = $ops[$j];
		if (in_array($o,[$OP_PERIOD,$OP_COMMA])) {
			if ($DEBUG_STRING_PHP) echo "Switch $op (i=$i) and $o (j=$j)<br>";
			$ops[$i] = $o;
			$ops[$j] = $op;
			$i = $j+1;
		} else $i += 1;
	}
	// Obliterate close quotes followed by open quotes
	$i = 0;
	while ($i < count($ops)-1) {
		$op = $ops[$i];
		if ($op !== $OP_RQUOTE)
		{ $i+=1;continue; }
		$j = $i+1;
		while ($j < count($ops)) {
			$o = $ops[$j];
			if (!ISOP($o)) break;
			if ($o !== $OP_LQUOTE)
			{ $j+=1; continue; }
			array_splice($ops, $i, $j-$i+1);
			break;
		}
		$i = $j;
	}
	return $ops;
}
function normalize_punctuation($str) {
	global $OP_COMMA, $OP_PERIOD;
	$ops = lexify_punctuation($str);
	$ops = simplify_punctuation($ops);
	if ($ops and $ops[count($ops)-1] === $OP_COMMA)
		$ops[count($ops)-1] = $OP_PERIOD;
	return serialize_sentence_part($ops);
}


// Combine all three
function compare_syntax3($syntax, $target, $dictionary, $matchall=false) {
	$syntax = nanomacro($syntax, $dictionary, 4);
	$match = compare_syntax($syntax, $target, ["unescaped"=>true,"matchall"=>$matchall]);
	return $match ? normalize_punctuation($match) : $match;
}
?>
