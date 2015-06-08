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
]);
