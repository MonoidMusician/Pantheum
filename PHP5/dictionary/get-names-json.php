<?php
require_once('/var/www/latin/config.php');
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

if (!array_key_exists("name", $_GET) or !(
	$names = vec_norm(explode(",", $_GET["name"]), "trim")
	))
	{ $names = NULL; }

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

if ($ids === NULL) {
	$searcher = defaultDB()->searcher();
	if ($names)
		$searcher = $searcher->name_includes($names);
	if ($langs)
		$searcher = $searcher->lang($langs);
	if ($sparts)
		$searcher = $searcher->spart($sparts);
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
	$list = $searcher->all("name");
} else {
	$list = [];
	foreach ($ids as $id)
		$list[] = WORD(defaultDB(), intval($id));
}
$res = array_map(function($e) {
	return format_word($e->name());
}, $list);
echo json_encode($res);
?>
