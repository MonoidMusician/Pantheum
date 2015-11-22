<?php
# Helper functions:
# Read depaths (and depath-constructing parts) from a JSON file at $path.
function read_depaths($path, $lang=NULL) {
	$string = file_get_contents($path);
	$json = json_decode($string,true);
	$parts = $_parts = $json["parts"];
	if (is_vec($parts)) {
		$parts = [];
		foreach ($_parts as $part)
			$parts = array_merge($parts, $part);
	} elseif ($parts === NULL) $parts = [];
	$result = []; # DEPATHs generated
	foreach (array_keys($parts) as $key)
		init_values($key, $result, $parts, $lang);
	$depaths = $json["depaths"]; # depath JSON representation
	unset($json);
	if (isset($depaths))
		return make_depaths($result, $depaths, $parts, $lang);
}
# Initialize a part array.
function init_values(&$key, &$result, &$parts, $lang=NULL) {
	global $sudata;
	$values = &$parts[$key];
	/*
	if ($lang === "la" and $key === "case") {
		$cases = trim(safe_get("cases", json_decode($sudata,true)));
		if ($cases) $values['_values_'] = array_map("trim", explode(",", $cases));
	}
	/**/
	# values
	if (!isset($values['_values_'])) {
		# make simple values based on aliases
		$_values_ = $values;
		unset($_values_['_aliases_']);
		$_values_ = array_values(array_unique($_values_));
		$values['_values_'] = $_values_;
	} else {
		$subr = []; # sub-elements that need to become depaths
		foreach ($values['_values_'] as $k => &$v)
			if (is_array($v))
				$subr["$key:$k"] = &$v;
		#echo "\$subr:\n";
		#var_dump($subr);
		make_depaths($result, $subr, $parts);
	}
}
# Add a key entry to a depath initializer.
function add_key(&$duo, $key, &$parts, &$result) {
	$values = &$parts[$key];
	# aliases: local and global
	if (isset($values['_aliases_']))
		# explicit aliases linked to the key
		$duo[1][$key] = &$values['_aliases_'];
	foreach ($values as $k => &$v)
		if ($k !== '_values_' and $k !== '_aliases_')
			# general aliases (global)
			$duo[1][$k] = &$v;
	$duo[0][$key] = &$values['_values_'];
	return $duo;
}
# Copy keys and values from $h2 onto &$h1.
function copy_onto(&$h1, $h2) {
	foreach ($h2 as $k => $v)
		$h1[$k] = $v;
}
# Take certain keys from the possible ones provided in the corresponding part.
function reduced_values(&$hash, $parts, &$r2=NULL) {
	$ret = [&$hash,&$r2];
	if ($r2 === NULL) {$ret[1] = [];$r2=&$ret[1];}
	foreach (array_keys($hash) as $k) {
		if (array_key_exists($k, $parts))
			copy_onto($r2, catch_aliases($parts[$k], $hash[$k]));
	}
	return $ret;
}
# Return a associative array with the same values plus aliases.
function catch_aliases($aliases, $values) {
	$r = [];
	foreach ($values as $v)
		foreach ($aliases as $k => $_)
			if ($v === $_)
				$r[$k] = $v;
	return $r;
}
# Turn a JSON array syntax representing depaths into an array of PHP-object depaths.
function make_depaths(&$result, &$depaths, &$parts, $lang=NULL) {
	foreach ($depaths as $key => &$depath) {
		$duo = [[],[]];
		# Add keys to populate the depath via duo[]
		foreach ($depath as $_) {
			if (is_scalar($_))
				add_key($duo, $_, $parts, $result);
			else
				copy_onto($duo[0], reduced_values($_, $parts, $duo[1])[0]);
		}
		# link depath into result, update caller's record of the depaths
		$depath = $result[$key] = DEPATH($duo[0], $duo[1], "$lang/$key");
	}
	return $result;
}
?>
