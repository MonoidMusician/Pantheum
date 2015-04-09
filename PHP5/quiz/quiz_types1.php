<?php
global $quiz_types;
$quiz_types = array_merge($quiz_types,[
	"random-definitions" => [
		"name" => "Define random words",
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
	"irregular-pronouns" => [
		"name" => "Irregular pronouns",
		"category" => "Irregular",
		"lang" => "la",
		"n_questions" => "auto",
		"options" => [
			make_chart(WORD(defaultDB(), 212)),/*hic*/
			make_chart(WORD(defaultDB(), 216)),/*ille*/
			make_chart(WORD(defaultDB(), 172)),/*ego*/
			make_chart(WORD(defaultDB(), 231)),/*is*/
			make_chart(WORD(defaultDB(), 354)),/*tu*/
			make_chart(WORD(defaultDB(), 611)),/*iste*/
		]
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
						"attr" => ["!template"=>NULL,"!hidden"=>NULL],
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
						"attr" => ["!template"=>NULL,"!hidden"=>NULL],
						"path" => [get_pick(0,0), "person-3", "indicative", "active", PICK("tense")]
					],
					[
						"spart" => "adjective",
						"attr" => ["!template"=>NULL,"!hidden"=>NULL],
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
	"hic-haec-hoc" => [
		"name" => "Hic, Haec, Hoc",
		"lang" => "la",
		"n_questions" => "auto",
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
	"007" => [
		/*"name" => "Noun–verb agreement",*/
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
				1=>PICK(3,"person")->l("la"),
				2=>PICK(2,"number")->l("la"),
				3=>PICK("gender")->l("la"),
				4=>NULL,
				5=>PICK("voice")->l("la"),
				6=>NULL,
				"+adj"=>PICK([TRUE,FALSE],[TRUE=>6,FALSE=>2])->l("la"),
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
						"accusative", PICK("number")->l("la"),
						PICK("gender")->l("la")
					],
				],
				[
					"lang" => "la",
					"speechpart" => "verb",
					"attr" => [
						"transitive" => get_pick(0),
					],
					"path" => [
						PICK(["indicative","subjunctive"])->l("la"),
						get_pick(1,0),
						get_pick(2,0),
						make_pick(PICK("tense")->l("la"), 4),
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
					"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_MULTIPLE_CHOICE,
				[
					"spart" => "verb",
					"attr" => ["transitive"=>"true"],
					"path" => ["indicative", "active", "person-1",
					           PICK("tense")->l("la"), PICK("number")->l("la")],
					"verb-gender" => get_pick(1,0)
				],
				$OP_COMMA,
				[
					"name" => "sum",
					"path" => [get_pick(0,0), "person-3", "indicative", "active", PICK("tense")->l("la")]
				],
				[
					"spart" => "adjective",
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
					"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_MULTIPLE_CHOICE,
				[
					"spart" => "verb",
					"attr" => ["transitive"=>"false"],
					"path" => ["indicative/active/person-3",
					           PICK("tense")->l("la"), get_pick(0,0)],
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
					"path" => [ get_pick(0,0), get_pick(1,1), get_pick(2,0) ]
				],
				[
					"name"=>"qui",
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
					"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
				],
				$OP_COMMA,
				$OP_USER_INPUT,
				[
					"spart" => "verb",
					"attr" => ["transitive"=>"false"],
					"path" => ["indicative/active/person-3",
					           PICK("tense")->l("la"), get_pick(0,0)],
					"verb-gender" => get_pick(1,0),
				],
				$OP_COMMA,
				[
					"name" => "sum",
					"path" => [get_pick(0,0), "person-3/indicative/active", PICK("tense")->l("la")]
				],
				[
					"spart" => "adjective",
					"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
				]
			],
			"answer0" => [
				"name"=>"qui",
				"path" => [ get_pick(0,0), get_pick(1,0), "nominative" ]
			],
			"answer0-tooltip"=>"relative pronoun form",
		]]
	],
	"modelsentences-12" => [
		"name" => "Stage 12 Model Sentences",
		"category" => "Model Sentences",
		"lang" => "la",
		"no_shuffle" => true,
		"n_questions" => "auto",
		"options" => [[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms1.jpg' style='width: 205px;'><br>"),
				format_word("1. Syphāx et Celer in portū stābant. amīcī montem spectābant.","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => [
					"Syphāx and Celer were standing in the harbor. The friends were watching the mountain."
				],
				"acceptable" => permute_sentence_choices([
					"Syphāx and Celer",
					["were standing","stood"],
					["in","at"],
					["the","a",""],
					["harbor.","harbour."],
					["The",""],
					"friends were",
					["watching","looking at"],
					["the","a",""],
					"mountain.",
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms2.jpg' style='width: 205px;'><br>"),
				format_word("2. Syphāx amīcō dīxit, “ego prope portum servōs vēndēbam. ego subitō sonōs audīvī.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Syphāx said to his friend, “I was selling slaves near the harbor. Suddenly I heard sounds.”"],
				"acceptable" => permute_sentence_choices([
					"Syphāx",
					["said to","told"],
					["his","the","a",""],
					"friend, ",
					["“I was selling slaves near the harbor."],
					["Suddenly I","I suddenly"],
					"heard",
					["the","some",""],
					["sounds.”","noises.”"]
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms3.jpg' style='width: 205px;'><br>"),
				format_word("3. Celer Syphācī respondit, “tū sonōs audīvistī. ego tremōrēs sēnsī. ego prope montem ambulābam.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Celer replied to Syphāx, “You heard sounds. I felt tremors. I was walking near the mountain.”"],
				"acceptable" => permute_sentence_choices([
					"Celer",
					["replied","responded"],
					"to Syphāx, “You heard",
					["sounds.","noises."],
					"I felt",
					["the","some",""],
					["tremors.","shakes","shaking"],
					"I was walking near the mountain.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms4.jpg' style='width: 205px;'><br>"),
				format_word("4. Poppaea et Lucriō in ātriō stābant. sollicitī erant.","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Poppaea and Lucriō were standing in the atrium. They were worried."],
				"acceptable" => permute_sentence_choices([
					"Poppaea and Lucriō were",
					["standing",""],
					["in","inside","at"],
					["the","an",""],
					["atrium."],
					"They were worried."
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms5.jpg' style='width: 205px;'><br>"),
				format_word("5. Poppaea Lucriōnī dīxit, “ego in forō eram. ego tibi togam quaerēbam. ego nūbem mīrābilem cōnspexī.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Poppaea said to Lucriō, “I was in the forum. I was searching for a toga for you. I caught sight of a marvelous cloud.”"],
				"acceptable" => permute_sentence_choices([
					"Poppaea",
					["said to","told"],
					"Lucriō, “I was",
					["in","inside","at"],
					["the","a",""],
					"forum. I was",
					["searching for","seeking","looking for"],
					["the","a",""],
					"toga for you. I",
					["caught sight of","saw"],
					["the","a",""],
					["wonderful","marvelous","strange","odd","weird"],
					"cloud.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms6.jpg' style='width: 205px;'><br>"),
				format_word("6. Lucriō Poppaeae respondit, “tū nūbem cōnspexistī. ego cinerem sēnsī. ego flammās vīdī.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Lucriō replied to Poppaea, “You caught sight of a cloud. I felt ashes. I saw flames.”"],
				"acceptable" => permute_sentence_choices([
					"Lucriō",
					["replied","responded"],
					"to Poppaea, “You",
					["caught sight of","saw","spied"],
					["the","a",""],
					"cloud. I felt",
					["the","some",""],
					["ashes.","ash"],
					"I saw",
					["the","some",""],
					"flames.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms7.jpg' style='width: 205px;'><br>"),
				format_word("7. Marcus et Quārtus in forō erant. Sulla ad frātrēs contendit.","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Marcus and Quārtus were in the forum. Sulla hurried to the brothers."],
				"acceptable" => permute_sentence_choices([
					"Marcus and Quārtus were",
					["in","inside","at"],
					["the","a",""],
					"forum. Sulla",
					["hurried","rushed"],
					["up to","to"],
					["the brothers","brothers","them"]
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms8.jpg' style='width: 205px;'><br>"),
				format_word("8. Sulla frātribus dīxit, “ego ad theātrum contendēbam. ego sonōs audīvī et tremōrēs sēnsī. vōs sonōs audīvistis? vōs tremōrēs sēnsistis?”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["Sulla said to the brothers, “I was hurrying to the theater. I heard sounds and felt tremors. Did you hear sounds? Did you feel tremors?”."],
				"acceptable" => permute_sentence_choices([
					"Sulla",
					["said to","told","asked"],
					["the brothers,","brothers,"],
					"“I was",
					["hurrying","running"],
					"to the theater. I heard",
					["the","some",""],
					["sounds","noises"],
					"and",["","I"],"felt",
					["the",""],
					["tremors.","shaking"],
					"Did you hear",
					["the",""],
					["sounds?","noises?"],
					"Did you feel",
					["the",""],
					["tremors?”.","shakes?”.","shaking?”."]
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s12/ms9.jpg' style='width: 205px;'><br>"),
				format_word("9. frātrēs Sullae respondērunt, “nōs tremōrēs sēnsimus et sonōs audīvimus. nōs nūbem mīrābilem vīdimus. nōs sollicitī sumus.”","la"),
				HTML("<br>"),
				$OP_USER_INPUT
			],
			"answer0" => [
				"correct" => ["The brothers replied to Sulla, “We felt tremors and heard the sounds. We saw the marvelous cloud. We are worried.”"],
				"acceptable" => permute_sentence_choices([
					["The",""],
					"brothers",
					["replied","responded"],
					"to Sulla, “We felt",
					["the",""],
					["tremors","shaking."],
					"and",["We",""],"heard",
					["the",""],
					"sounds. We saw",
					["the","a",""],
					["wonderful","marvelous","strange","odd","weird"],
					"cloud. We are worried.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		]]
	],
	"modelsentences-25" => [
		"name" => "Stage 25 Model Sentences",
		"category" => "Model Sentences",
		"lang" => "la",
		"no_shuffle" => true,
		"n_questions" => "auto",
		"options" => [[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms1.jpg' style='width: 205px;'><br>"),
				format_word("1. mīles legiōnis secundae per castra ambulābat. subitō iuvenem ignōtum prope horreum latentem cōnspexit.
“heus tū,” clāmāvit mīles, “quis es?”
iuvenis nihil respondit. mīles iuvenem iterum rogāvit quis esset. iuvenis fūgit.","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => [
					"A soldier of the second legion was walking through the camp. Suddenly he caught sight of a strange young man hiding near the barn.
“Hey you,” the soldier shouted, “Who are you?”
The young man said nothing. The soldier asked the young man again who he was. The young man fled."
				],
				"acceptable" => permute_sentence_choices([
					"A soldier",
					["of","with","in"],
					"the second legion was walking through",
					["the",""],
					"camp. Suddenly he caught sight of a",
					["strange","unfamiliar","unknown"],
					["young man","man","boy"],
					"hiding near the barn.
“Hey you,” the soldier shouted, “Who are you?”
The",
					["young man","man","boy"],
					"said nothing. The soldier asked the",
					["young man","man","boy"],
					"again who he was. The",
					["young man","man","boy"],
					"fled."
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms2.jpg' style='width: 205px;'><br>"),
				format_word("2. mīles iuvenem petīvit et facile superāvit. “furcifer!” exclāmāvit. “quid prope horreum facis?”
iuvenis dīcere nōlēbat quid prope horreum faceret. mīles eum ad centuriōnem dūxit.","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => ["The soldier followed the young man and easily overcame him. “Thief!” he shouted. “What are you doing near the barn?”
The young man did not want to say what he was doing near the barn. The soldier lead him to the centurion."],
				"acceptable" => permute_sentence_choices([
					"The soldier",
					["followed","chased"],
					"the",
					["young man","man","boy"],
					"and",
					["the soldier","he",""],
					"easily",
					["reached","caught up to","overcame","overpowered"],
					"him. “Thief!”",
					["he","the soldier"],
					"shouted. “What are you doing near the ",
					["barn?”", "granary?”"],
					"The",
					["young man","man","boy"],
					["did not want","was not wanting","was not inclined"],
					"to say what he was doing near the",
					["barn.","granary."],
					"The soldier lead him to the centurion."
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms3.jpg' style='width: 205px;'><br>"),
				format_word("3. centuriō, iuvenem cōnspicātus, “hunc agnōscō!” inquit. “explōrātor Britannicus est, quem saepe prope castra cōnspexī. quō modō eum cēpistī?”
tum mīles explicāvit quō modō iuvenem cēpisset.","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => ["The centurion, having caught sight of the young man, said, “I recognize this man! He is a British explorer, whom I have often seen near the camp. How did you catch him?”
Then the soldier explained how he had caught the young man."],
				"acceptable" => permute_sentence_choices([
					"The centurion, ",
					["having","after he had"],
					["caught sight of","seen","saw"],
					"the",
					["young man","man","boy"],
					"said, “I recognize this",
					["man!","person!","!"],
					"He is a",
					["British","Britannic"],
					"explorer, whom I have often seen near the camp. How did you catch him?”
Then the soldier explained how he had caught the",
					["young man.","man.","boy."],
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		],[
			"help" => "Translate the sentence",
			"selections" => [],
			"sentence" => [
				HTML("<img src='http://www.cambridgescp.com/singles/webbook/s25/ms4.jpg' style='width: 205px;'><br>"),
				format_word("4. centuriō, ad iuvenem conversus, “cūr in castra vēnistī?” rogāvit. iuvenis tamen tacēbat.
    centuriō, ubi cognōscere nōn poterat cūr iuvenis in castra vēnisset, mīlitem iussit eum ad carcerem dūcere.
    iuvenis, postquam verba centuriōnis audīvit, “ego sum Vercobrix,” inquit, “fīlius prīncipis Deceanglōrum. vōbīs nōn decōrum est mē in carcere tenēre.”
    “fīlius prīncipis Deceanglōrum?” exclāmāvit centuriō. “libentissimē tē videō. nōs tē diū quaerimus, cellamque optimam tibi in carcere parāvimus.”","la"),
				HTML("<br><br>"),
				$OP_USER_PARAGRAPH
			],
			"answer0" => [
				"correct" => ["The centurion, having turned to the young man, asked, “Why have you come into the camp?” The young man however was silent.
The centurion, when he was unable to learn why the young man had come into the camp, ordered the soldier to lead him to the jail.
The young man, after he heard the centurion’s words, said, “I am Vercobrix, son of the chief of the Deceangli. It is not proper for you to hold me in prison.”
“The son of the chief of the Deceangli?” shouted the centurion. “I see you very happily. We have sought you for a long time, and we will prepare the best room for you in jail.”"],
				"acceptable" => permute_sentence_choices([
					"The centurion,",
					["having","after he"],
					"turned to the",
					["young man,","man,","boy,"],
					"asked, “Why have you",
					["come into","come to","entered","entered into"],
					"the camp?” The",
					["young man","man","boy"],
					"however",
					["was silent.","remained silent.","said nothing"],"
The centurion, when he was unable to learn why the young man had",
					["come into","come to","entered","entered into"],
					["the",""],
					"camp, ordered the soldier to lead him to the jail.
The",
					["young man,","man,","boy,"],
					"after he heard the",
					["centurion’s words,","words of the centurion","words spoken by the centurion"],
					"said, “I am Vercobrix, son of the chief of the Deceangli. It is not proper for you to hold me in prison.”
“The son of the chief of the Deceangli?” shouted the centurion. “I see you very happily. We have sought you for a long time, and we will prepare the best room for you in jail.”"
				])
			],
			"answer0-tooltip" => "English translation",
			"answer0-language" => "",
		]]
	],
]);
