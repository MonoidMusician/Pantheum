<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/lib/PHPLang/sql_stmts.php');

##
# "Operator"-style r/wpunctuation etc.
#
class _OP
{
	function __construct($arg) {
		if (count($arg) === 2)
			if (is_int($arg[0]))
				$arg = array_merge($arg, [1]);
			elseif (is_int($arg[1]))
				$arg = array_merge([1], $arg);
			else _die("bad arguments to OP()");
		elseif (count($arg) == 1)
			$arg = array_merge([1], $arg, [1]);
		if (count($arg) == 3)
			list($this->space_before, $this->text, $this->space_after) = $arg;
	}
}



function ISOP($obj) {
	return $obj instanceof _OP;
}
function OP() {
	$arg = func_get_args();
	return new _OP($arg);
}
$GLOBALS["OP_COMMA"]  = OP(0, ",");
$GLOBALS["OP_COLON"]  = OP(0, ":");
$GLOBALS["OP_LPAREN"] = OP("(", 0);
$GLOBALS["OP_RPAREN"] = OP(0, ")");
$GLOBALS["OP_LQUOTE"] = OP("“", 0);
$GLOBALS["OP_RQUOTE"] = OP(0, "”");
$GLOBALS["OP_DASH"]   = OP(0, "—", 0); # m-dash
$GLOBALS["OP_PARAGRAPH"]   = OP(0, "<br>", 0); # m-dash
$GLOBALS["OP_MATCHING_CHOICES"] = OP("[matching-choices]");
$GLOBALS["OP_MULTIPLE_CHOICE"] = OP("[multiple-choice]");
$GLOBALS["OP_MATCHING"] = OP("[matching]");
$GLOBALS["OP_USER_INPUT"] = OP("[user-input]");

##
# Simple class for coordinating random selections
# of values for a key, based on an id principle.
#
class _PICK
{
	public $exclude = [];
	function __construct($key, $id=NULL, $n=NULL, $rand=NULL) {$this->key=$key;$this->id=$id;$this->n=$n;$this->rand=$rand;}
	function rand($path=NULL) {
		if (is_array($this->key))
			$possibles = $this->key;
		elseif (ISPATH($path)) $possibles = $path->iterate($this->key);
		elseif (ISDB($path)) {
			$possibles = [];
			foreach ($path->depaths as $lang) foreach ($lang as $d) {
				#var_dump($d);
				if (array_key_exists($this->key, $d->key2values))
					$possibles = array_merge($possibles, $d->key2values[$this->key]);
			}
			$possibles = array_unique($possibles);
		}
		if ($this->exclude)
		foreach ($this->exclude as $e) {
			if(($key = array_search($e, $possibles)) !== false) {
				unset($possibles[$key]);
			}
		}
		if (is_array($this->rand)) {
			$rand = [];
			foreach ($this->rand as $k => $weight) {
				if (in_array($k, $possibles))
					$rand[array_search($k, $possibles)] = (int)($weight);
			}
		} else $rand = $this->rand;
		return choose_n_unique($possibles, $this->n, $rand);
	}
}



function ISPICK($obj) {
	return $obj instanceof _PICK;
}
function PICK() {
	$key=$id=$n=$weight=NULL;
	$arg = func_get_args();
	if ($arg and is_int($arg[0]))
	{ $n=$arg[0]; $arg=array_slice($arg,1); }
	foreach ($arg as $a) {
		if (is_array($a))
			if ($key === NULL)
				$key = $a;
			elseif ($weight === NULL)
				$weight = $a;
			else _die("extra array passed to PICK()");
		elseif (is_scalar($a))
			if ($key === NULL)
				$key = $a;
			elseif ($id === NULL)
				$id = $a;
			else _die("extra scalar passed to PICK()");
		else _die("bad type '".gettype($a)."' passed to PICK()");
	}
	if ($key === NULL) _die("bad parameters to PICK()");
	return new _PICK($key,$id,$n,$weight);
}

##
# ATTR for linking specific forms of a word.
#
class _ATTR
{
	public $_word = NULL; # defaults
	public $_value = NULL;
	function __construct($init) {
		$_key = NULL;
		$_word = NULL;
		$_value = NULL;
		if (count($init) == 1) {
			if (is_string($init[0])) {
				$_key = $init[0];
			}
		} elseif (count($init) == 2) {
			if (is_string($init[0]) and is_string($init[1]))
				list($_key, $_value) = $init;
			elseif (ISWORD($init[0]) and is_string($init[1]))
				list($_word, $_key) = $init;
		} elseif (count($init) == 2) {
			if (ISWORD($init[0]) and is_string($init[1]) and is_string($init[2]))
				list($_word, $_key, $_value) = $init;
		}
		if ($_key === NULL) _die("bad arguments");
		$this->_key = $_key;
		$this->_word = $_word;
		$this->_value = $_value;
	}
	function tag() {return $this->_key;}
	function word() {return $this->_word;}
	function value() {
		global $sql_stmts;
		if (ISWORD($this->word()) and ISSQLDB($this->word()->db())) {
			sql_getone($sql_stmts["word_id,attr_tag->attr_value"], $this->_value, ["is", $this->word()->id(), $this->tag()]);
		}
		return $this->_value;
	}
	function set($val, &$hash=NULL) {
		if ($hash === NULL) {
			$hash = $this->word();
		}
		if (ISWORD($hash))
			$hash = &$hash->attr_storage;
		return $hash[$this->tag()] = $val;
	}
	function get($hash=NULL) {
		if ($hash === NULL) {
			$hash = $this->word();
		}
		if (ISWORD($hash))
			$hash = &$hash->attr_storage;
		if (array_key_exists($this->key(), $hash))
			return $hash[$this->tag()];
	}
	function remove($hash=NULL) {
		if ($hash === NULL) {
			if (ISWORD($this->word()) and ISSQLDB($this->word()->db())) {
				_die("no implemented");
			}
			$hash = $this->word();
		}
		if ($hash === NULL) return NULL;
		$r = $hash[$this->key()];
		unset($hash[$this->key()]);
		return $r;
	}
	function move($new, $hash) {return$new.set($this->remove($hash), $hash);}
};



function ISATTR($obj) {
	return $obj instanceof _ATTR;
}
function ATTR() {
	$init = func_get_args();
	return new _ATTR($init);
}


class _HTML {
	function __construct($text) {
		$this->text = $text;
	}
}
function HTML($text) { return new _HTML($text); }
function ISHTML($obj) {
	return $obj instanceof _HTML;
}

?>
