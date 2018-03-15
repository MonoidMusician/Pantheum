// From http://blog.stevenlevithan.com/archives/javascript-roman-numeral-converter
module.exports.romanize = function romanize (num) {
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

module.exports.deromanize = function deromanize (str) {
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
module.exports.reromanize = function reromanize(str) {
	// also thanks to http://ingram-braun.net/public/programming/web/roman-numeral-unicode-form/
	for (let r of [
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
		['VIII','Ⅷ'],
		['III', 'Ⅲ'],
		['VII', 'Ⅶ'],
		['XII', 'Ⅻ'],
		['II',  'Ⅱ'],
		['VI',  'Ⅵ'],
		['IX',  'Ⅸ'],
		['XI',  'Ⅺ'],
		['X',   'Ⅹ'],
		['IV',  'Ⅳ'],
		['V',   'Ⅴ'],
		['I',   'Ⅰ'],
	]) {
		str = str.split(r[0]).join(r[1]);
	}
	return str;
}
module.exports.halfreromanize = function halfreromanize(str) {
	for (let r of [
		['MMMMM','ↁ'],
		['MMMM','Ⅿↁ'],
		['ↁↁ',    'ↂ'],
		['ↂↂↂↂↂ',    'ↇ'],
		['ↂↂↂↂ',    'ↂↇ'],
		['ↇↇ',    'ↈ'],
		['ↇↂↇ',    'ↂↈ'],
		['ↁⅯↁ',    'Ⅿↂ'],
		['ↂↁⅯↁ',    'ↂⅯↂ'],
	]) {
		str = str.split(r[0]).join(r[1]);
	}
	return str;
}
