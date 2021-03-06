<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/string.php');
sro('/PHP5/quiz/common.php');

class QuizType {
	public $name;
	public $user_selections;
	public $help;
	public $no_shuffle = false;
	public $selections = [];
	public $sentence   = [];
	public $answers    = [];
	public $wrap       = [];
	public $others     = [];
	public $hints      = [];
	function get_options_n() {return [0];}
	function get_option($n) {return $this;}
	function merge_selections($vec) {
		foreach ($vec as $k=>$v)
			$this->selections[$k] = $v;
	}
	function &get_selections() {return $this->selections;}
	function get_wrap() {return $this->wrap;}
	function get_help() {return $this->help;}
	function get_sentence() {return $this->sentence;}
	function get_answers() {return $this->answers;}
	function pop_answer() {return array_pop($this->answers);}
	function get_others() {return $this->others;}
	function get_other($k) {return $this->others[$k];}
	function get_hints() {return $this->hints;}
}

class MultiQuizType extends QuizType {
	public $options = [];
	function get_options_n() {return array_keys($this->options);}
	function get_option($n) {
		$o = $this->options[$n];
		if (is_callable($o))
			$o = $o();
		return $o;
	}
}

class CompatQuizType extends QuizType {
	function __construct($old) {
		$this->name = safe_get("name", $old);
		$this->help = safe_get("help", $old);
		$this->selections = safe_get("selections", $old);
		if (!$this->selections) $this->selections = [];
		$this->sentence = safe_get("sentence", $old);
		$this->others = $old;
		$this->no_shuffle = safe_get("no_shuffle", $old);
	}
	private function _process_value($v) {
		return _process_value($v, $this->selections, defaultDB());
	}
	function get_sentence() {
		return $this->_process_value($this->sentence);
	}
	function get_wrap() {
		return $this->_process_value($this->wrap);
	}
	function get_help() {
		return $this->_process_value($this->help);
	}
	function &get_selections() {
		return $this->selections;
	}
}

class MultiCompatQuizType extends MultiQuizType {
	function __construct($old) {
		$options = $old["options"];
		if (is_callable($options)) $options = $options();
		foreach ($options as $opt) {
			if (is_callable($opt)) $opt = $opt();
			if (!array_key_exists("condition", $opt) || $opt["condition"]())
				$this->options[] = $opt;
		}
		$this->no_shuffle = safe_get("no_shuffle", $old);
	}
	function get_option($n) {
		$o = $this->options[$n];
		if (is_callable($o))
			$o = $o();
		return is_array($o) ? new CompatQuizType($o) : $o;
	}
}
