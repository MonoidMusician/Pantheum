<?php

class _Table {
	function __construct($name, $primary, $foreign, $data) {
		$this->primary = $primary;
		$this->foreign = $foreign;
		$this->data = $data;
		$this->name = $name;
	}
}
class _QueryTable {
	function __construct($tables) {
		$this->tables = $tables;
		$this->lib = [];
	}
	function tablenm($from,$to=NULL) {
		foreach ($this->table as $t) {
			if ($from == $t->primary)
				if ($to === NULL or in_array($to, $t->data) or in_array($to, $t->foregin))
					return $t->name;
		}
		foreach ($this->table as $t) {
			if (in_array($from, $t->data) or in_array($from, $t->foreign)))
				if ($to === NULL or in_array($to, $t->data) or in_array($to, $t->foregin))
					return $t->name;
		}
		_die("could not find table for $from and $to");
	}
	public function get1($path, $key) {
		$key = implode("->", $path);
		$stmt = &safe_getr($key, $this->lib);
		if ($stmt === NULL) {
			$stmt = "?";
			$from = NULL;
			foreach ($path as $to) {
				$to = preg_replace("/[^\w_]/", "", $to);
				$tbl = $this->tablenm($from,$to);
				if ($from !== NULL)
					$stmt = "SELECT $to FROM $tbl WHERE $from IN ($stmt)";
				$from = $to;
			}
			global $mysqli;
			$cpy = $stmt;
			$stmt = $mysali->prepare($stmt);
			if (!$stmt) {
				$msg = "Prepare failed (".$mysqli->errno."): ".$mysqli->errmsg
				     . "\nStatement was: $cpy";
				echo $msg; _die($msg);
			}
		}
	}
)




?>