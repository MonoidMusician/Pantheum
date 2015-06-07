/**
 * LA-IPA - Transcribe Latin into IPA
 * 
 * Copyright (C) 2015 Nick Scheel
 * All rights reserved.
 * Licensed under BSD 2 Clause License:
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, 
 *   this list of conditions and the following disclaimer in the documentation 
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
**/

var la_ipa = (function () {
	var my = {};

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
		return my.ae_oe(r).split("").map(function(a) {return a.match(/[^\u0000-\u007f]/)?"":a}).join("");
	};
	my.ae_oe = function(r) {
		return (
			r.split("\u00E6").join("ae")
			 .split("\u0153").join("oe")
			 .split("\u00C6").join("Ae")
			 .split("\u0152").join("Oe")
		);
	}
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
			// Convert sequences to IPA
				// er allophone
				.replace(/e(?=[.]?r)/g,"\u00E6")
				.replace(/([ao])\u00E6/g, "$1e")
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
		return r.split("qu").join("qv");
	};
	my.x = function (r) {
		return r.replace(/[kcg]s/g,"x");
	};
	my.Greek = function (r) {
		return (
			r.split("\u0304").join("")
			 .split("\u00E6").join("ai")
			 .split("\u0153").join("oi")
			 .split("\u00C6").join("Ai")
			 .split("\u0152").join("Oi")
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
			 .split("C").join("k")
			 .split("D").join("Δ")
			 .split("E\u0304").join("Η")
			 .split("E").join("Ε")
			 .split("F").join("Φ")
			 .split("G").join("Γ")
			 .split("I").join("j")
			 .split("J").join("Ι")
			 .split("K").join("Κ")
			 .split("L").join("Λ")
			 .split("M").join("Μ")
			 .split("N").join("Ν")
			 .split("O").join("Ο")
			 .split("P").join("Π")
			 .split("Qu").join("Κυ")
			 .split("QU").join("ΚΥ")
			 .split("R").join("Ρ")
			 .split("S").join("Σ")
			 .split("T").join("Τ")
			 .split("U").join("Υ")
			 .split("V").join("Υ")
			 .split("Y").join("Υ")
			 .split("Z").join("Ζ")
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
		"No diacritics": my.mix(
			my.x,
			my.ASCIIize
		),
		"Finnish": my.mix(
			my.double_vowels,
			my.replasor("c","k"),
			my.ASCIIize
		),
		"Latin inscription (upper-case)": my.mix(
			my.x,
			my.LA,
			my.upper,
			my.no_j
		),
		"Old Latin inscription (upper-case)": my.mix(
			my.x,
			my.LA,
			my.old_latin,
			my.upper,
			my.no_j
		),
		"Old Latin inscription (upper-case, apex)": my.mix(
			my.x,
			my.LA_apex,
			my.old_latin,
			my.upper,
			my.no_j
		),
		"Mark all vowels": my.mix(
			my.undouble_vowels,
			my.lower,
			my.x,
			my.short_vowels,
			my.orthography_j
		),
		"IPA transcription": my.mix(
			my.undouble_vowels,
			my.lower,
			my.x,
			my.IPA_longa,
			my.IPA_accent,
			my.IPA_transcr
		),
		"Original": my.nspace,
		"Silicus": my.mix(my.x, my.silicus, my.orthography_j),
		"Nasal": my.mix(my.x, my.nasal, my.orthography_j),
		"Silicus+Nasal": my.mix(my.x, my.nasal, my.silicus, my.orthography_j),
		"Silicus+Eszett+Nasal": my.mix(my.x, my.nasal, my.eszett, my.silicus, my.orthography_j),
		"Silicus2": my.silicus,
		"IPA length": my.IPA_longa,
		"IPA length + accent": my.mix(
			my.IPA_longa,
			my.IPA_accent
		),
		"x": my.x,
		"qv": my.qv,
		"x+qv": my.mix(my.x,my.qv),
		"x+ae+oe": my.mix(my.x,my.ae_oe),
		"x+ae+oe+dagger": my.mix(my.x,my.ae_oe,function(r){return r.split('*').join('†')}),
		"fullwidth": my.mix(
			my.ASCIIize,
			my.fullwidth
		),
		"Greek": my.Greek,
	};
	my.transform = my.old_transform = null;
	my.transform = my.transforms["x+ae+oe+dagger"];
	my.select_transformer = function(nomen) {
		my.transform = my.transforms[nomen];
		return my;
	}
	//my.transform = my.transforms["Silicus+Eszett+Nasal"];
	//my.transform = my.transforms["Greek"];
	my.unformatted = {};
	my.selector = '.format-word-la';
	my.format = function (space) {
		/*getTextNodesIn(my.selector).each(function() {
			var t = $(this), r, original;
			if (my.old_transform === null) {
				r = t.text();
			} else {
				r = my.unformatted[my.old_transform][t.text()];
			}
			original = r;
			r = r.normalize('NFKD');
			r = my.transforms[my.transform](r);
			// XXX: this is really ugly
			// because we can't do this:
			// t.text(r);
			// (t is not a valid jQuery object??)
			t.context.textContent = r;
			if (!(my.transform in my.unformatted)) {
				my.unformatted[my.transform] = {};
			}
			my.unformatted[my.transform][r] = original;
		});
		my.old_transform = my.transform;*/
		if (space === undefined) space = $(my.selector);
		else space = space.find(my.selector);
		space.each(function() {
			var $this = $(this);
			$this.attr("data-original-word", $this.text());
			var v = my.transform(
				//$this.text().trim().replace(/\s*\/\s*/g,"\n").normalize('NFKD')
				$this.text().trim().normalize('NFKD')
			);
			if (v === null) return v = "An error occurred";
			$this.text(v);
		});
	}

	return my;
}());

if (!æ) var æ = la_ipa;
