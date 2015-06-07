<?php
global $quiz_types;
global $df_exclude;
$quiz_types = array_merge($quiz_types,[
	"random-definitions1" => [
		"name" => "Define random words",
		"category" => "Vocabulary",
		"lang" => "la",
		"options" => [[
			"help" => "Choose a correct definition for the given word",
			"selections" => [
				"word"=>function($_, $db, $path) {
					$s = $db->searcher();
					$s->stmt .= "
						WHERE word_id IN (
							SELECT word_id FROM definitions
							WHERE def_lang = 'en'
						)
						AND word_lang = 'la'
						AND word_id NOT IN (
							SELECT word_id FROM attributes
							WHERE attr_tag = 'template' OR attr_tag = 'hidden'
						)";
					$s->args = [];
					return $s->rand();
				},
			],
			"sentence" => [
				$OP_LQUOTE,
				function($pick_db) { return format_word($pick_db["word"]->name()); },
				$OP_RQUOTE, $OP_COLON,
				$OP_MULTIPLE_CHOICE,
			],
			"choices0-language" => "en",
			"choices0" => function($pick_db, $db) {
				global $mysqli;
				$query = $mysqli->prepare("
					SELECT DISTINCT def_id FROM definitions
					WHERE def_lang = 'en'
					AND word_id = (?)
					ORDER BY rand()
					LIMIT 1
				");
				$res0 = NULL;
				sql_getmany($query, $res0, ["i",$pick_db["word"]->id()]);
				$query->close();
				if (!$res0) return NULL;
				$query = $mysqli->prepare("
					SELECT DISTINCT def_id FROM definitions
					WHERE def_lang = 'en'
					AND def_id != (?)
					AND def_value != ''
					AND word_id in (
						SELECT word_id FROM words WHERE word_lang = 'la'
					)
					AND word_id NOT IN (
						SELECT word_id FROM attributes
						WHERE attr_tag = 'template' OR attr_tag = 'hidden'
					)
				");
				$res1 = NULL;
				sql_getmany($query, $res1, ["i", $res0[0]]);
				$query->close();
				if (!$res1) return NULL;
				$res1 = choose_n_unique($res1, 4);
				$res = array_merge($res0, $res1);
				foreach ($res as &$r) $r=definition(defaultDB(), $r);
				foreach ($res as &$r) $r=[
					"correct" => $r->word()->id() === $pick_db["word"]->id(),
					"value" => ((string)$r->path() ? "(".$r->path().") " : "").str_replace("\n", ", ", $r->value()),
				];
				return $res;
			},
			"choices0-tooltip"=>"Pick correct definition",
		]]
	],
	"random-definitions2" => [
		"name" => "Stage 21–27 Vocabulary",
		"category" => "Vocabulary",
		"lang" => "la",
		"options" => [[
			"help" => "Choose a correct definition for the given word",
			"selections" => [
				"word"=>function($_, $db, $path) {
					$s = $db->searcher();
					$s->stmt .= "
						WHERE word_id IN (
							SELECT word_id FROM definitions
							WHERE def_lang = 'en'
						)
						AND word_lang = 'la'
						AND word_id NOT IN (
							SELECT word_id FROM attributes
							WHERE attr_tag = 'template' OR attr_tag = 'hidden'
						)
						AND (
							SELECT attr_value FROM attributes
							WHERE word_id = words.word_id
							AND attr_tag = 'clc-stage' 
						) IN (21,22,23,24,25,26,27)";
					$s->args = [];
					return $s->rand();
				},
			],
			"sentence" => [
				$OP_LQUOTE,
				function($pick_db) { return format_word($pick_db["word"]->name()); },
				$OP_RQUOTE, $OP_COLON,
				$OP_MULTIPLE_CHOICE,
			],
			"choices0-language" => "en",
			"choices0" => function($pick_db, $db) {
				global $mysqli;
				$query = $mysqli->prepare("
					SELECT DISTINCT def_id FROM definitions
					WHERE def_lang = 'en'
					AND word_id = (?)
					ORDER BY rand()
					LIMIT 1
				");
				$res0 = NULL;
				sql_getmany($query, $res0, ["i",$pick_db["word"]->id()]);
				$query->close();
				if (!$res0) return NULL;
				$query = $mysqli->prepare("
					SELECT DISTINCT def_id FROM definitions
					WHERE def_lang = 'en'
					AND def_id != (?)
					AND def_value != ''
					AND word_id in (
						SELECT word_id FROM words WHERE word_lang = 'la'
					)
					AND word_id NOT IN (
						SELECT word_id FROM attributes
						WHERE attr_tag = 'template' OR attr_tag = 'hidden'
					)
				");
				$res1 = NULL;
				sql_getmany($query, $res1, ["i", $res0[0]]);
				$query->close();
				if (!$res1) return NULL;
				$res1 = choose_n_unique($res1, 4);
				$res = array_merge($res0, $res1);
				foreach ($res as &$r) $r=definition(defaultDB(), $r);
				foreach ($res as &$r) $r=[
					"correct" => $r->word()->id() === $pick_db["word"]->id(),
					"value" => ((string)$r->path() ? "(".$r->path().") " : "").str_replace("\n", ", ", $r->value()),
				];
				return $res;
			},
			"choices0-tooltip"=>"Pick correct definition",
		]]
	],

	"subj01" => [
		"name" => "Subjunctive or Indicative?",
		"lang" => "la",
		"options" => [
			[
				"help" => "What is the mood of this verb?",
				"selections" => [
					"path" => PICK([
						["indicative","present","singular","person-3","active"],
						["indicative","imperfect","singular","person-3","active"],
						["indicative","pluperfect","singular","person-3","active"],
						["indicative","perfect","singular","person-3","active"],
						["subjunctive","imperfect","singular","person-3","active"],
						["subjunctive","pluperfect","singular","person-3","active"],
						["subjunctive","imperfect","singular","person-3","active"],
						["subjunctive","pluperfect","singular","person-3","active"],
						/*["indicative","future-perfect","singular","person-3","active"],
						["indicative","future","singular","person-3","active"],/**/
					])
				],
				"sentence" => [
					[
						"spart" => "verb",
						"attr" => $df_exclude,
						"path" => get_pick("path")
					],
					$OP_MULTIPLE_CHOICE
				],
				"choices0" => [
					"correct" => get_pick("path",0),
					function($pick_db){
						if ($pick_db["path"][0] == "indicative")
							return "subjunctive";
						return "indicative";
					}
				],
				"choices0-tooltip" => "Which mood?"
			]
		],
	],
	"001" => [
		"name" => "Nouns: number and case",
		"lang" => "la",
		"options" => [
			which3("la","noun","case",3,NULL,
			       ["case"=>["vocative"=>0,
			                 "locative"=>0,
			                 "nominative"=>1,
			                 "genitive"=>3,
			                 "dative"=>3,
			                 "ablative"=>4,
			                 "accusative"=>2]]),
			which("la","noun","number",NULL,
			      ["case"=>["vocative"=>0,
			                "locative"=>0,
			                "nominative"=>1,
			                "genitive"=>3,
			                "dative"=>3,
			                "ablative"=>4,
			                "accusative"=>2]]),
		],
	],
	"002" => [
		"name" => "Verbs: tense and number",
		"lang" => "la",
		"options" => [
			which3("la","verb","tense",3,["indicative"]),
			which3("la","verb","number",2,["indicative"]),
		],
	],
	"random-verb-forms1" => [
		"name" => "Random verb forms",
		"category" => "Challenge",
		"lang" => "la",
		"options" => [
			[
				"help" => function(&$pick_db, $db) {
					$word = $pick_db["word"];
					$word_name = format_word($word->name(), $word->lang());
					$paths = $pick_db["word"]->read_paths();
					$paths = array_filter($paths, function($p) {
						return $p->get();
					});
					$path = $pick_db["path"] = choose_one($paths);
					error_log(var_export($path->get(),1));
					$pick_db["form"] = $path->get();
					$path = implode(" ", array_map("format_path",array_reverse(explode("/",(string)$path))));
					return "What is the $path for $word_name.";
				},
				"selections" => [
					"word"=>function($_, $db, $path) {
						$s = $db->searcher()->spart("verb")->only_without_attr(ATTR("irregular"))->only_without_attr(ATTR("template"));
						$s->stmt .= " AND EXISTS (SELECT 1 FROM forms WHERE forms.word_id = words.word_id AND form_tag != '' AND form_value != '')";
						return $s->rand();
					}
				],
				"sentence" => [$OP_USER_INPUT],
				"answer0" => function($pick_db) {
					return array_map("format_word",explode("\n",$pick_db["form"]));
				},
				"answer0-tooltip" => "Enter form"
			]
		]
	],
	"random-noun-forms1" => [
		"name" => "Random noun forms",
		"category" => "Challenge",
		"lang" => "la",
		"options" => [
			[
				"help" => function(&$pick_db, $db) {
					$word = $pick_db["word"];
					$word_name = format_word($word->name(), $word->lang());
					$paths = $pick_db["word"]->read_paths();
					$paths = array_filter($paths, function($p) {
						return $p->get();
					});
					$path = $pick_db["path"] = choose_one($paths);
					error_log(var_export($path->get(),1));
					$pick_db["form"] = $path->get();
					$path = implode(" ", array_map("format_path",array_reverse(explode("/",(string)$path))));
					return "What is the $path for $word_name.";
				},
				"selections" => [
					"word"=>function($_, $db, $path) {
						$s = $db->searcher()->spart("noun")->only_without_attr(ATTR("irregular"))->only_without_attr(ATTR("template"));
	    	$s->stmt .= " AND EXISTS (SELECT 1 FROM forms WHERE forms.word_id = words.word_id AND form_tag != '' AND form_value != '')";
						return $s->rand();
					}
				],
				"sentence" => [$OP_USER_INPUT],
				"answer0" => function($pick_db) {
					return array_map("format_word",explode("\n",$pick_db["form"]));
				},
				"answer0-tooltip" => "Enter form"
			]
		]
	],
	"subjunctive-infinitives" => [
		"name" => "Subjunctive practice",
		"category" => "Grammar",
		"lang" => "la",
		"stage" => 24,
		"options" => [
			[
				"help" => function(&$pick_db, $db) {
					$word = $pick_db["word"];
					$word_name = format_word($word->name(), $word->lang());
					$pick_db["word"]->read_paths();
					$path = $pick_db["path"] = PATH($word);
					foreach ($pick_db as $k => $v) {
						if($k==="word" or $k==="path") continue;
						#error_log("$k = $v".var_export($v,true));
						$path->add2($v);
					}
					#error_log(var_export($path->get(),1));
					$pick_db["form"] = $path->get();
					$path = implode(" ", array_map("format_path",array_reverse(explode("/",(string)$path))));
					return "What is the $path for $word_name.";
				},
				"selections" => [
					"word"=>function($_, $db, $path) {
						$s = $db->searcher()->spart("verb")->only_without_attr(ATTR("irregular"))->only_without_attr(ATTR("template"));
	    	$s->stmt .= " AND EXISTS (SELECT 1 FROM forms WHERE forms.word_id = words.word_id AND form_tag != '' AND form_value != '') AND NOT EXISTS (SELECT 1 FROM attributes WHERE attr_tag = 'conjugation' AND attr_value like '%deponent%' AND word_id = words.word_id)";
						return $s->rand();
					},
					"tense"=>PICK(["imperfect","pluperfect"]),
					"mood"=>PICK(["indicative","indicative","indicative","subjunctive","subjunctive","subjunctive","subjunctive","subjunctive"]),
					"number"=>PICK(["singular","plural"]),
					"person"=>"person-3",
					"voice"=>"active",
				],
				"sentence" => [$OP_USER_INPUT],
				"answer0" => function($pick_db) {
					return array_map("format_word",explode("\n",$pick_db["form"]));
				},
				"answer0-tooltip" => "Enter form"
			],/**/
			make_chart(RWORD2("la","verb"),null,["participle","infinitive","supine","imperative","passive","person-1","person-2","present","perfect","future","future-perfect"]),
		],
	],
	"004" => [
		"name" => "Relative clauses",
		"category" => "Grammar",
		"lang" => "la",
		"options" => [
			which("la","pronoun","gender",NULL,[
			"case"=>["dative" => 0,"ablative"=>1,
			         "accusative"=>3,"nominative"=>3,
			         "genitive"=>1]
			],"qui"),
			[
				"help" => "Choose the pronoun that correctly 
				           fills in the blank.
				           ",
				"selections" => [
					0=>NULL,
					1=>NULL,
					2=>PICK(2, ["dative","genitive","ablative"])->l("la"),
				],
				"sentence" => [
					[
						"spart" => "noun",
						"attr" => $df_exclude,
						"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
					],
					$OP_COMMA,
					$OP_MULTIPLE_CHOICE,
					[
						"spart" => "verb",
						"attr" => ["transitive"=>"true","!template"=>NULL,"!hidden"=>NULL],
						"path" => ["indicative", "active", "person-1",
						           PICK("tense")->l("la"), PICK("number")->l("la")],
						"verb-gender" => get_pick(1,0)
					],
					$OP_COMMA,
					[
						"name" => "sum",
						"spart" => "verb",
						"path" => [get_pick(0,0), "person-3", "indicative", "active", PICK("tense")]
					],
					[
						"spart" => "adjective",
						"attr" => $df_exclude,
						"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
					]
				],
				"choices0" => [
					"correct" => [
						"name"=>"qui",
						"path" => [ get_pick(0,0), get_pick(1,0), "accusative" ]
					],
					[
						"name"=>"qui",
						"path" => [ get_pick(0,1), get_pick(1,0), "accusative" ]
					],
					[
						"name"=>"qui",
						"path" => [ get_pick(0,0), get_pick(1,1), "accusative" ]
					],
					[
						"name"=>"qui",
						"path" => [ get_pick(0,0), get_pick(1,1), get_pick(2,0) ]
					],
					[
						"name"=>"qui",
						"path" => [ get_pick(0,1), get_pick(1,0), get_pick(2,1) ]
					],
				],
				"choices0-tooltip"=>"",
			]
		],
	],
	"005" => [
		"name" => "English to Latin: Simple verbs",
		"category" => "English to Latin",
		"lang" => "la",
		"options" => [[
			"help" => "Translate this into Latin, either the
			           whole sentence or just the
			           (inflected) verb. Double check your
			           spelling! Word order does not matter.",
			"selections" => [
				"person" => PICK("person")->l("la"),
				"number" => PICK(["singular","plural"])->l("la"),
				"prep" => PICK([NULL,"in","ex","prō"])->l("la"),
				"tense" => PICK(["present","imperfect"])->l("la"),
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
				$subj = ["person-1"=>["singular"=>"ego","plural"=>"nōs"/*noos*/],
				"person-2"=>["singular"=>"tū"/*tuu*/,"plural"=>"vōs"/*voos*/],
				"person-3"=>["singular"=>"Grumiō"/*Grumioo*/,"plural"=>"servī"/*servii*/]];
				$subj = $subj[$pick_db["person"]][$pick_db["number"]];
				$verb = $db->searcher()->name("ambulo")->spart("verb")->rand();
				$verb->read_paths();
				$path = PATH($verb, "active/indicative");
				$path->add($pick_db["tense"]);
				$path->add($pick_db["person"]);
				$path->add($pick_db["number"]);
				$verb = $path->get();
				$prep = $pick_db["prep"];
				if ($prep === "prō"/*proo*/
				 or $prep === "in"
				 or $prep === "ex") $prep .= " forō "/*foroo*/;
				$correct = ["$subj $prep$verb", "$verb"];
				if ($prep) $permissible = permute_sentence([$verb],[$subj,$prep]);
				else $permissible = permute_sentence([$verb],[$subj]);
				return ["correct"=>$correct, "acceptable"=>$permissible];
			},
			"answer0-tooltip" => "Latin translation",
		]],
	],
	"009" => [
		"name" => "Synonyms",
		"lang" => "la",
		"n_questions" => "auto",
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
	"013" => [
		"name" => "Subjunctive matching",
		"lang" => "la",
		"n_questions" => "auto",
		"options" => [
			make_matching([
				"Pluperfect Subjunctive"=>"portāvisset",
				"Pluperfect Indicative"=>"discēderat",
				"Imperfect Indicative"=>"veniēbat",
				"Imperfect Subjunctive"=>"prōmitteret" ,
			]),
		],
	],
	"007" => [
		"name" => "Noun–verb agreement",
		"category" => "Grammar",
		"lang" => "la",
		"options" => [[
			"help" => "Choose the pronoun that correctly 
			           restates the subject of the verb.
			           (Note: for third-person pronouns,
			           assume both options are of the same
			           gender and thus only differ in number.)",
			"selections" => [
				#PICK(["true","false"])->l("la"),
				0=>"true",
				1=>PICK(3,"person")->l("la"),/**/
				2=>PICK(2,"number")->l("la"),/**/
				3=>PICK("gender")->l("la"),
				4=>NULL,
				//5=>PICK("voice")->l("la"),
				5=>"active",
				6=>NULL,
				"+adj"=>FALSE/*/PICK([TRUE,FALSE],[TRUE=>6,FALSE=>2])->l("la")*/,
			],
			"sentence" => [
				$OP_MULTIPLE_CHOICE," ",
				[ "condition" => eq_pick("+adj", TRUE),
					"lang" => "la",
					"attr" => $df_exclude,
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
					"attr" => $df_exclude,
					"speechpart" => "noun",
					"path" => [
						"accusative", PICK("number")->l("la"),
						PICK("gender")->l("la")
					],
				],
				[
					"lang" => "la",
					"speechpart" => "verb",
					"attr" => [
						"transitive" => get_pick(0),
						"!template"=>NULL, "!hidden"=>NULL, "!irregular"=>NULL,
					],
					"path" => [
						//PICK(["indicative","subjunctive"])->l("la"),
						"indicative",
						get_pick(1,0),
						get_pick(2,0),
						make_pick(PICK(["present","imperfect","perfect"]),4),
						//make_pick(PICK("tense")->l("la"), 4),
						get_pick(5),
					],
					"verb-gender" => get_pick(3)
				]
			],
			"choices0" => [
				"correct" => [
					"lang" => "la",
					"speechpart" => "pronoun",
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
	"complementary-infinitives" => [
		"name" => "Complementary infinitives",
		"category" => "Grammar",
		"lang" => "la",
		"options" => [[
			"help" => "Which word is the complementary infinitive in this sentence?",
			"selections" => [
				0=>null,
			],
			"sentence" => [
				[
					"lang" => "la",
					"speechpart" => "noun",
					"attr" => $df_exclude,
					"path" => [
						"nominative",
						make_pick(PICK("number"),0),
						PICK("gender")->l("la"),
					]
				],
				[
					"lang" => "la",
					"speechpart" => "verb",
					"attr" => [
						"transitive" => "",
						"!template" => null,
					],
					"path" => [
						"infinitive/active/present",
					],
				],
				[
					"lang" => "la",
					"speechpart" => "verb",
					"name" => PICK(["volo",/*"nolo","malo",*/"possum"]),
					"path" => [
						"indicative/active/person-3",
						get_pick(0),
						PICK(["present","imperfect","perfect"])->l("la"),
					],
				],
				HTML("<br>"),
				$OP_USER_INPUT,
			],
			"answer0" => [
				"correct" => [
					"lang" => "la",
					"speechpart" => "verb",
					"attr" => [
						"transitive" => "",
						"!template" => null,
					],
					"path" => [
						"infinitive/active/present",
					],
				],
			],
			"answer0-tooltip"=>"Infinitive", 
		]]
	],
	"008" => [
		"name"=>"Relative Pronouns",
		"category" => "Grammar",
		"lang" => "la",
		"options" => [[
			"help" => "Choose the pronoun that correctly 
			           fills in the blank.
			           ",
			"selections" => [
				0=>NULL,
				1=>NULL,
				2=>PICK(2, ["dative","genitive","ablative"])->l("la"),
			],
			"sentence" => [
				[
					"spart" => "noun",
					"lang" => "la",
					"attr" => $df_exclude,
					"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_MULTIPLE_CHOICE,
				[
					"spart" => "verb",
					"lang" => "la",
					"attr" => array_merge($df_exclude,["transitive"=>"true"]),
					"path" => ["indicative", "active", "person-1",
					           PICK("tense")->l("la"), PICK("number")->l("la")],
					"verb-gender" => get_pick(1,0)
				],/**/
				$OP_COMMA,
				[
					"name" => "sum",
					"lang" => "la",
					"spart" => "verb",
					"path" => [get_pick(0,0), "person-3", "indicative", "active", PICK("tense")->l("la")]
				],
				[
					"spart" => "adjective",
					"lang" => "la",
					"attr" => $df_exclude,
					"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
				]/**/
			],
			"choices0" => [
				"correct" => [
					"name"=>"qui",
					"lang" => "la",
					"spart" => "pronoun",
					"path" => [ get_pick(0,0), get_pick(1,0), "accusative" ]
				],
				[
					"name"=>"qui",
					"lang" => "la",
					"spart" => "pronoun",
					"path" => [ get_pick(0,1), get_pick(1,0), "accusative" ]
				],
				[
					"name"=>"qui",
					"lang" => "la",
					"spart" => "pronoun",
					"path" => [ get_pick(0,0), get_pick(1,1), "accusative" ]
				],
				[
					"name"=>"qui",
					"lang" => "la",
					"spart" => "pronoun",
					"path" => [ get_pick(0,0), get_pick(1,1), get_pick(2,0) ]
				],
				[
					"name"=>"qui",
					"lang" => "la",
					"spart" => "pronoun",
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
				2=>PICK(2, ["dative","genitive","ablative"])->l("la"),
			],
			"sentence" => [
				[
					"spart" => "noun",
					"lang" => "la",
					"attr" => $df_exclude,
					"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_MULTIPLE_CHOICE,
				[
					"spart" => "verb",
					"lang" => "la",
					"attr" => array_merge($df_exclude,["transitive"=>"false"]),
					"path" => ["indicative/active/person-3",
					           PICK("tense")->l("la"), get_pick(0,0)],
					"verb-gender" => get_pick(1,0),
				],
				$OP_COMMA,
				[
					"name" => "sum",
					"lang" => "la",
					"spart" => "verb",
					"path" => [get_pick(0,0), "person-3/indicative/active", PICK("tense")]
				],
				[
					"spart" => "adjective",
					"lang" => "la",
					"attr" => $df_exclude,
					"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
				]
			],
			"choices0" => [
				"correct" => function (&$_,$db,$path) {
					$w = $db->searcher()->name("qui")->spart("pronoun")->rand();
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
					"name"=>"qui",
					"lang" => "la",
					"spart" => "pronoun",
					"path" => [ get_pick(0,0), get_pick(1,1), get_pick(2,0) ]
				],
				[
					"name"=>"qui",
					"lang" => "la",
					"spart" => "pronoun",
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
				2=>PICK(2, ["dative","genitive","ablative"])->l("la"),
			],
			"sentence" => [
				[
					"spart" => "noun",
					"lang" => "la",
					"attr" => $df_exclude,
					"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_USER_INPUT,
				[
					"spart" => "verb",
					"lang" => "la",
					"attr" => array_merge($df_exclude,["transitive"=>"true"]),
					"path" => ["indicative/active/person-3",
					           PICK("tense")->l("la"), get_pick(0,0)],
					"verb-gender" => get_pick(1,0),
				],
				$OP_COMMA,
				[
					"name" => "sum",
					"lang" => "la",
					"spart" => "verb",
					"path" => [get_pick(0,0), "person-3/indicative/active", PICK("tense")->l("la")]
				],
				[
					"spart" => "adjective",
					"lang" => "la",
					"attr" => $df_exclude,
					"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
				]
			],
			"answer0" => [
				"name"=>"qui",
				"lang" => "la",
				"spart" => "pronoun",
				"path" => [ get_pick(0,0), get_pick(1,0), "nominative" ]
			],
			"answer0-tooltip"=>"relative pronoun form",
		]/**/]
	],
]);
