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

var_dump(strncmp("hhh","hh",2));
var_dump(strncmp("hhh","aa",2));
/*var_dump(compare_syntax("test", "test", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("test1|test2", "test1", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("test1|test2", "test2", ["unescaped"=>true]));echo "<hr><hr>";*/
var_dump(compare_syntax("(this|that [system]) machina", "this machina", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("(this|that [system]) machina", "that machina", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("(this|that [system]) machina", "that system machina", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("{this} {that}", "this that", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("{this} {that}", "that this", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("{*test (this|that [system])}, {computer|machine}!", "test this computer", ["unescaped"=>true]));echo "<hr><hr>";
var_dump(compare_syntax("{*test (this|that [system])}, {computer|machine}!", "test this machine", ["unescaped"=>true]));echo "<hr><hr>";
/**/
/*var_dump(nanolexify_replacements("hi\\\$\\{1\\}hi"));
var_dump(nanolexify("hi\\\$\\{1\\}hi, \\_fn\\$\\(r\\)"));/**/
var_dump(nanomacro("hi\\\$\\{1\\}hi, \\_fn\\\$\\(hai\\)\\\$\\(hey\\)",[
	"fn" => "This is argument 1: \\\$\\{1\\}, and 2: \\\$\\{2\\}, and 3: \\\$\\{3\\}"
]));
var_dump(nanomacro("hi\${1}hi, _fn\$(hai)\$(hey)",[
	"fn" => "This is argument 1: \\\$\\{1\\}, and 2: \\\$\\{2\\}, and 3: \\\$\\{3\\}"
], 1));
var_dump(nanomacro("hi\${1}hi, _fn\$(hai)\$(hey)",[
	"fn" => "This is argument 1: \\\$\\{1\\}, and 2: \\\$\\{2\\}, and 3: \\\$\\{3\\}"
], 2));
var_dump(nanomacro("hi\${1}hi, _fn$(hai)$(hey)",[
	"fn" => "This is argument 1: \${1}, and 2: \${2}, and 3: \${3}"
], 4));
/**/
echo "<hr><hr>";
$syntax = nanomacro("{This} {is} {_of$(Aristo)$(house)}.", [
	"of" => "(\${1}${OP_APOS}s \${2}|\${2} of \${1})",
	"a" => "[a[n]|the]",
], 4);
var_dump($syntax);
var_dump(compare_syntax($syntax, "This is Aristos house", ["unescaped"=>true]));
/**/
echo "<hr><hr>";
$syntax = nanomacro('{*Agricola} {_cum$({he|Agricola} {had arrived})} {_imp$(want) to (say|speak)} {_a few (words|things)}.', [
	'cum' => ', when ${1},',
	'a' => '[a[n]|the]',
	'imp' => '(was ${1}ing|${1}ed)',
], 4);
var_dump($syntax);
var_dump(compare_syntax($syntax, "Agricola, when he had arrived, wanted to say a few things", ["unescaped"=>true]));
var_dump(compare_syntax($syntax, "When he had arrived, Agricola was wanting to say a few words", ["unescaped"=>true]));
?>
