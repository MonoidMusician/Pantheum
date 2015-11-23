<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

##
# Pronunciation class.
#
class _PRONUNCIATION
{
	private $_word_sublang = NULL;
	function __construct($db, $id, $word) {
		$this->_id = $id;
		$this->issql = ISSQLDB($db);
		$this->_word = $word !== NULL ? $word : NULL;
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
			return sql_getone(sql_stmt("pron_id->form_tag"), $_form_tag, ["i", &$this->_id]); # still NULL if not found
		return $_form_tag;
	}
	function set_path($p) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_exec(sql_stmt("pron_id->form_tag="), ["is", &$this->_id, (string)$p]); # still NULL if not found
		$this->_path = $p;
	}
	function remove() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			return sql_exec(sql_stmt("pron_id->delete from pronunciations"), ["i", &$this->_id]); # still NULL if not found
	}
	private $_src = NULL;
	function src() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone(sql_stmt("pron_id->pron_src"), $this->_src, ["i", &$this->_id]); # still NULL if not found
		return $this->_src;
	}
	private $_type = NULL;
	function type() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone(sql_stmt("pron_id->pron_type"), $this->_type, ["i", &$this->_id]); # still NULL if not found
		return $this->_type;
	}
	function set_type($type) {
		global $sql_stmts;
		$this->_type = $type;
		if ($this->issql and $this->_id !== NULL)
			sql_set(sql_stmt("pron_id->pron_type="), $this->_type, ["i", &$this->_id]);
	}
	private $_sublang = NULL;
	function sublang() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone(sql_stmt("pron_id->pron_sublang"), $this->_sublang, ["i", &$this->_id]); # still NULL if not found
		return $this->_sublang;
	}
	function set_sublang($lang) {
		global $sql_stmts;
		$this->_sublang = $sublang;
		if ($this->issql and $this->_id !== NULL)
			sql_set(sql_stmt("pron_id->pron_sublang="), $this->_sublang, ["i", &$this->_id]);
	}
	private $_value = NULL;
	function value() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone(sql_stmt("pron_id->pron_value"), $this->_value, ["i", &$this->_id]); # still NULL if not found
		return $this->_value;
	}
	function set_value($value) {
		global $sql_stmts;
		$this->_value = $value;
		if ($this->issql and $this->_id !== NULL)
			sql_set(sql_stmt("pron_id->pron_value="), $this->_value, ["i", &$this->_id]);
	}
}

function ISPRONUNCIATION($obj) {
	return $obj instanceof _PRONUNCIATION;
}
function PRONUNCIATION($db, $id, $word=NULL) {
	return new _PRONUNCIATION($db, $id, $word);
}
?>
