<?php
sro('/PHP5/lib/PHPLang/translation.php');

global $quiz_types;
global $df_exclude;
$category = "Translation";
function pluralize($noun) {
	return Inflect::pluralize($noun);
}

$quiz_types = array_merge($quiz_types,[
	"trans001" => [
		"name" => "En to La: Simple verbs",
		"category" => $category,
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
	"trans002" => [
		"name" => "La to En: Simple sentences",
		"category" => $category,
		"lang" => "la",
		"options" => [[
			"help" => "Translate this into English.",
			"selections" => [
				"person" => PICK("person")->l("la"),
				"number" => PICK("number")->l("la"),
				"gender" => PICK("gender")->l("la"),
				"obj_number" => NULL,
				"word" => NULL,
			],
			"sentence" => [
				[
					"condition" => make_pick(PICK([0,1]),"pronoun"),
					"spart" => "pronoun",
					"attr" => [
						"person" => get_pick("person"),
					],
					"path" => [
						"nominative",
						get_pick("number"),
						get_pick("gender"),
					]
				],
				[
					"spart" => "noun",
					"language" => "la",
					"attr" => $df_exclude,
					"store_word" => "word",
					"path" => [
						"accusative",
						PICK("gender"),
						make_pick(PICK("number"),"obj_number")
					]
				],
				[
					"spart" => "verb",
					"language" => "la",
					"name" => "habeo",
					"attr" => $df_exclude,
					"path" => [
						"indicative/present/active",
						get_pick("person"), get_pick("number")
					]
				],
				$OP_COLON,
				$OP_PARAGRAPH,
				$OP_USER_INPUT
			],
			"answer0" => function($pick_db,$db) {
				$person = $pick_db["person"];
				$number = $pick_db["number"];
				$gender = $pick_db["gender"];
				$word = $pick_db["word"];

				$pron = [
					"person-1"=>[
						"singular"=>["I"],
						"plural"=>["we"]
					],"person-2"=>[
						"singular"=>["you (sg)","you","thou"],
						"plural"=>["you (pl)","you","ye"]
					],"person-3"=>[
						"singular"=>["masculine"=>["he","it"],"feminine"=>["she","it"],"neuter"=>["it"],"one"],
						"plural"=>["they"]
					]
				];
				$pron = $pron[$person][$number];
				if ($person === "person-3" and $number === "singular")
					$pron = $pron[$gender];
				else $pron = array_unique(flatten($pron));

				$have = [
					"person-1"=>[
						"singular"=>["have"],
						"plural"=>["have"]
					],"person-2"=>[
						"singular"=>["have","hast","havest"],
						"plural"=>["have"]
					],"person-3"=>[
						"singular"=>["has","hath"],
						"plural"=>["have"]
					]
				];
				$have = $have[$person][$number];


				$path = PATH($word,"accusative/$gender/".$pick_db["obj_number"]);
				$defs = []; $def_expr = NULL;
				foreach ($word->definitions() as $def) {
					if ($def->lang() !== "en") continue;
					$_path = $def->path();
					if ($_path and !$path->issub($_path,TRUE))
						continue;

					if ($def->type() === "expr")
						$def_expr = $def->value();
					else
						$defs = array_merge($defs, explode("\n",$def->value()));
				}
				$defs = array_map("trim", $defs);
				if ($pick_db["obj_number"] === "plural") {
					$defs = array_map("pluralize", $defs);
					$defs = flatten($defs);
					$art = "[the|some]";
				} else $art = "[a[n]|the]";
				if (!$def_expr) $def_expr = make_expr($defs);

				$correct = [capitalize(implode(" ", [$pron[0],$have[0],"(the)",$defs[0]]))];
				$expr = "{*".implode("} {", array_map("make_expr",[$pron, $have, $art]))." $def_expr}";
				error_log($expr);

				return ["correct" => $correct, "expr" => $expr];
			},
			"answer0-tooltip" => "English translation",
			"answer0-language" => "en",
		], [
			"help" => "Translate this into English.",
			"selections" => [
				"person" => PICK("person")->l("la"),
				"number" => PICK("number")->l("la"),
				"gender" => PICK("gender")->l("la"),
				"type" => PICK(["location","location/room","location/building","location/city"]),
				"word" => NULL,
				"obj_gender" => NULL,
				"obj_number" => NULL,
				"ablative" => function($pick_db) {
					return $pick_db["type"] !== "location/city";
				},
				"case" => function($pick_db) {
					return $pick_db["ablative"] ? "ablative" : "locative";
				},
			],
			"sentence" => [
				[
					"condition" => make_pick(PICK([0,1]),"pronoun"),
					"spart" => "pronoun",
					"attr" => [
						"person" => get_pick("person"),
					],
					"path" => [
						"nominative",
						get_pick("number"),
						get_pick("gender"),
					]
				],
				[
					"spart" => "verb",
					"language" => "la",
					"name" => "sum",
					"path" => [
						"indicative/present/active",
						get_pick("number"),
						get_pick("person")
					]
				],
				[
					"spart" => "preposition",
					"language" => "la",
					"name" => "in",
					"condition" => get_pick("ablative")
				],
				[
					"spart" => "noun",
					"language" => "la",
					"attr" => array_merge($df_exclude, [
						"type"=>get_pick("type"),
					]),
					"store_word" => "word",
					"path" => [
						get_pick("case"),
						make_pick(PICK("gender"),"obj_gender"),
						make_pick(PICK(["singular"]),"obj_number")
					]
				],
				$OP_COLON,
				$OP_PARAGRAPH,
				$OP_USER_INPUT
			],
			"answer0" => function($pick_db,$db) {
				$person = $pick_db["person"];
				$number = $pick_db["number"];
				$gender = $pick_db["gender"];
				$word = $pick_db["word"];

				$pron = [
					"person-1"=>[
						"singular"=>["I"],
						"plural"=>["we"]
					],"person-2"=>[
						"singular"=>["you (sg)","thou"],
						"plural"=>["you (pl)","ye"]
					],"person-3"=>[
						"singular"=>["masculine"=>["he","it"],"feminine"=>["she","it"],"neuter"=>["it"],"one"],
						"plural"=>["they"]
					]
				];
				$pron = $pron[$person][$number];
				if ($person === "person-3" and $number === "singular")
					$pron = $pron[$gender];
				else $pron = array_unique(flatten($pron));

				$be = [
					"person-1"=>[
						"singular"=>["am"],
						"plural"=>["are"]
					],"person-2"=>[
						"singular"=>["are","art"],
						"plural"=>["are"]
					],"person-3"=>[
						"singular"=>["is"],
						"plural"=>["are"]
					]
				];
				$be = $be[$person][$number];


				$path = PATH($word,"$pick_db[case]/$pick_db[obj_number]/$pick_db[obj_gender]");
				$defs = []; $def_expr = NULL;
				foreach ($word->definitions() as $def) {
					if ($def->lang() !== "en") continue;
					$_path = $def->path();
					if ($_path and !$path->issub($_path,TRUE))
						continue;

					if ($def->type() === "expr")
						$def_expr = $def->value();
					else
						$defs = array_merge($defs, explode("\n",$def->value()));
				}
				$defs = array_map("trim", $defs);
				if ($pick_db["obj_number"] === "plural") {
					$defs = array_map("pluralize", $defs);
					$defs = flatten($defs);
					$art = "[the|some]";
				} else $art = "[a[n]|the]";
				if (!$def_expr) $def_expr = make_expr($defs);

				$correct = [capitalize(implode(" ", [$pron[0],$be[0],"in (the)",$defs[0]]))];
				$expr = "{*".implode("} {", array_map("make_expr",[$pron, $be]))."} {(in|at) ".make_expr($art)." $def_expr}";
				error_log($expr);

				return ["correct" => $correct, "expr" => $expr];
			},
			"answer0-tooltip" => "English translation",
			"answer0-language" => "en",
		], [
			"help" => "Translate this into English.",
			"selections" => [
				"number" => PICK("number")->l("la"),
				"gender" => PICK("gender")->l("la"),
				"word" => NULL,
			],
			"sentence" => [
				[
					"spart" => "verb",
					"language" => "la",
					"name" => "sum",
					"path" => [
						"indicative/present/active/person-3",
						get_pick("number")
					]
				],
				[
					"spart" => "noun",
					"language" => "la",
					"attr" => $df_exclude,
					"store_word" => "word",
					"path" => [
						"nominative",
						PICK("gender"),
						get_pick("number")
					]
				],
				$OP_COLON,
				$OP_PARAGRAPH,
				$OP_USER_INPUT
			],
			"answer0" => function($pick_db,$db) {
				$number = $pick_db["number"];
				$gender = $pick_db["gender"];
				$word = $pick_db["word"];

				$pron = [
					"singular"=>["there","it","he","she"],
					"plural"=>["there","they"]
				];
				$pron = $pron[$number];

				$be = [
					"singular"=>["is"],
					"plural"=>["are"]
				];
				$be = $be[$number];


				$path = PATH($word,"nominative/$gender/$number");
				$defs = []; $def_expr = NULL;
				foreach ($word->definitions() as $def) {
					if ($def->lang() !== "en") continue;
					$_path = $def->path();
					if ($_path and !$path->issub($_path,TRUE))
						continue;

					if ($def->type() === "expr")
						$def_expr = $def->value();
					else
						$defs = array_merge($defs, explode("\n",$def->value()));
				}
				$defs = array_map("trim", $defs);
				if ($number === "plural") {
					$defs = array_map("pluralize", $defs);
					$defs = flatten($defs);
					$art = "[the|some]";
				} else $art = "[a[n]|the]";
				if (!$def_expr) $def_expr = make_expr($defs);

				$correct = [capitalize(implode(" ", [$pron[0],$be[0],"(the)",$defs[0]]))];
				$expr = "{*".implode("} {", array_map("make_expr",[$pron, $be, $art]))." $def_expr}";
				error_log($expr);

				return ["correct" => $correct, "expr" => $expr];
			},
			"answer0-tooltip" => "English translation",
			"answer0-language" => "en",
		]],
	],
]);
