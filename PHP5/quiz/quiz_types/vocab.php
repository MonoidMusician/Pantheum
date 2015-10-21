<?php
global $quiz_types;
global $df_exclude;
$quiz_types = array_merge($quiz_types,[
	"random-definitions0" => [
		"name" => "Full vocabulary (FR)",
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
							AND def_type IS NULL
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
				$OP_USER_INPUT,
			],
			"answer0-language" => "en",
			"answer0" => function($pick_db, $db) {
				global $mysqli;
				$query = $mysqli->prepare("
					SELECT DISTINCT def_id FROM definitions
					WHERE def_lang = 'en'
					AND def_type IS NULL
					AND word_id = (?)
				");
				$res = NULL;
				sql_getmany($query, $res, ["i",$pick_db["word"]->id()]);
				$query->close();
				if (!$res) return NULL;
				$ret = []; $ret2 = [];
				foreach ($res as $r) $ret = array_merge($ret, explode("\n",definition(defaultDB(), $r)->value()));
				foreach ($ret as $r) $ret2 = array_merge($ret2, array_map("trim",explode(",", $r)));
				return ["correct"=>explode(",",$ret[0]),"acceptable"=>$ret2];
			},
			"answer0-tooltip"=>"Type correct definition",
		]]
	],
	"random-definitions1" => [
		"name" => "Full vocabulary (MC)",
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
							AND def_type IS NULL
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
					AND def_type IS NULL
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
					AND def_type IS NULL
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
							AND def_type IS NULL
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
					AND def_type IS NULL
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
					AND def_type IS NULL
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
	"random-definitions-stage28-29-mc" => [
		"name" => "Stage 28–29 Vocabulary (MC)",
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
							AND def_type IS NULL
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
						) IN (28,29)";
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
					AND def_type IS NULL
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
					AND def_type IS NULL
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
	"random-definitions-stage28-29-fr" => [
		"name" => "Stage 28–29 Vocabulary (FR)",
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
							AND def_type IS NULL
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
						) IN (28,29)";
					$s->args = [];
					return $s->rand();
				},
			],
			"sentence" => [
				$OP_LQUOTE,
				function($pick_db) { return format_word($pick_db["word"]->name()); },
				$OP_RQUOTE, $OP_COLON,
				$OP_USER_INPUT,
			],
			"answer0-language" => "en",
			"answer0" => function($pick_db, $db) {
				global $mysqli;
				$query = $mysqli->prepare("
					SELECT DISTINCT def_id FROM definitions
					WHERE def_lang = 'en'
					AND def_type IS NULL
					AND word_id = (?)
				");
				$res = NULL;
				sql_getmany($query, $res, ["i",$pick_db["word"]->id()]);
				$query->close();
				if (!$res) return NULL;
				$ret = []; $ret2 = [];
				foreach ($res as $r) $ret = array_merge($ret, explode("\n",definition(defaultDB(), $r)->value()));
				foreach ($ret as $r) $ret2 = array_merge($ret2, array_map("trim",explode(",", $r)));
				return ["correct"=>explode(",",$ret[0]),"acceptable"=>$ret2];
			},
			"answer0-tooltip"=>"Type correct definition",
		]]
	],
	"009" => [
		"name" => "Synonyms",
		"lang" => "la",
		"category" => "Vocabulary",
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
]);
