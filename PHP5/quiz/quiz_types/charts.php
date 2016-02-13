<?php
sro('/PHP5/quiz/quiz_types.php');

global $quiz_types;
global $df_exclude;
$quiz_types = array_merge($quiz_types,[
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
		"n_questions" => -4,
		"options" => [
			function(){return make_chart(WORD2("la","nolo","verb"),NULL,["infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"]);},
			function(){return make_chart(WORD2("la","volo","verb"),NULL,["infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"]);},
			function(){return make_chart(WORD2("la","sum","verb"),NULL, ["infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"]);},
			function(){return make_chart(WORD2("la","possum","verb"),NULL, ["infinitive","participle","subjunctive","imperative","future","pluperfect","future-perfect"]);},
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
		if ($this->person === "random")
			$this->person = PICK(["person-1","person-2","person-3"]);
		if ($this->number === "random")
			$this->number = PICK(["singular","plural"]);
	}
	function get_others() {
		if ($this->others === NULL) {
			$p = $this->person;
			if (ISPICK($p)) $p = $p->rand();
			$n = $this->number;
			if (ISPICK($n)) $n = $n->rand();
			$add = "using only the ";
			$a = [
				"person-1" => "1st person",
				"person-2" => "2nd person",
				"person-3" => "3rd person",
			];
			$add .= $a[$p]." ".$n;
			$this->others = make_chart($this->word, [
				[FALSE],
				["indicative///$p/$n","subjunctive///$p/$n","infinitive"],
				["present","imperfect","future","perfect","pluperfect","future-perfect"],
				["active","passive"],
				[""]
			], [
				//"perfect/passive",
				//"subjunctive/pluperfect/passive",
				"infinitive/imperfect","infinitive/perfect","infinitive/pluperfect",
				"infinitive/passive",
				"subjunctive/present","subjunctive/perfect",
				"subjunctive/future", "subjunctive/future-perfect",
				"infinitive/future", "infinitive/future-perfect",
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

class VerbFormTranslation extends QuizType {
	public $person;
	public $number;
	public $help = "Translate this verb form into English.";
	function __construct($word, $translation=FALSE) {
		$this->word = $word;
		$this->translation = $translation;
		$this->others = NULL;
		$this->selections = [
			"voice" => PICK(["active","passive"]),
			"mood-tense" => PICK([
				"indicative/present",
				"indicative/imperfect",
				"indicative/future",
				"indicative/perfect",
				"indicative/pluperfect",
				"indicative/future-perfect",
				"subjunctive/imperfect",
				"subjunctive/pluperfect"
			]),
		];
	}
	function merge_selections($selections) {
		$this->person = safe_get("selected-person", $selections);
		$this->number = safe_get("selected-number", $selections);
		if (!$this->person) $this->person = "person-3";
		if (!$this->number) $this->number = "singular";
		if ($this->person === "random")
			$selections["selected-person"] =
			$this->person = PICK(["person-1","person-2","person-3"]);
		if ($this->number === "random")
			$selections["selected-number"] =
			$this->number = PICK(["singular","plural"]);
		parent::merge_selections($selections);
	}
	function get_sentence() {
		global $OP_PARAGRAPH;
		return [
			[
				"store_path" => "path",
				"word" => $this->word,
				"path" => [
					get_pick("voice"),
					get_pick("mood-tense"),
					get_pick("selected-person"),
					get_pick("selected-number"),
				]
			],
			$OP_PARAGRAPH,
			function($pick_db) {
				$path = $pick_db["path"];
				$t = la_en($path, true); $T = la_en($path, false);
				return name_answer_lang_tool("translation", new FreeResponseExpr(
					$t, $T
				), "en", "English translation");
			}
		];
	}
}

class TenseConjugation extends QuizType {
	public $tense;
	function __construct($word, $translation=FALSE) {
		$this->word = $word;
		$this->translation = $translation;
		$this->others = NULL;
	}
	function merge_selections($selections) {
		$this->tense = safe_get("selected-mood-tense", $selections);
		if (!$this->tense) $this->tense = "person-3";
		if ($this->tense === "random")
			$this->tense = PICK([
				"indicative/present",
				"indicative/imperfect",
				"indicative/future",
				"indicative/perfect",
				"indicative/pluperfect",
				"indicative/future-perfect",
				//"subjunctive/present",
				"subjunctive/imperfect",
				//"subjunctive/perfect",
				"subjunctive/pluperfect",
			]);
	}
	function get_others() {
		if ($this->others === NULL) {
			$t = $this->tense;
			if (ISPICK($t)) $t = $t->rand();
			$this->others = make_chart($this->word, [
				[$t],
				["singular","plural"],
				["person-1","person-2","person-3"],
				["active","passive"],
				[FALSE],
			], [
				//"perfect/passive",
				//"subjunctive/pluperfect/passive",
				"infinitive/imperfect","infinitive/perfect","infinitive/pluperfect",
				"infinitive/passive",
				"subjunctive/present","subjunctive/perfect",
				"subjunctive/future", "subjunctive/future-perfect",
				"infinitive/future", "infinitive/future-perfect",
			], "the ".format_path($t), NULL, $this->translation);
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
}
function make_synopsisT($word) {
	return make_synopsis($word, ["subjunctive"]);
}
function make_verbform_translation($word) {
	return new VerbFormTranslation(WORD2("la",$word,"verb"));
}
function make_tense_conjugation($word) {
	return new TenseConjugation(WORD2("la",$word,"verb"));
}

$synopsis_words = [
	// !facio ago habeo
	"sperno", "appello",
	"moneo","augeo",
	"mitto","conduco",
	"reficio","accipio",
	"vincio","punio",
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
					"random" => "Random",
					"person-1" => "1st Person",
					"person-2" => "2nd Person",
					"person-3" => "3rd Person",
				]
			],
			"number" => [
				"name" => "Number",
				"values" => [
					"random" => "Random",
					"singular" => "Singular",
					"plural" => "Plural",
				]
			]
		],
	],
	"conjugate-tense" => [
		"name" => "Conjugate Verb Tense",
		"category" => "Charts",
		"lang" => "la",
		"n_questions" => -1,
		"options" => array_map("make_tense_conjugation",  $synopsis_words),
		"user_selections" => [
			"mood-tense" => [
				"name" => "Mood and Tense",
				"default" => "indicative/future",
				"values" => [
					"indicative/present" => "Present Indicative",
					"indicative/imperfect" => "Imperfect Indicative",
					"indicative/future" => "Future Indicative",
					"indicative/perfect" => "Perfect Indicative",
					"indicative/pluperfect" => "Pluperfect Indicative",
					"indicative/future-perfect" => "Future-perfect Indicative",
					//"subjunctive/present" => "Present Subjunctive",
					"subjunctive/imperfect" => "Imperfect Subjunctive",
					//"subjunctive/perfect" => "Perfect Subjunctive",
					"subjunctive/pluperfect" => "Pluperfect Subjunctive"
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
					"random" => "Random",
					"person-1" => "1st Person",
					"person-2" => "2nd Person",
					"person-3" => "3rd Person",
				]
			],
			"number" => [
				"name" => "Number",
				"values" => [
					"random" => "Random",
					"singular" => "Singular",
					"plural" => "Plural",
				]
			]
		],
	],
	"verb-form-translation" => [
		"name" => "Verb Form Translation",
		"category" => "Translation",
		"lang" => "la",
		"n_questions" => -5,
		"options" => array_map("make_verbform_translation", $synopsis_words),
		"user_selections" => [
			"person" => [
				"name" => "Person",
				"values" => [
					"random" => "Random",
					"person-1" => "1st Person",
					"person-2" => "2nd Person",
					"person-3" => "3rd Person",
				]
			],
			"number" => [
				"name" => "Number",
				"values" => [
					"random" => "Random",
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

