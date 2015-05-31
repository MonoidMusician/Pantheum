<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/sql_stmts.php');

global $mysqli;

$attr = safe_get("attr", $_GET);
$attr_t = strpos($attr,"=");
if ($attr_t !== FALSE) {
	$get = "CONCAT(attr_tag, '=', attr_value)";
	$group = "";
} else {
	$get = "CONCAT(attr_tag, '={', GROUP_CONCAT(DISTINCT attr_value), '}')";
	$group = "GROUP BY attr_tag";
}
$stmt = "SELECT DISTINCT $get FROM attributes";
$op = " WHERE word_id in (SELECT word_id FROM words WHERE ";
if (safe_get("lang",$_GET)) {
	$stmt .= "$op (";
	$_ = "";
	foreach (explode(",",$_GET["lang"]) as $l) {
		if (!preg_match("/^[a-zA-Z0-9]+$/", $l)) continue;
		$stmt .= "$_ word_lang = '$l'";
		$_ = " OR ";
	}
	$stmt .= ")"; $op = " AND ";
}
if (safe_get("spart",$_GET)) {
	$stmt .= "$op (";
	$_ = "";
	foreach (explode(",",$_GET["spart"]) as $l) {
		if (!preg_match("/^[a-zA-Z0-9]+$/", $l)) continue;
		$stmt .= "$_ word_spart = '$l'";
		$_ = " OR ";
	}
	$stmt .= ")"; $op = " AND ";
}
if ($op === " AND ") $stmt .= ")";
else $op = " WHERE ";
if ($attr_t) {
	$stmt .= "$op attr_tag = '".explode("=",$attr)[0]."'";
} elseif ($attr) {
	$stmt .= "$op attr_tag LIKE '%".explode("=",$attr)[0]."%'";
}
$stmt .= " $group";
//echo $stmt;
$stmt = $mysqli->prepare($stmt);
if (!$stmt)
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
$res = [];
sql_getmany($stmt, $res, []);
echo json_encode(array_values($res));
?>
