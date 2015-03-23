<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

##
# Definition class.
#
class _DEFINITION
{
	private $_word_sublang = NULL;
	function __construct($db, $id, $word) {
		$this->_id = $id;
		$this->issql = ISSQLDB($db);
		if ($word === NULL) {
			$_wid = $this->_getword();
			$word = WORD($db, $_wid);
		}
		$this->_word = $word;
		if ($_fid = $this->_getpath())
			$this->_path = PATH($this->word(), $_fid);
		elseif ($word !== NULL) $this->_path = PATH($this->word());
		else $this->_path = NULL;
		return $this;
	}
	function word() { return $this->_word; }
	function path() { return $this->_path; }
	function id() { return $this->_id; }
	function set_id($id) { $this->_id = $id; }
	private function _getpath() {
		global $sql_stmts;
		$_form_tag = NULL;
		if ($this->issql and $this->_id !== NULL)
			return sql_getone($sql_stmts["def_id->form_tag"], $_form_tag, ["i", &$this->_id]); # still NULL if not found
		return $_form_tag;
	}
	private function _getword() {
		global $sql_stmts;
		$_word_id = NULL;
		if ($this->issql and $this->_id !== NULL)
			return sql_getone($sql_stmts["def_id->word_id"], $_word_id, ["i", &$this->_id]); # still NULL if not found
		return $_word_id;
	}
	function set_path($p) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_exec($sql_stmts["def_id->form_tag="], ["is", &$this->_id, (string)$p]); # still NULL if not found
		$this->_path = $p;
	}
	function remove() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			return sql_exec($sql_stmts["def_id->delete from definitions"], ["i", &$this->_id]); # still NULL if not found
	}
	private $_src = NULL;
	function src() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["def_id->def_src"], $this->_src, ["i", &$this->_id]); # still NULL if not found
		return $this->_src;
	}
	private $_type = NULL;
	function type() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["def_id->def_type"], $this->_type, ["i", &$this->_id]); # still NULL if not found
		return $this->_type;
	}
	private $_lang = NULL;
	function lang() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["def_id->def_lang"], $this->_lang, ["i", &$this->_id]); # still NULL if not found
		return $this->_lang;
	}
	function set_lang($lang) {
		$this->_lang = $lang;
		if ($this->issql and $this->_id !== NULL)
			sql_set($sql_stmts["def_id->def_lang="], $this->_lang, ["i", &$this->_id]);
	}
	private $_value = NULL;
	function value() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["def_id->def_value"], $this->_value, ["i", &$this->_id]); # still NULL if not found
		return $this->_value;
	}
	function set_value($value) {
		$value = preg_replace('/ {2,}/', ' ', trim($value));
		$this->_value = $value;
		if ($this->issql and $this->_id !== NULL)
			sql_set($sql_stmts["def_id->def_value="], $this->_value, ["i", &$this->_id]);
	}
}

function ISDEFINITION($obj) {
	return $obj instanceof _DEFINITION;
}
function DEFINITION($db, $id, $word=NULL) {
	return new _DEFINITION($db, $id, $word);
}
?>
