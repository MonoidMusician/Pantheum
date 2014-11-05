<?php
require_once('/var/www/latin/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/make_example.php');
sro('/PHP5/lib/PHPLang/display.php');

function get_pick($i,$i2=NULL) {
	return function ($_) use ($i,$i2) {
		return $i2===NULL ? $_[$i] : $_[$i][$i2];
	};
}
function make_pick($pick, $i,$i2=NULL) {
	return function (&$_,$db,$path) use ($pick,$i,$i2) {
		$_[$i] = $pick->rand($path);
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
				"literal"=>$v
			];
			$ret["choices$i"][] = $v;
		}
	}
	return $ret;
}*/
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
	for ($i=0;$i<count($map);$i++) {
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
		$ret["choices$i-tooltip"] = "Choose synonym";
		for ($_=0;$_ < count($map);$_++) {
			$ret["choices$i"][] = function($pick_db) use ($i,$_,$map) {
				$v = get_matching2("choices","answers",$_,"order");
				$v = $v($pick_db);
				return [
					"correct"=>($i === $pick_db["order"][$_]),
					"literal"=>("<td colspan='".count($map)."'>".($_+1).". $v</td>"),
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

function make_chart($w,$values0,$values1,$values2,$values3,$values4) {
	global $OP_USER_INPUT;
	$ret = [
		"help" => "Fill in the chart for this irregular pronoun.",
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
	/*$i = 0;
	if (!$values4) $values4 = [FALSE];
	if (!$values3) $values3 = [FALSE];
	if (!$values2) $values2 = [FALSE];
	if (!$values1) $values1 = [FALSE];
	if (!$values0) $values0 = [FALSE];
	foreach ($values0 as $_0) foreach ($values1 as $_1)
	foreach ($values2 as $_2) foreach ($values3 as $_3)
	foreach ($values4 as $_4) {
		$p = PATH($w, $_0,$_1,$_2,$_3,$_4);
		$ret["answer$i-hidden"] = TRUE;
		$ret["answer$i"] = [format_word($p->get())];
		$ret["answer$i-tooltip"] = "Enter form";
		$i++;
	}*/
	return $ret;
}

global $OP_MULTIPLE_CHOICE;
global $OP_USER_INPUT;
global $OP_PARAGRAPH;
global $OP_LQUOTE;
global $OP_RQUOTE;
global $OP_COLON;
global $OP_COMMA;

$w = WORD(defaultDB(), 212);
$w->read_paths();
$values4 = $w->path()->iterate("case");
$values3 = $w->path()->iterate("gender");
$values2 = $w->path()->iterate("number");
$GLOBALS["quiz_types"] = [
	[
		"name" => "Random",
	],
	[
		"name" => "Test",
		"options" => [
			make_chart($w,NULL,NULL,$values2,$values3,$values4),
		]
	],
	[
		"name" => "English to Latin: Simple verbs",
		"category" => "English to Latin",
		"options" => [[
			"help" => "Translate this into Latin, either the
			           whole sentence or just the
			           (inflected) verb. Double check your
			           spelling! Word order does not matter.",
			"selections" => [
				"person" => PICK("person"),
				"number" => PICK("number"),
				"prep" => PICK([NULL,"in","ex","prō"]),
				"tense" => PICK(["present","imperfect"]),
			],
			"sentence" => [
				/**/function($pick_db) {
					$i = [
						"person-1"=>[
						"singular"=>"I",
						"plural"=>"We"
						],"person-2"=>[
						"singular"=>"You (sg)",
						"plural"=>"You (pl)",
						],"person-3"=>[
						"singular"=>"Grumiō",
						"plural"=>"The slaves",
					]];
					return $i[$pick_db["person"]][$pick_db["number"]];
				},
				function($pick_db) {
					if ($pick_db["tense"] === "present") {
						if ($pick_db["person"] === "person-3" and $pick_db["number"] === "singular") return "walks";
						else return "walk";
					} elseif ($pick_db["tense"] === "imperfect") {
						if (($pick_db["person"] === "person-3" or $pick_db["person"] === "person-1") and $pick_db["number"] === "singular") $be = "was";
						else $be = "were";
						return "$be walking";
					}
				},
				function($pick_db){
					$prep = $pick_db["prep"];
					if ($prep === "ex") return "out of the forum";
					elseif ($prep === "in") return "in the forum";
					elseif ($prep === "prō") return "in front of the forum";
					return FALSE;
				},
				$OP_COLON,
				$OP_PARAGRAPH,
				$OP_USER_INPUT
			],
			"answer0" => function($pick_db,$db) {
				$subj = ["person-1"=>["singular"=>"ego","plural"=>"nōs"],
				"person-2"=>["singular"=>"tu","plural"=>"vōs"],
				"person-3"=>["singular"=>"Grumiō","plural"=>"servi"]];
				$subj = $subj[$pick_db["person"]][$pick_db["number"]];
				$verb = $db->searcher()->name("ambulo")->spart("verb")->rand();
				$verb->read_paths();
				$path = PATH($verb, "active/indicative");
				$path->add($pick_db["tense"]);
				$path->add($pick_db["person"]);
				$path->add($pick_db["number"]);
				$verb = $path->get();
				$prep = $pick_db["prep"];
				if ($prep === "prō"
				 or $prep === "in"
				 or $prep === "ex") $prep .= " forō ";
				$correct = ["$subj $prep$verb", "$verb"];
				if ($prep) $permissible = permute_sentence([$verb],[$subj,$prep]);
				else $permissible = permute_sentence([$verb],[$subj]);
				return ["correct"=>$correct, "acceptable"=>$permissible];
			},
			"answer0-tooltip" => "Latin translation",
		]],
	],
	[
		"name" => "Define random words",
		"options" => [[
			"help" => "Choose a correct definition for the given word",
			"selections" => [
				"word"=>function($_, $db, $path) {
					$s = $db->searcher();
					$s->stmt .= " WHERE word_id in (SELECT word_id FROM definitions) AND word_lang = 'la'";
					return $s->rand();
				},
			],
			"sentence" => [
				"Choose a correct definition for ",
				$OP_LQUOTE,
				function($pick_db) { return format_word($pick_db["word"]->name()); },
				$OP_RQUOTE, $OP_COLON,
				$OP_MULTIPLE_CHOICE,
			],
			"choices0" => function($pick_db, $db) {
				global $mysqli;
				$query = $mysqli->prepare("SELECT def_id FROM definitions WHERE word_id = (?) ORDER BY rand() LIMIT 1");
				$res0 = NULL;
				sql_getmany($query, $res0, ["i",$pick_db["word"]->id()]);
				$query->close();
				if (!$res0) return NULL;
				$query = $mysqli->prepare("SELECT def_id FROM definitions");
				$res1 = NULL;
				sql_getmany($query, $res1, []);
				$query->close();
				if (!$res1) return NULL;
				$res1 = choose_n_unique($res1, 4);
				$res = array_merge($res0, $res1);
				foreach ($res as &$r) $r=definition(defaultDB(), $r);
				foreach ($res as &$r) $r=[
					"correct" => $r->word()->id() === $pick_db["word"]->id(),
					"literal" => ((string)$r->path() ? "(".$r->path().") " : "").str_replace("\n", ", ", $r->value()),
				];
				return $res;
			},
			"choices0-tooltip"=>"Pick correct definition",
		]]
	],
	[
		"name" => "Synonyms",
		"options" => [
			make_matching([
				"cædō"=>"interficiō",
				"exeō"=>"discēdō",
				"pulsō"=>"verberō",
				"volō"=>"cupiō",
			]),
			make_matching([
				"turba"=>"multitūdō",
				"mīles"=>"arma",
				"imperātor"=>"rēx",
			]),
		],
	],
	[
		"name" => "Hic, Haec, Hoc",
		"options" => [[
			"help" => "Fill in the chart for this irregular pronoun.",
			"selections" => [
			],
			"sentence" => [
				HTML("<table class='jquiz-matching'>"
				."<tr><th></th><th>Hic (m)</th><th>Haec (f)</th><th>Hoc (n)</th></tr>"),
				HTML("<tr><th>Nom</th><td>"),
				$OP_USER_INPUT,
				HTML("</td><td>"),
				$OP_USER_INPUT,
				HTML("</td><td>"),
				$OP_USER_INPUT,
				HTML("</td></tr>"),
				HTML("<tr><th>Acc</th><td>"),
				$OP_USER_INPUT,
				HTML("</td><td>"),
				$OP_USER_INPUT,
				HTML("</td><td>"),
				$OP_USER_INPUT,
				HTML("</td></tr>"),
			],
			"answer0" => [
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "hic",
					"path" => [
						"singular", "masculine",
						"nominative",
					]
				],
			],
			"answer1" => [
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "hic",
					"path" => [
						"singular", "feminine",
						"nominative",
					]
				],
			],
			"answer2" => [
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "hic",
					"path" => [
						"singular", "neuter",
						"nominative",
					]
				],
			],
			"answer3" => [
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "hic",
					"path" => [
						"singular", "masculine",
						"accusative",
					]
				],
			],
			"answer4" => [
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "hic",
					"path" => [
						"singular", "feminine",
						"accusative",
					]
				],
			],
			"answer5" => [
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "hic",
					"path" => [
						"singular", "neuter",
						"accusative",
					]
				],
			],
			"answer0-tooltip"=>"Enter form",
			"answer1-tooltip"=>"Enter form", 
			"answer2-tooltip"=>"Enter form", 
			"answer3-tooltip"=>"Enter form",
			"answer4-tooltip"=>"Enter form", 
			"answer5-tooltip"=>"Enter form", 
			"answer6-tooltip"=>"Enter form",
			"answer7-tooltip"=>"Enter form", 
			"answer8-tooltip"=>"Enter form", 
			"answer9-tooltip"=>"Enter form",
			"answer10-tooltip"=>"Enter form", 
			"answer11-tooltip"=>"Enter form", 
		]]
	],
	/*[
		"name" => "Noun–verb agreement",
		"options" => [[
			"help" => "Choose the pronoun that correctly 
			           restates the subject of the verb.
			           (Note: for third-person pronouns,
			           assume both options are of the same
			           gender and thus only differ in number.)",
			"selections" => [
				#PICK(["true","false"]),
				0=>"true",
				1=>PICK(3,"person"),
				2=>PICK(2,"number"),
				3=>PICK("gender"),
				4=>NULL,
				5=>PICK("voice"),
				6=>NULL,
				"+adj"=>PICK([TRUE,FALSE],[TRUE=>6,FALSE=>2]),
			],
			"sentence" => [
				$OP_MULTIPLE_CHOICE,
				[ "condition" => eq_pick("+adj", TRUE),
					"lang" => "la",
					"speechpart" => "adjective",
					"path" => [
						"nominative",
						"positive",
						get_pick(2,0),
						get_pick(3)
					]
				],
				[ "condition" => fn_and(eq_pick(0,"true"), eq_pick(5,"active")),
					"lang" => "la",
					"speechpart" => "noun",
					"path" => [
						"accusative", PICK("number"),
						PICK("gender")
					],
				],
				[
					"lang" => "la",
					"speechpart" => "verb",
					"attr" => [
						"transitive" => get_pick(0),
					],
					"path" => [
						PICK(["indicative","subjunctive"]),
						get_pick(1,0),
						get_pick(2,0),
						make_pick(PICK("tense"), 4),
						get_pick(5),
					],
					"verb-gender" => get_pick(3)
				]
			],
			"choices0" => [
				"correct" => [
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "personal pronoun",
					"attr" => [
						"person" => get_pick(1,0)
					],
					"path" => [
						"number" => get_pick(2,0),
						"nominative", get_pick(3)
					]
				],
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "personal pronoun",
					"attr" => [
						"person" => get_pick(1,1)
					],
					"path" => [
						"number" => get_pick(2,0),
						"nominative", get_pick(3)
					]
				],
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "personal pronoun",
					"attr" => [
						"person" => get_pick(1,2)
					],
					"path" => [
						"number" => get_pick(2,0),
						"nominative", get_pick(3)
					]
				],
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "personal pronoun",
					"attr" => [
						"person" => get_pick(1,0)
					],
					"path" => [
						"number" => get_pick(2,1),
						"nominative", get_pick(3)
					]
				],
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "personal pronoun",
					"attr" => [
						"person" => get_pick(1,1)
					],
					"path" => [
						"number" => get_pick(2,1),
						"nominative", get_pick(3)
					]
				],
				[
					"lang" => "la",
					"speechpart" => "pronoun",
					"name" => "personal pronoun",
					"attr" => [
						"person" => get_pick(1,2)
					],
					"path" => [
						"number" => get_pick(2,1),
						"nominative", get_pick(3)
					]
				]
			],
			"choices0-tooltip"=>"Quis/qui", 
		]]
	],
	[
		"name"=>"Relative Pronouns",
		"options" => [[
			"help" => "Choose the pronoun that correctly 
			           fills in the blank.
			           ",
			"selections" => [
				0=>NULL,
				1=>NULL,
				2=>PICK(2, ["dative","genitive","ablative"]),
			],
			"sentence" => [
				[
					"spart" => "noun",
					"path" => [ make_picks(PICK(2,"number"), 1, 0,0), make_picks(PICK(2, "gender"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_MULTIPLE_CHOICE,
				[
					"spart" => "verb",
					"attr" => ["transitive"=>"true"],
					"path" => ["indicative", "active", "person-1",
					           PICK("tense"), PICK("number")],
					"verb-gender" => get_pick(1,0)
				],
				$OP_COMMA,
				[
					"name" => "sum",
					"path" => [get_pick(0,0), "person-3", "indicative", "active", PICK("tense")]
				],
				[
					"spart" => "adjective",
					"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
				]
			],
			"choices0" => [
				"correct" => [
					"name"=>"relative pronoun",
					"path" => [ get_pick(0,0), get_pick(1,0), "accusative" ]
				],
				[
					"name"=>"relative pronoun",
					"path" => [ get_pick(0,1), get_pick(1,0), "accusative" ]
				],
				[
					"name"=>"relative pronoun",
					"path" => [ get_pick(0,0), get_pick(1,1), "accusative" ]
				],
				[
					"name"=>"relative pronoun",
					"path" => [ get_pick(0,0), get_pick(1,1), get_pick(2,0) ]
				],
				[
					"name"=>"relative pronoun",
					"path" => [ get_pick(0,1), get_pick(1,0), get_pick(2,1) ]
				],
			],
			"choices0-tooltip"=>"",
		], [
			"help" => "Choose the pronoun that correctly 
			           fills in the blank.
			           ",
			"selections" => [
				0=>NULL,
				1=>NULL,
				2=>PICK(2, ["dative","genitive","ablative"]),
			],
			"sentence" => [
				[
					"spart" => "noun",
					"path" => [ make_picks(PICK(2,"number"), 1, 0,0), make_picks(PICK(2, "gender"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_MULTIPLE_CHOICE,
				[
					"spart" => "verb",
					"attr" => ["transitive"=>"false"],
					"path" => ["indicative/active/person-3",
					           PICK("tense"), get_pick(0,0)],
					"verb-gender" => get_pick(1,0),
				],
				$OP_COMMA,
				[
					"name" => "sum",
					"path" => [get_pick(0,0), "person-3/indicative/active", PICK("tense")]
				],
				[
					"spart" => "adjective",
					"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
				]
			],
			"choices0" => [
				"correct" => function (&$_,$db,$path) {
					$w = $db->searcher()->name("relative pronoun")->spart("pronoun")->rand();
					$w->read_paths();
					$qui = PATH($w, "masculine/nominative/singular")->get();
					$quae = PATH($w, "feminine/nominative/singular")->get();
					$quod = PATH($w, "neuter/nominative/singular")->get();
					if ($_[1][0] == "masculine") {
						$_[10] = [$qui,$quae,$quod];
					} elseif ($_[1][0] == "feminine" or ($_[1][0] == "neuter" and $_[0][0] == "plural")) {
						$_[10] = [$quae,$qui,$quod];
					} else {
						$_[10] = [$quod,$qui,$quae];
					}
					return $_[10][0];
				},
				get_pick(10,1),
				get_pick(10,2),
				[
					"name"=>"relative pronoun",
					"path" => [ get_pick(0,0), get_pick(1,1), get_pick(2,0) ]
				],
				[
					"name"=>"relative pronoun",
					"path" => [ get_pick(0,1), get_pick(1,0), get_pick(2,1) ]
				],
			],
			"choices0-tooltip"=>"",
		], [
			"help" => "Choose the pronoun that correctly 
			           fills in the blank.
			           ",
			"selections" => [
				0=>NULL,
				1=>NULL,
				2=>PICK(2, ["dative","genitive","ablative"]),
			],
			"sentence" => [
				[
					"spart" => "noun",
					"path" => [ make_picks(PICK(2,"number"), 1, 0,0), make_picks(PICK(2, "gender"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_USER_INPUT,
				[
					"spart" => "verb",
					"attr" => ["transitive"=>"false"],
					"path" => ["indicative/active/person-3",
					           PICK("tense"), get_pick(0,0)],
					"verb-gender" => get_pick(1,0),
				],
				$OP_COMMA,
				[
					"name" => "sum",
					"path" => [get_pick(0,0), "person-3/indicative/active", PICK("tense")]
				],
				[
					"spart" => "adjective",
					"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
				]
			],
			"answer0" => [
				"name"=>"relative pronoun",
				"path" => [ get_pick(0,0), get_pick(1,0), "nominative" ]
			],
			"answer0-tooltip"=>"relative pronoun form",
		]]
	],*/
];
global $quiz_types;

$options = [];
foreach ($quiz_types as $k=>$v) {
	if (array_key_exists("options", $v)) {
		$options = array_merge($options, $v["options"]);
	}
}
$quiz_types[0]["options"] = $options;
#var_dump($quiz_types[0]);
?>
