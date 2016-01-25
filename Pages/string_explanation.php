<?php
require_once('/var/www/config.php');
sro('/Includes/mysql.php');
sro('/Includes/session.php');
sro('/Includes/functions.php');

sro('/PHP5/lib/PHPLang/common.php');
sro('/PHP5/lib/PHPLang/display.php');
sro('/PHP5/lib/PHPLang/string.php');


function dump_string($s) {
	echo "<code class='string'>".var_export($s,1)."</code><!-- (length: ".strlen($s).")-->";
}
function echo2($s) {
	echo "<span class='output'>$s</span>";
}
?>
<style>
code {
	color: rgb(0, 3, 205);
}
.output {
	color: rgb(137, 44, 182);
}
.output {
	display: block;
	margin: 8px 0px;
}
code.string {
	color: rgb(204, 0, 0);
	font: inherit;
}
</style>
<h1>String Library</h1>
<p>This is a (sub-)library I built that's made of 3 parts: punctuation normalization, a "nano" macro language, and (natural language) pattern matching. Each will be described here.
<h2>NanoMacro language</h2>
<p>This follows 4 simple rules (approximate syntax given in regex notation):
<ol>
	<li> <code>/_([a-zA-Z_][a-zA-Z0-9_]*)?/</code><br>
	Dictionary replacement to be inserted: an underscore followed by a name. The replacement may include ${0}, ${1}, ${2}, etc., to be replaced as parameters.
	<li> <code>/\$\(.*?\)/</code><br>
	Parameter replacement. Every ${0} is replaced by the value inside the parentheses, then ${1} becomes ${0}, ${2} ${1}, ${3} ${2}, etc.
	<li> <code>/\$\[[+-]=[0-9]+\]/</code><br>
	Every replacement expression gets the value added to or subtracted from it. Thus ${3} would become ${0} under the map $[-=3], etc.
	<li> <code>/\$\[([0-9]+=>[0-9]+,)*[0-9]=>[0-9]\]/</code><br>
	Explicit map from the number specified left of => to that specified on the right (PHP array notation). Thus $[0=>1,1=>0] would swap ${1} and ${0}.
</ol>
<p>Additionally, a dictionary is provided from PHP (a mapping from names to replacements for rule #1). Here's a sample dictionary, used for the following exempla:
<?php
$dict = nano_dfdict();
?><dl style="max-height: 300px; overflow: auto;"><?php
foreach ($dict as $k=>$v) {
	?><dt><?php
	dump_string($k);
	?> ⇒ </dt><dd><?php
	echo2($v);
	?></dd><?php
}
?></dl><?php
?>

<p>Here are some exempla to illustrate how it works.
<ul>
<?php
foreach ([
	['_test$(hey)$(hello).','This consists of a dictionary replacement "test", then two arguments: "hey" and "hello", and it becomes'],
	['_a centurion said _quot$(Hey there!).','The function "quot" can put nice quotes around our argument:'],
	['_a centurion _perfactv$(seen)$(_a guard).','We can use macros for translation patterns with many alternatives, like participles:'],
] as $example) {
	?><li><?php
	echo2($example[0]);
	echo "${example[1]}";
	echo2(nanomacro($example[0], $dict, 4));
	?><br><?php
}
?>
</ul>


<h2>Punctuation Normalization</h2>
<p>You might have noticed that some of the exempla in the last section had awful punctuation. Commas everywhere. Well, there's a reason for that, explained in the next section, but we still need to clean it up. Which is what <strong>Punctuation Normalization</strong> does. Instead of going into great detail with how it works, here are some exempla:
<ul>
<?php
foreach ([
	'The centurion said , “Hey there!”,.',
	'The centurion , having seen the guard, shouted.',
	', Having seen the guard, the centurion shouted.',
	'“Let us go to Rome!” “We will have fun there”, he said.',
	'“I do not know, will we?”, the other replied , once he heard this, .',
] as $example) {
	?><li><?php
	echo2($example);
	echo "becomes";
	echo2(normalize_punctuation($example));
	?><br><?php
}
?>
</ul>

<p>Ahhhh, doesn't it look like English again?




<h2>Natural Language Pattern Matching</h2>

<p>A few more rules again (pseudo-BNF this time):
<ol>
	<li> <code>expr := ;</code><br>
	Null expression. Matches nothing, or anything that looks like nothing.
	<li> <code>expr := character ;</code><br>
	Expressions match characters, fundamentally.
	<li> <code>expr := expr expr ;</code><br>
	Concatenation is without any syntax.
	<li> <code>expr := expr `|' expr ;</code><br>
	Options (one of which will match) are separated by the vertical pipe symbol.
	<li> <code>expr := `(' expr `)' ;</code><br>
	Parentheses mean next to nothing... except for grouping the above.
	<li> <code>expr := `[' expr `]' ;</code><br>
	Brackets, however, denote an optional expression.
	<li> <code>expr := (`{' capitalize expr `}' expr?)+ ;</code><br>
	Curly braces, which much occur in adjacent pair(s), allow each expression to occur in any order. Thus the first can be last, and the last first, or the middle first, or whatever.
	<li> <code>expr := `{' expr `}' ;</code><br>
	Actually braces can be by themselves... but they don't do anything. (Equivalent to parentheses.)
	<li> <code>capitalize := `*' | ;</code><br>
	If an asterisk starts a brace-enclosed expression, the result will be capitalized.
</ol>

<p>The true innovation over, say, regular expressions, is the permutation opportunities with curly braces. This allows sentences in languages, such as Latin, Ancient Greek, and Russian, which have a more flexible word order due to their nominal inflections, to be easily expressed. Even in English, dependent clauses and other phrases can sometimes move around, coming before or after their subject.

<p>These expressions are only meant to work against an input string to match. They essentially provide a (finite) set of alternatives, from which the one nearest to the input will be picked. The punctuation and syntax will come from the expression, but the content and form from the input.

<p>A few exempla to illustrate just the pattern matching:

<ul>
<?php
foreach ([
	['{*this }{that}', 'this that'],
	['{*this }{that}', 'that this'],
	['This ([wo]man|person) [here]', 'this man'],
	['This ([wo]man|person) [here]', 'this woman'],
	['This ([wo]man|person) [here]', 'this person here'],
	['{*thou} {knowest} {not} {me}.','me thou knowest not'],
	['{*venistine} {ex foro} {[tu]}?','uenistine·tu·ex·foro'],
	['{*[a[n]|the] centurion} {, (having (caught sight of) ([a[n]|the] young man)|who had (caught sight of) ([a[n]|the] young man)),} {said} {, “I recognize (this [man|guy]|him)!”,}.','thecenturionhavingcaughtsightoftheyoungmansaidirecognizehim']
] as $example) {
	?><li><?php
	echo2($example[0]);
	echo "matches";
	echo2($example[1]);
	echo "creating";
	echo2(compare_syntax($example[0],$example[1],["unescaped"=>true]));
	?><br><?php
}
?>
</ul>

<p>You can see why we need to normalize the punctuation (make it look pretty) and use macros (simplify our expression-writing needs). Let's see how it all works:
<ul>
<?php
foreach ([
	['{*_a centurion} {_perfactv$(caught sight of)$(_a young man)} {said} {_quot$(I recognize (this [man|guy]|him)!)}.','thecenturionhavingcaughtsightoftheyoungmansaidirecognizehim'],
	['{*{, {himself} {_a young man}, } {_a centurion}} {smiled|was smiling}.','the centurion a young man himself was smiling'],
	['{*{, {himself} {_a young man}, } {_a centurion}} {smiled|was smiling}.','a young man himself the centurion was smiling'],
	['_opts$(*thou} {not} {me)$(dost} {know)$(knowest).','thou dost not know me'],
	['_opts$(*thou} {not} {me)$(dost} {know)$(knowest).','thou dost know me not'],
	['_opts$(*thou} {not} {me)$(dost} {know)$(knowest).','thou not me knowest'],
	['_Appos$(_a centurion)$(having seen _a child) {wept} _quot$({*have mercy} {on me} {,O deity,}).','the centurion having seen the child wept on me have mercy O deity']
] as $example) {
	?><li><?php
	echo2($example[0]);
	echo "makes this syntax";
	echo2(nanomacro($example[0], $dict, 4));
	echo "and matches";
	echo2($example[1]);
	echo "creating";
	echo2(compare_syntax3($example[0],$example[1],$dict));
	?><br><?php
}
?>
</ul>

<h2>Try it!</h2>
<p>IT'S ALIVE!!</p>

Damerau-Levenshtein error: <input id="dist" style="width:150px" type="number" placeholder="Max Distance">
<label><input type="checkbox" id="debug">Debug</label>
<label><input type="checkbox" checked id="matchall">Only match whole string</label>
<br>
<input style="width: 100%" id="syntax" placeholder="Expression">
<br>
<code class="string" id="expression">
</code>
<br>
<input style="width: 100%" id="input" placeholder="String to match">
<br>
<span class="output" id="result">
</span>
<div id="log">
</div>
<div id="permutations" style="max-height: 300px; overflow: auto;">
</div>
<script>
$('#debug').on('change', function() {
	$('#log').css('display', $(this).is(':checked') ? 'block' : 'none');
}).trigger('change');
$('input').on('keypress', function(e) {
	if (e.which !== 13) return;
	$.get('PHP5/string_api.php',{"syntax":$('#syntax').val(),"dist":$('#dist').val(),"input":$('#input').val(),"debug":"true","matchall":$('#matchall:checked').length})
	.done(function(data) {
		data = JSON.parse(data);
		$('#expression').text(data["expression"]);
		$('#result').text(data["result"]);
		$('#log').html(data["log"]);
	});
	var permutations = permute(swap_escaping($('#syntax').val()));
	if (permutations.length > 300) $('#permutations').text(permutations.length+' permutations');
	permutations = permutations.gen_unique();
	if (permutations.length > 100) $('#permutations').text(permutations.length+' permutations');
	$('#permutations').html('<ul>');
 	permutations.forEach(function(p) {
		$('#permutations > ul').append('<li>'+p);
	});
});
</script>
