/**
 * LA-IPA - Transcribe Latin into IPA, other formatting options
 * 
 * Copyright (C) 2015 Nick Scheel <nasalmusician@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.

 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.

**/

var la_ipa = (function () {
	var my = {};
	my.constructor = arguments.callee;

	my.mix = function () {
		var arg = arguments;
		return function(r) {
			for (i in arg) {
				r = arg[i](r);
			}
			return r;
		};
	};
	my.replasor = function (from,to) {
		return function(r) {return r.split(from).join(to)}
	};
	my.upper = function (r) {
		return r.toUpperCase();
	};
	my.lower = function (r) {
		return r.toLowerCase();
	};
	my.nspace = function (r) {
		return r.replace(/^\s*|\s(?=\s)|\s*$/g, "");
	};
	my.ASCIIize = function (r) {
		return my.ae_oe(my.sonusmedius(r)).split("").map(function(a) {return a.match(/[^\u0000-\u007f]/)?"":a}).join("");
	};
	my.sonusmedius = function (r) {
		return r.split("ⱶ").join("i");
	};
	my.ae_oe = function(r) {
		return (
			r.split("\u00E6").join("ae")
			 .split("\u0153").join("oe")
			 .split("\u00C6").join("Ae")
			 .split("\u0152").join("Oe")
		);
	};
	my.deASCIIize = function (r) {
		return (
			r.split("ß").join("ss")
			 .replace(/ae(?![e\u0308\u0304])/g, "æ")
			 .replace(/oe(?![e\u0308\u0304])/g, "œ")
			 .replace(/æ([\u0308\u0304e])/g, "ae$1")
			 .replace(/œ([\u0308\u0304e])/g, "oe$1")
			 .replace(/([aeiouy])\1(?!\u0304)/g, "$1\u0304")
		);
	};
	my.double_vowels = function (r) {
		return r.split("\u0304").map(function(a,i,l) {return i<l.length-1?a+a[a.length-1]:a;}).join("");
	};
	my.undouble_vowels = function (r) {
		return r.replace(/([aeiouy])\1(?!\u0304)/g, "$1\u0304");
	};
	my.LA = function (r) {
		return r.split("i\u0304").map(my.ASCIIize).join("\uA7FE").split("U").join("V").split("J").join("I");
	};
	my.LA_apex = function (r) {
		var map = function(r){return r.split("\u0304").map(my.ASCIIize).join("\u0301")};
		return r.split("i\u0304").map(map).join("\uA7FE").split("U").join("V").split("J").join("I");
	};
	my.old_latin = function (r) {
		return r.replace(/[uv](?=[sm])/gi,"o").replace(/([ao])e/gi,"$1i");
	};
	my.short_vowels = function (r) {
		return r.replace(/([aeiouy])\u0301?(?!\u0304)/g, "$1\u0306").replace(/([qg]u)\u0306|([aeiou])\u0306([eiu])\u0306|([aeiou])\u0306(m)(?![\w])/g,"$1$2$3$4$5");
	};
	my.apex = function (r) {
		return r.split("\u0304").join("\u0301");
	};
	my.orthography_j = function (r) {
		return r.replace(/jj?i/g, "i").replace(/jj/g, "j").replace(/JJ?I/g, "I").replace(/JJ/g, "J");
	};
	my.no_j = function (r) {
		return r.replace(/j/g, "i").replace(/ii+/g, "i").replace(/J/g, "I").replace(/I[Ii]+/g, "I");
	};
	my.nasal = function (r) {
		return r.replace(/([aeiouy])m(?![a-zA-Z])/g,"$1\u0303");
	};
	my.eszett = function (r) {
		return r.split("ss").join("ß");
	};
	my.silicus = function (r) {
		return r.replace(/([bcdfghj-np-tvwxz])\1/g,"$1\u0357");
	};
	function dbl_vwls_hlpr(r) {
		var l = r.length;
		var ret = r[0];
		for (var i=l; i>1; i-=2) {
			ret += "\u02D0";
		}
		if (i) ret += "\u02D1";
		return ret;
	};
	my.IPA_longa = function (r) {
		return r.split("\u0304").join("\u02D0").replace(/([bcdfghj-np-tvwxz])\1+/g,dbl_vwls_hlpr).split("\u0306").join("");
	};
	my.IPA_accent = function (r) {
		return r.replace(/([aeiouy])\u0301/g, "\u02C8$1");
	};
	my.IPA_transcr = function (r) {
		// Note: assumes orthography uses i/j and u/v correctly
		return (
			r
			// Normalize
			.split("x").join("ks")
			.split("c").join("k")
			.split("v").join("w")
			// Assimilations
			.split("bs").join("ps")
			// Convert sequences to IPA
				// er allophone
				/*.replace(/e(?=[.]?r)/g,"\u00E6")
				.replace(/([ao])\u00E6/g, "$1e")/**/
				// Rhotics
				.replace(/r(?![.]?[r\u02D0])/g,"\u027E")
				.replace(/r[.]\u027E/g, "r.r")
				.replace(/([tdbpfvnm][\u02D0]?)\u027E/g, "$1r")
				// Double articulations
				.replace(/(?=mn|pt|pk|bd|bg)(.)(.)/g,"$1\u0361$2")
				// Digraphs/dipthongs
				.split("qu").join("k\u02B7")
				.split("ngu").join("ng\u02B7")
				.replace(/([ao]e|[aeo]u|[eu]i)(?![aeiou]?[\u02D0\u032F])/g,"$1\u032F")
				// Aspiration
				.replace(/([tkp])h/g, "$1\u02B0")
				// Velar nasal
				.replace(/g([.]?n)|n([.]?[gk])/g,"\u014B$1$2")
				// Nasalized vowels
				.replace(/([aeiou])(n(?=[szfv])|m((?![.]|(?=[.][.])|k\u02B7e|ve)|(?![\w.])))/g,"$1\u0303\u02D0")
				//.replace(/([aeiou])(n(?=[szfv])|m((?![.]|(?=[.][.])|k\u02B7e|ve)|(?![\w.])))/g,"$1\u0303\u02D0$2\u0325")
				//.replace(/\u02B7(?=[ei\u025B\u026A])/g,"\u1DA3")
				// Short vowels
				.replace(/\u2C76(?=[mbpf])/g, "\u0289") // sonus medius rounded/labialized
				.replace(/\u2C76/g, "\u0268") // sonus medius
				.replace(/i(?![aeiou]|[\u02D0\u032F\u02D0])/g, "\u026A") // note: all vowels here, not just dipthongs
				.replace(/e(?![eiu]\u032F|[\u0303\u032F\u02D0])/g, "\u025B")
				.replace(/o(?![eiu]\u032F|[\u0303\u032F\u02D0])/g, "\u0254")
				.replace(/u(?![eiu]\u032F|[\u0303\u032F\u02D0])/g, "\u028A")
				// Double period means ignore the adjancency, but has no IPA value
				.replace(/[.][.]+/g, ".")
		);
	};
	my.IPA_eccl = function (r) {
		return (
			r
			// Normalization
			.split("y").join("i")
			.split("x").join("ks")
			// Rhotics
			.replace(/r(?![.]?[r\u02D0])/g,"\u027E")
			.replace(/r[.]\u027E/g, "r.r")
			.replace(/([tdbpfvnm])\u027E/g, "$1r")
			// Double articulations
			.replace(/(?=mn|pt|pk|bd|bg)(.)(.)/g,"$1\u0361$2")
			// Digraphs/dipthongs
			.split("qu").join("k\u02B7")
			.split("ngu").join("ng\u02B7")
			.replace(/([ao]e|[aeo]u|[eu]i)(?![aeiou]?[\u02D0\u032F])/g,"$1\u032F")
			.replace(/[ao]e\u032F/g, "e")
			// Velar nasal
			.replace(/g([.])?n/g,"$1\u0272")
			// Italian rules
			.replace(/(^|[^stx])ti(?=[aeou])/g, "$1t\u0361si")
			.replace(/z/g, "d\u0361z")
			.replace(/sc(?=[ei])/g, "\u0283")
			.replace(/g(?=[ei])/g, "d\u0361\u0292")
			.replace(/c(?:h(?=[ei])|(?![ei]))/g, "k")
			.replace(/c(?:i(?=[auo]))?/g, "t\u0361\u0283")
			.replace(/([aeiou])s(?=[aeiou])/g, "$1z")
			.split("gh").join("g")
			.split("w").join("v")
			// Double period means ignore the adjancency, but has no IPA value
			.replace(/[.][.]+/g, ".")
			.replace(/[.](?=\u02C8)/g, "")
		);
	};
	my.qv = function (r) {
		return r.split("qu").join("qv").split("Qu").join("Qv").split("QU").join("QV");
	};
	my.Llath = function (r) {
		return (
			r.split("i\u0304").join("j")
			 .split("I\u0304").join("J")
			 .split("u\u0304").join("w")
			 .split("U\u0304").join("W")
			 .split("v").join("w")
			 .split("V").join("W")
			 .split("ⱶ").join("ı") //sonus medius -> ih (i-dot)
			 .split("gn").join("ñ") //enya
			 .split("qu").join("ẇ") //wh (w-dot)
			 .split("Qu").join("Ẇ") //wh (w-dot)
			 .split("QU").join("Ẇ") //wh (w-dot)
			 .split("ph").join("ṗ") //ph (p-dot)
			 .split("Ph").join("Ṗ") //ph (p-dot)
			 .split("PH").join("Ṗ") //ph (p-dot)
			 .split("th").join("þ") //th (thorn)
			 .split("Th").join("Þ") //th (thorn)
			 .split("TH").join("Þ") //th (thorn)
			 .split("ts").join("ŝ") //s-hat
			 .split("TS").join("Ŝ") //s-hat
			 .split("Ts").join("Ŝ") //s-hat
			 .split("dz").join("ẑ") //z-hat
			 .split("Dz").join("Ẑ") //z-hat
			 .split("DZ").join("Ẑ") //z-hat
		);
	};
	my.Greek = function (r) {
		return (
			r.replace(/us(?=[^w])/,"os")
			 .replace(/um(?=[^w])/,"on")
			 .split("\u00E6").join("ai")
			 .split("\u0153").join("oi")
			 .split("\u00C6").join("Ai")
			 .split("\u0152").join("Oi")
			 .split("Qu").join("Κυ")
			 .split("QU").join("ΚΥ")
			 .split("ph").join("f")
			 .split("th").join("θ")
			 .split("ch").join("χ")
			 .split("ps").join("ψ")
			 .split("x").join("ξ")
			 .split("a").join("α")
			 .split("b").join("β")
			 .split("c").join("k")
			 .split("d").join("δ")
			 .split("e\u0304").join("η")
			 .split("e").join("ε")
			 .split("f").join("φ")
			 .split("g").join("γ")
			 .split("i").join("j")
			 .split("j").join("ι")
			 .split("k").join("κ")
			 .split("l").join("λ")
			 .split("m").join("μ")
			 .split("n").join("ν")
			 .split("o\u0304").join("ω")
			 .split("o").join("ο")
			 .split("p").join("π")
			 .split("qu").join("κυ")
			 .split("r").join("ρ")
			 .split("s").join("σ")
			 .split("t").join("τ")
			 .split("u").join("υ")
			 .split("v").join("υ")
			 .split("y").join("υ")
			 .split("z").join("ζ")
			 .split("PH").join("Ph")
			 .split("TH").join("Th")
			 .split("CH").join("Ch")
			 .split("PS").join("Ps")
			 .split("Ph").join("F")
			 .split("Th").join("Θ")
			 .split("Ch").join("Χ")
			 .split("Ps").join("Ψ")
			 .split("X").join("Ξ")
			 .split("A").join("Α")
			 .split("B").join("Β")
			 .split("C").join("Κ")
			 .split("D").join("Δ")
			 .split("E\u0304").join("Η")
			 .split("E").join("Ε")
			 .split("F").join("Φ")
			 .split("G").join("Γ")
			 .split("I").join("J")
			 .split("J").join("Ι")
			 .split("K").join("Κ")
			 .split("L").join("Λ")
			 .split("M").join("Μ")
			 .split("N").join("Ν")
			 .split("O\u0304").join("Ω")
			 .split("O").join("Ο")
			 .split("P").join("Π")
			 .split("R").join("Ρ")
			 .split("S").join("Σ")
			 .split("T").join("Τ")
			 .split("U").join("Υ")
			 .split("V").join("Υ")
			 .split("Y").join("Υ")
			 .split("Z").join("Ζ")
			 .split("\u0304").join("")
			 .replace(/(^|[^Α-Ωα-ω])h([αειουωη])/gi, "$1$2\u0314")
			 .replace(/(^|[^Α-Ωα-ω])([αειουωη])(?![\u0314])/gi, "$1$2\u0313")
			 .replace(/σ(?![Α-Ωα-ω])/g, "ς") 
		);
	};
	// Adapted from: https://sim0n.wordpress.com/2009/03/28/javascript-char-code-to-unicode-fullwidth-latin/
	my.fullwidth = function (r) {
		var ret = "";
		for(i=0; i<r.length; i++) {
			if(r.charCodeAt(i) >= 33 && r.charCodeAt(i) <= 270) {
				ret += String.fromCharCode(r.charCodeAt(i) + 65248);
			} else if(r.charCodeAt(i) == 32) {
				ret += String.fromCharCode(12288);
			}
		}
		return ret;
	}
	my.transforms = {
		"Original": function(a){return a},
		"No diacritics": my.mix(my.sonusmedius, my.ASCIIize),
		"CLC": my.mix(my.ae_oe, my.sonusmedius, my.no_j),
		"Mark all vowels": my.mix(
			my.undouble_vowels,
			my.lower,
			my.short_vowels,
			my.orthography_j
		),
		"Silicus": my.mix(my.silicus, my.sonusmedius, my.orthography_j),
		"Nasal": my.mix(my.nasal, my.sonusmedius, my.orthography_j),
		"Silicus+Nasal": my.mix(my.nasal, my.silicus, my.sonusmedius, my.orthography_j),
		"Silicus+Eszett+Nasal": my.mix(my.nasal, my.eszett, my.silicus, my.orthography_j),
		"qv": my.qv,
		"fullwidth": my.mix(
			my.ASCIIize,
			my.fullwidth
		),
		"Greek": my.Greek,
		"Ļaþ": my.Llath,
		"Finnish": my.mix(
			my.double_vowels,
			my.replasor("c","k"),
			my.ASCIIize
		),
		"Latin inscription (upper-case)": my.mix(
			my.LA,
			my.upper,
			my.no_j
		),
		"Old Latin inscription (upper-case)": my.mix(
			my.LA,
			my.old_latin,
			my.upper,
			my.no_j
		),
		"Old Latin inscription (upper-case, apex)": my.mix(
			my.LA_apex,
			my.old_latin,
			my.upper,
			my.no_j
		),
		"IPA transcription": my.mix(
			my.undouble_vowels,
			my.lower,
			my.IPA_longa,
			my.IPA_accent,
			my.IPA_transcr
		),
	};
	my.transform_key = my.old_transform = null;
	my.select_transformer = function(nomen) {
		my.transform_key = nomen;
		my.transform = my.transforms[nomen];
		return my;
	}
	my.set_selector = function(selector) {
		my.selector = selector;
		return my;
	}
	my.set_lang = function(lang) {
		my.selector = '.format-word-'+lang;
		return my;
	}
	my.select_transformer(
		pantheum && pantheum.udata && pantheum.udata.la_ipa
		? pantheum.udata.la_ipa
		: "CLC"
	);
	//my.select_transformer("IPA transcription");
	//my.transform = my.transforms["Silicus+Eszett+Nasal"];
	//my.select_transformer("Greek");
	my.unformatted = {};
	my.selector = '.format-word-la';
	my.format = function (space) {
		if (space === undefined) space = $(my.selector);
		else space = $(space).find(my.selector);
		getTextNodesIn(space).each(function() {
			var t = $(this), r, original;
			var p = $(this.parentElement);
			var i = getTextNodesIn(p).index(this);
			var attr = 'data-original-word'+i;
			if (!p.attr(attr)) p.attr(attr, r=t.text());
			else r = p.attr(attr);
			r = r.normalize('NFKD');
			r = my.transforms[my.transform_key](r);
			// XXX: this is really ugly
			// because we can't do this:
			// t.text(r);
			// (t is not a valid jQuery object??)
			this.textContent = r;
		});
	}
	$(function() {
		my.format();
	});

	return my;
}());

if (!æ) var æ = la_ipa;
