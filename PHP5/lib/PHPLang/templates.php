<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');

function TEMPLATE($templ) {
	$interp = [""];
	$array = str_split($templ);
	$i = 0;
	foreach($array as $char) {
		if (is_numeric($char)) {
			$interp[] = intval($char);
			$interp[] = "";
			$i += 2;
		} else $interp[$i] .= $char;
	}
	return function($arg) use($interp) {
		$res = "";
		$raw = TRUE;
		foreach ($interp as $i) {
			if ($raw)
				$res .= $i;
			elseif (!array_key_exists($i, $arg))
				return NULL;
			else $res .= $arg[$i];
			$raw = !$raw;
		}
		return $res;
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
		foreach ($p2->map as $value) {
			if (in_array($value, $ignore)){$cont=TRUE;break;}
		}
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
