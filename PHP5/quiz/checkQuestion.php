<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');
	sro('/PHP5/lib/PHPLang/display.php');
	sro('/PHP5/lib/PHPLang/string.php');
	sro('/PHP5/quiz/common.php');

	$result = [];
	$subscore = 0;
	$out_of = 0;
	if (!is_array(quiz_getvalue("current_answer"))) exit("session timed out");
	if (quiz_getvalue("mode") != "question") exit("Checking answers is not allowed for this quiz (mode: ".quiz_getvalue("mode").")");
	$flags = ["unescaped"=>TRUE,"matchall"=>TRUE];
	$name = safe_get("name", $_POST);
	if (!$name) exit("no key name supplied");
	$values = quiz_getvalue("current_answer")[$name];
	$answer = safe_get("answer", $_POST);
	if (!$answer) exit("no answer supplied");

	if (array_key_exists("correct", $values)
	and is_array($values["correct"])
	and array_key_exists("expr", $values)
	and is_string($values["expr"])) {
		$value = compare_syntax3($values["expr"], $answer, nano_dfdict(), true);
		if ($value === null) {
			echo "false";
		} else {
			echo "true";
		}
	} else {
		if (array_key_exists("correct", $values)
		and array_key_exists("acceptable", $values)
		and is_array($values["correct"])
		and is_array($values["acceptable"])) {
			$also = $values["correct"];
			$correct = $values["correct"];
			$values = $values["acceptable"];
		} else $correct = ($also=$values);
		$score = FALSE;
		foreach ($values as $value) {
			if (!$score and compare_strings($answer,$value,$flags)) {
				$score = TRUE;
				break;
			}
		}
		if ($score) echo "true";
		else echo "false";
	}
?>
