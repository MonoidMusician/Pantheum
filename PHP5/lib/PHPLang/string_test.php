<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/string.php');
sro('/PHP5/lib/PHPLang/misc.php');
global $OP_APOS;
global $DEBUG_STRING_PHP;
echo "<meta charset='utf-8'>";

/*
var_dump(strncmp("hhh","hh",2));
var_dump(strncmp("hhh","aa",2));
/**/

/*var_dump(compare_syntax("test", "test", ["unescaped"=>true]));echo "<hr>";
var_dump(compare_syntax("test1|test2", "test1", ["unescaped"=>true]));echo "<hr>";
var_dump(compare_syntax("test1|test2", "test2", ["unescaped"=>true]));echo "<hr>";*/
var_dump(compare_syntax("(this|that [system]) machina", "this machina", ["unescaped"=>true]));
var_dump(compare_syntax("(this|that [system]) machina", "that machina", ["unescaped"=>true]));
var_dump(compare_syntax("(this|that [system]) machina", "that system machina", ["unescaped"=>true]));
echo "<hr>";
var_dump(compare_syntax("{this} {that}", "this that", ["unescaped"=>true]));
var_dump(compare_syntax("{this} {that}", "that this", ["unescaped"=>true]));
echo "<hr>";
var_dump(compare_syntax("{*test (this|that [system])}, {computer|machine}!", "test this computer", ["unescaped"=>true]));
var_dump(compare_syntax("{*test (this|that [system])}, {computer|machine}!", "test this machine", ["unescaped"=>true]));
var_dump(compare_syntax("{*test (this|that [system])}, {computer|machine}!", "computer test that system", ["unescaped"=>true]));
echo "<hr>";
var_dump(compare_syntax("{a} {[c]}", "a c", ["unescaped"=>true]));
var_dump(compare_syntax("{a} {[c]}", "a", ["unescaped"=>true]));
echo "<hr>";
// backtracking 1: brackets do not *have* to match
var_dump(compare_syntax("[a] Agricola", "a agricola", ["unescaped"=>true]));
var_dump(compare_syntax("[a] Agricola", "agricola", ["unescaped"=>true]));
echo "<hr>";
// backtracking 2: the first choice might not be the best choice
var_dump(compare_syntax("{a here} here {a}", "a here here a", ["unescaped"=>true]));
var_dump(compare_syntax("{a here} here {a}", "a here a here", ["unescaped"=>true]));
var_dump(compare_syntax("{a here} here {a}", "a here a hear", ["unescaped"=>true]));
echo "(the last is null on purpose)";
echo "<hr>";
// backtracking 3: initial assumption (longer match for option) is false
var_dump(compare_syntax("a {here a|here} {a}", "a here a a", ["unescaped"=>true]));
var_dump(compare_syntax("a {here a|here} {a}", "a a here a", ["unescaped"=>true]));
var_dump(compare_syntax("a {here a|here} {a}", "a here a", ["unescaped"=>true]));
echo "<hr>";
$DEBUG_STRING_PHP = true;
$DEBUG_STRING_PHP = false;
/**/
/*var_dump(nanolexify_replacements("hi\\\$\\{0\\}hi"));
var_dump(nanolexify("hi\\\$\\{0\\}hi, \\_fn\\$\\(r\\)"));/**/
var_dump(nanomacro("hi\\\$\\{0\\}hi, \\_fn\\\$\\(hai\\)\\\$\\(hey\\)",[
	"fn" => "This is argument 1: \\\$\\{0\\}, and 2: \\\$\\{1\\}, and 3: \\\$\\{2\\}"
]));
var_dump(nanomacro("hi\${0}hi, _fn\$(hai)\$(hey)",[
	"fn" => "This is argument 1: \\\$\\{0\\}, and 2: \\\$\\{1\\}, and 3: \\\$\\{2\\}"
], 1));
var_dump(nanomacro("hi\${0}hi, _fn\$(hai)\$(hey)",[
	"fn" => "This is argument 1: \\\$\\{0\\}, and 2: \\\$\\{1\\}, and 3: \\\$\\{2\\}"
], 2));
var_dump(nanomacro("hi\${0}hi, _fn$(hai)$(hey)",[
	"fn" => "This is argument 1: \${0}, and 2: \${1}, and 3: \${2}"
], 4));
/**/
echo "<hr>";
$syntax = nanomacro("{This} {is} {_of$(Aristo)$(house)}.", [
	"of" => "(\${0}${OP_APOS}s \${1}|\${1} of \${0})",
	"a" => "[a[n]|the]",
], 4);
var_dump($syntax);
var_dump(compare_syntax($syntax, "This is Aristos house", ["unescaped"=>true]));
/**/
echo "<hr>";
$syntax = nanomacro('{*Agricola} {_cum$({he|Agricola} {had arrived})} {_imp$(want) to (say|speak)} {_a few (words|things)}.', [
	'cum' => ', when ${0},',
	'a' => '[a[n]|the]',
	'imp' => '(was ${0}ing|${0}ed)',
], 4);
var_dump($syntax);
var_dump(compare_syntax($syntax, "Agricola, when he had arrived, wanted to say a few things", ["unescaped"=>true]));
var_dump(compare_syntax($syntax, "When he had arrived, Agricola was wanting to say a few words", ["unescaped"=>true]));
/**/
echo "<hr>";
$sentence = " , When he said ${OP_LQUOTE}Hello?${OP_RQUOTE}, they were silent. , ${OP_LQUOTE}Hello${OP_RQUOTE}, he said. ${OP_LQUOTE}What happened?${OP_RQUOTE}. He repeated, ${OP_LQUOTE}Hello${OP_RQUOTE}. ${OP_LQUOTE}What happened?${OP_RQUOTE}";
var_dump($sentence);
var_dump(lexify_punctuation($sentence));
var_dump(normalize_punctuation($sentence));
/**/
// centuriō, iuvenem cōnspicātus, “hunc agnōscō!” inquit.
// kenturioo, iuvenem koonspikaatus, "hunk agnooskoo!" inquit.
echo "<hr>";
$syntax = '{*_a centurion} {_perfactv$(centurion|he)$(caught sight of)$(_a young man)} {said} {_quot$(I recognize (this [man|guy]|him))}.';
$dict = [
	'a' => '[a[n]|the]',
	'perfactv' => ', (having ${1} ${2}|(when|once) ${0} had ${1} ${2}|who had ${1}${2}),',
	'quot' => ', “${0}”,',
];
var_dump(nanomacro($syntax, $dict, 4));
$answer = 'the centurion having caught sight of the young man said I recognize him';
var_dump(compare_syntax3($syntax, $answer, $dict));
$answer = 'the centurion said I recognize him having caught sight of the young man';
var_dump(compare_syntax3($syntax, $answer, $dict));
$answer = 'I recognize him said the centurion having caught sight of the young man';
var_dump(compare_syntax3($syntax, $answer, $dict));
echo "<hr>";
$syntax = '{*_a centurion} {_perfactv$(centurion|he)$(caught sight of)$(_a young man)} {said} {_quot$(I recognize (this [man|guy]|him)!)}.';
$dict = [
	'a' => '[a[n]|the]',
	'perfactv' => ', (having ${1} ${2}|(when|once) ${0} had ${1} ${2}|who had ${1}${2}),',
	'quot' => ', “${0}”,',
];
var_dump(nanomacro($syntax, $dict, 4));
$answer = 'the centurion having caught sight of the young man said I recognize him';
var_dump(compare_syntax3($syntax, $answer, $dict));
$answer = 'the centurion said I recognize him having caught sight of the young man';
var_dump(compare_syntax3($syntax, $answer, $dict));
$answer = 'I recognize him said the centurion having caught sight of the young man';
var_dump(compare_syntax3($syntax, $answer, $dict));
?>
