<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
function _subscript($obj, $k) {
	if (is_array($obj)) return $obj[$k];
	return $obj($k);
}
# Three helpers for initializing a depath.
function register(&$hic, $__k, $__v) {
	if (!is_string($__k) or !is_string($__v)) {
		var_dump($__v,$__k); _die("bad key or value");
	}
	if (array_key_exists($__v, $hic->key2values))
		_die("value '$__v' is already present as a key");
	elseif (array_key_exists($__k, $hic->value2key))
		_die("key '$__k' is already present as a value");
	elseif (!array_key_exists($__k, $hic->key2values))
		$hic->key2values[$__k] = [];
	$hic->key2values[$__k][] = $__v;
	$hic->all_sub_keys[] = $__k;
	if (array_key_exists($__v, $hic->value2key)
	    and $hic->value2key[$__v] != $__k)
		_die("value '$__v' already added with a different key");
	$hic->value2key[$__v] = $__k;
}
function register2(&$hic, $__k, $__vec) {
	foreach ($__vec as $_) register($hic, $__k,$_);
}
function register3(&$hic, $__hash) {
	foreach ($__hash as $__k => $__v)
		register2($hic, $__k, $__v);
}
##
# DEPATH "manager" for linking specific forms of a word.
#
class _DEPATH
{
	function __construct($init=NULL, $aliases=NULL) {
		$this->key2values = [];
		$this->value2key = [];
		$this->all_sub_keys = [];
		$this->level = [];
		$this->simple_keys = [];
		$this->recursive_keys = [];
		if ($aliases === NULL) $aliases = [];
		$this->aliases = $aliases;
		#echo "\$init:\n";
		#var_dump($init);
			if ($init !== NULL)
		foreach ($init as $k => $v) {
			if (!is_array($v)) _die("bad type, not array");
			if (is_vec($v)) {
				$this->simple_keys[] = $k;
				register2($this,$k,$v);
			} else {
				$this->recursive_keys[] = $k;
				foreach ($v as $_ => &$v_) {
					register($this,$k,$_);
					#echo "\$v_:\n";
					#var_dump($v_);
					if (!ISDEPATH($v_))
						$v_ = DEPATH($v_, $aliases);
					$this->all_sub_keys = array_merge($this->all_sub_keys, $v_->all_sub_keys);
					register3($this, $v_->key2values);
				}
			}
			$this->level[$k] = $v;
		}
		# Sort and remove duplicates
		$this->all_sub_keys = array_values(array_unique($this->all_sub_keys));
		foreach (array_keys($this->key2values) as $k)
			$this->key2values[$k] = array_unique($this->key2values[$k]);
	}
	function is_key($key) {
		return array_key_exists($key, $this->key2values);
	}
	function find_key($val) { return $this->value2key[$val]; }
	function key_index($key) {
		foreach (array_keys($this->all_sub_keys) as $k)
			if ($this->all_sub_keys[$k] == $key) return $k;
		_die("key '$key' not found");
	}
	function is_value($value) {
		return array_key_exists($value, $this->value2key);
	}
	function value_index($value) {
		$key = $this->find_key($value);
		if ($key !== NULL)
			if (FALSE !== ($v = array_search($value, $this->key2values[$key])))
				return $v;
		_die("value '$value' not found");
	}
	function resolve_alias($value, $key=NULL) {
		// Maybe it's a value already?
		if (key_exists($value, $this->value2key)) return $value;
		$ret = $key ? subscript(subscript($this->aliases, $key), $value) : NULL;
		if (!is_string($ret))
			$ret = subscript($this->aliases, $value);
		if (!is_string($ret)) _die("value '$value' has no alias for key ".($key==null?'nil':"'$key'"));
		return $ret;
	}
	function add_alias($alias, $value, $key=NULL) {
		if (!array_key_exists($value, $this->value2key))
			_die("bad value '$value'");
		if ($key !== NULL)
			$this->aliases[$key][$alias] = $value;
		else $this->aliases[$alias] = $value;
	}
}

function ISDEPATH($obj) {
	return $obj instanceof _DEPATH;
}
function DEPATH($init=NULL, $aliases=NULL) {
	if (function_exists("ISWORD") and ISWORD($init)) return $init->mgr();
	else return new _DEPATH($init, $aliases);
}
?>
