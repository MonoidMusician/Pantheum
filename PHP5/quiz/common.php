<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');
sro('/PHP5/lib/PHPLang/sql_stmts.php');
global $suid;

function quiz_setvalue($k,$v) {
	return $_SESSION["quiz_".$k] = $v;
}
function quiz_getvalue($k) {
	return safe_get("quiz_".$k,$_SESSION);
}
function quiz_poplist($k) {
	return array_pop($_SESSION["quiz_".$k]);
}
function quiz_appendlist($k,$v) {
	$_SESSION["quiz_".$k][] = $v;
}
function quiz_addkey($k,$i,$v) {
	return $_SESSION["quiz_".$k][$i] = $v;
}
function quiz_deletekey($k) {
	unset($_SESSION["quiz_".$k]);
}
function quiz_auth() {
	global $suid;
	return $suid == 14;
}

##
# "Operator"-style r/wpunctuation etc.
#
class Quiz
{
	function __construct($id) {
		$this->_id = $id;
	}
	function id() {
		return $this->_id;
	}
	function last() {
		$last = NULL;
		sql_getone(sql_stmt("quiz_id->last"), $last, ["i", &$this->_id]);
		return $last;
	}
	function set_last($last) {
		sql_setone(sql_stmt("quiz_id->last="), $last, ["i", &$this->_id]);
	}
	function mode() {
		$mode = NULL;
		sql_getone(sql_stmt("quiz_id->mode"), $mode, ["i", &$this->_id]);
		return $mode;
	}
	function set_mode($mode) {
		sql_setone(sql_stmt("quiz_id->mode="), $mode, ["i", &$this->_id]);
	}
	function type() {
		$type = NULL;
		sql_getone(sql_stmt("quiz_id->type"), $type, ["i", &$this->_id]);
		return $type;
	}
	function name() {
		global $quiz_types;
		$type = $this->type();
		if (array_key_exists($type,$quiz_types))
			return $quiz_types[$type]["name"];
		return $type;
	}
	function completed() {
		$completed = NULL;
		sql_getone(sql_stmt("quiz_id->completed"), $completed, ["i", &$this->_id]);
		return $completed;
	}
	function time_started() {
		$time_started = NULL;
		sql_getone(sql_stmt("quiz_id->time_started"), $time_started, ["i", &$this->_id]);
		return $time_started;
	}
	function time_finished() {
		$time_finished = NULL;
		sql_getone(sql_stmt("quiz_id->time_finished"), $time_finished, ["i", &$this->_id]);
		return $time_finished;
	}
	function is_authorized() {
		global $sql_stmts, $suid;
		if (quiz_auth()) return TRUE;
		$user_id = $this->user_id();
		if ($user_id !== NULL and $user_id == $suid) return TRUE;
		return FALSE;
	}
	function user_id() {
		$user_id = NULL;
		sql_getone(sql_stmt("quiz_id->user_id"), $user_id, ["i", &$this->_id]);
		return $user_id;
	}
	function username() {
		$username = NULL;
		sql_getone(sql_stmt("quiz_id->username"), $username, ["i", &$this->_id]);
		return $username;
	}
	function assert_authorized() {
		if (!$this->is_authorized()) return die("You are not authorized to do this quiz");
		return $this;
	}
	function add_question($question) {
		$questions = NULL;
		sql_getone(sql_stmt("quiz_id->questions"), $questions, ["i", &$this->_id]);
		if (!$questions) $questions = "[]";
		$questions = json_decode($questions);
		$questions[] = $question;
		$questions = json_encode($questions);
		sql_set(sql_stmt("quiz_id->questions="), $questions, ["i", &$this->_id]);
		return TRUE;
	}
	function add_result($result) {
		$results = NULL;
		sql_getone(sql_stmt("quiz_id->results"), $results, ["i", &$this->_id]);
		if (!$results) $results = "[]";
		$results = json_decode($results);
		$results[] = $result;
		$results = json_encode($results);
		sql_set(sql_stmt("quiz_id->results="), $results, ["i", &$this->_id]);
		return TRUE;
	}
	function options_n() {
		$options_n = NULL;
		sql_getone(sql_stmt("quiz_id->options_n"), $options_n, ["i", &$this->_id]);
		if (!$options_n) return TRUE;
		return json_decode($options_n);
	}
	function set_options_n($options_n) {
		$options_n = json_encode($options_n);
		sql_set(sql_stmt("quiz_id->options_n="), $options_n, ["i", &$this->_id]);
	}
	function pop_option() {
		$options_n = $this->options_n();
		$val = array_pop($options_n);
		$this->set_options_n($options_n);
		return $val;
	}
	function add_score($right,$total) {
		sql_exec(sql_stmt("add score"), ["iii", $right, $total, &$this->_id]);
		return TRUE;
	}
	function set_score($right,$total) {
		sql_exec(sql_stmt("set score"), ["iii", $right, $total, &$this->_id]);
		return TRUE;
	}
	function answers() {
		$answers = NULL;
		sql_getone(sql_stmt("quiz_id->answers"), $answers, ["i", &$this->_id]);
		if (!$answers) return NULL;
		return json_decode($answers,true);
	}
	function set_answers($answers=NULL) {
		if ($answers !== NULL) $answers = json_encode($answers);
		sql_set(sql_stmt("quiz_id->answers="), $answers, ["i", &$this->_id]);
		return TRUE;
	}
	function selections() {
		$selections = NULL;
		sql_getone(sql_stmt("quiz_id->selections"), $selections, ["i", &$this->_id]);
		if (!$selections) return NULL;
		return json_decode($selections,true);
	}
	function set_selections($selections=NULL) {
		if ($selections !== NULL) $selections = json_encode($selections);
		sql_set(sql_stmt("quiz_id->selections="), $selections, ["i", &$this->_id]);
		return TRUE;
	}
	function score() {
		$score = NULL;
		sql_getone(sql_stmt("quiz_id->score"), $score, ["i", &$this->_id]);
		return $score;
	}
	function out_of() {
		$out_of = NULL;
		sql_getone(sql_stmt("quiz_id->out_of"), $out_of, ["i", &$this->_id]);
		return $out_of;
	}
	function percentage() {
		if (!$this->out_of()) return 0;
		return round($this->score() / $this->out_of() * 100);
	}
	function finish() {
		sql_exec(sql_stmt("finish quiz"), ["i", &$this->_id]);
		return TRUE;
	}
	function delete() {
		sql_exec(sql_stmt("quiz_id->delete from quizzes"), ["i", &$this->_id]);
		return TRUE;
	}
	function all_hints() {
		$hints = NULL;
		sql_getone(sql_stmt("quiz_id->hints"), $hints, ["i", &$this->_id]);
		if (!$hints) return [];
		return json_decode($hints);
	}
	function set_hints($all_hints) {
		$all_hints = json_encode($all_hints);
		sql_set(sql_stmt("quiz_id->hints"), $all_hints, ["i", &$this->_id]);
	}
	function _current_hints() {
		$hints = $this->all_hints();
		return $hints[count($hints)-1];
	}
	function current_hints() {
		return $this->_current_hints()[0];
	}
	function use_hint($n) {
		$all_hints = $this->all_hints();
		$hints = &$all_hints[count($all_hints)-1];
		if (!safe_get($n, $hints[0])) return; // hint does not exist
		$hints[1] &= 1 << $n;
		if (gmp_popcount(gmp_init($hints[1])) > $hints[2]) return; // hint already used
		$this->set_hints($all_hints);
		return $hints[0][$n];
	}
	function add_hints($hints,$max=NULL) {
		if ($max === NULL) $max = count($hints);
		$all_hints = $this->all_hints();
		$all_hints[] = [$hints,0,$max];
		$this->set_hints($all_hints);
	}
	function data() {
		$questions = NULL;
		$results = NULL;
		sql_getone(sql_stmt("quiz_id->questions"), $questions, ["i", &$this->_id]);
		if (!$questions) $questions = "[]";
		$questions = json_decode($questions);
		sql_getone(sql_stmt("quiz_id->results"), $results, ["i", &$this->_id]);
		if (!$results) $results = "[]";
		$results = json_decode($results);
		return ["questions"=>$questions,"results"=>$results,"last"=>$this->last(),"score"=>$this->score(),"out_of"=>$this->out_of(),"completed"=>$this->completed(),"mode"=>$this->mode(),"id"=>$this->id()];
	}
}
class SessionQuiz {
	function init($type, $last, $mode=NULL) {
		if ($mode === NULL) $mode = "question";
		$this->set_mode($mode);
		$this->set_type($type);
		$this->set_last($last);
		quiz_setvalue("questions", []);
		quiz_setvalue("results", []);
		quiz_setvalue("hints", []);
		quiz_setvalue("score",0);
		quiz_setvalue("out_of",0);
		quiz_setvalue("options_n", TRUE);
	}
	function id() {return NULL;}
	function type() {return quiz_getvalue("type");}
	function set_type($type) {quiz_setvalue("type",$type);}
	function last() {return quiz_getvalue("last");}
	function set_last($last) {quiz_setvalue("last",$last);}
	function mode() {return quiz_getvalue("mode");}
	function set_mode($mode) {quiz_setvalue("mode",$mode);}
	function add_question($question) {
		quiz_appendlist("questions",$question);
		return TRUE;
	}
	function add_result($result) {
		quiz_appendlist("results", $result);
		return TRUE;
	}
	function options_n() {return quiz_getvalue("options_n");}
	function set_options_n($options_n) {quiz_setvalue("options_n", $options_n);}
	function pop_option() {return quiz_poplist("options_n");}
	function add_score($right,$total) {
		$this->set_score($this->score()+$right,$this->out_of()+$total);
	}
	function set_score($right,$total) {
		quiz_setvalue("score", $right);
		quiz_setvalue("out_of", $total);
	}
	function answers() {
		return quiz_getvalue("answers");
	}
	function set_answers($answers=NULL) {
		quiz_setvalue("answers", $answers);
	}
	function selections() {
		return quiz_getvalue("selections");
	}
	function set_selections($selections=NULL) {
		quiz_setvalue("selections", $selections);
	}
	function score() {return quiz_getvalue("score");}
	function out_of() {return quiz_getvalue("out_of");}
	function percentage() {
		if (!$this->out_of()) return 0;
		return round($this->score() / $this->out_of() * 100);
	}
	function finish() {
		quiz_deletekey("mode");
		quiz_deletekey("type");
		quiz_deletekey("last");
		quiz_deletekey("questions");
		quiz_deletekey("results");
		quiz_deletekey("hints");
		quiz_deletekey("score");
		quiz_deletekey("out_of");
		quiz_deletekey("options_n");
	}
	function all_hints() {
		return $this->hints;
	}
	function set_hints($all_hints) {
		$this->hints = $all_hints;
	}
	function _current_hints() {
		return $this->hints[count($this->hints)-1];
	}
	function current_hints() {
		return $this->_current_hints()[0];
	}
	function use_hint($n) {
		$all_hints = $this->all_hints();
		$hints = &$all_hints[count($all_hints)-1];
		if (!safe_get($n, $hints[0])) return; // hint does not exist
		$hints[1] &= 1 << $n;
		if (gmp_popcount(gmp_init($hints[1])) > $hints[2]) return; // hint already used
		$this->set_hints($all_hints);
		return $hints[0][$n];
	}
	function add_hints($hints,$max=NULL) {
		if ($max === NULL) $max = count($hints);
		$all_hints = $this->all_hints();
		$all_hints[] = [$hints,0,$max];
		$this->set_hints($all_hints);
	}
	function data() {
		$questions = quiz_getvalue("questions");
		$results = quiz_getvalue("results");
		return ["questions"=>$questions,"results"=>$results,"last"=>$this->last(),"score"=>$this->score(),"out_of"=>$this->out_of(),"completed"=>FALSE,"mode"=>$this->mode(),"id"=>$this->id()];
	}
}



function ISQUIZ($obj) {
	return $obj instanceof Quiz;
}
function QUIZ($id) {
	if ($id === NULL)
			return new SessionQuiz();
	return new Quiz($id);
}
function NEWQUIZ($type,$last,$mode=NULL,$selections=NULL) {
	global $sql_stmts, $suid;
	if (!$suid) {
		$quiz = QUIZ(NULL);
		$quiz->init($type, $last, $mode, $selections);
		return NULL;
	}
	if (!$mode)
		sql_exec(sql_stmt("user_id,type,last->new in quizzes"), ["isi", $suid, $type, $last]);
	elseif (!$selections)
		sql_exec(sql_stmt("user_id,type,last,mode->new in quizzes"), ["isis", $suid, $type, $last, $mode]);
	else
		sql_exec(sql_stmt("user_id,type,last,mode,selections->new in quizzes"), ["isiss", $suid, $type, $last, $mode, json_encode($selections)]);
	$id = NULL;
	sql_getone(sql_stmt("user_id->last quiz_id"), $id, ["i",$suid]);
	if ($id !== NULL) {
		quiz_setvalue("current_quiz_id", $id);
		return QUIZ($id);
	}
}
function CURRENTQUIZ() {
	global $suid;
	if (safe_get("quiz_id", $_POST)) {
		$q = QUIZ($_POST["quiz_id"]);
		if ($q->is_authorized())
			return $q;
	} elseif (safe_get("quiz_id", $_GET)) {
		$q = QUIZ($_GET["quiz_id"]);
		if ($q->is_authorized())
			return $q;
	} elseif ($suid and quiz_getvalue("current_quiz_id")) {
		$q = QUIZ(quiz_getvalue("current_quiz_id"));
		if ($q->is_authorized())
			return $q;
		else
			quiz_delvalue("current_quiz_id");
	} elseif (quiz_getvalue("type")) {
		return QUIZ(NULL);
	}
}
?>
