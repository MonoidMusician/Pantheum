"use strict";
window.Combo = (function() {
	function filterer(v) {
		return v !== null && (Array.isArray(v) ? v.length !== 0 : true);
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
		return arg.filter ? arg.filter(filterer).map(mapper) : arg;
	}
	// All string arguments become a list of strings
	function parseArg(arg) {
		if (arg === undefined || arg.length === 1)
			return arg;
		for (let i in arg)
			if (typeof arg[i] !== 'string') return arg;
		return [arg];
	}
	function* iterate() {
		if (!arguments.length) yield '';
		else {
			var arg = new Array(arguments.length-1);
			var one = arguments[0];
			for (let i=0; i<arg.length; ++i) arg[i] = arguments[i+1];
			if (typeof one == 'string') {
				if (arg.length)
					for (let l of iterate.apply(null, arg))
						yield one + l;
				else yield one;
			} else {
				if (arg.length)
					for (let l of iterate.apply(null, arg))
						for (let r of one)
							yield* iterate(r,l);
				else for (let r of one) yield* iterate(r);
			}
		}
	}
	function getlength(v) {
		if (Array.isArray(v)) return v.reduce(function(a,b) {return a + getlength(b);}, 0);
		if (typeof v === 'string') return 1;
		return v.length;
	}
	function getsingle(v) {
		if (Array.isArray(v)) return getsingle(v[0]);
		if (typeof v === 'string') return v;
		return v.single;
	}
	function escape(s) {
		for (let r of ['(',')','[',']','{','}','|'])
			s = s.split(r).join('\\'+r);
		return s;
	}
	function getrepr(v) {
		if (Array.isArray(v)) return '('+v.map(getrepr).join('|')+')';
		if (typeof v === 'string') return escape(v);
		return v.data.map(getrepr).join('');
	}

	function Combo() {
		this.clear();
		this.post.apply(this, arguments);
	}
	Combo.prototype.clear = function() {
		this.length = 1;
		this.single = "";
		return (this.data = []);
	};
	Combo.prototype[Symbol.iterator] = function*() {
		yield* iterate.apply(null, this.data);
	};
	Combo.prototype.gen = function() {
		return [...this];
	};
	Combo.prototype.repr = function() {
		return getrepr(this);
	};
	Combo.prototype.pre = function() {
		var arg = new Array(arguments.length);
		for (let i=0; i<arg.length; ++i) arg[i] = arguments[i];
		arg = parseArg(filter(arg));
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
		arg = parseArg(filter(arg));
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
	function* iterate() {
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
			for (let i of inner(arg)) yield i.join('');
		}
	}
	function PermuteOrder() {
		this.clear();
		this.post.apply(this, arguments);
	}
	PermuteOrder.prototype.clear = function() {
		this.length = 1;
		this.data = [];
	};
	PermuteOrder.prototype[Symbol.iterator] = function*() {
		yield* iterate.apply(null, this.data);
	};
	PermuteOrder.prototype.gen = function() {
		return [...this];
	};
	PermuteOrder.prototype.pre = function() {
		var arg = new Array(arguments.length);
		for (let i=0; i<arg.length; ++i) arg[i] = arguments[i];
		arg = parseArg(arg);
		if (arg === undefined)
			return this.clear();
		for (let a of arg) {
			this.data.unshift(a);
			this.length *= this.data.length;
			if (typeof a !== 'string') this.length *= a.length;
		}
		return this.data;
	};
	PermuteOrder.prototype.post = function() {
		var arg = new Array(arguments.length);
		for (let i=0; i<arg.length; ++i) arg[i] = arguments[i];
		arg = parseArg(arg);
		if (arg === undefined)
			return this.clear();
		for (let a of arg) {
			this.data.push(a);
			this.length *= this.data.length;
			if (typeof a !== 'string') this.length *= a.length;
		}
		return this.data;
	};

	return PermuteOrder;
})();