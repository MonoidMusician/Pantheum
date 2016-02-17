<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
include_once('quiz_types.php');
global $quiz_types;

$type = array_key_exists("type",$_POST) ? $_POST["type"] : NULL;
$last = array_key_exists("last",$_POST) ? $_POST["last"] : NULL;
$mode = array_key_exists("mode",$_POST) ? $_POST["mode"] : 'page';
if ($type !== NULL and $last and
    array_key_exists($type, $quiz_types) and
    is_array($quiz_types[$type]) and
    array_key_exists("options", $quiz_types[$type]) and
    safe_get("name", $quiz_types[$type])) {
	$quiz = $quiz_types[$type];
	if (safe_get("modes", $quiz) and !in_array($mode, $quiz["modes"])) {
		exit("Please select a mode valid for the quiz");
	}
	$selections = [];
	if (safe_get("user_selections", $quiz))
		foreach ($quiz["user_selections"] as $k=>$desc) {
			$v = safe_get("selected-$k", $_POST);
			// TODO: validate
			$selections["selected-$k"] = $v;
		}
	$q = NEWQUIZ($type,$last,$mode,$selections);
	if ($q !== NULL) {
		if ($q->id()) exit("".$q->id());
		else exit("success");
	} else exit("no-credit");
} else if ($last) {
	var_dump(array_keys($quiz_types));
	if (array_key_exists($type, $quiz_types) and
	    is_array($quiz_types[$type]) and
	    array_key_exists("name", $quiz_types[$type]))
		$type = $quiz_types[$type]["name"];
	exit("Cannot find the type of quiz: \"$type\", or it was not valid");
} else exit("You must have at least 1 question!");
?>
