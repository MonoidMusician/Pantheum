<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/string.php');

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
?>
