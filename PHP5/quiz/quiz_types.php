<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/quiz/common.php');

function get_pick($i,$i2=NULL) {
	return function ($_) use ($i,$i2) {
		return $i2===NULL ? $_[$i] : $_[$i][$i2];
	};
}
function make_pick($pick, $i,$i2=NULL) {
	return function (&$_,$db,$path) use ($pick,$i,$i2) {
		if (!safe_get($i,$_))
			if ($path === null) $_[$i] = $pick->rand($db);
			else $_[$i] = $pick->rand($path);
		return $i2===NULL ? $_[$i] : $_[$i][$i2];
	};
}
function make_picks($pick, $n_correct, $i,$i2=NULL) {
	return function (&$_,$db,$path) use ($pick,$n_correct,$i,$i2) {
		$n = $pick->n;
		$pick->n = $n_correct;
		$_1 = $pick->rand($path);
		$pick->n = $n-$n_correct;
		$excl = $pick->exclude;
		$pick->exclude = array_merge($excl,$_1);
		$_2 = $pick->rand($db);
		$pick->n = $n;
		$pick->exclude = $excl;
		#error_log(var_export($_1,1).var_export($_2,1));
		$_[$i] = array_merge($_1, $_2);
		return $i2===NULL ? $_[$i] : $_[$i][$i2];
	};
}
function eq_pick($i, $v) {
	return function ($_) use ($i,$v) {
		return $_[$i]==$v;
	};
}
function fn_and() {
	$arg = func_get_args();
	return function ($_) use ($arg) {
		foreach ($arg as $fn)
			if (!$fn($_)) return FALSE;
		return TRUE;
	};
}
function fn_or() {
	$arg = func_get_args();
	return function ($_) use ($arg) {
		foreach ($arg as $fn)
			if ($fn($_)) return TRUE;
		return FALSE;
	};
}

/*function make_matching($map) {
	global $OP_MULTIPLE_CHOICE;
	global $OP_PARAGRAPH;
	$ret = [
		"help" => "Match each word to the one most similar in meaning",
		"selections" => [
			"choices"=>PICK(count($map),array_keys($map)),
			"answers"=>$map,
		],
		"sentence" => [
		],
	];
	for ($i=0;$i<count($map);$i++) {
		$ret["sentence"] = array_merge($ret["sentence"], [
			get_pick("choices",$i),
			$OP_MULTIPLE_CHOICE,
			$OP_PARAGRAPH
		]);
		$ret["choices$i"] = [
			"correct" => get_matching("choices","answers",$i),
		];
		$ret["choices$i-tooltip"] = "Choose synonym";
		for ($_=0;$_ < count($map);$_++) {
			if ($i === $_) continue;
			$ret["choices$i"][] = get_matching("choices","answers",$_);
		}
	}
	return $ret;
}*/
function get_matching($i,$i2,$i3) {
	return function ($_) use ($i,$i2,$i3) {
		return $_[$i2][$_[$i][$i3]];
	};
}
function get_matching2($i,$i2,$i3,$i4) {
	return function ($_) use ($i,$i2,$i3,$i4) {
		return $_[$i2][$_[$i][$_[$i4][$i3]]];
	};
}
/*function make_matching($map) {
	global $OP_MULTIPLE_CHOICE;
	global $OP_PARAGRAPH;
	$ret = [
		"help" => "Match each word to the one most similar in meaning",
		"selections" => [
			"choices"=>PICK(count($map),array_keys($map)),
			"order"=>PICK(count($map),array_keys(array_keys($map))),
			"answers"=>$map,
		],
		"sentence" => [
		],
	];
	$ret["sentence"][] = "<div class='answers' style='float:right;'>";
	for ($i=0;$i<count($map);$i++) {
		$ret["sentence"][] = ($i+1).".";
		$ret["sentence"][] = get_matching2("choices","answers",$i,"order");
		$ret["sentence"][] = $OP_PARAGRAPH;
	}
	$ret["sentence"][] = "</div>";
	for ($i=0;$i<count($map);$i++) {
		$ret["sentence"] = array_merge($ret["sentence"], [
			get_pick("choices",$i),
			$OP_MULTIPLE_CHOICE,
			$OP_PARAGRAPH
		]);
		$ret["choices$i"] = [
			"no_shuffle" => true,
		];
		$ret["choices$i-tooltip"] = "Choose synonym";
		for ($_=0;$_ < count($map);$_++) {
			$v = ($_+1).".";
			$v = [
				"correct"=>function($pick_db) use ($i,$_){
					return $i === $pick_db["order"][$_];
				},
				"value"=>$v
			];
			$ret["choices$i"][] = $v;
		}
	}
	return $ret;
}/*/
function make_matching($map) {
	global $OP_MATCHING_CHOICES;
	global $OP_PARAGRAPH;
	$ret = [
		"help" => "Match each word to the one most similar in meaning",
		"selections" => [
			"choices"=>PICK(count($map),array_keys($map)),
			"order"=>PICK(count($map),array_keys(array_keys($map))),
			"answers"=>$map,
		],
		"sentence" => [
		],
	];
	$ret["sentence"][] = HTML("<table class='jquiz-matching'><tr><th>");
	for ($i=0;$i<count($map);$i++) {
		$ret["sentence"][] = HTML("</th><th>".($i+1).".");
		$ret["sentence"][] = get_matching2("choices","answers",$i,"order");
	}
	$ret["sentence"][] = HTML("</th></tr>");
	$size = count($map);
	$half_size = round($size/2);
	$half_size1 = $size-$half_size;
	$ret["wrap"] = [];
	for ($i=0;$i<$size;$i++) {
		$ret["sentence"] = array_merge($ret["sentence"], [
			HTML("<tr><th>"),
			get_pick("choices", $i),
			HTML("</th>"),
			$OP_MATCHING_CHOICES,
			HTML("</tr>"),
		]);
		$ret["choices$i"] = [
			"no_shuffle" => true,
		];
		$ret["wrap"]["answer$i"] = [
			"user-correct" => ["<td class='jquiz-correct' colspan='$size'>","</td>"],
			"user-incorrect" => ["<td class='jquiz-incorrect' colspan='$half_size1'>","</td>"],
			"machine" => ["<td class='jquiz-other' colspan='$half_size'>","</td>"],
		];
		$ret["choices$i-tooltip"] = "Choose synonym";
		for ($_=0;$_ < $size;$_++) {
			$ret["choices$i"][] = function($pick_db) use ($i,$_,$map) {
				$v = get_matching2("choices","answers",$_,"order");
				$v = $v($pick_db);
				$correct = ($i === $pick_db["order"][$_]);
				return [
					"correct"=>$correct,
					/*"value"=>("<td class='jquiz-".($correct?'correct':'incorrect')."' colspan='".($correct?count($map):$half_size)."'>".($_+1).". $v</td>"),*/
					"value"=>$v,
				];
			};
		}
	}
	$ret["sentence"][] = HTML("</table>");
	return $ret;
}
/*/
function make_matching($map) {
	global $OP_MATCHING;
	global $OP_PARAGRAPH;
	$ret = [
		"help" => "Match each word to the one most similar in meaning",
		"selections" => [
			"choices"=>PICK(count($map),array_keys($map)),
			"order"=>PICK(count($map),array_keys(array_keys($map))),
			"answers"=>$map,
		],
		"sentence" => [
			$OP_MATCHING,
		],
	];
	$ret["matching0"] = function($pick_db) {
		$ret = [[],[]];
		foreach ($pick_db["order"] as $i) {
			$ret[0][] = $pick_db["choices"][$i];
			$ret[1][] = $pick_db["answers"][$pick_db["choices"][$i]];
		}
		return $ret;
	};
	$ret["matching0-tooltip"] = "Choose synonym";
	return $ret;
}/**/

function make_chart($w,$values=NULL) {
	if ($values === NULL) $values = word_table_values($w);
	list ($values0,$values1,$values2,$values3,$values4) = $values;
	global $OP_USER_INPUT;
	$ret = [
		"help" => "Fill in the chart for “".$w->name()."”.",
		"selections" => [],
		"sentence" => [/*function($pick_db,$db) use($w,$values0,$values1,$values2,$values3,$values4) {

		}*/],
	];
	$i = 0;
	$get_question = function($word) use(&$i,&$ret) {
		$ret["answer$i-hidden"] = TRUE;
		$ret["answer$i"] = [format_word($word)];
		$ret["answer$i-tooltip"] = "Enter form";
		$i++;
		return '<input>';
	};
	ob_start();
	do_table(
		$w,$values0,$values1,$values2,$values3,$values4,
		"format_value",
		$get_question,
		NULL, NULL,
		0
	);
	$table = explode("<input>",ob_get_contents());
	ob_clean();
	$i = count($table)-1;
	foreach ($table as $r) {
		$ret["sentence"][] = HTML($r);
		if ($i) $ret["sentence"][] = $OP_USER_INPUT;
		$i-=1;
	}
	return $ret;
}
function which($lang,$spart,$key,$given=NULL,$rand=NULL,$name=NULL) {
	global $OP_MULTIPLE_CHOICE;
	global $OP_PARAGRAPH;
	global $OP_RPAREN;
	global $OP_LQUOTE;
	global $OP_RQUOTE;
	$selections = [];
	$path = [];
	$mgr = defaultDB()->get_mgr($lang,$spart);
	$given = PATH($mgr,$given);
	$paren = [];
	$_gender = null;
	$recurse = function($mgr) use(&$_gender,$spart,$given,&$recurse,&$path,&$paren,$rand,$key,$lang) {
		global $OP_LPAREN;
		foreach ($mgr->simple_keys as $k) {
			if ($given->key_exists($k)) {
				$path[] = $given->key_value($k);
			} else {
				if ($k === "gender" and $spart === "verb")
					$_gender = make_pick(PICK($k,safe_get($k,$rand))->l($lang),$k);
				else {
					$path[] = make_pick(PICK($k,safe_get($k,$rand))->l($lang),$k);
					if ($k !== $key) {
						if (!$paren) $paren[] = $OP_LPAREN;
						$paren[] = make_pick(PICK($k,safe_get($k,$rand))->l($lang),$k);
					}
				}
			}
		}
		foreach ($mgr->recursive_keys as $k) {
			if ($given->key_exists($k)) {
				$path[] = $given->key_value($k);
				$recurse($mgr->level[$k][$given->key_value($k)]);
			}
		}
	};
	$recurse($mgr);
	if ($paren) $paren[] = $OP_RPAREN;
	$get_val = function($pick_db,$v) use($mgr,$path) {
		$p = $path;
		$path = PATH($pick_db["word"]);
		foreach ($p as $k=>$_) {
			$path->add2([$k=>_process_value($_,$pick_db,defaultDB(),$path)]);
		}
		$path->add($v);
		return $path->get();
	};
	$answers = [];
	foreach ($mgr->key2values[$key] as $v) {
		$answers[] = [
			"correct" => function($pick_db) use($v,$get_val) {
				return $pick_db["result"] === $get_val($pick_db,$v);
			},
			"value"=>$v,
		];
	}
	$ret = [
		"help" => "What $key is this word?",
		"selections" => $selections,
		"sentence" => array_merge([
			[
				"lang" => $lang,
				"speechpart" => $spart,
				"path" => $path,
				"attr" => [
					"!template" => NULL,
					"!hidden" => NULL,
				],
				"store_word" => "word",
				"store" => "result",
			]
		], $paren, [
			$OP_PARAGRAPH,
			$OP_MULTIPLE_CHOICE
		]),
		"choices0" => $answers,
		"choices0-tooltip" => "What $key?",
		"choices0-no-shuffle" => true,
	];
	if ($name !== null)
		$ret["sentence"][0]["name"] = $name;
	if ($_gender !== null)
		$ret["sentence"][0]["verb-gender"] = $_gender;
	return $ret;
}

function which2($lang,$spart,$key,$given=NULL,$rand=NULL,$name=NULL) {
	global $OP_MULTIPLE_CHOICE;
	global $OP_PARAGRAPH;
	global $OP_LPAREN;
	global $OP_RPAREN;
	global $OP_LQUOTE;
	global $OP_RQUOTE;
	$selections = [];
	$path = [];
	$mgr = defaultDB()->get_mgr($lang,$spart);
	$given = PATH($mgr,$given);
	$_gender = null;
	$recurse = function($mgr) use(&$_gender,$spart,$given,&$recurse,&$path,$rand,&$selections,$lang) {
		foreach ($mgr->simple_keys as $k) {
			if ($given->key_exists($k)) {
				$path[] = $given->key_value($k);
			} else {
				if ($k === "gender" and $spart === "verb")
					$_gender = make_pick(PICK($k,safe_get($k,$rand))->l($lang),$k);
				else $path[] = make_pick(PICK($k,safe_get($k,$rand))->l($lang),$k);
			}
		}
		foreach ($mgr->recursive_keys as $k) {
			if ($given->key_exists($k)) {
				$path[] = $given->key_value($k);
				$recurse($mgr->level[$k][$given->key_value($k)]);
			}
		}
	};
	$recurse($mgr);
	$answers = [];
	foreach ($mgr->key2values[$key] as $v) {
		$answers[] = [
			"correct" => function($pick_db) use($v) {
				return safe_get($v, $pick_db);
			},
			"value"=>$v,
		];
	}
	$ret = [
		"help" => "What $key is this word?",
		"selections" => $selections,
		"sentence" => [
			[
				"lang" => $lang,
				"speechpart" => $spart,
				"path" => $path,
				"attr" => [
					"!template" => NULL,
					"!hidden" => NULL,
				],
				"store_word" => "word",
				"store" => "result",
			],
			// Calculate correct responses
			function(&$pick_db) use($key) {
				global $mysqli;
				$query = $mysqli->prepare("
					SELECT form_tag FROM forms
					WHERE word_id = (?)
					AND form_value = (?)
				");
				$res = NULL;
				sql_getmany($query, $res, ["is",$pick_db["word"]->id(),$pick_db["result"]]);
				$query->close();
				foreach ($res as $tag) {
					$p = PATH($pick_db["word"], $tag);
					$v = $p->key_value($key);
					$pick_db[$v] = TRUE;
				}
				return FALSE; // No word
			},
			$OP_PARAGRAPH,
			$OP_MULTIPLE_CHOICE
		],
		"choices0" => $answers,
		"choices0-tooltip" => "What $key?",
		"choices0-no-shuffle" => true,
	];
	if ($name !== null)
		$ret["sentence"][0]["name"] = $name;
	if ($_gender !== null)
		$ret["sentence"][0]["verb-gender"] = $_gender;
	return $ret;
}

function which3($lang,$spart,$key,$N=NULL,$given=NULL,$rand=NULL,$name=NULL) {
	global $OP_MULTIPLE_CHOICE;
	global $OP_PARAGRAPH;
	global $OP_LPAREN;
	global $OP_RPAREN;
	global $OP_LQUOTE;
	global $OP_RQUOTE;
	$selections = [];
	$path = [];
	$mgr = defaultDB()->get_mgr($lang,$spart);
	$given = PATH($mgr,$given);
	$_gender = null;
	if ($N === NULL)
		$N = count($mgr->key2values[$key]);
	$selections["answers"] = PICK($N, $key, safe_get($key,$rand))->l($lang);
	$recurse = function($mgr) use(&$_gender,$spart,$given,&$recurse,&$path,$rand,&$selections,$lang) {
		foreach ($mgr->simple_keys as $k) {
			if ($given->key_exists($k)) {
				$path[] = $given->key_value($k);
			} else {
				if ($k === "gender" and $spart === "verb")
					$_gender = make_pick(PICK($k,safe_get($k,$rand))->l($lang),$k);
				else $path[] = make_pick(PICK($k,safe_get($k,$rand))->l($lang),$k);
			}
		}
		foreach ($mgr->recursive_keys as $k) {
			if ($given->key_exists($k)) {
				$path[] = $given->key_value($k);
				$recurse($mgr->level[$k][$given->key_value($k)]);
			}
		}
	};
	$recurse($mgr);
	$answers = [];
	$selections[$key] = function($pick_db){
		error_log(var_export($pick_db,1));
		return $pick_db["answers"][0];
	};
	for ($v=0;$v<$N;$v++) {
		$answers[] = [
			"correct" => function($pick_db) use($v) {
				return safe_get($pick_db["answers"][$v], $pick_db);
			},
			"value"=>function($pick_db) use($v) {return $pick_db["answers"][$v];}/*/get_pick("answers", $v)*/,
		];
	}
	$ret = [
		"help" => "What $key is this word?",
		"selections" => $selections,
		"sentence" => [
			[
				"lang" => $lang,
				"speechpart" => $spart,
				"path" => $path,
				"attr" => [
					"!template" => NULL,
					"!hidden" => NULL,
				],
				"store_word" => "word",
				"store" => "result",
			],
			// Calculate correct responses
			function(&$pick_db) use($key) {
				global $mysqli;
				$query = $mysqli->prepare("
					SELECT form_tag FROM forms
					WHERE word_id = (?)
					AND form_value = (?)
				");
				$res = NULL;
				sql_getmany($query, $res, ["is",$pick_db["word"]->id(),$pick_db["result"]]);
				$query->close();
				foreach ($res as $tag) {
					$p = PATH($pick_db["word"], $tag);
					$v = $p->key_value($key);
					$pick_db[$v] = TRUE;
				}
				return FALSE; // No word
			},
			$OP_PARAGRAPH,
			$OP_MULTIPLE_CHOICE
		],
		"choices0" => $answers,
		"choices0-tooltip" => "What $key?",
	];
	if ($name !== null)
		$ret["sentence"][0]["name"] = $name;
	if ($_gender !== null)
		$ret["sentence"][0]["verb-gender"] = $_gender;
	return $ret;
}

global $OP_MULTIPLE_CHOICE;
global $OP_USER_INPUT;
global $OP_USER_PARAGRAPH;
global $OP_PARAGRAPH;
global $OP_LQUOTE;
global $OP_RQUOTE;
global $OP_COLON;
global $OP_COMMA;

$GLOBALS["quiz_types"] = [
	"random" => ["name" => "Random"]
];
include_once('quiz_types1.php');
global $quiz_types;

$options = [];
foreach ($quiz_types as $k=>$v) {
	if (array_key_exists("options", $v)) {
		$options = array_merge($options, $v["options"]);
	}
}
$quiz_types["random"]["options"] = $options;
#var_dump($quiz_types[0]);
?>
