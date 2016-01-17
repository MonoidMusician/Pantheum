"use strict";
window.parser = (function() {
	// Ugly hack around no lookbehind support: capture a single non backslash character
	var meta = /(^|[^\\])(\\\\)*(\\)/.source;
	var brace = new RegExp(meta+"\\{", 'g'),
		ecarb = new RegExp(meta+"\\}", 'g'),
		paren = new RegExp(meta+"\\(", 'g'),
		nerap = new RegExp(meta+"\\)", 'g'),
		brckt = new RegExp(meta+"\\[", 'g'),
		tkcrb = new RegExp(meta+"\\]", 'g');
	var regexes = [brace,paren,brckt];

	function search2(s, regex, inclusive) {
		if (regex.lastIndex > 0) regex.lastIndex -= 1; // lookbehind hack
		var r = regex.exec(s);
		if (r === null)
			return s.length;
		var off = (r[1]?r[1].length:0); // continue lookbehind hack
		if (inclusive)
			return r.index + r[0].length;
		return r.index + off + (r[2]?r[2].length:0);
	};
	function getfirst(s, regexes, startAt) {
		for (let type in regexes) {
			let r = regexes[type];
			r.lastIndex = startAt;
			var j = search2(s, r, false);
			return [type,j];
		}
		return null;
	}
	function level_content(s, regex, xeger, startAt) {
		if (startAt === undefined) startAt = 0;
		var L, l, R, r,
			next = function(p) {
				if (typeof p === 'number') regex.lastIndex = p;
				var r = search2(s, regex, true);
				xeger.lastIndex = regex.lastIndex;
				return r;
			},
			txen = function(p) {
				if (typeof p === 'number') xeger.lastIndex = p;
				var r = search2(s, xeger, false);
				regex.lastIndex = xeger.lastIndex;
				return r;
			},
			// like txen, but advances the position past the end of the current match first
			TXEN = function(p) {
				if (typeof p === 'number') xeger.lastIndex = p;
				var r = search2(s, xeger, true);
				regex.lastIndex = xeger.lastIndex;
				if (r == s.length) return r;
				r = search2(s, xeger, false);
				regex.lastIndex = xeger.lastIndex;
				return r;
			};
		L = next(startAt);
		l = next(L);
		R = txen(L);
		while (l <= R && R < s.length) {
			R = TXEN(R);
			l = next(l);
		}
		return s.substr(L, R-L);
	}
	function* split1(s) {
		var l = 0, s = '', m = false;
		for (let c of s.split('')) {
			if (c === '\\' && !m)
			{m=true;continue}
			if (m && c != '\\') {
				m=false;
				switch(c) {
					case '|':
						if (l == 0) {
							yield s;
							s = '';
							continue;
						} else
							break;
					case '{':
					case '(':
					case '[':
						l += 1;
						break;
					case '}':
					case ')':
					case ']':
						l -= 1;
						break;
				}
				c = '\\'+c;
			}
			s += c;
		}
		yield s;
	}
	function parser(s) {
		this.position = 0;
		this.s = s;
		this.udata = {};
	}
	parser.prototype.inner_part = function(s) {
		this.push();
		var pos = 0;
		var m = getfirst(s, regexes, pos);
		while (m != null) {
			var type = m[0]; var jump = m[1] - pos;
			if (jump) this.add(this.TEXT(s.substr(pos, jump)));
			if (type === 0)
				this.add(this.BRACE(this.inner(c = level_content(s, brace, ecarb, pos+jump))));
			else if (type === 1)
				this.add(this.PAREN(this.inner(c = level_content(s, paren, nerap, pos+jump))));
			else if (type === 2)
				this.add(this.BRCKT(this.inner(c = level_content(s, brckt, tkcrb, pos+jump))));
			pos += c.length + 4; // 4 = "\\{\\}".length;
			m = getfirst(s, regexes, pos);
		}
		if (pos < s.length) this.add(this.TEXT(s.substr(pos)));
		return this.pop();
	}
	parser.prototype.inner = function(s) {
		return this.CHOICES([...function*() {
			for (let S of split1(s)) {
				yield this.inner_part(S);
			}
		}]);
	}
	return parser;
})();
