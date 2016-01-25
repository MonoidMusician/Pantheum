"use strict";
window.Permutator = function(data) {
	this.data = data;
};
Permutator.prototype.gen = function() {
	return [...this];
};
Permutator.prototype.unique = function*() {
	var r = [];
	for (let a of this) {
		if (!r.includes(a)) {
			r.push(a);
			yield a;
		}
	}
};
Permutator.prototype.gen_unique = function() {
	return [...this.unique()];
};
Permutator.derived = function(cls) {
	cls.prototype = Object.create(Permutator.prototype);
	cls.prototype.constructor = cls;
	return cls;
};
Permutator.issimple = function(a) {
	var t = typeof a;
	return t === 'string' || a === null || t === 'undefined';
};
Permutator.escape = function(s) {
	for (let r of ['(',')','[',']','{','}','|'])
		s = s.split(r).join('\\'+r);
	return s;
};
window.Combo = (function() {
	function filterer(v) {
		return (Array.isArray(v) ? v.length !== 0 : true);
	}
	function mapper(v) {
		if (Array.isArray(v))
			for (let i in v)
				if (Array.isArray(v[i]))
					v[i] = new Combo(v[i]);
		return v;
	}
	function filter(arg) {
		for (let i in arg)
			if (arg[i] === undefined) return;
		return Permutator.issimple(arg) ? arg : (arg.filter ? arg.filter(filterer).map(mapper) : arg);
	}
	function* _iterate() {
		if (!arguments.length) yield '';
		else {
			var arg = new Array(arguments.length-1);
			var one = arguments[0];
			for (let i=0; i<arg.length; ++i) arg[i] = arguments[i+1];
			if (Permutator.issimple(one)) {
				if (arg.length)
					for (let l of _iterate.apply(null, arg))
						yield [one].concat(l);
				else yield [one];
			} else {
				if (arg.length)
					for (let r of _iterate.apply(null, arg))
						for (let l of one)
							yield* _iterate.apply(null, [l].concat(r));
				else for (let l of one) yield* _iterate(l);
			}
		}
	}
	function* iterate() {
		if (!arguments.length) yield '';
		else {
			var arg = new Array(arguments.length-1);
			var one = arguments[0];
			for (let i=0; i<arg.length; ++i) arg[i] = arguments[i+1];
			if (Permutator.issimple(one)) {
				if (arg.length)
					for (let l of iterate.apply(null, arg))
						yield one + l;
				else yield one;
			} else {
				if (arg.length)
					for (let r of iterate.apply(null, arg))
						for (let l of one)
							yield* iterate(l,r);
				else for (let l of one) yield* iterate(l);
			}
		}
	}
	function getlength(v) {
		if (!v) return 1;
		if (Array.isArray(v)) return v.reduce(function(a,b) {return a + getlength(b);}, 0);
		if (Permutator.issimple(v)) return 1;
		return v.length;
	}
	function getsingle(v) {
		if (!v) return '';
		if (Array.isArray(v)) return getsingle(v[0]);
		if (Permutator.issimple(v)) return v;
		return v.single;
	}
	function getrepr(v) {
		if (!v) return '';
		if (Array.isArray(v))
			if (v.includes(''))
				return '['+v.filter(function(a){return a}).map(getrepr).join('|')+']';
			else return '('+v.map(getrepr).join('|')+')';
		if (typeof v === 'string') return Permutator.escape(v);
		if (v.repr === Combo.prototype.repr)
			return v.data.map(getrepr).join('');
		return v.repr();
	}

	function Combo() {
		this.clear();
		this.post.apply(this, arguments);
	}
	Permutator.derived(Combo);
	Combo.prototype.clear = function() {
		this.length = 1;
		this.single = "";
		return (this.data = []);
	};
	Combo.prototype[Symbol.iterator] = function*() {
		yield* iterate.apply(null, this.data);
	};
	Combo.prototype.raw = function*() {
		yield* _iterate.apply(null, this.data);
	};
	Combo.prototype.repr = function() {
		return getrepr(this);
	};
	Combo.prototype.pre = function() {
		var arg = new Array(arguments.length);
		for (let i=0; i<arg.length; ++i) arg[i] = arguments[i];
		arg = filter(arg);
		if (arg === undefined)
			return this.clear();
		for (let a of arg) {
			a = filter(a);
			if (a === undefined) return this.clear();
			this.single += getsingle(a);
			this.data.unshift(a);
			this.length *= getlength(a);
		}
		return this.data;
	};
	Combo.prototype.post = function() {
		var arg = new Array(arguments.length);
		for (let i=0; i<arg.length; ++i) arg[i] = arguments[i];
		arg = filter(arg);
		if (arg === undefined)
			return this.clear();
		for (let a of arg) {
			a = filter(a);
			if (a === undefined) return this.clear();
			this.single += getsingle(a);
			this.data.push(a);
			this.length *= getlength(a);
		}
		return this.data;
	};
	Combo.prototype.simplify = function() {
		if (this.data.length === 1 && !Permutator.issimple(this.data[0]) && !Array.isArray(this.data[0]))
			return typeof this.data[0].simplify === 'function' ? this.data[0].simplify() : this.data[0];
		return this;
	};

	return Combo;
})();
window.PermuteOrder = (function() {
	function parseArg(arg) {
		if (arg === undefined || arg.length === 1)
			return arg;
		if (arg.length === 1 && Array.isArray(arg[0]))
			return arg[0];
		return arg;
	}
	function* _iterate() {
		if (!arguments.length) yield '';
		else {
			var arg = new Array(arguments.length);
			for (let i=0; i<arg.length; ++i) arg[i] = arguments[i];
			var inner = function*(arg) {
				var current = arg[0], arg = arg.slice(1);
				if (typeof current === 'string')
					var Current = [[current]];
				else var Current = [...(function*(){
					for (let i of current) yield [i]; // wrap each as an Array
				})()];
				if (!arg.length) {
					yield* Current;
				}
				else {
					for (let l of inner(arg)) {
						var r = [];
						while (l.length) {
							for (let c of Current)
								yield r.concat(c).concat(l);
							r.push(l.shift());
						}
						for (let c of Current)
							yield r.concat(c);
					}
				}
			};
			yield* inner(arg);
		}
	}
	function* iterate() {
		for (let i of _iterate.apply(this, arguments))
			yield i.join('');
	}
	function getrepr(v) {
		if (!v) return '';
		if (Array.isArray(v)) return '{'+v.map(getrepr).join('}{')+'}';
		if (typeof v === 'string') return Permutator.escape(v);
		if (v.repr === PermuteOrder.prototype.repr)
			return getrepr(v.data);
		return v.repr();
	}

	function PermuteOrder() {
		this.clear();
		this.post.apply(this, arguments);
	}
	Permutator.derived(PermuteOrder);
	PermuteOrder.prototype.clear = function() {
		this.length = 1;
		this.data = [];
	};
	PermuteOrder.prototype[Symbol.iterator] = function*() {
		yield* iterate.apply(null, this.data);
	};
	PermuteOrder.prototype.raw = function*() {
		yield* _iterate.apply(null, this.data);
	};
	PermuteOrder.prototype.repr = function() {
		return getrepr(this);
	};
	PermuteOrder.prototype.pre = function() {
		for (let i=0; i<arguments.length; ++i) {
			let a = arguments[i];
			this.data.unshift(a);
			this.length *= this.data.length;
			if (!Permutator.issimple(a)) this.length *= a.length;
		}
		return this.data;
	};
	PermuteOrder.prototype.post = function() {
		for (let i=0; i<arguments.length; ++i) {
			let a = arguments[i];
			this.data.push(a);
			this.length *= this.data.length;
			if (!Permutator.issimple(a)) this.length *= a.length;
		}
		return this.data;
	};
	PermuteOrder.prototype.simplify = function() {
		if (this.data.length === 1 && !Permutator.issimple(this.data[0]) && !Array.isArray(this.data[0]))
			return typeof this.data[0].simplify === 'function' ? this.data[0].simplify() : this.data[0];
		return this;
	};

	return PermuteOrder;
})();
window.Mixed = (function() {
	function concat(a) {
		if (typeof a === 'string') return a;
		var r = '';
		for (let b of a) r += concat(b);
		return b;
	}
	function Mixed() {
		this.c = [];
		this.p = [];
	}
	Permutator.derived(Mixed);
	var make_combo = function(arg) {
		var C = Combo.bind.apply(Combo, [null].concat(arg));
		return new C();
	};
	var make_permute = function(arg) {
		var P = PermuteOrder.bind.apply(PermuteOrder, [null].concat(arg));
		return new P();
	};
	function getrepr(v) {
		if (!v) return '';
		if (Array.isArray(v)) return '\\'+v.map(getrepr).join('\\')+'\\';
		if (typeof v === 'string') return Permutator.escape(v);
		if (v.repr === Mixed.prototype.repr) {
			let c = v.c, p = v.p;
			let i=0, j=0, r = '';
			for (; i<c.length; ++i) {
				if (c[i] === null) {
					r += '{'+getrepr(p[j++])+'}';
				} else {
					r += getrepr(c[i]);
				}
			}
			return r;
		}
		return v.repr();
	}

	Mixed.prototype[Symbol.iterator] = function*() {
		for (let v of this.raw()) yield v.join('');
	};
	Mixed.prototype.raw = function*() {
		for (let C of this.C.raw()) {
			for (let P of this.P.raw()) {
				let c = C.slice(0);
				let i=0, j=0;
				for (; i<c.length; ++i)
					if (C[i] == null)
						c[i] = P[j++];
				yield c;
			}
		}
	};
	Mixed.prototype.repr = function() {
		return getrepr(this);
	};
	Mixed.prototype.post = function() {
		for (let i=0; i<arguments.length; ++i) {
			var a = arguments[i];
			if (a instanceof Combo && a.data.length === 1)
				a = a.data[0];
			this.c.push(a);
		}
		return this;
	};
	Mixed.prototype.post_permute = function() {
		for (let i=0; i<arguments.length; ++i) {
			this.c.push(null);
			this.p.push(arguments[i]);
		}
		return this;
	};
	Mixed.prototype.generate = function() {
		this.C = make_combo(this.c).simplify();
		this.P = make_permute(this.p).simplify();
		this.length = this.C.length * this.P.length;
		return this;
	};
	Mixed.prototype.pre = 'TODO';
	Mixed.prototype.pre_permute = 'TODO';
	Mixed.prototype.simplify = function() {
		this.generate();
		if (this.p.length) return this;
		return this.C;
	}
	return Mixed;
})();