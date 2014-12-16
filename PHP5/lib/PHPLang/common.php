<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

function subscript($obj, $index) {
	if (is_array($obj)) {
		if (array_key_exists($index, $obj))
			return $obj[$index];
		return NULL;
	}
	else return $obj($index);
}
function equals($a,$b){return $a==$b;}
function is_vec($obj) {
	if (!is_array($obj)) return FALSE;
	for ($i=0; $i<count($obj); $i++)
		if (!array_key_exists($i,$obj)) return FALSE;
	return TRUE;
}
function make_vec($obj) {
	return is_vec($obj) ? $obj : array_values($obj);
}
function _die($msg) {
	error_log($msg);
	if(0) {
		debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
		die("an error occurred in php\n");
	} else {
		echo $msg;
		#echo "an error occurred in php\n";
		_____die();
	}
}



// From http://stackoverflow.com/a/1320156
function flatten(array $array) {
	$return = array();
	array_walk_recursive($array, function($a) use (&$return) { $return[] = $a; });
	return $return;
}



// From http://stackoverflow.com/a/7168986
function startswith($haystack, $needle) {
	return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
}
function endswith($haystack, $needle) {
	return !$needle or substr_compare($haystack, $needle, -strlen($needle)) === 0;
}


// From http://stackoverflow.com/a/11872928
/**
* getRandomWeightedElement()
* Utility function for getting random values with weighting.
* Pass in an associative array, such as array('A'=>5, 'B'=>45, 'C'=>50)
* An array like this means that "A" has a 5% chance of being selected, "B" 45%, and "C" 50%.
* The return value is the array key, A, B, or C in this case.  Note that the values assigned
* do not have to be percentages.  The values are simply relative to each other.  If one value
* weight was 2, and the other weight of 1, the value with the weight of 2 has about a 66%
* chance of being selected.  Also note that weights should be integers.
* 
* @param array $weightedValues
*/
function getRandomWeightedElement(array $weightedValues) {
	$rand = mt_rand(1, (int) array_sum($weightedValues));

	foreach ($weightedValues as $key => $value) {
		$rand -= $value;
		if ($rand <= 0) {
			return $key;
		}
	}
}

function getRandomWeightedElement2(array $results, array $weightedValues) {
	$rand = mt_rand(1, (int) array_sum($weightedValues));

	foreach ($weightedValues as $key => $value) {
		$rand -= $value;
		if ($rand <= 0) {
			return $results[$key];
		}
	}
}

// Normalize a vector: non-NULL and non-"", apply callback, return indices 0..n
function vec_norm($vec, $fn=NULL) {
	$res = [];
	foreach ($vec as $v) {
		if ($v !== NULL and (!is_string($v) or $v)) {
			$res[] = is_callable($fn) ? $fn($v) : $v;
		}
	}
	return $res;
}

function normalize_spaces($str,$all=TRUE) {
	$s = $all ? '\s' : (is_array($all) ? '['.implode('',$all).']' : ' ');
	return preg_replace('/'.$s.'{2,}/', ' ', trim($str));
}
function join_spaced(array $pieces) {
	return implode(" ", $pieces);
}

function choose_one($arr, $rand=NULL) {
	$idx = array_rand($arr);
	#echo "\$idx:";
	#var_dump($idx);
	if (is_int($idx))
		return $arr[$idx];
}
function choose_n_unique($arr, $n, $rand=NULL) {
	if ($n === NULL) return choose_one($arr, $rand);
	$ret = [];
	for ( $i = 0; $i < $n; $i++ ) {
		if (is_array($rand))
			$key = getRandomWeightedElement($rand);
		else
			$key = array_rand($arr, 1);
		$selected = $arr[$key];
		unset($arr[$key]);
		if (is_array($rand))
			unset($rand[$key]);
		$ret[] = $selected;
	}
	return $ret;
}
function safe_get($key, $array) {
	return (is_array($array) and array_key_exists($key, $array)) ? $array[$key] : NULL;
}
function &safe_getr($key, &$array) {
	if (!array_key_exists($key, $array))
		$array[$key] = NULL;
	return $array[$key];
}
function &safe_getr_vector($key, &$array) {
	if (!array_key_exists($key, $array))
		$array[$key] = [];
	return $array[$key];
}


function pc_permute($items, $perms = array( )) {
	if (empty($items)) {
		$return = array($perms);
	} else {
		$return = array();
		for ($i = count($items) - 1; $i >= 0; --$i) {
			$newitems = $items;
			$newperms = $perms;
			list($foo) = array_splice($newitems, $i, 1);
			array_unshift($newperms, $foo);
			$return = array_merge($return, pc_permute($newitems, $newperms));
		}
	}
	return $return;
}
function pc_permute_optional($items, $optional=NULL) {
	if (!$optional) return pc_permute($items);
	$optional = make_vec($optional);
	$opt2 = array_slice($optional, 1);
	return array_merge(
		pc_permute_optional(
			array_merge($items, [$optional[0]]),
		$opt2),
		pc_permute_optional($items, $opt2)
	);
}
// Permutes order of provided phrases, joins into space-separated sentences
// For example, ["in foro", "ambulat"] will become ["in foro ambulat", "ambulat in foro"].
function permute_sentence($phrases, $optional=NULL) {
	return array_map("join_spaced",
		pc_permute_optional($phrases, $optional)
	);
}


function array_key_exists_r($needle, $haystack)
{
	if (array_key_exists($needle, $haystack))
		return TRUE;
	foreach ($haystack as $v)
	{
		if (is_array($v) || is_object($v))
			if (array_key_exists_r($needle, $v))
				return TRUE;
	}
	return FALSE;
}
?>
