<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/make_example.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/dictionary/search.php');

$_start_t = microtime(true);
$size = NULL;
$list = search_GET(1000,$size);
$result = [];
foreach ($list as $w) {
	$result[$w->id()] = format_word($w->name());
}
$result["sorted"] = array_keys($result);
$result["changed"] = [];
foreach ($list as $w)
	$result["changed"][$w->id()] = $w->last_changed();
if ($size !== NULL)
	$result["max_length"] = $size;
else $result["max_length"] = count($result["sorted"]);
echo json_encode($result);
$time = microtime(true) - $_start_t;
$time = round($time, 4);
#error_log($time);
?>
