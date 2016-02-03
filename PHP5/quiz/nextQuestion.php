<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/quiz/common.php');
include_once('quiz_types.php');
include_once('QuizType.php');
global $quiz_types;

const QUIZ_MAX_RECURSE = 10;

$type = safe_get(quiz_getvalue("type"),$quiz_types);
if (is_array($type)) $type = new MultiCompatQuizType($type);
if (!$type instanceof QuizType)
	exit("bad quiz type, or session expired");

$type->merge_selections(quiz_getvalue("selections"));

$idx = NULL; $i=0; $quiz = NULL;
while ($idx === NULL || ($quiz = $type->get_option($idx)) === NULL) {
	if (!quiz_getvalue("options_n") or quiz_getvalue("options_n") === true) {
		$opts = $type->get_options_n();
		if (!$type->no_shuffle)
			shuffle($opts);
		else $opts = array_reverse($opts);
		quiz_setvalue("options_n", $opts);
	}
	$idx = quiz_poplist("options_n");
	if (CURRENTQUIZ() !== NULL)
		CURRENTQUIZ()->set_options_n(quiz_getvalue("options_n"));
	if ($i++ > 12) exit("ran out of indices");
}

$quiz->merge_selections(quiz_getvalue("selections"));

$try = NULL; $recurse=0; $reason=NULL;
$try = function() use($quiz,&$try,&$recurse,&$reason) {
	global $OP_MATCHING_CHOICES;
	global $OP_MULTIPLE_CHOICE;
	global $OP_MATCHING;
	global $OP_USER_INPUT;
	global $OP_USER_PARAGRAPH;
	if ($recurse >= QUIZ_MAX_RECURSE) {echo json_encode([['text', '<span class="jquiz-incorrect" style="font-size: 8px">could not complete sentence '.$reason.'</span>']]); return;}
	$recurse += 1;
	$selections = &$quiz->get_selections();
	$sentence = do_template($quiz->get_sentence(), NULL, $selections, $reason);
	#error_log(var_export($sentence,1));
	if ($sentence === NULL) {
		return $try();
	}
	$result_json = [];
	$wrap = $quiz->get_wrap();
	if ($wrap)
		$result_json[] = ["wrap",$wrap];
	$help = $quiz->get_help();
	if ($help)
		$result_json[] = ["help", $help];
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
	$dopick = function($answer,$mode=TRUE) use(&$reason,&$selections) {
		return $mode ? do_pick($answer, NULL, $selections, $reason) : _process_value($answer, $selections, defaultDB());
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
			$choices = $quiz->get_other("choices$n");
			$lang = safe_get("choices$n-language", $quiz->get_others());
			if (is_callable($choices)) {
				$choices = $choices($selections, defaultDB());
			}
			if (!is_array($choices)) {
				$reason = "choices were not in an array (".gettype($choices).")";
				return $try();
			}
			if (safe_get("choices$n-no-shuffle",$quiz->get_others()) or safe_get("no_shuffle",$choices)) {
				$shuffle = false;
				unset($choices["no_shuffle"]);
			}
			$results = array_map(function($answer,$key) use($dopick,&$selections,&$stop,&$reason,&$correct,$lang) {
				if ($stop) return;
				$ret = $dopick($answer,TRUE);
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
			$result_json[] = [$type, "answer$n", $quiz->get_other("choices$n-tooltip"), $results];
			$n += 1;

		} elseif ($word === $OP_MATCHING) {
			$refresh(1);
			$shuffle = true;
			$stop = FALSE;
			$choices = $quiz->get_other("matching$n");
			$lang = safe_get("matching$n-language", $quiz->get_others());
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
			$result_json[] = ["matching", "answer$n", $quiz->get_other("matching$n-tooltip"), $left, $answers];
			$n += 1;

		} elseif ($word === $OP_USER_INPUT || $word === $OP_USER_PARAGRAPH) {
			$refresh(1);
			$stop = FALSE;
			$answers = $quiz->get_other("answer$n");
			$lang = safe_get("answer$n-language", $quiz->get_others());
			if (is_callable($answers)) {
				$answers = $answers($selections, defaultDB());
			}
			if (!is_array($answers)) {
				$reason = "answers were not in an array";
				return $try();
			}
			$process = function($answer) use($dopick,&$stop,&$correct,$lang) {
				if ($stop) return;
				$ret = $dopick($answer, TRUE);
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
					"expr"=>$dopick($answers["expr"], TRUE)
				];
			} else
				$results = array_map($process, $answers);
			if ($stop) {
				return $try();
			}

			quiz_addkey("current_answer","answer$n",$results);

			$result_json[] = [$word === $OP_USER_INPUT?"input":"paragraph", "answer$n", $quiz->get_other("answer$n-tooltip"), $quiz->get_other("answer$n-tooltip"), $lang];
			$n += 1;

		} else if ($word instanceof Answers) {
			$refresh(1);
			$answers = $word->process($dopick,$reason);
			if ($answers === NULL) return $try();
			quiz_addkey("current_answer", $answers->name, $answers->serialize_PHP());
			$result_json[] = $answers->serialize_JSON();
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
