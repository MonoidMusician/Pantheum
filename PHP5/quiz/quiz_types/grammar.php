<?php
global $quiz_types;
global $df_exclude;
$quiz_types = array_merge($quiz_types,[
	"001" => [
		"name" => "Nouns: number and case",
		"lang" => "la",
		"category" => "Grammar",
		"options" => [
			function(){return
				which3("la","noun","case",3,NULL,
					   ["case"=>["vocative"=>0,
					             "locative"=>0,
					             "nominative"=>1,
					             "genitive"=>3,
					             "dative"=>3,
					             "ablative"=>4,
					             "accusative"=>2]]);
			},
			function(){return
				which("la","noun","number",NULL,
				      ["case"=>["vocative"=>0,
				                "locative"=>0,
				                "nominative"=>1,
				                "genitive"=>3,
				                "dative"=>3,
				                "ablative"=>4,
				                "accusative"=>2]]);
			},
		]
	],
	"002" => [
		"name" => "Verbs: tense and number",
		"lang" => "la",
		"category" => "Grammar",
		"options" => [
			function(){return which3("la","verb","tense",3,["indicative"]);},
			function(){return which3("la","verb","number",2,["indicative"]);},
		],
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
					"store" => "answer",
					"attr" => array_merge($df_exclude,["transitive" => "false"]),
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
				"correct" => get_pick("answer"),
			],
			"answer0-tooltip"=>"Infinitive", 
		]]
	],
	"004" => [
		//"name" => "Relative clauses",
		"category" => "Grammar",
		"lang" => "la",
		"options" => [
			function(){return
				which("la","pronoun","gender",NULL,[
				"case"=>["dative" => 0,"ablative"=>1,
					     "accusative"=>3,"nominative"=>3,
					     "genitive"=>1]
				],"qui");
			},
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
						"lang" => "la",
						"path" => [ make_picks(PICK(2,"number")->l("la"), 1, 0,0), make_picks(PICK(2, "gender")->l("la"), 1, 1,0), "nominative" ]
					],
					$OP_COMMA,
					$OP_MULTIPLE_CHOICE,
					[
						"spart" => "verb",
						"attr" => ["transitive"=>"true","!template"=>NULL,"!hidden"=>NULL],
						"lang" => "la",
						"path" => ["indicative", "active", "person-1",
						           PICK("tense")->l("la"), PICK("number")->l("la")],
						"verb-gender" => get_pick(1,0)
					],
					$OP_COMMA,
					[
						"name" => "sum",
						"spart" => "verb",
						"lang" => "la",
						"path" => [get_pick(0,0), "person-3", "indicative", "active", PICK("tense")]
					],
					[
						"spart" => "adjective",
						"attr" => $df_exclude,
						"lang" => "la",
						"path" => [get_pick(0,0), get_pick(1,0), "nominative/positive"]
					]
				],
				"choices0" => [
					"correct" => [
						"name"=>"qui",
						"spart" => "pronoun",
						"lang" => "la",
						"path" => [ get_pick(0,0), get_pick(1,0), "accusative" ]
					],
					[
						"name"=>"qui",
						"spart" => "pronoun",
						"lang" => "la",
						"path" => [ get_pick(0,1), get_pick(1,0), "accusative" ]
					],
					[
						"name"=>"qui",
						"spart" => "pronoun",
						"lang" => "la",
						"path" => [ get_pick(0,0), get_pick(1,1), "accusative" ]
					],
					[
						"name"=>"qui",
						"spart" => "pronoun",
						"lang" => "la",
						"path" => [ get_pick(0,0), get_pick(1,1), get_pick(2,0) ]
					],
					[
						"name"=>"qui",
						"spart" => "pronoun",
						"lang" => "la",
						"path" => [ get_pick(0,1), get_pick(1,0), get_pick(2,1) ]
					],
				],
				"choices0-tooltip"=>"",
			]
		],
	],
	"008" => [
		//"name"=>"Relative Pronouns",
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
	"subj01" => [
		"name" => "Subjunctive or Indicative?",
		"category" => "Grammar",
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
						"lang" => "la",
						"path" => get_pick("path"),
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
				"choices0-tooltip" => "Which mood?",
				"choices0-language" => "en",
			]
		],
	],
	"subjunctive-infinitives" => [
		"name" => "Subjunctive practice",
		"category" => "Grammar",
		"lang" => "la",
		"stage" => 24,
		"options" => function(){
			global $OP_USER_INPUT;
			return [
				[
					"help" => function(&$pick_db, $db) {
						$word = $pick_db["word"];
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
						$word_name = display_word_name($word);
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
			];
		}
	],
	"013" => [
		"name" => "Subjunctive matching",
		"lang" => "la",
		"category" => "Grammar",
		"n_questions" => 1,
		"options" => function(){return[
			make_matching([
				"Pluperfect Subjunctive"=>"portāvisset",
				"Pluperfect Indicative"=>"discēderat",
				"Imperfect Indicative"=>"veniēbat",
				"Imperfect Subjunctive"=>"prōmitteret" ,
			]),
		];},
	],
]);
