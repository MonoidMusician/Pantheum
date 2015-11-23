<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

global $mysqli;
global $sql_stmts;

sro('/PHP5/lib/PHPLang/common.php');





// Parse in a macro file as shorthand for SQL queries.
// Prepare those queries.
// Save output also to .php and .csv.


function do_trim(&$str) {$str = trim($str);}
# based on: http://codeumbra.eu/lets-write-a-php-macro-parser
class StmtsMacros {
	public function __construct(&$sql_stmts=NULL) {
		if ($sql_stmts === NULL)
			$this->sql_stmts = [];
		else $this->sql_stmts = $sql_stmts;
	}

	/**
	 * Replace the macros in an input string
	 * @param string $input
	 * @return string
	 */
	public function replace($input) {
		return preg_replace_callback("/\{{3}([a-z]+\|(?:[^{}]|(?R))+)\}{3}/Us",[$this,'_replace'],$input);
	}

	/**
	 * Run the replacement code on a given macro string
	 * @param string $macro
	 * @return string
	 */
	private function _replace($matches) {
		$macro = $matches[1];
		list ($name,$params) = explode("|",$macro,2);
		do_trim($name); do_trim($params);

		if (method_exists($this,$name)) {
			return $this->$name($params);
		}

		throw new Exception("Unrecognised macro: {$name}.",500);
	}

	public function _normname($name) {
		do_trim($name);
		$ex = explode("<-", $name);
		return implode("->", array_reverse($ex));
	}

	public function _flip($name) {
		do_trim($name);
		$ex = explode("->", $name);
		return implode("<-", array_reverse($ex));
	}

	/*
	 * Macro to get a sql statement
	 */
	private function get($params) {
		$name = $this->_normname($params);
		return "\$sql_stmts['$name']";
	}

	/**
	 * Macro to define a sql statement
	 * @param string $params
	 * @return string
	 */
	private function define($params) {
		list ($name,$code) = explode("|",$params,2);
		do_trim($code);
		$code = str_replace('"','\\"', $code);
		return $this->get($name) . "= \"$code\"";
	}

	/**
	 * Macro to define a simple singular sql "select" statement
	 * @param string $params
	 * @return string
	 */
	private function defineselect($params) {
		parse_str(trim($params));
		if (!isset($op))
			$op = "="; else $op = explode(",",$op);
		$getopt = function() use($op,&$op_i) {
			if (is_array($op))
				if ($op_i < count($op)) return $op[$op_i];
				else return $op[$count($op)-1];
			else return $op;
		};
		if (!isset($name)) {
			$ret = [];
			foreach (pc_permute(explode(",",$from)) as $from) {
				$name = implode(",",$from)."->$to";
				$op_i = 0;
				$from = array_map(function($v) use($getopt,$op,&$op_i) {
					$ret = $v . " " . $getopt();
					$op_i += 1;
					return $ret;
				}, $from);
				$from = implode(" (?) AND ", $from);
				$ret[] = $this->define("$name | SELECT $to FROM $table WHERE $from (?)");
			}
			return implode(";\n", $ret);
		}
		$from = explode(",", $from);
		$from = array_map(function($v) use($getopt,$op,&$op_i) {
			$ret = $v . " " . $getopt();
			$op_i += 1;
			return $ret;
		}, $from);
		$from = implode(" (?) AND ", $from);
		return $this->define("$name | SELECT $to FROM $table WHERE $from (?)");
	}

	/**
	 * Macro to define a simple singular sql "select" statement
	 * @param string $params
	 * @return string
	 */
	private function defineupdate($params) {
		parse_str(trim($params));
		/*if (!isset($name))
			$name = "$from->$to=";
		if (!isset($op))
			$op = "="; else $op = explode(",",$op);
		$getopt = function() use($op,&$op_i) {
			if (is_array($op))
				if ($op_i < count($op)) return $op[$op_i];
				else return $op[$count($op)-1];
			else return $op;
		};
		$op_i = 0;
		$vals = explode(",", $from);
		$vals = array_map(function($v) use($getopt,$op,&$op_i) {
			$ret = $v . " " . $getopt();
			$op_i += 1;
			return $ret;
		}, $vals);
		$vals = implode(" (?) AND ", $vals);
		return $this->define("$name | UPDATE $table SET $to = (?) WHERE $vals (?)");*/
		if (!isset($op))
			$op = "="; else $op = explode(",",$op);
		$getopt = function() use($op,&$op_i) {
			if (is_array($op))
				if ($op_i < count($op)) return $op[$op_i];
				else return $op[$count($op)-1];
			else return $op;
		};
		if (!isset($name)) {
			$ret = [];
			foreach (pc_permute(explode(",",$from)) as $perm) {
				$from = implode(",",$perm);
				$name = "$from->$to=";
				$op_i = 0;
				$vals = explode(",", $from);
				$vals = array_map(function($v) use($getopt,$op,&$op_i) {
					$ret = $v . " " . $getopt();
					$op_i += 1;
					return $ret;
				}, $vals);
				$vals = implode(" (?) AND ", $vals);
				$ret [] = $this->define("$name | UPDATE $table SET $to = (?) WHERE $vals (?)");
			}
			return implode(";\n", $ret);
		} else {
			$op_i = 0;
			$vals = explode(",", $from);
			$vals = array_map(function($v) use($getopt,$op,&$op_i) {
				$ret = $v . " " . $getopt();
				$op_i += 1;
				return $ret;
			}, $vals);
			$vals = implode(" (?) AND ", $vals);
			return $this->define("$name | UPDATE $table SET $to = (?) WHERE $vals (?)");
		}
	}

	/**
	 * Macro to define a simple singular sql "select" statement
	 * @param string $params
	 * @return string
	 */
	private function defineinsert($params) {
		parse_str(trim($params));
		if (!isset($name)) {
			$ret = [];
			foreach (pc_permute(explode(",",$from)) as $perm) {
				$from = implode(",",$perm);
				$name = "$from"."->new in $table";
				$vals = explode(",", $from);
				$vals = array_map(function($v) {
					return "?";
				}, $vals);
				$vals = implode(", ", $vals);
				$ret[] = $this->define("$name | INSERT INTO $table ($from) VALUES ($vals)");
			}
			return implode(";\n", $ret);
		} else {
			$vals = explode(",", $from);
			$vals = array_map(function($v) {
				return "?";
			}, $vals);
			$vals = implode(", ", $vals);
			return $this->define("$name | INSERT INTO $table ($from) VALUES ($vals)");
		}
	}

	/**
	 * Macro to define a simple singular sql "select" statement
	 * @param string $params
	 * @return string
	 */
	private function definedelete($params) {
		parse_str(trim($params));
		if (!isset($name))
			$name = "$from"."->delete from $table";
		if (!isset($op))
			$op = "="; else $op = explode(",",$op);
		$getopt = function() use($op,&$op_i) {
			if (is_array($op))
				if ($op_i < count($op)) return $op[$op_i];
				else return $op[$count($op)-1];
			else return $op;
		};
		$op_i = 0;
		$vals = explode(",", $from);
		$vals = array_map(function($v) use($getopt,$op,&$op_i) {
			$ret = $v . " " . $getopt();
			$op_i += 1;
			return $ret;
		}, $vals);
		$vals = implode(" (?) AND ", $vals);
		return $this->define("$name | DELETE FROM $table WHERE ($vals (?))");
	}

	private function attrs($params) {
		parse_str(trim($params));
		if (isset($bi))
			$bi = explode(",", $bi); else $bi = [];
		if (isset($to))
			$to = explode(",", $to); else $to = [];
		if (isset($pre))
			if ($pre == "\$table_") $pre = $table . "_";
			elseif ($pre == "\$table") $pre = $table;
			else {} else $pre = "";
		if (!isset($key))
			$key = "id";
		$ret = "";
		foreach ($bi as $b) {
			$ret .= $this->defineselect("table=$table&from=$pre$b&to=$pre$key") . ";\n";
			$ret .= $this->defineselect("table=$table&to=$pre$b&from=$pre$key") . ";\n";
			$ret .= $this->defineupdate("table=$table&to=$pre$b&from=$pre$key") . ";\n";
		}
		foreach ($to as $b) {
			$ret .= $this->defineselect("table=$table&to=$pre$b&from=$pre$key") . ";\n";
			$ret .= $this->defineupdate("table=$table&to=$pre$b&from=$pre$key") . ";\n";
		}
		return $ret;
	}

	/**
	 * Macro to compose a statement with arguments
	 */
	private function compose($params) {
		list ($name,$params) = explode("=",$params,2);
		do_trim($name);
		list ($sub,$params) = explode("|",$params,2);
		do_trim($sub);
		$needle = "?";
		$var = $this->get($name);
		$result = "$var = " . $this->get($sub) . ";\n";
		$result .= "foreach (explode('|','(' . ".$this->replace($params)." . ')') as \$arg) {\n";
		$result .= "    $var = substr_replace($var, trim(\$arg), strpos($var,'$needle'), strlen('$needle'));\n";
		$result .= "}";
		return $result;
	}

	/**
	 * Macro to compose a statement with arguments
	 */
	private function composepl($params) {
		list ($name,$params) = explode("=",$params,2);
		do_trim($name);
		list ($sub,$params) = explode("|",$params,2);
		do_trim($sub);
		$needle = "?";
		$var = $this->get($name);
		$result = "$var = " . $this->get($sub) . ";\n";
		$result .= "foreach (explode('|','(' . ".$this->replace($params)." . ')') as \$arg) {\n";
		$result .= "    $var = preg_replace('/=\s*\(?\?\)?/', 'IN '.trim(\$arg), $var);\n";
		$result .= "}";
		return $result;
	}
}

// Set up and parse stmts.sql
$syntax = new StmtsMacros();
$dir = "/var/www/MySQL/";
$time = filemtime("$dir/stmts.sql");
if (apcu_fetch("sqlstmts_time") == $time) {
	$GLOBALS['sql_stmts'] = apcu_fetch("sqlstmts");
} else {
	$start = '/*
	 * NOTE: this file is auto-generated, PLEASE do NOT edit!
	 */

	$sql_stmts = [];';
	$end = 'return $sql_stmts;';
	$code = file_get_contents("$dir/stmts.sql");
	$code = $syntax->replace($code);
	if ($code) {
		file_put_contents("$dir/stmts.php", '<'.'?php'."\n".$start."\n".$code."\n".$end.'?'.'>');
	}
	#echo "$code\n";
	$GLOBALS['sql_stmts'] = eval($start.$code.$end);
	apcu_store("sqlstmts", $sql_stmts);
	apcu_store("sqlstmts_time", $time);
}

global $sql_stmts;
$list = [];
foreach (array_keys($sql_stmts) as $name) {
	$list[$name] = $value = $sql_stmts[$name];
	$aliases = [$syntax->_flip($name)];
	$value = $sql_stmts[$name];
	foreach ($aliases as $a)
		$sql_stmts[$a] = $value;
}
array_walk($list, function (&$v,$k){$v="$k; $v";});

file_put_contents("$dir/stmts.csv", implode("\n", $list));





// PHP Statement Helpers

function sql_stmt($name) {
	global $sql_stmts, $mysqli;
	if (is_string($sql_stmts[$name])) {
		$value = $sql_stmts[$name];
		if (!($sql_stmts[$name] = $mysqli->prepare($value))) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			echo "\nStatement was: " . var_export($value, 1);
			return NULL;
		}
	}
	return $sql_stmts[$name];
}
// Execute a prepared (and handle it)
function &sql_getN($stmt, &$result, $params, $n) {
	$result = NULL;
	if ($params) {
		$bind_names[] = $params[0];
		for ($i=1; $i<count($params);$i++) {
			$bind_name = 'bind' . $i;
			$$bind_name = $params[$i];
			$bind_names[] = &$$bind_name;
		}
		if (!call_user_func_array(array($stmt,"bind_param"), $bind_names)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return $result;
		}
	}
	if (!$stmt->execute()) {
		echo "Execute failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error;
		return $result;
	}
	if ($n === -1) {
		$stmt->bind_result($result);
		$stmt->fetch();
	} elseif ($n === 0) {
		$result = [];
	} elseif ($n === NULL) {
		$result = [];
		$res = NULL;
		$stmt->bind_result($res);
		while ($stmt->fetch()) {
			$result[] = $res;
		}
	} else _die("bad \$n: ".var_export($n,1));
	$stmt->free_result();
	$stmt->reset();
	return $result;
}

// Same, minus &$result and $n
function &sql_exec($stmt, $params) {
	$result = NULL;
	if ($params) {
		$bind_names[] = $params[0];
		for ($i=1; $i<count($params);$i++) {
			$bind_name = 'bind' . $i;
			$$bind_name = $params[$i];
			$bind_names[] = &$$bind_name;
		}
		if (!call_user_func_array(array($stmt,"bind_param"), $bind_names)) {
			echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			return $result;
		}
	}
	if (!$stmt->execute()) {
		_die("Execute failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error);
		return $result;
	}
	$stmt->reset();
	$result = TRUE;
	return $result;
}

function sql_set($stmt, $result, $params) {
	$params[0] = "s".$params[0];
	array_splice($params, 1,0,[$result]);
	sql_exec($stmt, $params);
}

// Return the only value existing in a query
function &sql_oneresult(&$res) {
	if (is_array($res) and count($res) === 1)
		return $res[0];
	return $res;
}

// Return the only value existing in a query
function &sql_getone($stmt, &$result, $params) {
	sql_getN($stmt, $result, $params, -1);
	$result = &sql_oneresult($result);
	return $result;
}

// Return several one-column rows as the first element in each row
function &sql_getmany($stmt, &$result, $params) {
	sql_getN($stmt, $result, $params, NULL);
	return $result;
}

// Return the columns from a random row, or just the id of that row.
function rand_row($table, $columns, $id=NULL) {
	global $mysqli;
	if ($id === NULL) {
		$_ = TRUE;
		$id = $columns;
	} else $_ = FALSE;
	$query = $mysqli->query("SELECT COUNT(*) FROM $table");
	if (!$query) {
		echo "Query1 failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	}
	$size = $query->fetch_assoc()['COUNT(*)'];
	$query->free();
	$rand_id = mt_rand(1, $size);
	$query = $mysqli->query("SELECT $columns FROM $table WHERE $id >= $rand_id ORDER BY $id ASC LIMIT 1");
	if (!$query) {
		echo "Query2 failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	}
	if ($_) {
		$ret = $query->fetch_assoc()[$id];
		$query->close();
		return $ret;
	}
	return $query;
}





?>
