<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/quiz/common.php');
include_once('quiz_types.php');
global $quiz_types;

const QUIZ_MAX_RECURSE = 10;

$type = safe_get(quiz_getvalue("type"),$quiz_types);
if (!is_array($type)) exit("bad quiz type, or session expired");
$type_options = $type["options"];
if (is_callable($type_options))
	$type_options = $type_options();

$options = []; foreach ($type_options as $opt)
	if (!array_key_exists("condition", $opt) or $opt["condition"]())
		$options[] = $opt;

$idx = NULL; $i=0;
while ($idx === NULL or !array_key_exists($idx, $options)) {
	if (!quiz_getvalue("options_n") or quiz_getvalue("options_n") === true) {
		$opts = array_keys($options);
		if (!safe_get("no_shuffle", $type))
			shuffle($opts);
		else $opts = array_reverse($opts);
		quiz_setvalue("options_n", $opts);
	}
	$idx = quiz_poplist("options_n");
	if (CURRENTQUIZ() !== NULL)
		CURRENTQUIZ()->set_options_n(quiz_getvalue("options_n"));
	if ($i++ > 12) exit("ran out of indices");
}

$quiz = $options[$idx];
if (is_callable($quiz))
	$quiz = $quiz();

$try = NULL; $recurse=0; $reason=NULL;
$try = function() use($quiz,&$try,&$recurse,&$reason) {
	global $OP_MATCHING_CHOICES;
	global $OP_MULTIPLE_CHOICE;
	global $OP_MATCHING;
	global $OP_USER_INPUT;
	global $OP_USER_PARAGRAPH;
	if ($recurse >= QUIZ_MAX_RECURSE) {echo json_encode([['text', '<span class="jquiz-incorrect" style="font-size: 8px">could not complete sentence '.$reason.'</span>']]); return;}
	$recurse += 1;
	$selections = $quiz["selections"];
	$sentence = do_template($quiz["sentence"], NULL, $selections, $reason);
	if ($sentence === NULL) {
		return $try();
	}
	$result_json = [];
	$wrap = safe_get("wrap", $quiz);
	if ($wrap)
		$result_json[] = ["wrap",$wrap];
	if (array_key_exists("help", $quiz)) {
		$help = $quiz["help"];
		if (is_callable($help)) {
			$help = $help($selections, defaultDB());
		}
		$result_json[] = ["help", $help];
	}
	$answers = [];
	$_mini = []; $allow_space = FALSE;
	$n = 0;
	quiz_setvalue("current_answer", []);
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

			quiz_addkey("current_answer","answer$n", $correct);

			if ($stop) return $try();
			if ($shuffle) shuffle($results);
			if (count($results) !== count(vec_norm(array_unique($results)))) {
				$reason = "results were not unique (".implode(",", $results).")";
				return $try();
			}
			foreach ($results as &$r)
				$r = format_word($r,$lang);

			$type = ($word === $OP_MULTIPLE_CHOICE) ? "select" : "matching-row";
			if ($word === $OP_MULTIPLE_CHOICE) {
				$result_json[] = [$type, "answer$n", $quiz["choices$n-tooltip"], $results];
			} else {
				/*$question = $quiz["choices$n-question"];
				$question = do_pick($question, NULL, $selections, $reason);
				$result_json[] = ["matching-row", "answer$n", $quiz["choices$n-tooltip"], $results, $question];
				/*/
				$result_json[] = [$type, "answer$n", $quiz["choices$n-tooltip"], $results];
				/**/
			}
			$n += 1;

		} elseif ($word === $OP_MATCHING) {
			$refresh(1);
			$shuffle = true;
			$stop = FALSE;
			$choices = $quiz["matching$n"];
			$lang = safe_get("matching$n-language", $quiz);
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

			quiz_addkey("current_answer","answer$n", [implode("\n", array_map("format_word", $answers))]);

			if ($shuffle) shuffle($answers);
			if (count($answers) !== count(vec_norm(array_unique($answers)))) {
				$reason = "results were not unique (".implode(",", $results).")";
				return $try();
			}
			foreach ($answers as &$r)
				$r = format_word($r,$lang);
			$result_json[] = ["matching", "answer$n", $quiz["matching$n-tooltip"], $left, $answers];
			$n += 1;

		} elseif ($word === $OP_USER_INPUT || $word === $OP_USER_PARAGRAPH) {
			$refresh(1);
			$stop = FALSE;
			$answers = $quiz["answer$n"];
			$lang = safe_get("answer$n-language", $quiz);
			if (is_callable($answers)) {
				$answers = $answers($selections, defaultDB());
			}
			if (!is_array($answers)) {
				$reason = "answers were not in an array";
				return $try();
			}
			$process = function($answer) use(&$selections,&$stop,&$reason,&$correct,$lang) {
				if ($stop) return;
				$ret = do_pick($answer, NULL, $selections, $reason);
				if ($ret === NULL)
					$stop = TRUE;
				else $ret = format_word($ret,$lang);
				return $ret;
			};
			if (array_key_exists("correct", $answers)
			and array_key_exists("acceptable", $answers)
			and is_array($answers["correct"])
			and is_array($answers["acceptable"])) {
				$results = [
					"correct"=>array_map($process, $answers["correct"]),
					"acceptable"=>array_map($process, $answers["acceptable"])
				];
			} else if (array_key_exists("correct", $answers)
			and is_array($answers["correct"])
			and array_key_exists("expr", $answers)
			and is_string($answers["expr"])) {
				$results = [
					"correct"=>array_map($process, $answers["correct"]),
					"expr"=>do_pick($answers["expr"], NULL, $selections, $reason)
				];
			} else
				$results = array_map($process, $answers);
			if ($stop) {
				return $try();
			}

			quiz_addkey("current_answer","answer$n",$results);

			$result_json[] = [$word === $OP_USER_INPUT?"input":"paragraph", "answer$n", $quiz["answer$n-tooltip"], $quiz["answer$n-tooltip"], $lang];
			$n += 1;

		} else $_mini[] = $word;
	}
	$refresh();
	#var_dump($selections, $result_json, $answers);
	if (CURRENTQUIZ() !== NULL) {
		CURRENTQUIZ()->add_question($result_json);
		CURRENTQUIZ()->set_answers(quiz_getvalue("current_answer"));
	}
	echo json_encode($result_json);
};
$try();
?>
