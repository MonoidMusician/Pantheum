<?php
global $quiz_types;
global $df_exclude;
$challenge = function($spart) {
	return function()use($spart){
		global $OP_USER_INPUT;
		return [[
			"help" => function(&$pick_db, $db) {
				$word = $pick_db["word"];
				$paths = $pick_db["word"]->read_paths();
				$paths = array_filter($paths, function($p) {
					return $p->get();
				});
				$path = $pick_db["path"] = choose_one($paths);
				error_log(var_export($path->get(),1));
				$pick_db["form"] = $path->get();
				$path = implode(" ", array_map("format_path",array_reverse(explode("/",(string)$path))));
				$word_name = display_word_name($word);
				return "What is the $path for $word_name.";
			},
			"selections" => [
				"word"=>function($_, $db, $path)use($spart){
					$s = $db->searcher()->spart($spart)->only_without_attr(ATTR("irregular"))->only_without_attr(ATTR("hidden"))->only_without_attr(ATTR("template"));
					$s->stmt .= " AND EXISTS (SELECT 1 FROM forms WHERE forms.word_id = words.word_id AND form_tag != '' AND form_value != '')";
					return $s->rand();
				}
			],
			"sentence" => [$OP_USER_INPUT],
			"answer0" => function($pick_db) {
				$map = function($w) use($pick_db) {
					return word_link2($pick_db["word"], $w, true);
				};
				return array_map($map,explode("\n",$pick_db["form"]));
			},
			"answer0-tooltip" => "Enter form"
		]];
	};
};
$quiz_types = array_merge($quiz_types,[
	"random-verb-forms1" => [
		"name" => "Random verb forms",
		"category" => "Challenge",
		"lang" => "la",
		"options" => $challenge("verb")
	],
	"random-noun-forms1" => [
		"name" => "Random noun forms",
		"category" => "Challenge",
		"lang" => "la",
		"options" => $challenge("noun")
	],
]);
