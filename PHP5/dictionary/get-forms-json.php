<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/sql_stmts.php');
sro('/PHP5/lib/PHPLang/db.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/misc.php');

global $mysqli;

if (!array_key_exists("lang", $_GET) or !(
	$langs = vec_norm(explode(",", $_GET["lang"]), "trim")
	))
	{ $langs = ['la']; }

if (!array_key_exists("form", $_GET) or !(
	$forms = vec_norm(explode(",", $_GET["form"]), "trim")
	))
	{ $forms = NULL; }

if (!array_key_exists("spart", $_GET) or !(
	$sparts = vec_norm(explode(",", $_GET["spart"]), "trim")
	))
	{ $sparts = NULL; }

if (!array_key_exists("attr", $_GET) or !(
	$attrs = vec_norm(explode(",", $_GET["attr"]), "trim")
	))
	{ $attrs = []; }

if (!array_key_exists("id", $_GET) or !(
	$ids = vec_norm(explode(",", $_GET["id"]), "trim")
	))
	{ $ids = NULL; }

$no_definitions = safe_get("no_definitions", $_GET) === "true";
$no_templates = !(safe_get("show_templates", $_GET) === "true");

if ($ids === NULL) {
	$searcher = defaultDB()->searcher();
	if ($langs)
		$searcher = $searcher->lang($langs);
	if ($sparts)
		$searcher = $searcher->spart($sparts);
	if ($no_definitions)
		$searcher = $searcher->no_definitions();
	if ($no_templates) $attrs[] = "!template";
	foreach ($attrs as $attr) {
		if (!$attr) continue;
		$a = NULL;
		if ($reverse = (substr($attr, 0, 1) === "!")) {
			$attr = substr($attr, 1);
		}
		if (strpos($attr,"=") === FALSE)
			$a = ATTR($attr);
		else {
			list ($name,$value) = explode("=",$attr,2);
			$a = ATTR($name,$value);
		}
		if ($a !== NULL) {
			if (!$reverse)
				$searcher = $searcher->only_with_attr($a);
			else
				$searcher = $searcher->only_without_attr($a);
		}
	}
	$searcher = $searcher->only_without_attr(ATTR("template"));
	//$stmt = "SELECT form_value FROM forms WHERE word_id IN (".$searcher->stmt.")";
	$stmt = "SELECT DISTINCT form_value FROM forms WHERE EXISTS (".$searcher->stmt." AND word_id = forms.word_id)";
	if ($forms) {
		//$searcher = $searcher->form_includes($forms);
		$op = " AND (";
		foreach ($forms as $f) {
			$stmt .= $op."form_value LIKE CONCAT('%',?,'%')";
			$searcher->args[0] .= "s";
			$searcher->args[] .= $f;
			$op = " OR ";
		}
		if ($op === " OR ")
			$stmt .= ")";
	}
	$value = $stmt;
	error_log($value);
	$stmt = $mysqli->prepare($stmt);
	if (!$stmt) {
		echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		echo "\nStatement was: " . var_export($value, 1);
	}
	$_start = microtime(true);
	$list = sql_getmany($stmt, $searcher->map, $searcher->args);
	error_log("-- Took ".(microtime(true)-$_start)."s");
	$stmt->close();
} else {
	$list = [];
}
$res = vec_norm(array_map(function($e) {
	return format_word($e);
}, $list));
echo json_encode($res);
?>
