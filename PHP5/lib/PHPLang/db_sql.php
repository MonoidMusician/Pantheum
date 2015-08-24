<?php
##
# SQLDB class
#
class _SQLDB implements _DB
{
	function __construct() {
		global $mysqli;
		$this->depaths = [];
		$stmt = $mysqli->prepare("
			SELECT DISTINCT word_lang FROM words
		");
		if (!$stmt) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		} else
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		} else {
			$langid;
			$stmt->bind_result($langid);
			while ($stmt->fetch())
			{
				#echo "language: $langid\n";
				$this->load_language($langid);
			}
		}
		$stmt->close();
		$this->sparts_by_lang = [];
		$this->sparts = [];
		$stmt = $mysqli->prepare("
			SELECT DISTINCT word_lang,word_spart FROM words
		");
		if (!$stmt) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		} else
		if (!$stmt->execute()) {
			echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
		} else {
			$langid; $spart;
			$stmt->bind_result($langid,$spart);
			while ($stmt->fetch())
			{
				if (!array_key_exists($langid, $this->sparts_by_lang))
					$this->sparts_by_lang[$langid] = [];
				$this->sparts_by_lang[$langid][] = $spart;
				if (!in_array($spart, $this->sparts))
					$this->sparts[] = $spart;
			}
		}
		$stmt->close();
	}
	function load_language($langid) {
		global $data_dir;
		$this->depaths[$langid] = read_depaths($data_dir . $langid . "/depaths.json", $langid);
		/*if ($langid === "eo")
			foreach ($this->depaths[$langid] as $key => $value) {
				if (!$value->key2values) continue;
				echo "<h1>$key</h1>";
				var_dump($value);
			}/**/
	}
	function find_all_words($name=NULL, $lang=NULL, $spart=NULL) {
		global $sql_stmts;
		$stmt = []; $params = [""];
		if ($name !== NULL) {
			$stmt[] = "word_name";
			$params[0] .= "s";
			$params[] = $name;
		}
		if ($lang !== NULL) {
			$stmt[] = "word_lang";
			$params[0] .= "s";
			$params[] = $lang;
		}
		if ($spart !== NULL) {
			$stmt[] = "word_spart";
			$params[0] .= "s";
			$params[] = $spart;
		}
		if (!count($stmt)) _die("bad arguments: need one non-NULL");
		$stmt = $sql_stmts["word_id<-" . implode(",", $stmt)];
		$res = NULL;
		sql_getmany($stmt, $res, $params);
		foreach ($res as &$r) {
			$r = WORD($this, $r);
		}
		return $res;
	}
	function id_find_word(&$name, &$lang, &$spart=NULL) {
		global $sql_stmts;
		$id = NULL;
		if ($spart === NULL)
			sql_getone($sql_stmts["word_name,word_lang->word_id"], $id, ["ss",&$name,&$lang]);
		else
			sql_getone($sql_stmts["word_name,word_lang,word_spart->word_id"], $id, ["sss",&$name,&$lang,&$spart]);
		return $id === NULL ? NULL : intval($id);
	}
	function is_lang($lang) {
		return array_key_exists($lang, $this->depaths);
	}
	function langs() {
		return array_keys($this->depaths);
	}
	function sparts() {
		global $sql_stmts;
		$res = NULL;
		sql_getmany($sql_stmts["all_sparts"], $res, []);
		if ($res)
			$res = array_unique($res);
		return $res;
	}
	function add_mgr($lang, $speechpart, $depath) {
		$this->depaths[$lang][$speechpart] = $depath;
		return $this;
	}
	function get_mgr($lang, $speechpart) {
		return $this->depaths[$lang][$speechpart];
	}
	function add_mgrW($w, $depath) {
		if (!ISWORD($w)) $w = WORD($this, $w, FALSE);
		return $this->add_mgr($w->lang(), $w->speechpart(), $depath);
	}
	function get_mgrW($w) {
		if (!ISWORD($w)) $w = WORD($this, $w, FALSE);
		return $this->get_mgr($w->lang(), $w->speechpart());
	}
	function searcher(){return new _SQL_searcher($this);}
}
class _SQL_searcher
{
	function __construct($db) {
		$this->db = $db;
		$this->map = NULL;
		$this->stmt = "SELECT word_id FROM words";
		$this->op = " WHERE ";
		$this->args = [""];
		$this->limit_values = NULL;
	}
	function dup() {
		$n = new _SQL_searcher();
		return $n;
	}
	function trim() {
		$this->map = array_values(array_unique($this->map, SORT_REGULAR));
		return $this;
	}
	function partofspeech($spart) {
		return $this->_spredicate("word_spart",$spart);
	}
	function spart($spart) {
		return $this->_spredicate("word_spart",$spart);
	}
	function name($name) {
		return $this->_spredicate("word_name",$name);
	}
	function name_includes($name) {
		return $this->includes_spredicate("word_name",$name);
	}
	function definition($def) {
		if ($def) {
			$expr = "EXISTS (SELECT 1 FROM definitions WHERE definitions.word_id = words.word_id AND def_value LIKE CONCAT('%',?,'%'))";
			if (is_array($def) and count($def) === 1)
				$def = $def[array_keys($def)[0]];
			if (is_string($def)) {
				$this->stmt .= $this->op . $expr;
				$this->args[0] .= "s";
				$this->args[] = $def;
			} else {
				$this->stmt .= $this->op . "(";
				$op = "";
				foreach ($def as $v) {
					$this->stmt .= $op . $expr;
					$this->args[0] .= "s";
					$this->args[] = $v;
					$op = " OR ";
				}
				$this->stmt .= ")";
			}
			$this->op = " AND ";
		}
		return $this;
	}
	function definition_includes($def) {
		if ($def) {
			$expr = "EXISTS (SELECT 1 FROM definitions WHERE definitions.word_id = words.word_id AND def_value LIKE CONCAT('%',?,'%'))";
			if (is_array($def) and count($def) === 1)
				$def = $def[array_keys($def)[0]];
			if (is_string($def)) {
				$this->stmt .= $this->op . $expr;
				$this->args[0] .= "s";
				$this->args[] = $def;
			} else {
				$this->stmt .= $this->op . "(";
				$op = "";
				foreach ($def as $v) {
					$this->stmt .= $op . $expr;
					$this->args[0] .= "s";
					$this->args[] = $v;
					$op = " OR ";
				}
				$this->stmt .= ")";
			}
			$this->op = " AND ";
		}
		return $this;
	}
	function definition_parse($def) {
		if ($def) {
			if (is_array($def) and count($def) === 1)
				$def = $def[array_keys($def)[0]];

			if (is_string($def)) {
				$this->stmt .= $this->op;
				$this->_def_parse($def);
			} else {
				$this->stmt .= $this->op . "(";
				$op = "";
				foreach ($def as $v) {
					$this->stmt .= $op;
					$this->_def_parse($v);
					$op = " OR ";
				}
				$this->stmt .= ")";
			}
			$this->op = " AND ";
		}
		return $this;
	}

	function _def_parse($def) {
		$def_exists = "EXISTS (SELECT 1 FROM definitions WHERE definitions.word_id = words.word_id AND ";
		$expr = [
			"def_value LIKE CONCAT('%',?,'%')",
			"def_value REGEXP CONCAT('[[:<:]]',?,'[[:>:]]')",
			"def_value REGEXP CONCAT('(^|,|;|\\n)\W*',?,'\W*($|,|;|\\n)')",
		];
		$type = endswith($def, ",") ? 2 : (endswith($def, "~") ? 0 : 1);
		if (!$type) $def = substr($def,0,strlen($def)-1);

		$def = vec_norm(explode(",", $def), "trim");

		$this->stmt .= "(";
		$op = "";
		foreach ($def as $d) {
			$this->stmt .= $op . $def_exists . $expr[$type] . ")";
			$this->args[0] .= "s";
			$this->args[] = $type ? preg_quote($d) : $d;
			$op = " AND ";
		}
		$this->stmt .= ")";

		return $expr[$type];
	}
	function lang($lang) {
		return $this->_spredicate("word_lang", $lang);
	}
	function limit($start,$limit) {
		$this->limit_values = [$start,$limit];
		return $this;
	}
	function no_definitions() {
		$this->stmt .= $this->op . "word_id NOT IN (SELECT word_id FROM definitions)";
		$this->op = " AND ";
		return $this;
	}
	function _spredicate($name,$val) {
		if ($val) {
			if (is_array($val) and count($val) === 1)
				$val = $val[array_keys($val)[0]];
			if (is_string($val)) {
				$this->stmt .= $this->op . "$name = (?)";
				$this->args[0] .= "s";
				$this->args[] = $val;
			} else {
				$this->stmt .= $this->op . "(";
				$op = "";
				foreach ($val as $v) {
					$this->stmt .= "$op$name = (?)";
					$this->args[0] .= "s";
					$this->args[] = $v;
					$op = " OR ";
				}
				$this->stmt .= ")";
			}
			$this->op = " AND ";
		}
		return $this;
	}
	function includes_spredicate($name,$val) {
		if ($val) {
			if (is_array($val) and count($val) === 1)
				$val = $val[array_keys($val)[0]];
			if (is_string($val)) {
				$this->stmt .= $this->op . "$name LIKE CONCAT('%',?,'%')";
				$this->args[0] .= "s";
				$this->args[] = $val;
			} else {
				$this->stmt .= $this->op . "(";
				$op = "";
				foreach ($val as $v) {
					$this->stmt .= "$op$name LIKE CONCAT('%',?,'%')";
					$this->args[0] .= "s";
					$this->args[] = $v;
					$op = " OR ";
				}
				$this->stmt .= ")";
			}
			$this->op = " AND ";
		}
		return $this;
	}
	function only_with_attr($attr) {
		$this->stmt .= $this->op;
		$this->stmt .= "word_id IN (SELECT word_id FROM attributes WHERE attr_tag = (?)";
		$this->args[0] .= "s";
		$this->args[] = $attr->_key;
		if ($attr->_value) {
			$this->stmt .= " AND attr_value = (?)";
			$this->args[0] .= "s";
			$this->args[] = $attr->_value;
		}
		$this->stmt .= ")";
		$this->op = " AND ";
		#echo "\$this->map (only with attr=".$attr->_key."):";
		#var_dump($this->map);
		return $this;
	}
	function only_without_attr($attr) {
		$this->stmt .= $this->op;
		$this->stmt .= "word_id NOT IN (SELECT word_id FROM attributes WHERE attr_tag = (?)";
		$this->args[0] .= "s";
		$this->args[] = $attr->_key;
		if ($attr->_value) {
			$this->stmt .= " AND attr_value = (?)";
			$this->args[0] .= "s";
			$this->args[] = $attr->_value;
		}
		$this->stmt .= ")";
		$this->op = " AND ";
		#echo "\$this->map (only with attr=".$attr->_key."):";
		#var_dump($this->map);
		return $this;
	}
	function form($form) {
		if ($form) {
			if (is_array($form) and count($form) === 1)
				$form = $form[array_keys($form)[0]];
			if (is_string($form)) {
				$this->stmt .= $this->op . "word_id IN (SELECT word_id FROM forms WHERE form_value = (?))";
				$this->args[0] .= "s";
				$this->args[] = $form;
			} else {
				$this->stmt .= $this->op . "word_id IN (SELECT word_id FROM forms WHERE ";
				$op = "";
				foreach ($form as $v) {
					$this->stmt .= "${op}form_value = (?)";
					$this->args[0] .= "s";
					$this->args[] = $v;
					$op = " OR ";
				}
				$this->stmt .= ")";
			}
			$this->op = " AND ";
		}
		return $this;
	}
	function form_includes($form) {
		if ($form) {
			if (is_array($form) and count($form) === 1)
				$form = $form[array_keys($form)[0]];
			if (is_string($form)) {
				$this->stmt .= $this->op . "word_id IN (SELECT word_id FROM forms WHERE form_value LIKE CONCAT('%',?,'%'))";
				$this->args[0] .= "s";
				$this->args[] = $form;
			} else {
				$this->stmt .= $this->op . "word_id IN (SELECT word_id FROM forms WHERE ";
				$op = "";
				foreach ($form as $v) {
					$this->stmt .= "${op}form_value LIKE CONCAT('%',?,'%')";
					$this->args[0] .= "s";
					$this->args[] = $v;
					$op = " OR ";
				}
				$this->stmt .= ")";
			}
			$this->op = " AND ";
		}
		return $this;
	}

	function max_size() {
		global $mysqli;
		$stmt = $this->stmt;
		$stmt = str_replace("SELECT word_id FROM words","SELECT count(*) FROM words",$stmt);
		$value = $stmt;
		$stmt = $mysqli->prepare($stmt);
		if (!$stmt) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			echo "\nStatement was: " . var_export($value, 1);
		}
		$ret = sql_getone($stmt, $this->map, $this->args);
		$stmt->close();
		return $ret;
	}
	function all($order_by=NULL) {
		global $mysqli;
		if (count($this->args) === 1)
			$this->args = [];
		if ($order_by !== NULL) {
			$this->stmt .= " ORDER BY word_$order_by";
		}
		if ($this->limit_values !== NULL) {
			list($start,$limit) = $this->limit_values;
			$this->stmt .= " LIMIT $start, $limit";
		}
		$value = $this->stmt;
		$this->stmt = $mysqli->prepare($this->stmt);
		if (!$this->stmt) {
			echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			echo "\nStatement was: " . var_export($value, 1);
			return;
		}
		#error_log($value);
		sql_getmany($this->stmt, $this->map, $this->args);
		$this->stmt->close();
		#var_dump($this->map, $this->args);
		#echo "\nStatement was: " . var_export($value, 1);
		if ($this->map)
		foreach ($this->map as &$w)
			$w = WORD($this->db, $w);
		return $this->map;
	}
	function rand($rand=NULL) {
		return choose_one($this->all(), $rand);
	}
}
?>
