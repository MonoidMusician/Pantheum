<?php
sro('/PHP5/quiz/quiz_types.php');

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
	"hic-haec-hoc2" => [
		"name" => "Hic, Qui",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -2,
		"options" => [
			function(){return make_chart(WORD2("la","hic","pronoun"),null,["vocative"]);},
			function(){return make_chart(WORD2("la","qui","pronoun"),null,["vocative"]);},
		]
	],
	"irregular-pronouns1" => [
		"name" => "Irregular Pronouns",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -6,
		"options" => function(){
			$gender = PICK(3,["masculine","feminine","neuter"])->rand();
			return [
				function(){return make_chart(WORD2("la","hic","pronoun"));},
				function(){return make_chart(WORD2("la","iste","pronoun"),null,["vocative"]);},
				function(){return make_chart(WORD2("la","ille","pronoun"));},
				function()use($gender){return make_chart(WORD2("la","ego","pronoun"),null,["genitive","vocative",$gender[0],$gender[1]]);},
				function()use($gender){return make_chart(WORD2("la","tu","pronoun"),null,["genitive","vocative",$gender[0],$gender[2]]);},
				function(){return make_chart(WORD2("la","is","pronoun"),null);},
			];
		}
	],
	"irregular-verbs1" => [
		"name" => "Irregular Verbs",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -2,
		"options" => [
			function(){return make_chart(WORD2("la","volo","verb"),NULL,["infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"]);},
			function(){return make_chart(WORD2("la","sum","verb"),NULL, ["infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"]);},
		]
	],
	"irregular-verbs-latinII" => [
		"name" => "Latin II Irregular verbs",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -12,
		"no_shuffle" => true,
		"options" => [
			function(){return make_chart(WORD2("la","sum","verb"),NULL, ["perfect","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PRESENT tense");},
			function(){return make_chart(WORD2("la","sum","verb"),NULL, ["present","perfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the IMPERFECT tense");},
			function(){return make_chart(WORD2("la","sum","verb"),NULL, ["present","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PERFECT tense");},
			function(){return make_chart(WORD2("la","possum","verb"),NULL, ["perfect","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PRESENT tense");},
			function(){return make_chart(WORD2("la","possum","verb"),NULL, ["present","perfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the IMPERFECT tense");},
			function(){return make_chart(WORD2("la","possum","verb"),NULL, ["present","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PERFECT tense");},
			function(){return make_chart(WORD2("la","volo","verb"),NULL,["perfect","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PRESENT tense");},
			function(){return make_chart(WORD2("la","volo","verb"),NULL,["present","perfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the IMPERFECT tense");},
			function(){return make_chart(WORD2("la","volo","verb"),NULL,["present","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PERFECT tense");},
			function(){return make_chart(WORD2("la","nolo","verb"),NULL, ["perfect","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PRESENT tense");},
			function(){return make_chart(WORD2("la","nolo","verb"),NULL, ["present","perfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the IMPERFECT tense");},
			function(){return make_chart(WORD2("la","nolo","verb"),NULL, ["present","imperfect","infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"],"the PERFECT tense");},
		],
	],
]);



// SYNOPSES

class Synopsis extends QuizType {
	public $person;
	public $number;
	function __construct($word, $translation=FALSE) {
		$this->word = $word;
		$this->translation = $translation;
		$this->others = NULL;
	}
	function merge_selections($selections) {
		$this->person = safe_get("selected-person", $selections);
		$this->number = safe_get("selected-number", $selections);
		if (!$this->person) $this->person = "person-3";
		if (!$this->number) $this->number = "singular";
	}
	function get_others() {
		if ($this->others === NULL) {
			$add = "using only the ";
			$a = [
				"person-1" => "1st person",
				"person-2" => "2nd person",
				"person-3" => "3rd person",
			];
			$add .= $a[$this->person]." ".$this->number;
			$this->others = make_chart($this->word, [
				[FALSE],
				["indicative///$this->person/$this->number","subjunctive///$this->person/$this->number","infinitive"],
				["present","imperfect","perfect","pluperfect"],
				["active","passive"],
				[""]
			], [
				//"perfect/passive",
				"subjunctive/pluperfect/passive",
				"infinitive/imperfect","infinitive/perfect","infinitive/pluperfect",
				"infinitive/passive",
				"subjunctive/present","subjunctive/perfect",
			], "this synopsis", $add, $this->translation);
		}
		return $this->others;
	}
	function get_other($k) {return $this->get_others()[$k];}
	function get_help() {
		return $this->get_other("help");
	}
	function get_sentence() {
		return $this->get_other("sentence");
	}
}

function make_synopsis($word, $translation=FALSE) {
	return new Synopsis(WORD2("la",$word,"verb"), $translation);
	return function()use($word, $translation){
		$ret = make_chart(WORD2("la",$word,"verb"), [
			[FALSE],
			["indicative///person-3/singular","subjunctive///person-3/singular","infinitive"],
			["present","imperfect","perfect","pluperfect"],
			["active","passive"],
			[""]
		], [
			//"perfect/passive",
			"subjunctive/pluperfect/passive",
			"infinitive/imperfect","infinitive/perfect","infinitive/pluperfect",
			"infinitive/passive",
			"subjunctive/present","subjunctive/perfect",
		], "this synopsis",function($pick_db) {
			$a = [
				0 => "3rd person",
				"" => "3rd person",
				"person-1" => "1st person",
				"person-2" => "2nd person",
				"person-3" => "3rd person",
			];
			return "using only the ".$a[safe_get("selected-person",$pick_db)]." ".(safe_get("selected-number",$pick_db)?:"singular");
		}, $translation);
		return $ret;
	};
}
function make_synopsisT($word) {
	return make_synopsis($word, ["subjunctive"]);
}

$synopsis_words = [
	// !facio ago habeo
	"sperno", "appello",
	"moneo","augeo",
	"mitto","conduco",
	"reficio","accipio",
	"vincio"
];

$quiz_types = array_merge($quiz_types,[
	"synopsis-latinIII" => [
		"name" => "Latin III Synopsis",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -1,
		"options" => array_map("make_synopsis",  $synopsis_words),
		"user_selections" => [
			"person" => [
				"name" => "Person",
				"values" => [
					"person-1" => "1st Person",
					"person-2" => "2nd Person",
					"person-3" => "3rd Person",
				]
			],
			"number" => [
				"name" => "Number",
				"values" => [
					"singular" => "Singular",
					"plural" => "Plural",
				]
			]
		],
	],
	"synopsis-latinIII-translations" => [
		"name" => "Synopsis + Translations",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -1,
		"options" => array_map("make_synopsisT", $synopsis_words),
		"user_selections" => [
			"person" => [
				"name" => "Person",
				"values" => [
					"person-1" => "1st Person",
					"person-2" => "2nd Person",
					"person-3" => "3rd Person",
				]
			],
			"number" => [
				"name" => "Number",
				"values" => [
					"singular" => "Singular",
					"plural" => "Plural",
				]
			]
		],
	]
]);

function make_nounchart($word) {
	return function()use($word){
		return make_chart(WORD2("la",$word,"noun"), NULL, ["vocative"]);
	};
}

function make_nounadjchart($words) {
	return function()use($words){
		return make_chart2(WORD2("la",$words[0],"noun"), WORD2("la",$words[1],"adjective"), NULL, ["vocative"], "this chart", NULL, "positive");
	};
}

$synopsis_nouns = [
	"ancilla","agricola","cena",
	"servus","amicus","liber",
	"bellum",
	"canis",
	"os","nomen",
	"portus",
	"res"
];

$synopsis_nounadjs = [
	["cliens","bonus"],
];

$quiz_types = array_merge($quiz_types,[
	"noun-chart" => [
		"name" => "Noun Charts",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -1,
		"options" => array_map("make_nounchart",  $synopsis_nouns)
	],
	"noun-adjective-chart" => [
		"name" => "Noun–Adjective Charts",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -1,
		"options" => array_map("make_nounadjchart",  $synopsis_nounadjs)
	],
]);

