<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/string.php');
sro('/PHP5/lib/PHPLang/misc.php');
sro('/PHP5/quiz/common.php');

class Answers extends _OP {
	public $space_before = true;
	public $space_after  = true;
	public $text = "[question]";
	public $language;
	public $tooltip;
	public $name;
	public $correct;
	public $acceptable;
	public static $flags = ["unescaped"=>TRUE,"matchall"=>TRUE];
	function process($dopick, &$reason) {
		return $this;
	}
	function get_correct() {
		return $this->correct;
	}
	function is_correct($input) {
		foreach ($this->get_correct() as $value) {
			if (!$score and compare_strings($input,$value,$flags))
				return TRUE;
		}
		return FALSE;
	}
	function get_acceptable() {
		return $this->acceptable;
	}
	function is_acceptable($input) {
		foreach ($this->get_acceptable() as $value) {
			if (!$score and compare_strings($input,$value,$flags))
				return TRUE;
		}
		return FALSE;
	}
	function serialize_PHP() {
		
	}
	function serialize_JSON() {
		
	}
	function format_word($w) {
		return format_word($w, $this->language);
	}
}

function name_answer_lang_tool($name, $answers, $language, $tooltip) {
	$answers->name = $name;
	$answers->language = $language;
	$answers->tooltip = $tooltip;
	return $answers;
}


class MultipleChoice extends Answers {
	public $no_shuffle;
	function __construct($init,$no_shuffle=FALSE) {
		$this->init = $init;
		$this->no_shuffle = $no_shuffle;
	}
	function process($dopick, &$reason) {
		$this->answers = [];
		$this->correct = [];
		foreach ($dopick($this->init,FALSE) as $k=>$v) {
			$r = $dopick($v,TRUE);
			if ($r === NULL) return;
			$r = $this->format_word($r);
			$this->answers[] = $r;
			if ($k === "correct" or (
				is_array($v) and
				array_key_exists("correct", $v) and
				!!$dopick($v["correct"],FALSE)
			))
				$this->correct[] = $r;
		}
		if (count($this->answers) !== count(vec_norm(array_unique($this->answers)))) {
			$reason = "answers were not unique (".implode(",", $this->answers).")";
			return;
		}
		return $this;
	}
	function get_answers() {
		$a = $this->answers;
		if (!$this->no_shuffle)
			shuffle($a);
		return $a;
	}
	function serialize_JSON() {
		return ["select", $this->name, $this->tooltip, $this->get_answers()];
	}
	function serialize_PHP() {
		return $this->get_correct();
	}
}


class FreeResponse extends Answers {
	function __construct($correct) {
		$this->init = $correct;
	}
	function process($dopick, &$reason) {
		$this->correct = [];
		foreach ($dopick($this->init,FALSE) as $k=>$v) {
			$r = $dopick($v,TRUE);
			if ($r === NULL) return;
			$r = $this->format_word($r);
			$this->correct[] = $r;
		}
		return $this;
	}
	function serialize_JSON() {
		return ["input", $this->name, $this->tooltip, $this->tooltip, $this->language];
	}
	function serialize_PHP() {
		return $this->get_correct();
	}
}

class FreeResponseAcceptable extends FreeResponse {
	function __construct($correct, $acceptable) {
		if (is_string($correct)) $correct = [$correct];
		if (is_string($acceptable)) $acceptable = [$acceptable];
		$this->init = [$correct, $acceptable];
	}
	function process($dopick, &$reason) {
		$this->correct = [];
		$this->acceptable = [];
		foreach ($dopick($this->init[0],FALSE) as $k=>$v) {
			$r = $dopick($v,TRUE);
			if ($r === NULL) return;
			$r = $this->format_word($r);
			$this->correct[] = $r;
		}
		foreach ($dopick($this->init[1],FALSE) as $k=>$v) {
			$r = $dopick($v,TRUE);
			if ($r === NULL) return;
			$r = $this->format_word($r);
			$this->acceptable[] = $r;
		}
		return $this;
	}
	function serialize_PHP() {
		return [
			"correct" => $this->get_correct(),
			"acceptable" => $this->get_acceptable(),
		];
	}
}

class FreeResponseExpr extends FreeResponse {
	function __construct($correct, $expr) {
		if (is_string($correct)) $correct = [$correct];
		$this->init = [$expr, $correct];
	}
	function process($dopick, &$reason) {
		$this->correct = [];
		$this->expr = $dopick($this->init[0],FALSE);
		foreach ($dopick($this->init[1],FALSE) as $k=>$v) {
			$r = $dopick($v,TRUE);
			if ($r === NULL) return;
			$r = $this->format_word($r);
			$this->correct[] = $r;
		}
		return $this;
	}
	function is_acceptable($input) {
		return compare_syntax3($this->expr, $input, nano_dfdict(), true) !== NULL;
	}
	function serialize_PHP() {
		return [
			"correct" => $this->get_correct(),
			"expr" => $this->expr,
		];
	}
}

class FreeParagraph extends FreeResponse {
	function serialize_JSON() {
		return ["paragraph", $this->name, $this->tooltip, $this->tooltip, $this->language];
	}
}
class FreeParagraphAcceptable extends FreeResponseAcceptable {
	function serialize_JSON() {
		return ["paragraph", $this->name, $this->tooltip, $this->tooltip, $this->language];
	}
}
class FreeParagraphExpr extends FreeResponseExpr {
	function serialize_JSON() {
		return ["paragraph", $this->name, $this->tooltip, $this->tooltip, $this->language];
	}
}

