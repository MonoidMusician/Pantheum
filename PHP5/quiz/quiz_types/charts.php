<?php
global $quiz_types;
global $df_exclude;
$quiz_types = array_merge($quiz_types,[
	"hic-haec-hoc" => [
		"name" => "Hic, Haec, Hoc",
		"lang" => "la",
		"n_questions" => "auto",
		"category" => "Charts",
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
	"irregular-pronouns1" => [
		"name" => "Irregular pronouns",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => "auto",
		"options" => [
			make_chart(WORD2("la","hic","pronoun")),/*hic*/
			make_chart(WORD2("la","iste","pronoun"),null,["vocative"]),/*iste*/
			make_chart(WORD2("la","ille","pronoun")),/*ille*/
			make_chart(WORD2("la","ego","pronoun"),null,["genitive","vocative"]),/*ego*/
			make_chart(WORD2("la","tu","pronoun"),null,["genitive","vocative"]),/*tu*/
			make_chart(WORD2("la","is","pronoun")),/*is*/
		]
	],
	"irregular-verbs1" => [
		"name" => "Irregular verbs",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => "auto",
		"options" => [
			make_chart(WORD2("la","volo","verb"),NULL,["infinitive","participle","subjunctive","future","pluperfect","future-perfect"]),
		]
	],
]);
