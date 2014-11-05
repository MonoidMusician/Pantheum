<?php
class _PHPDB implements _DB
{
	function __construct() {
		global $data_dir;
		$this->depaths = [];
		$this->_data = [];
	}
	function _word($id) {
		return $this->_data[$id];
	}
	function find_all_words($name=NULL, $lang=NULL, $id=NULL) {
		if ($id !== NULL) return [$this->_data[$id]];
		if ($name === NULL) _die("need name");

		$res = [];
		foreach ($this->_data as &$word)
		{
			if ($lang !== NULL and $word->lang() !== $lang) continue; else
			if ($name !== NULL and $word->name() !== $name) continue; else
			$res[] = &$word;
		}
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
		if (!ISWORD($w)) $w = WORD($this, $w);
		return $this->add_mgr($w->lang(), $w->speechpart(), $depath);
	}
	function get_mgrW($w) {
		if (!ISWORD($w)) $w = WORD($this, $w);
		return $this->get_mgr($w->lang(), $w->speechpart());
	}
	function addword($name, $w) {
		if (!$w->id())
		{ $w->set_id($name); $w->set_name($name); }
		$this->_data[$name] = $w;
	}
	function getword($name) {
		return $this->_data[$name];
	}
	function is_lang($lang) {
		return $lang == "la";
	}
	function langs() {
		return ["la"];
	}
	function searcher(){return new _PHPDB_searcher($this->_data);}
}
class _PHPDB_searcher
{
	function __construct($master) {
		$this->map = array_values($master);
		#echo "\$this->master:";
		#var_dump($this->master);
	}
	function dup() {
		$n = new _PHPDB_searcher($this->map);
		return $n;
	}
	function all() {
		return $this->map;
	}
	function partofspeech($pos) {
		$map = $this->map;
		foreach ($map as $k=>$v) {
			if ($v->speechpart() !== $pos)
				unset($this->map[$k]);
		}
		#echo "\$this->map (partofspeech=$pos):";
		#var_dump($this->map);
		return $this;
	}
	function name($name) {
		$map = $this->map;
		foreach ($map as $k=>$v) {
			if ($v->name() !== $name)
				unset($this->map[$k]);
		}
		#echo "\$this->map (name=$name):";
		#var_dump($this->map);
		return $this;
	}
	function append($v) {$this->map[] = $v; return $this;}
	function only_with_attr($attr) {
		$map = $this->map;
		foreach ($map as $k=>$v)
			if (!$v->has_attr($attr))
				unset($this->map[$k]);
		#echo "\$this->map (only with attr=".$attr->_key."):";
		#var_dump($this->map);
		return $this;
	}
	function rand($rand=NULL) {
		if (!count($this->map)) return NULL;
		return choose_one($this->map, $rand);
	}
}
?>
