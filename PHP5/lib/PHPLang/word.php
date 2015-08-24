<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

##
# Word class.
#
class _WORD
{
	public $path_storage = [];
	public $attr_storage = [];
	public $df_path_values = [];
	function __construct($db, $id, $mgr, $lang) {
		if (is_array($id) and $mgr !== NULL) {
			$id = $db->new_word($mgr, $id, $lang);
		}
		$this->_db = $db;
		$this->issql = ISSQLDB($db);
		if ($this->issql and is_string($id))
			$id = $db->id_find_word($id, $lang);
		$this->_id = $id;
		#var_dump($this->id(), $this->lang(), $this->speechpart());
		if ($mgr === NULL) {
			if(!is_scalar($id)) _die("_WORD.id ".var_export($id,1)." needs to be scalar");
			// defer $this->_mgr = $db->get_mgrW($this); to $this->mgr()
		}
		$this->_mgr = $mgr;
		$this->_path = NULL;
		return $this;
	}
	function path() {
		if ($this->_path === NULL) {
			$this->_path = PATH($this->mgr());
			$this->_path->set_word($this);
		}
		return $this->_path;
	}
	function mgr() {
		if ($this->_mgr === NULL)
			$this->_mgr = $this->db()->get_mgrW($this);
		return $this->_mgr;
	}
	function db() { return $this->_db; }
	function id() { return $this->_id; }
	function __toString() {return $this->id().$this->name();}
	function set_id($id) {
		if ($this->id() === NULL)
			$this->_id = $id;
		else _die("already had id, could not set to $id");
	}
	private $_name = NULL;
	function name() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["word_id->word_name"], $this->_name, ["i", &$this->_id]); # still NULL if not found
		return $this->_name;
	}
	function set_name($name) {
		global $sql_stmts;
		$this->_name = $name;
		if ($this->issql and $this->_id !== NULL)
			sql_set($sql_stmts["word_id->word_name="], $this->_name, ["i", &$this->_id]);
	}
	private $_cached = NULL;
	function cached() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["word_id->inflection_cache"], $this->_cached, ["i", &$this->_id]); # still NULL if not found
		return $this->_cached;
	}
	function set_cached($cached) {
		global $sql_stmts;
		$this->_cached = $cached;
		if ($this->issql and $this->_id !== NULL)
			sql_set($sql_stmts["word_id->inflection_cache="], $this->_cached, ["i", &$this->_id]);
	}
	private $_speechpart = NULL;
	function speechpart() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["word_id->word_spart"], $this->_speechpart, ["i", &$this->_id]); # still NULL if not found
		return $this->_speechpart;
	}
	function set_speechpart($spart) {
		global $sql_stmts;
		$this->_speechpart = $spart;
		if ($this->issql and $this->_id !== NULL)
			sql_set($sql_stmts["word_id->word_spart="], $this->_speechpart, ["i", &$this->_id]);
	}
	private $_lang = NULL;
	function lang() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["word_id->word_lang"], $this->_lang, ["i", &$this->_id]); # still NULL if not found
		return $this->_lang;
	}
	function set_lang($lang) {
		global $sql_stmts;
		$this->_lang = $lang;
		if ($this->issql and $this->_id !== NULL)
			sql_set($sql_stmts["word_id->word_lang="], $this->_lang, ["i", &$this->_id]);
	}
	private $_last_changed = NULL;
	function last_changed() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["word_id->last_changed"], $this->_last_changed, ["i", &$this->_id]); # still NULL if not found
		return $this->_last_changed;
	}
	private $_info = NULL;
	function info() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["word_id->word_info_formatted"], $this->_info, ["i", &$this->_id]); # still NULL if not found
		return $this->_info;
	}


	private $_definitions = [];
	function clear_definitions() {
		$this->_definitions = [];
	}
	function get_def($def) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$id = NULL;
			sql_getone($sql_stmts["word_id,def_lang,def_value->def_id"], $id, ["iss", $this->_id, $def->lang(), $def->value()]);
			$def = DEFINITION($this->db(), $id, $this);
		}
		return $def;
	}
	function definitions() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$added = []; # id's returned
			sql_getmany($sql_stmts["word_id->def_id"], $added, ["i", $this->_id]);
			foreach ($added as $def) {
				$cont=FALSE;
				foreach ($this->_definitions as $_def) {
					if (ISDEFINITION($_def) ? $_def->id() == $def : $_def == $def) {$cont=TRUE;break;}
				}
				if (!$cont)
					$this->_definitions[] = DEFINITION($this->db(), $def, $this);
			}
		}
		return $this->_definitions;
	}
	function add_definition($def) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			if ($def->type())
				sql_exec($sql_stmts["word_id,def_lang,def_value,form_tag,def_type->new in definitions"], ["issss", $this->_id, $def->lang(), $def->value(), (string)$def->path(), $def->type()]);
			else sql_exec($sql_stmts["word_id,def_lang,def_value,form_tag->new in definitions"], ["isss", $this->_id, $def->lang(), $def->value(), (string)$def->path()]);
			$def = $this->get_def($def);
		}
		$this->_definitions[] = $def;
		return $def;
	}


	private $_pronunciations = [];
	function clear_pronunciations() {
		$this->_pronunciations = [];
	}
	function get_pron($pron) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$id = NULL;
			sql_getone($sql_stmts["word_id,pron_type,pron_value->pron_id"], $id, ["iss", $this->_id, $pron->type(), $pron->value()]);
			if ($id !== NULL) $pron = PRONUNCIATION($this->db(), $id, $this);
		}
		return $pron;
	}
	function pronunciations() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$added = []; # id's returned
			sql_getmany($sql_stmts["word_id->pron_id"], $added, ["i", $this->_id]);
			foreach ($added as $pron) {
				$cont=FALSE;
				foreach ($this->_pronunciations as $_pron) {
					if (ISPRONUNCIATION($_pron) ? $_pron->id() == $pron : $_pron == $pron) {$cont=TRUE;break;}
				}
				if (!$cont)
					$this->_pronunciations[] = PRONUNCIATION($this->db(), $pron, $this);
			}
		}
		return $this->_pronunciations;
	}
	function add_pronuncation($pron) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			sql_exec($sql_stmts["word_id,pron_type,pron_value,form_tag->new in pronunciations"], ["isss", $this->_id, $pron->type(), $pron->value(), (string)$pron->path()]);
			$pron = $this->get_pron($pron);
			error_log(var_export($pron->id(),1));
		}
		$this->_pronunciations[] = $pron;
		return $pron;
	}



	private $_connections = [];
	function clear_connections() {
		$this->_connections = [];
	}
	function connections() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$stmt = $sql_stmts["from_word_id->to_word_id,connect_type"];
			if (!$stmt->bind_param("i", $this->_id)) {
				echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
				return $this->_connections;
			}
			$stmt->bind_result($to_word_id, $type);
			if (!$stmt->execute()) {
				_die("Execute failed (".__FILE__."@".__LINE__."): (" . $stmt->errno . ") " . $stmt->error);
				return $result;
			}
			$added = [];
			while ($stmt->fetch()) {
				$added[] = [$to_word_id,$type];
			}
			foreach ($added as $connect) {
				$this->_connections[] = CONNECTION(
					$this,
					WORD($this->db(),$connect[0]),
					$connect[1]
				);
			}
			$stmt->free_result();
			$stmt->reset();
		}
		return $this->_connections;
	}
	function add_connection($connect) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			sql_exec($sql_stmts["from_word_id,to_word_id,connect_type->new in connections"], ["iis", $this->_id, $connect->to()->id(), $connect->type()]);
		}
		$this->_connections[] = $connect;
		return $connect;
	}
	function remove_connection($connect) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			sql_exec($sql_stmts["from_word_id,to_word_id,connect_type->delete from connections"], ["iis", $this->_id, $connect->to()->id(), $connect->type()]);
		}
	}



	private $_paths = [];
	function clear_paths() {
		$this->_paths = [];
	}
	function paths() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$added = []; # id's returned
			sql_getmany($sql_stmts["word_id->form_id"], $added, ["i", $this->_id]);
			foreach ($added as $path) {
				$cont=FALSE;
				foreach ($this->_paths as $_path) {
					if (ISPATH($_path) ? $_path->id() == $path : $_path == $path) {$cont=TRUE;break;}
				}
				if (!$cont)
					$this->_paths[] = PATH($this, $path);
			}
		}
		return $this->_paths;
	}
	function get_path($path) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$id = NULL;
			sql_getone($sql_stmts["word_id,form_tag,form_value->form_id"], $id, ["iss", $this->_id, (string)$path, $path->get()]);
			if ($id !== NULL) $path = PATH($path, $id);
		}
		return $path;
	}
	function add_path($path) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			sql_exec($sql_stmts["word_id,form_tag,form_value->new in forms"], ["iss", $this->_id, (string)$path, $path->get()]);
			$path = $this->get_path($path);
		}
		$this->_paths[] = $path;
		return $path;
	}

	// Ugly interaction with forms/paths

	function path_by_tag() {
		global $sql_stmts;
		$_id = NULL;
		$tag = func_get_args();
		$p = PATH($this, $tag);
		if ($this->issql) {
			$tag = (string)$p;
			sql_getone($sql_stmts["word_id,form_tag->form_id"], $_id, ["is", $this->_id, $tag]);
			if ($_id !== NULL)
				return PATH($this, $_id);
		}
		if ($p->exists())
			return $p;
	}
	function read_paths() {
		global $sql_stmts;
		$this->clear_paths();
		foreach ($this->paths() as $p) {
			$p->set($p->value());
		}
		return $this->_paths;
	}


	private $_attrs = [];
	function clear_attrs() {
		$this->_attrs = [];
	}
	function attrs() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL) {
			$added = []; # id's returned
			sql_getmany($sql_stmts["word_id->attr_tag"], $added, ["i", $this->_id]);
			foreach ($added as $attr) {
				$cont=FALSE;
				foreach ($this->_attrs as $_attr) {
					if (ISPATH($_attr) ? $_attr->tag() == $attr : $_attr == $attr) {$cont=TRUE;break;}
				}
				if (!$cont)
					$this->_attrs[] = ATTR($this, $attr);
			}
		}
		return $this->_attrs;
	}
	function add_attr($attr) {
		global $sql_stmts;
		$this->_attrs[] = $attr;
		if ($this->issql and $this->_id !== NULL) {
			sql_exec($sql_stmts["set attr"], ["iss", $this->_id, $attr->tag(), $attr->value()]);
		}
	}
	function remove_attr($attr) {
		global $sql_stmts;
		$a = $this->_attrs;
		foreach ($a as $k => $_) {
			if ($_->tag() === $attr->tag())
				unset($this->_attrs[$k]);
		}
		if ($this->issql and $this->_id !== NULL) {
			sql_exec($sql_stmts["word_id,attr_tag->delete from attributes"], ["is", $this->_id, $attr->tag()]);
		}
	}
	function read_attrs() {
		global $sql_stmts;
		$this->clear_attrs();
		foreach ($this->attrs() as $p) {
			$p->set($p->value());
		}
		return $this->_attrs;
	}


	function has_attr($attr) {
		if ($attr->value() === NULL)
			return !!$attr->get($this);
		else return $attr->get($this) == $attr->value();
	}
	function dump() {
		if (!$this->issql) {
			return var_dump($this);
		} else {
			global $mysqli;
			$query = $mysqli->query("SELECT * FROM words WHERE word_id = ".$this->_id);
			if (!$query) {
				echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
				return NULL;
			}
			$res = $query->fetch_assoc();
			$query->reset();
			var_dump($res);
		}
	}

	function remove() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			return sql_exec($sql_stmts["word_id->delete from words"], ["i", &$this->_id]); # still NULL if not found
	}
}

function ISWORD($obj) {
	return $obj instanceof _WORD;
}
function WORD() {
	$db=defaultDB(); $mgr=NULL; $id=NULL; $lang=NULL;
	$arg = func_get_args();
	#var_dump($arg);
	foreach ($arg as $a) {
		if (ISDB($a)) $db = $a;
		elseif (ISDEPATH($a)) $mgr = $a;
		elseif (ISDB($db) and is_string($a) and $db->is_lang($a))
			$lang = $a;
		else $id = $a;
	} unset($arg, $a);
	#var_dump($db, $id, $mgr, $lang);
	return new _WORD($db, $id, $mgr, $lang);
}
function RWORD($db) {
	$id = intval(rand_row("words", "word_id"));
	return WORD($db, $id);
}
function WORD2($lang, $name, $spart=null) {
	$w = defaultDB()->searcher()->name($name)->lang($lang);
	if ($spart !== null)
		$w = $w->spart($spart);
	$w = $w->all();
	$r = array_pop($w);
	if ($w) return null;
	return $r;
}
function RWORD2($lang, $spart, $name=null) {
	$w = defaultDB()->searcher()->spart($spart)->lang($lang)->only_without_attr(ATTR("irregular"))->only_without_attr(ATTR("template"));
	if ($name !== null)
		$w = $w->name($name);
	$w->stmt .= " AND EXISTS (SELECT 1 FROM forms WHERE forms.word_id = words.word_id AND form_tag != '' AND form_value != '') AND NOT EXISTS (SELECT 1 FROM attributes WHERE attr_tag = 'conjugation' AND attr_value like '%deponent%' AND word_id = words.word_id)";
	return $w->rand();
}
sro('/PHP5/lib/PHPLang/definition.php');
sro('/PHP5/lib/PHPLang/pronunciation.php');
sro('/PHP5/lib/PHPLang/connection.php');
sro('/PHP5/lib/PHPLang/path.php');
?>
