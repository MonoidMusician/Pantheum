<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

function array_of_size($sz) {
	if (!is_int($sz)) $sz = count($sz);
	return array_pad([], $sz, NULL);
}

const FLAT_STORAGE = TRUE;

##
# Path for linking specific forms of a word.
#
class _PATH implements Countable
{
	public $_word = NULL; # default
	public $_map_dirty = 1; # for _calculate_valid_values
	function __construct($init, $path) {
		$this->issql = 0;
		if (ISWORD($init)) {
			$this->issql = $init->issql;
			$p = $init->path();
			$this->_mgr = $p->mgr();
			$this->map = array_of_size($p->map);
			$this->_word = $init;
			#$path = array_merge($init->df_path_values, $path);
			$this->add2($init->df_path_values);
		} elseif (ISPATH($init)) {
			$this->issql = $init->issql;
			$this->_mgr = $init->mgr();
			$this->map = array_of_size($init->map);
		} else {
			if (!ISDEPATH($init)) _die("bad init object ".var_export($init,1));
			$this->_mgr = &$init;
			$this->map = array_of_size($init->all_sub_keys);
		}
		if (is_int($path)) {
			$this->_id = $path;
			$path = explode("/", $this->tag());
		}
		$this->add2($path);
		return $this;
	}
	function &resolve_hash(&$hash) {
		if (ISWORD($hash)) {
			return $hash->path_storage;
		}
		if ($hash === NULL and $this->word() !== NULL) {
			if ($this->word()->path_storage !== NULL)
				return $this->word()->path_storage;
			#elseif ($this->word()->db() !== NULL)
			#	return $this->word()->db();
		}
		return $hash;
	}
	private $_id = NULL;
	function id() { return $this->_id; }
	function set_id($id) { $this->_id = $id; }
	function mgr() { return $this->_mgr; }
	function set_mgr($m) { return $this->_mgr = $m; }
	function word() { return $this->_word; }
	function set_word($w) { return $this->_word = $w; }
	private $_tag = NULL;
	function tag() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["form_id->form_tag"], $this->_tag, ["i", &$this->_id]); # still NULL if not found
		return $this->_tag;
	}
	private $_value = NULL;
	function value() {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_getone($sql_stmts["form_id->form_value"], $this->_value, ["i", &$this->_id]); # still NULL if not found
		return $this->_value;
	}
	function set_value($value) {
		global $sql_stmts;
		$this->_value = $value;
		/*if ($this->issql and $this->_id !== NULL)
			error_log($this->_id." -> $value");/**/
		if ($this->issql and $this->_id !== NULL)
			sql_set($sql_stmts["form_id->form_value="], $this->_value, ["i", &$this->_id]);
	}
	function resolve_key_value($arg) {
		$key = NULL; $value = NULL;
		if (count($arg) === 1) {
			$value = $this->mgr()->resolve_alias($arg[0]);
			if ($value !== NULL)
				$key = $this->mgr()->find_key($value);
		} elseif (count($arg) === 2) {
			list($key,$value) = $arg;
			$value = $this->mgr()->resolve_alias($value, $key);
			# In case key was "" or to catch incorrect value:
			$key = $this->mgr()->find_key($value);
		}
		return [$key,$value];
	}
	function add() {
		$this->_map_dirty = 1;
		if (isset($key, $value)) $arg = [$key, $value];
		else $arg = flatten(func_get_args());
		list($key, $value) = $this->resolve_key_value($arg);
		$key_index = $this->mgr()->key_index($key);
		$this->map[$key_index] = $value;
		return $this;
	}
	function add2() {
		$recurse = NULL;
		$recurse = function($vec) use(&$recurse) {
			foreach ($vec as $a) {
				if (is_callable($a)) $a = $a();
				if (ISPATH($a)) $a = (string)$a;
				if (!$a) {}
				elseif (is_string($a)) {
					foreach (explode("/", trim($a)) as $_)
						if ($_) $this->add($_);
				} elseif (is_array($a))
					if (count($a) === 2 and is_string($a[0]) and $this->mgr()->is_key($a[0]))
						$this->add($a);
					else $recurse($a);
				else _die("bad PATH.add2 argument of type ".gettype($a));
			}
		};
		$recurse(func_get_args());
		return $this;
	}
	function addp($basepath) {
		foreach ($this->mgr()->all_sub_keys as $k) {
			if (!$this->key_exists($k))
				$this->add($basepath->key_value($k));
		}
		$this->_map_dirty = 1;
		return $this;
	}
	function take($key) {
		$this->_map_dirty = 1;
		$this->map[$this->mgr()->key_index($key)] = NULL;
		return $this;
	}
	function take2() {
		$recurse = function($vec) {
			foreach ($vec as $a) {
				if (!$a) {}
				elseif (is_string($a))
					$this->take($a);
				elseif (is_array($a))
					if (count($a) === 2 and $this->mgr()->is_key($a[0])
					    and $this->mgr()->is_value($a[1]))
						$this->take($a);
					else $recurse($a);
				else die("bad PATH.take2 argument of type ".gettype($a));
			}
		};
		$recurse(func_get_args());
		return $this;
	}
	function __toString() {
		$ret = "";
		$sep = "";
		foreach ($this->map as $m) {
			if ($m) {
				$ret .=  $sep.$m;
				$sep = "/";
			}
		}
		return $ret;
	}
	function reset() {
		foreach (array_keys($this->path) as $i)
			$this->path[$i] = NULL;
		return $this;
	}
	function key_exists($key) {
		return $this->map[$this->mgr()->key_index($key)] !== NULL;
	}
	function key_value($key) {
		return $this->map[$this->mgr()->key_index($key)];
	}
	function &walk(&$hash, $create=0) { # ambula totum indicem ad locum tuum
		$this->validate();
		$hash = &$this->resolve_hash($hash);
		$dbg = 0;
		if ($dbg) {
			$cpy = $hash;
			echo "--start\n";
			echo "\$h:";
			#var_export($hash); echo "\n";
		}
		foreach ($this->map as $p) {
			if ($hash === NULL) break;
			if ($p === NULL) continue;
			if ($create and !array_key_exists($p, $hash)) $hash[$p] = [];
			$hash = &$hash[$p];
			if ($dbg and 0) {
				echo "[$p]:";
				#var_export($hash); echo "\n";
			}
		}
		if ($dbg) {
			echo "\$cpy:";
			#var_export($cpy); echo "\n";
			echo "--done\n";
		}
		return $hash;
	}
	function &walk_part(&$hash, $max=-1, $min=0) { # ambula partem indicis ad locum tuum
		$this->validate();
		$hash = &$this->resolve_hash($hash);
		$i = 0;
		foreach ($this->map as $p) {
			if ($min) {$min -= 1;continue;}
			if ($hash === NULL) return $hash;
			$i += 1;
			if ($p === NULL) continue;
			if ($create and !array_key_exists($p, $hash)) $hash[$p] = [];
			$hash = &$hash[$p];
			if (!($max -= 1)) break;
		}
		return [$hash,$i,$this->map[$i]];
	}
	function _calculate_valid_values() { # calcula valores validos tuos
		if ($this->_map_dirty === 0) return $this->_valid_values;
		$this->_map_dirty = 0;
		$ret = [];
		$recurse = NULL;
		$recurse = function($dp) use(&$ret, &$recurse) {
			assert('ISDEPATH($dp)');
			if ($dp === NULL) return $ret;
			foreach ($dp->simple_keys as $k) {
				if (array_key_exists($k, $ret)) die("duplicate key");
				$ret[$k] = $dp->level[$k];
			}
			foreach ($dp->recursive_keys as $k) {
				if (array_key_exists($k, $ret)) die("duplicate key");
				$ret[$k] = array_keys($dp->level[$k]);
				$i = $dp->key_index($k);
				$v = $this->map[$i];
				if ($v)
					$recurse($dp->level[$k][$v]);
			}
			return $ret;
		};
		$recurse($this->mgr());
		$this->_valid_values = $ret;
		#echo "\$this->map";
		#var_dump($this->map);
		#echo "\$ret = \$this->_valid_values";
		#var_dump($ret);
		return $ret;
	}
	function valid($msg=FALSE) { # esne validum?
		$dp = $this->mgr();
		$vals = $this->_calculate_valid_values();
		foreach (array_keys($vals) as $k) {
			$i = $dp->key_index($k);
			$vs = $vals[$k];
			$v = $this->map[$i];
			if ($v === NULL) continue;
			$match = 0;
			foreach ($vs as $v_p)
				if ($v_p == $v) { $match=1; break; }
			if (!$match) return $msg ? "value '$v' of key '$k' was not in set ".var_export($vs, TRUE) : 0;
		}
		return $msg ? NULL : 1;
	}
	function validate() # verifica te aut morere!
		{ if (($msg=$this->valid(TRUE)) !== NULL) _die("invalid path: $msg"); }
	# Count how many values there are
	function count() {$s=0;foreach($this->map as $p)if($p!==NULL)$s+=1;return $s;}
	function set($val, &$hash=NULL) { # da tabulae associativae valorem in positionem tuam
		$this->_map_dirty = 1;
		if(FLAT_STORAGE) {
			$h = &$this->resolve_hash($hash);
			return $h[(string)$this] = $val;
		}
		$h = &$this->walk($hash,1);
		return $h[""] = $val;
	}
	function get(&$hash=NULL) { # fer valorem ex tabula associativa
		if(FLAT_STORAGE) {
			$h = &$this->resolve_hash($hash);
			if ($this->exists($h)) {
				return $h[(string)$this];
			} else return;
		}
		$h = &$this->walk($hash,0);
		if ($h === NULL or !array_key_exists("", $h)) return NULL;
		return $h[""];
	}
	function exists($hash=NULL) { # es in tabula associativa?
		if(FLAT_STORAGE) {
			$h = &$this->resolve_hash($hash);
			return $h and array_key_exists((string)$this, $h);
		}
		$h = $this->walk($hash,0);
		return $h !== NULL;
	}
	function iterate($k, $hash=NULL) {
		$h = &$this->resolve_hash($hash);
		#echo "\$hash:";
		#var_dump($hash);
		$vals = $this->_calculate_valid_values();
		$ret = $vals[$k];
		/*if (!$ret) {
			echo "\$vals ($k, $this, ".$this->word()->id()."):";
			var_dump($vals);
			var_dump($this->word()->id());
			die($this->word()->id()." was not valid");
		}/**/
		if(FLAT_STORAGE) {
			if ($h !== NULL)
			foreach ($vals[$k] as $i=>$k) {
				$found = FALSE;
				foreach (array_keys($h) as $_) {
					if (!PATH($this->_mgr, $_)->issub($this)) continue;
					foreach (explode("/",$_) as $__) {
						if ($__ === $k)
						{ $found = TRUE; break; }
					}
					if ($found) break;
				}
				if (!$found) {
					//error_log("$k does not exist under ".$this);
					unset($ret[$i]);
				}
				//else error_log("$k exists under ".$this);
			}
			return array_values($ret);
		}
		if ($h !== NULL)
		foreach (array_values($ret) as $i=>$k) {
			if (!array_key_exists_r($k, $h)) {
				unset($ret[$i]);
			}
		}
		#echo "\$ret:";
		#var_dump($ret);
		return array_values($ret);
	}
	function issub($other,$ret=FALSE) {
		foreach ($this->mgr()->all_sub_keys as $k) {
			if ($other->key_value($k) != "" and $this->key_value($k) != $other->key_value($k)) {
				//error_log("!$this issub $other");
				return FALSE;
			} elseif ($this->key_value($k) != "") {
				$ret = TRUE;
			}
		}
		//error_log("$this issub $other ? $ret");
		return $ret;
	}
	function hasvalue($hash=NULL) { return $this->get($hash) !== NULL; } # habesne valorem in tabula assocativa?
	function remove($hash=NULL) {
		global $sql_stmts;
		if ($this->issql and $this->_id !== NULL)
			sql_exec($sql_stmts["form_id->delete from forms"], ["i", &$this->_id]);
		$hash = &$this->resolve_hash($hash);
		if(FLAT_STORAGE) {
			$ret = $hash[(string)$this];
			unset($hash[(string)$this]);
			return $ret;
		}
		$h = $this->walk($hash,0);
		if ($h === NULL) return NULL;
		$this->_map_dirty = 1;
		$r = $h[""];
		unset($h[""]);
		# Clean up dead branches if we have killed them:
		while (!count($h) and count($this)) {
			$max = count($this)-1;
			list($h2,$i,$k) = $this->walk_part($hash, $max);
			assert('$this->map[$i] == $k');
			assert('$h == $h2[$k]');
			unset($h2[$k]);
			$h = $h2;
		}
		return $r;
	}
	function remove_all($hash=NULL) {
		$hash = &$this->resolve_hash($hash);
		foreach (array_keys($hash) as $k)
			unset($hash[$k]);
		return $this;
	}
	function move($new, $hash=NULL) {return $new->set($this->remove($hash), $hash);} # move valorem ab te ad novum
	function values($key) { # valores clavis
		return $this->_calculate_valid_values()[$key];
	}
}

function ISPATH($obj) {
	return $obj instanceof _PATH;
}
function PATH($init) {
	$path = array_slice(func_get_args(), 1);
	if (count($path) == 1 and is_int($path[0]))
		$path = $path[0];
	return new _PATH($init, $path);
}
?>
