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
	$flags = ["unescaped"=>TRUE,"matchall"=>TRUE];
	foreach (quiz_getvalue("current_answer") as $name=>$values) {
		$answer = $_POST[$name];

		if (array_key_exists("correct", $values)
		and is_array($values["correct"])
		and array_key_exists("expr", $values)
		and is_string($values["expr"])) {
			$value = compare_syntax($values["expr"], $answer, $flags);
			if ($value === null) {
				$score = FALSE;
				$correct = $values["correct"];
			} else {
				$score = TRUE;
				$_also = $also = $values["correct"];
				foreach ($_also as $key => $v2) {
					if (compare_strings($value,$v2,$flags))
						unset($also[$key]);
				}
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
			#error_log($answer2);
			foreach ($values as $value) {
				#error_log($_val);
				if (!$score and compare_strings($answer,$value,$flags)) {
					$score = TRUE;
					$_also = $also;
					foreach ($_also as $key => $v2) {
					if (compare_strings($value,$v2,$flags))
						unset($also[$key]);
					}
					break;
				}
			}
		}
		$result[$name] = array_merge([$score, $score?$value:$answer], array_values($score?$also:$correct));
		$out_of += 1; if ($score) $subscore += 1;
	}
	$result["subscore"] = $subscore;
	$result["out_of"] = $out_of;
	
	quiz_setvalue("current_answer", NULL);
	quiz_setvalue("score",$subscore+quiz_getvalue("score"));
	quiz_setvalue("out_of",$out_of+quiz_getvalue("out_of"));
	if (CURRENTQUIZ() !== NULL) {
		CURRENTQUIZ()->add_score($subscore,$out_of);
		CURRENTQUIZ()->add_result($result);
	}
	print json_encode($result);

/*
	$answer = $_POST;
	$correct = array_key_exists("current_answer", $_SESSION) ? $_SESSION["current_answer"] : NULL;
	if ($correct) {
		if (count($correct) == 1) {
			$answer = $answer[array_keys($correct)[0]];
			$correct = $correct[array_keys($correct)[0]];
			if ($answer == $correct) {
				echo "Correctum est $answer! Bene";
			} else {
				echo "Minime, non est $answer, esset $correct.";
			}
		}
		unset($_SESSION["current_answer"]);
	}
*/
?>
