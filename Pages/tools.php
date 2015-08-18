<?php
	require_once('/var/www/config.php');
	sro('/Includes/mysql.php');
	sro('/Includes/session.php');
	sro('/Includes/functions.php');

	sro('/PHP5/lib/PHPLang/common.php');
	sro('/PHP5/lib/PHPLang/db.php');
	sro('/PHP5/lib/PHPLang/display.php');
?>
<header>
	<h1>Tools</h1>
</header>
<article>
<input id="arabic-number" placeholder="Arabic Number" value="<?= safe_get('number', $_GET) ?>">
= <input id="roman-number" placeholder="Roman Numeral">
= <span id="output-uc"></span>
= <span id="output-lc"></span>
</article>
<script>
// From http://blog.stevenlevithan.com/archives/javascript-roman-numeral-converter
function romanize (num) {
	if (!+num)
		return false;
	var	digits = String(+num).split(""),
		key = ["","C","CC","CCC","CD","D","DC","DCC","DCCC","CM",
		       "","X","XX","XXX","XL","L","LX","LXX","LXXX","XC",
		       "","I","II","III","IV","V","VI","VII","VIII","IX"],
		roman = "",
		i = 3;
	while (i--)
		roman = (key[+digits.pop() + (i * 10)] || "") + roman;
	return Array(+digits.join("") + 1).join("M") + roman;
}

function deromanize (str) {
	var	str = str.toUpperCase(),
		validator = /^M*(?:D?C{0,3}|C[MD])(?:L?X{0,3}|X[CL])(?:V?I{0,3}|I[XV])$/,
		token = /[MDLV]|C[MD]?|X[CL]?|I[XV]?/g,
		key = {M:1000,CM:900,D:500,CD:400,C:100,XC:90,L:50,XL:40,X:10,IX:9,V:5,IV:4,I:1},
		num = 0, m;
	if (!(str && validator.test(str)))
		return false;
	while (m = token.exec(str))
		num += key[m[0]];
	return num;
}

// unicode!
function reromanize(str) {
	// also thanks to http://ingram-braun.net/public/programming/web/roman-numeral-unicode-form/
	$.each([
        ['MMMMM','ↁ'],
        ['MMMM','Ⅿↁ'],
        ['ↁↁ',    'ↂ'],
        ['ↂↂↂↂↂ',    'ↇ'],
        ['ↂↂↂↂ',    'ↂↇ'],
        ['ↇↇ',    'ↈ'],
        ['ↇↂↇ',    'ↂↈ'],
        ['ↁⅯↁ',    'Ⅿↂ'],
        ['ↂↁⅯↁ',    'ↂⅯↂ'],
		['M',   'Ⅿ'],
		['D',   'Ⅾ'],
		['C',   'Ⅽ'],
		['L',   'Ⅼ'],
		['IX',  'Ⅸ'],
		['XI',  'Ⅺ'],
		['XII', 'Ⅻ'],
		['X',   'Ⅹ'],
		['VIII','Ⅷ'],
		['VII', 'Ⅶ'],
		['VI',  'Ⅵ'],
		['IV',  'Ⅳ'],
		['V',   'Ⅴ'],
		['III', 'Ⅲ'],
		['II',  'Ⅱ'],
		['I',   'Ⅰ'],
	], function(_,r) {
		str = str.split(r[0]).join(r[1]);
	});
	return str;
}
function halfreromanize(str) {
	$.each([
        ['MMMMM','ↁ'],
        ['MMMM','Ⅿↁ'],
        ['ↁↁ',    'ↂ'],
        ['ↂↂↂↂↂ',    'ↇ'],
        ['ↂↂↂↂ',    'ↂↇ'],
        ['ↇↇ',    'ↈ'],
        ['ↇↂↇ',    'ↂↈ'],
        ['ↁⅯↁ',    'Ⅿↂ'],
        ['ↂↁⅯↁ',    'ↂⅯↂ'],
    ], function(_,r) {
		str = str.split(r[0]).join(r[1]);
	});
	return str;
}

var change = [function() {
	var val = $(this).val(), result = val && (romanize(val) || "Error"), rr;
	$('#output-uc').text(rr = reromanize(result));
	$('#output-lc').text(rr.toLowerCase());
	if (result === "Error")
	{ $('#roman-number').attr('placeholder', result); result = "" }
	else $('#roman-number').attr('placeholder', "Roman Numeral");
	$('#roman-number') .val(halfreromanize(result));
}, function() {
	var val = $(this).val(), result = val && (deromanize(val) || "Error");
	$('#output-uc').text(rr = reromanize(val));
	$('#output-lc').text(rr.toLowerCase());
	if (result === "Error")
	{ $('#arabic-number').attr('placeholder', result); result = "" }
	else $('#arabic-number').attr('placeholder', "Arabic Number");
	$('#arabic-number').val(result);
}]
$('#arabic-number').on('keyup', change[0]).on('keydown', change[0]).trigger('keyup');
$('#roman-number') .on('keyup', change[1]).on('keydown', change[1]);
</script>
