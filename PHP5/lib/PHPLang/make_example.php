<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/db.php');
sro('/PHP5/lib/PHPLang/misc.php');
sro('/PHP5/lib/PHPLang/display.php');
function serialize_sentence($vector) {
	return serialize_sentence_part($vector).".";
};
function serialize_sentence_part($vector) {
	$r = "";
	$allow_space = 0;
	foreach ($vector as $w) {
		if (ISOP($w)) $n=$w;
		else $n=OP(format_word($w));
		if ($n->space_before and $allow_space) $r .= " ";
		$allow_space = $n->space_after;
		$r .= $n->text;
	}
	return $r;
};

function find_word_path($word) {
	$path = array_slice(func_get_args(), 1);
	if (!ISWORD($word)) $word = WORD($word);
	$p = PATH($word);
	foreach ($path as $v) {
		if (!is_vec($v))
			foreach (array_keys($v) as $k)
				$path->add($k, $v[$k]);
		else $path->add($v);
	}
	return $p->get();
};

function dbg_msg($msg) {
	#echo "$msg\n";
}

function _process_value($v,&$pick_db,$db,$path=NULL) {
	if (is_callable($v)) return $v($pick_db, $db, $path);
	if (ISPICK($v))
		if (ISPATH($path)) return $v->rand($path);
		else return $v->rand($db);
	if (is_array($v)) {
		$res = [];
		foreach ($v as $k=>$_)
			$res[$k] = _process_value($_,$pick_db,$db,$path);
		return $res;
	}
	return $v;
}

function do_pick($t, $db, &$pick_db, &$reason) {
	if ($db === NULL) $db = defaultDB();
	if ($t === NULL) {
		$reason = "pick was null";
		return $t;
	} elseif (ISOP($t) or ISHTML($t))
		return $t;
	elseif (ISPICK($t))
		return $t->rand($db);
	elseif (is_string($t)) return $t;
	elseif (is_callable($t)) {
		$ret = _process_value($t,$pick_db,$db);
		if ($ret === NULL)
			$reason = "custom function returned NULL";
		return $ret;
	}
	elseif (array_key_exists("condition", $t) and !$t["condition"]($pick_db, $db))
		return FALSE;
	elseif (array_key_exists("literal", $t))
		return $t["literal"];
	$searcher = $db->searcher();
	#var_dump(array_keys($searcher->master));
	if (array_key_exists("name", $t))
		$searcher = $searcher->name($t["name"]);
	if (array_key_exists("language", $t))
		$searcher = $searcher->lang($t["language"]);
	elseif (array_key_exists("lang", $t))
		$searcher = $searcher->lang($t["lang"]);
	if (array_key_exists("speechpart", $t))
		$searcher = $searcher->partofspeech($t["speechpart"]);
	elseif (array_key_exists("spart", $t))
		$searcher = $searcher->partofspeech($t["spart"]);

	if (array_key_exists("attr", $t))
		foreach ($t["attr"] as $k=>$v) {
			$v = _process_value($v,$pick_db,$db);
			$searcher = $searcher->only_with_attr(ATTR($k,$v));
		}

	$word = $searcher->rand();
	if (!ISWORD($word)) {
		$reason = "could not find a word with suitable attrs";
		return;
	}
	$word->read_paths();

	$path = PATH($word);
	if (array_key_exists("path", $t)) {
		$p = $t["path"];
		foreach ($p as $k=>$_) {
			$path->add2([$k=>_process_value($_,$pick_db,$db,$path)]);
		}
	}
	if (array_key_exists("verb-gender", $t)) {
		$g = $t["verb-gender"];
		$g = _process_value($g,$pick_db,$db,$path);
		if ($g !== NULL and $path->exists()) {
			$path->add($g);
			if (!$path->hasvalue()) {
				$path->take("gender");
			}
		}
	}

	if ($path->hasvalue()) {
		return $path->get();
	} else {
		$reason = "path $path didn't exist in word with id ".$word->id()." or was NULL";
		return;
	}
}

##
# Instantiate a template into a sentence, evaluating choices
# randomly, e->g-> PICK() or choosing a word from a part of speech->
#
# A template is a form for a sentence, e.g.
# $example_template = [
#	[
#		"spart" => "noun",
#		"path" => [ PICK("number", 0), PICK("gender", 1), "nominative" ]
#	],
#	$OP_COMMA,
#	[
#		"name"=>"qui, quae, quod",
#		"path" => [ PICK("number", 0), PICK("gender", 1), "accusative" ]
#	],
#	[
#		"spart" => "verb",
#		"attr" => [ATTR("transitive", "true")],
#		"path" => [PICK("tense"), PICK("number"),
#		           "person-1", "active", "indicative"]
#	],
#	$OP_COMMA,
#	[
#		"name" => "sum, esse, fui",
#		"path" => [PICK("number", 0), "person-3", PICK("tense"), "indicative"] # active is an implicit default
#	],
#	[
#		"spart" => "adjective",
#		"path" => [PICK("number", 0), PICK("gender", 1), "nominative"]
#	]
#];
#
function do_template($temp, $db=NULL, &$pick_db=NULL, &$reason=NULL) {
	if ($db === NULL) $db = defaultDB();
	if (is_string($temp)) return $temp;
	$repeats = 0;
	$sentence = NULL;
	$reason = NULL;
	$ignore = NULL;
	$pick_db = _process_value($pick_db,$ignore,$db);
	if ($pick_db === NULL) $reset = [];
	else $reset = &$pick_db;
	while ($repeats < 10 and $sentence === NULL) {
		$repeats += 1;
		$sentence = [];
		$pick_db = &$reset;
		foreach ($temp as $k=>$t) {
			$res = do_pick($t, $db, $pick_db, $reason);
			if ($res === NULL)
				{$reason.=" on key $k";$sentence = NULL; break;}
			elseif ($res !== FALSE)
				$sentence[$k] = $res;
		}
	}
	#var_dump($pick_db);
	if ($sentence !== NULL)
		return array_values($sentence);
};

function do_quiz_many_rand($temp, $selectors, $db=NULL, &$pick_db=NULL) {
	if ($pick_db === NULL) $pick_db = [];
	$sentence = do_template($temp, $db, $pick_db);
	$options = [];
	foreach ($selectors as $select) {
		$option = [];

		$options[] = $option;
	}
	return $sentence;
}
?>
