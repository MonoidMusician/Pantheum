<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
include_once('quiz_types.php');
global $quiz_types;

const QUIZ_MAX_RECURSE = 1;

$type = $quiz_types[$_SESSION["quiz_type"]];
$options = []; foreach ($type["options"] as $opt)
	if (!array_key_exists("condition", $opt) or $opt["condition"]())
		$options[] = $opt;
//$quiz = choose_one($options);
$idx = NULL; $i=0;
while ($idx === NULL or !array_key_exists($idx, $options)) {
	if (!safe_get("options_n",$_SESSION) or $_SESSION["options_n"] === true) {
		$_SESSION["options_n"] = array_keys($options);
		shuffle($_SESSION["options_n"]);
	}
	$idx = array_pop($_SESSION["options_n"]);
	if ($i++ > 12) exit("ran out of indices");
}
$quiz = $options[$idx];
$try = NULL; $recurse=0; $reason=NULL;
$try = function() use($quiz,&$try,&$recurse,&$reason) {
	global $OP_MATCHING_CHOICES;
	global $OP_MULTIPLE_CHOICE;
	global $OP_MATCHING;
	global $OP_USER_INPUT;
	if ($recurse >= QUIZ_MAX_RECURSE) {echo json_encode([['text', '<span class="jquiz-incorrect" style="font-size: 7px">could not complete sentence '.$reason.'</span>']]); return;}
	$recurse += 1;
	$selections = $quiz["selections"];
	$sentence = do_template($quiz["sentence"], NULL, $selections, $reason);
	if ($sentence === NULL) {
		return $try();
	}
	$result_json = [];
	if (array_key_exists("help", $quiz)) {
		$help = $quiz["help"];
		if (is_callable($help)) {
			$help = $help($selections, defaultDB());
		}
		$result_json[] = ["text", "<span class='help'>$help</span><br><br>"];
	}
	$answers = [];
	$_mini = []; $allow_space = FALSE;
	$n = 0;
	$_SESSION["current_answer"] = [];
	$refresh = function($finish=false) use(&$result_json,&$_mini,&$allow_space) {
		if ($_mini) {
			$result_json[] = ["text", serialize_sentence_part($_mini,$allow_space).(($finish and $allow_space)?" ":"")];
			$_mini = [];
		}
	};
	foreach ($sentence as $word) {
		if (ISHTML($word)) {
			$refresh();
			$result_json[] = ["html", $word->text];
		} elseif ($word === $OP_MULTIPLE_CHOICE or $word === $OP_MATCHING_CHOICES) {
			$refresh(1);
			$shuffle = true;
			$stop = FALSE;
			$correct = [];
			$choices = $quiz["choices$n"];
			$lang = safe_get("choices$n-language", $quiz);
			if (is_callable($choices)) {
				$choices = $choices($selections, defaultDB());
			}
			if (!is_array($choices)) {
				$reason = "choices were not in an array (".gettype($choices).")";
				return $try();
			}
			if (safe_get("choices$n-no-shuffle",$quiz) or safe_get("no_shuffle",$choices)) {
				$shuffle = false;
				unset($choices["no_shuffle"]);
			}
			$results = array_map(function($answer,$key) use(&$selections,&$stop,&$reason,&$correct,$lang) {
				if ($stop) return;
				if (is_callable($answer)) $answer = _process_value($answer,$selections,defaultDB());
				$ret = do_pick($answer, NULL, $selections, $reason);
				if ($ret === NULL)
					$stop = TRUE; else
				if ($key === "correct" or (
					is_array($answer) and
					array_key_exists("correct", $answer) and
					!!_process_value($answer["correct"], $selections, defaultDB())
				))
					$correct[] = format_word($ret,$lang);
				return $ret;
			}, $choices, array_keys($choices));
			$_SESSION["current_answer"]["answer$n"] = $correct;
			if ($stop) {
				return $try();
			}
			if ($shuffle) shuffle($results);
			if (count($results) !== count(vec_norm(array_unique($results)))) {
				$reason = "results were not unique (".implode(",", $results).")";
				return $try();
			}
			foreach ($results as &$r)
				$r = format_word($r,$lang);
			$type = ($word === $OP_MULTIPLE_CHOICE) ? "select" : "matching-row";
			if ($word === $OP_MULTIPLE_CHOICE) {
				$result_json[] = ["select", "answer$n", $quiz["choices$n-tooltip"], $results];
			} else {
				/*$question = $quiz["choices$n-question"];
				$question = do_pick($question, NULL, $selections, $reason);
				$result_json[] = ["matching-row", "answer$n", $quiz["choices$n-tooltip"], $results, $question];
				/*/
				$result_json[] = ["matching-row", "answer$n", $quiz["choices$n-tooltip"], $results];
				/**/
			}
			$n += 1;
		} elseif ($word === $OP_MATCHING) {
			$refresh(1);
			$shuffle = true;
			$stop = FALSE;
			$choices = $quiz["matching$n"];
			if (is_callable($choices)) {
				$choices = $choices($selections, defaultDB());
			}
			if (!is_array($choices)) {
				$reason = "matching elements were not in an array (".gettype($choices).")";
				return $try();
			}
			$left = array_map(function($answer,$key) use(&$selections,&$stop,&$reason,&$correct) {
				if ($stop) return;
				$ret = do_pick($answer, NULL, $selections, $reason);
				if ($ret === NULL)
					$stop = TRUE; else
				return $ret;
			}, $choices[0], array_keys($choices[0]));
			if ($stop) {
				return $try();
			}
			$answers = array_map(function($answer,$key) use(&$selections,&$stop,&$reason,&$correct) {
				if ($stop) return;
				$ret = do_pick($answer, NULL, $selections, $reason);
				if ($ret === NULL)
					$stop = TRUE; else
				return $ret;
			}, $choices[1], array_keys($choices[1]));
			if ($stop) {
				return $try();
			}
			$_SESSION["current_answer"]["answer$n"] = [implode("\n", array_map("format_word", $answers))];
			if ($shuffle) shuffle($answers);
			if (count($answers) !== count(vec_norm(array_unique($answers)))) {
				$reason = "results were not unique (".implode(",", $results).")";
				return $try();
			}
			foreach ($answers as &$r)
				$r = format_word($r);
			$result_json[] = ["matching", "answer$n", $quiz["matching$n-tooltip"], $left, $answers];
			$n += 1;
		} elseif ($word === $OP_USER_INPUT) {
			$refresh(1);
			$stop = FALSE;
			$answers = $quiz["answer$n"];
			if (is_callable($answers)) {
				$answers = $answers($selections, defaultDB());
			}
			if (!is_array($answers)) {
				$reason = "answers were not in an array";
				return $try();
			}
			if (array_key_exists("correct", $answers)
			and array_key_exists("acceptable", $answers)
			and is_array($answers["correct"])
			and is_array($answers["acceptable"])) {
				$results = ["correct"=>array_map(function($answer) use(&$selections,&$stop,&$reason,&$correct) {
					if ($stop) return;
					$ret = do_pick($answer, NULL, $selections, $reason);
					if ($ret === NULL)
						$stop = TRUE;
					else $ret = format_word($ret);
					return $ret;
				}, $answers["correct"]), "acceptable"=>array_map(function($answer) use(&$selections,&$stop,&$reason,&$correct) {
					if ($stop) return;
					$ret = do_pick($answer, NULL, $selections, $reason);
					if ($ret === NULL)
						$stop = TRUE;
					else $ret = format_word($ret);
					return $ret;
				}, $answers["acceptable"])];
			} else
				$results = array_map(function($answer) use(&$selections,&$stop,&$reason,&$correct) {
					if ($stop) return;
					$ret = do_pick($answer, NULL, $selections, $reason);
					if ($ret === NULL)
						$stop = TRUE;
					else $ret = format_word($ret);
					return $ret;
				}, $answers);
			$_SESSION["current_answer"]["answer$n"] = $results;
			$result_json[] = ["input", "answer$n", $quiz["answer$n-tooltip"]];
			$n += 1;
		} else $_mini[] = $word;
	}
	$refresh();
	#var_dump($selections, $result_json, $answers);
	echo json_encode($result_json);
};
$try();
?>
