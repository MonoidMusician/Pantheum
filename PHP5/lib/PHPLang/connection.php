<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

##
# Pronunciation class.
#
class _CONNECTION
{
	function __construct($from, $to, $type) {
		$this->_from = $from;
		$this->_to = $to;
		$this->_type = $type;
	}
	function from() { return $this->_from; }
	function to() { return $this->_to; }
	function type() { return $this->_type; }
}

function ISCONNECTION($obj) {
	return $obj instanceof _CONNECTION;
}
function CONNECTION() {
	$arg = func_get_args();
	if (count($arg) == 1) {
		$from_word_id = $arg["from_word_id"];
		$to_word_id = $arg["to_word_id"];
		$connect_type = $arg["connect_type"];
	} else {
		list($from_word_id, $to_word_id, $connect_type) = $arg;
	}
	return new _CONNECTION($from_word_id, $to_word_id, $connect_type);
}
?>
