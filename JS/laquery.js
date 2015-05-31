var laquery = {};
laquery.common = {
	'decl12': /(?:u[sm]|am|[ioa]s?|ae)/,
	'decl3': /([ei]s?|ibus)/,
};
laquery.concat = function() {
	var res = "", flags = "";
	jQuery.each(arguments, function(i,r) {
		res += r.source;
		if (flags == "" || flags == "i") {
			if (r.global) flags += "g";
		}
		if (flags == "" || flags == "g") {
			if (r.ignoreCase) flags += "i";
		}
	});
	return RegExp(res, flags);
}
laquery.simplifications = (function(cc,c){return [
	[ /\u0304/g, '' ],
	[ /omnes/g, '' ],
	[ /omn(es|ibus) qu(i|ae)/g, "qui" ],
	[ /\s+/, ' ' ],
	[ /visibil([ei]s?|ibus)/g, 'visible' ],
	[ /ianuar([ei]s?|ibus)/g, 'input' ],
	[ /s?elect(u[sm]|am|[ioa]s?|ae)/g, 'selected' ],
	[ cc(/prim/g,c['decl12']), 'first' ],
	[ cc(/sol/g,c['decl12']), 'only' ],
]})(laquery.concat,laquery.common);
laquery.simplify = function(expr) {
	jQuery.each(this.simplifications, function(i,s) {
		expr = expr.replace(s[0],s[1]);
	});
	return expr.toLowerCase().trim();
};
laquery.cache = {};
laquery.escape = function(id) {
	var escapes = " !\"#$%&'()*+,./:;<=>?@[\]^`{|}~".split("");
	jQuery.each(escapes, function(i,e) {
		id = id.split(e).join("\\"+e);
	});
	return id;
}
laquery.parse = function(expr) {
	expr = this.simplify(expr);
	//if (expr === "omnes") expr = "";
	var res = "";
	if ($ === jQuery)
		var start = "$";
	else
		var start = "jQuery";
	start += "('";
	var end = "')";
	var capt;
	function deletion(t,x) {
		capt=arguments;
		return "";
	}
	function gobble(regex) {
		capt = null;
		expr = expr.replace(regex,deletion);
		return capt != null;
	}
	// FIXME: capture and sort?
	while (expr) {
		if (gobble(/\btypi ([._a-zA-Z0-9-]+)((?: vel [_a-zA-Z0-9-]+(?!(?! vel ) ?[_a-zA-Z0-0-]))*)/)) {
			if (capt[2]) {
				var sep;
				if (start) {sep = start;start=""}
				else sep = end + ".filter('";
				res += sep + capt[1] + capt[2].replace(/ vel /g, ', ');
				end = "')";
				start = end+".filter('";
			} else {
				if (start) {res+=start;start=""}
				res += capt[1];
			}
		} else if (gobble(/^classis ([._a-zA-Z0-9-]+)((?: vel [_a-zA-Z0-9-]+(?!(?! vel ) ?[_a-zA-Z0-0-]))*)/)) {
			if (capt[2]) {
				var sep;
				if (start) {sep = start;start=""}
				else sep = end + ".filter('";
				res += sep + '.' + capt[1] + capt[2].replace(/ vel /g, ', .');
				end = "')";
				start = end+".filter('";
			} else {
				if (start) {res+=start;start=""}
				res += "."+capt[1].replace(".","\\.");
			}
		} else if (gobble(/^signi ([._a-zA-Z0-9-]+)((?: vel [_a-zA-Z0-9-]+(?!(?! vel ) ?[_a-zA-Z0-0-]))*)/)) {
			if (capt[2]) {
				var sep;
				if (start) {sep = start;start=""}
				else sep = end + ".filter('";
				res += sep + '#' + capt[1] + capt[2].replace(/ vel /g, ', #');
				end = "')";
				start = end+".filter('";
			} else {
				if (start) {res+=start;start=""}
				res += "#"+capt[1];
			}
		} else if (gobble(/^:?visible\b/)) {
			if (start) {res+=start;start=""}
			res += ":visible";
		} else if (gobble(/^:?selected\b/)) {
			if (start) {res+=start;start=""}
			res += ":selected";
		} else if (gobble(/^:?input\b/)) {
			if (start) {res+=start;start=""}
			res += ":input";
		} else if (gobble(/^:?first\b/)) {
			if (start) {res+=start;start=""}
			res += ":first";
		} else if (gobble(/^:?only\b/)) {
			if (start) {res+=start;start=""}
			res += ":first:last";
		// FIXME: this shouldn't be a gobble
		} else if (gobble(/^(,\s*)?vel\b/)) {
			if (start) {res+=start;start=""}
			res += ", ";
		} else return("Could not parse: "+expr);
		expr = expr.trim().replace(/  +/g," ");
		first = false;
	}
	if (!res) return start+"*"+end;
	return res + end;
};
