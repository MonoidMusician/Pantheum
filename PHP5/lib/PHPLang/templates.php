<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');

function TEMPLATE($template) {
	$interp = [];
	foreach (explode("\n",$template) as $templ) {
		$inter = [""];
		$array = str_split($templ);
		$i = 0;
		foreach($array as $char) {
			if (is_numeric($char)) {
				$inter[] = intval($char);
				$inter[] = "";
				$i += 2;
			} else $inter[$i] .= $char;
		}
		$interp[] = $inter;
	}
	return function($arg) use($interp) {
		$rres = [];
		foreach ($interp as $inter) {
			$res = [""];
			$raw = TRUE;
			foreach ($inter as $i) {
				if ($raw) {
					foreach ($res as &$r) {
						$r .= $i;
					}
				} elseif (array_key_exists($i, $arg)) {
					$old = $res;
					$res = [];
					foreach (explode("\n",$arg[$i]) as $a) {
						foreach ($old as $r) {
							$res[] = $r.trim($a);
						}
					}
				} else {
					return NULL;
				}
				$raw = !$raw;
			}
			$rres = array_merge($rres,$res);
		}
		$rres = array_unique($rres);
		return implode("\n", $rres);
	};
}
function run_template($w, $p, $t, $arg, $ignore, $change, $overwrite=FALSE) {
	$t->read_paths();
	error_log("Template size: ".count($t->path_storage));
	$w->read_paths();
	foreach ($t->paths() as $path) {
		// Path: create from $w
		$p2 = PATH($w);
		// Merge $p and $path into $p2
		$p2->add2((string)$p, (string)$path);
		// Don't overwrite existing values
		if (!$overwrite and $p2->hasvalue()) continue;
		// Don't do if matches an ignore field
		$cont=FALSE;
		foreach ($ignore as $ig) {
			foreach ($ig as $value) {
				if (!in_array($value,$p2->map))
				// If not all of them are present, the ignore failed
				{ $cont=FALSE;break; }
				else $cont=TRUE;
			}
			if ($cont) break;
		}
		#error_log($cont ? "TRUE: $p2" : "FALSE: $p2");
		if ($cont) continue;
		// Modify path based on changes
		foreach ($p2->map as $value) {
			if (array_key_exists($value, $change)) {
				$p2->add($change[$value]);
			}
		}
		// Make template
		$l = TEMPLATE($path->get());
		// Evaluate the template
		$value = $l($arg);
		if ($value === NULL) return "Missing variable";
		if (!$p2->hasvalue()) {
			#error_log($p2 . $value);
			#error_log(var_export($w->path_storage,1));
			$p2->set($value);
			#error_log(var_export($w->path_storage,1));
			#error_log(var_export($p2->get(),1));
			$p2 = $w->add_path($p2);
			#error_log(var_export($p2->id(),1));
		} else {
			$p2 = $w->get_path($p2);
			$p2->set_value($value);
			$p2->set($value);
		}
	}
	return NULL;
}
?>
