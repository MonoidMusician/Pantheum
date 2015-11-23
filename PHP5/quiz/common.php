<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');
sro('/PHP5/lib/PHPLang/sql_stmts.php');
global $suid;

function quiz_setvalue($k,$v) {
	return $_SESSION[$k] = $v;
}
function quiz_getvalue($k) {
	return safe_get($k,$_SESSION);
}
function quiz_poplist($k) {
	return array_pop($_SESSION[$k]);
}
function quiz_appendlist($k,$v) {
	$_SESSION[$k][] = $v;
}
function quiz_addkey($k,$i,$v) {
	return $_SESSION[$k][$i] = $v;
}
function quiz_auth() {
	global $suid;
	return $suid == 14;
}

##
# "Operator"-style r/wpunctuation etc.
#
class _QUIZ
{
	function __construct($id) {
		$this->_id = $id;
	}
	function id() {
		return $this->_id;
	}
	function last() {
		global $sql_stmts;
		$last = NULL;
		sql_getone(sql_stmt("quiz_id->last"), $last, ["i", &$this->_id]);
		return $last;
	}
	function set_last($last) {
		sql_setone(sql_stmt("quiz_id->last="), $last, ["i", &$this->_id]);
	}
	function type() {
		global $sql_stmts;
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
		global $sql_stmts;
		$completed = NULL;
		sql_getone(sql_stmt("quiz_id->completed"), $completed, ["i", &$this->_id]);
		return $completed;
	}
	function time_started() {
		global $sql_stmts;
		$time_started = NULL;
		sql_getone(sql_stmt("quiz_id->time_started"), $time_started, ["i", &$this->_id]);
		return $time_started;
	}
	function time_finished() {
		global $sql_stmts;
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
		global $sql_stmts;
		$user_id = NULL;
		sql_getone(sql_stmt("quiz_id->user_id"), $user_id, ["i", &$this->_id]);
		return $user_id;
	}
	function username() {
		global $sql_stmts;
		$username = NULL;
		sql_getone(sql_stmt("quiz_id->username"), $username, ["i", &$this->_id]);
		return $username;
	}
	function assert_authorized() {
		if (!$this->is_authorized()) return die("You are not authorized to do this quiz");
		return $this;
	}
	function add_question($question) {
		global $sql_stmts;
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
		global $sql_stmts;
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
		global $sql_stmts;
		$options_n = NULL;
		sql_getone(sql_stmt("quiz_id->options_n"), $options_n, ["i", &$this->_id]);
		if (!$options_n) return TRUE;
		return json_decode($options_n);
	}
	function set_options_n($options_n) {
		global $sql_stmts;
		$options_n = json_encode($options_n);
		sql_set(sql_stmt("quiz_id->options_n="), $options_n, ["i", &$this->_id]);
	}
	function add_score($right,$total) {
		global $sql_stmts;
		sql_exec(sql_stmt("add score"), ["iii", $right, $total, &$this->_id]);
		return TRUE;
	}
	function set_score($right,$total) {
		global $sql_stmts;
		sql_exec(sql_stmt("set score"), ["iii", $right, $total, &$this->_id]);
		return TRUE;
	}
	function answers($answers=NULL) {
		global $sql_stmts;
		$answers = NULL;
		sql_getone(sql_stmt("quiz_id->answers"), $answers, ["i", &$this->_id]);
		if (!$answers) return NULL;
		return json_decode($answers,true);
	}
	function set_answers($answers=NULL) {
		global $sql_stmts;
		if ($answers !== NULL) $answers = json_encode($answers);
		sql_set(sql_stmt("quiz_id->answers="), $answers, ["i", &$this->_id]);
		return TRUE;
	}
	function score() {
		global $sql_stmts;
		$score = NULL;
		sql_getone(sql_stmt("quiz_id->score"), $score, ["i", &$this->_id]);
		return $score;
	}
	function out_of() {
		global $sql_stmts;
		$out_of = NULL;
		sql_getone(sql_stmt("quiz_id->out_of"), $out_of, ["i", &$this->_id]);
		return $out_of;
	}
	function percentage() {
		if (!$this->out_of()) return 0;
		return round($this->score() / $this->out_of() * 100);
	}
	function finish() {
		global $sql_stmts;
		sql_exec(sql_stmt("finish quiz"), ["i", &$this->_id]);
		return TRUE;
	}
	function delete() {
		global $sql_stmts;
		sql_exec(sql_stmt("quiz_id->delete from quizzes"), ["i", &$this->_id]);
		return TRUE;
	}
	function data() {
		global $sql_stmts;
		$questions = NULL;
		$results = NULL;
		sql_getone(sql_stmt("quiz_id->questions"), $questions, ["i", &$this->_id]);
		if (!$questions) $questions = "[]";
		$questions = json_decode($questions);
		sql_getone(sql_stmt("quiz_id->results"), $results, ["i", &$this->_id]);
		if (!$results) $results = "[]";
		$results = json_decode($results);
		return ["questions"=>$questions,"results"=>$results,"last"=>$this->last(),"score"=>$this->score(),"out_of"=>$this->out_of(),"completed"=>$this->completed()];
	}
}



function ISQUIZ($obj) {
	return $obj instanceof _QUIZ;
}
function QUIZ($id) {
	return new _QUIZ($id);
}
function NEWQUIZ($type,$last) {
	global $sql_stmts, $suid;
	if (!$suid) return NULL;
	sql_exec(sql_stmt("user_id,type,last->new in quizzes"), ["isi", $suid, $type, $last]);
	$id = NULL;
	sql_getone(sql_stmt("user_id->last quiz_id"), $id, ["i",$suid]);
	if ($id !== NULL) {
		quiz_setvalue("current_quiz_id", $id);
		return QUIZ($id);
	}
}
function CURRENTQUIZ() {
	global $suid;
	if ($suid and quiz_getvalue("current_quiz_id")) {
		$q = QUIZ(quiz_getvalue("current_quiz_id"));
		if (!$q->is_authorized())
			quiz_delvalue("current_quiz_id");
		else return $q;
	}
	return NULL;
}
?>
