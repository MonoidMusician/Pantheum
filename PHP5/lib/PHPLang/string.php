<?php
// String utilites/languages/parsers





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
const DEBUG_STRING_PHP = /*true/*/false/**/;

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
	$lens = strlen($s);
	$l = strpos($s,$ls,$offset);
	if ($l === FALSE) return null;
	$r = strpos($s,$rs,$l+strlen($ls));
	if ($r === false) $r = $lens;
	$l2 = strpos($s,$ls,$l+1);
	if (DEBUG_STRING_PHP) echo "<br>Initial: $l $r ".var_export($l2,1);
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
	if (DEBUG_STRING_PHP) echo "<br>Final: $l $r";
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
	$w = str_replace("aa", "a", $w);
	$w = str_replace("ee", "e", $w);
	$w = str_replace("ii", "i", $w);
	$w = str_replace("oo", "o", $w);
	$w = str_replace("uu", "u", $w);
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
function _strtoupper($matches) {
	return mb_strtoupper($matches[0]);
}
function capitalize($str) {
	return preg_replace_callback('/\w/',"_strtoupper",$str,1);
}
function compare_strings($l,$r,$flags) {
	return $l === $r or sanitize($l,$flags) === sanitize($r,$flags);
}
function match($l,$r,$flags,$silent=false) {
	$ls = sanitize($l,$flags);
	$rs = sanitize($r,$flags);
	if (!DEBUG_STRING_PHP) $silent = true;
	if (!$silent) echo "Try match:<ol>";
	if (!$silent) var_dump($ls,$rs);
	if (!$rs) {
		if (!$silent) echo "</ol>Null string";
		return $l;
	}
	if (!$silent) var_dump(strncmp($ls,$rs,strlen($rs)));
	if (strncmp($ls,$rs,strlen($rs))) {
		if (!$silent) echo "</ol>No match";
		return null;
	}
	$ignore = "[^\\w]";
	if (!safe_get("keephtml",$flags)) $ignore .= "|<[^>]*>";
	$regex = implode("($ignore)*", array_map("preg_quote",str_split($rs)));
	$regex = "/^($ignore)*$regex\s*/i";
	if (!$silent) var_dump($regex);
	$ret = preg_replace($regex,"",$l,1);
	if (!$silent) echo "</ol>Result remaining:";
	if (!$silent) var_dump($ret);
	return $ret;
}
function compare_part($s, $i, $flags) {
	if (strpos($s, "\\") === false) {
		if (DEBUG_STRING_PHP) echo "Simple:<ol>";
		if (match($i,$s,$flags) === null)
		{if (DEBUG_STRING_PHP) echo "</ol>False";return null;}
		else {if (DEBUG_STRING_PHP) echo "</ol>True";return $s;}
	}
	if (DEBUG_STRING_PHP) echo "compare part: '$s' and '$i'<ol>";
	$opts=[];$l=0;$arr=[];$lens=strlen($s);$capitals=[];
	while ($l < $lens) {
		$r0 = str_between("\\{","\\}",$s,$l);
		if ($r0 === null) break;
		if ($r0[0] === "*" or ($r0[0] === "\\" and $r0[1] === "*")) {
			$capitalize = ($r0[0] === "*");
			$r0 = substr($r0,1);
		} else $capitalize = false;
		$capitals[]=$capitalize;
		$opts[] = $r0;
		$arr[] = [$r0,$l]; // cache for later
		$l+=strlen($r0);
	}
	if (DEBUG_STRING_PHP and $arr) var_dump($arr);
	if (DEBUG_STRING_PHP and $capitals) var_dump($capitals);
	$r = ""; $l = 0;
	while ($l < $lens) {
		$capitalize = false;
		$j0=$j1=$j2=$l; /* set to indices of opening elements */
		if ($r0 = array_shift($arr))
			list($r0,$j0) = $r0;
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
		if (DEBUG_STRING_PHP) echo "<br>Fixed ($l to $j):";
		if (DEBUG_STRING_PHP) var_dump($rr);
		if ($rr) {
			$i = match($i,$rr,$flags);
			if ($i === null) {if (DEBUG_STRING_PHP) echo "</ol>No match";return null;} else
			$r .= $rr;
		}
		if ($j === $lens) break; else
		if (DEBUG_STRING_PHP) echo "<br>Variable (".($j+2)." to ".($j+2+strlen($rn))."):";
		if (DEBUG_STRING_PHP) var_dump($rn);
		if ($t) {
			$rr = compare_syntax($rn, $i, $flags);
			array_unshift($arr, [$r0,$j0]);
		} else {
			$rr = null;
			foreach ($opts as $idx => $opt) {
				$rr = compare_syntax($opt, $i, $flags);
				if ($rr === null) continue; else
				unset($opts[$idx]);
				break;
			}
		}
		if (DEBUG_STRING_PHP and $rr===null) echo "<br>No match ($t)";
		if ($rr === null)
			if ($t == 2) {$l=$j+strlen($rn)+4+$capitalize;continue;}
			else {if (DEBUG_STRING_PHP) echo "</ol>";return null;} else
		if ($capitalize) $rr = capitalize($rr);
		$r .= $rr;
		$i = match($i,$rr,$flags,true); // XXX: ugly hack?
		if ($i === null) /*go ballastic*/ die("internal error");
		$l=$j+strlen($rn)+4+$capitalize;
		if (DEBUG_STRING_PHP) echo "<br>Remaining:";
		if (DEBUG_STRING_PHP) var_dump($i);
	}
	if (DEBUG_STRING_PHP) echo "</ol>";
	return $r;
}
function compare_syntax($ss, $i, $flags) {
	if (safe_get("unescaped",$flags))
		$ss = swap3($ss, [
			"(",")","[","]",
			"{","}","|"
		]);/**/
	if (DEBUG_STRING_PHP) echo "compare_syntax '$ss' and '$i'<ol>";
	//echo " -> '$ss'";
	$flags["unescaped"] = false;
	if (strpos($ss, "\\") === false) {
		$m = match($i,$ss,$flags);
		if (DEBUG_STRING_PHP) echo "</ol>";
		if ($m === null)
			return null;
		else return $ss;
	}
	//var_dump(split1($ss));
	$rr = null;
	foreach (split1($ss) as $s) {
		$r = compare_part($s, $i, $flags);
		if ($r !== null and strlen($r) > strlen($rr) /* greediness */)
			$rr = $r;
		if (DEBUG_STRING_PHP) echo "<hr>";
	}
	if (DEBUG_STRING_PHP) var_dump($rr);
	if (DEBUG_STRING_PHP) echo "</ol>";
	return $rr;
}


// Nano Macro replacer
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
				$result .= $dictionary[$expr];
			else $result .= $expr;
		if ($type === 1)
			{$result = str_replace("\\\$\\{1\\}", $expr, $result);$expr="-=1";}
		if ($type) $result = run_map($result,$expr);
	}
	if ($escape === 2 || $escape === 4)
		$result = nanoescape($result);
	return $result;
}
?>
