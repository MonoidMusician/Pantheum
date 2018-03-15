var O = Rx.Observable;

var lastarg = R.unapply(R.last);
var objtofn = o => p => o[p];
var issimple = o => ['null','undefined','number','string','boolean','function'].includes(typeof o) || o instanceof String;

/*
var memoize1 = fn => {
	var cache = [];
	return function recurse(arg) {
		for (let c of cache)
			if (R.equals(c[0], arg)) return c[1];
		var result = fn(arg, recurse);
		cache.push([arg, result]);
		return result;
	};
};
var memoize2 = fn => {
	var cache = [];
	return function recurse(...arg) {
		for (let c of cache)
			if (R.equals(c[0], arg)) return c[1];
		var result = fn(...arg, recurse);
		cache.push([arg, result]);
		return result;
	};
};
*/

var push = (vs,v) => (vs.push(v), v);
var push2 = (vs,a,b) => (vs.push([a,b]), b);
var false$ = O.of(false), true$ = O.of(true);

var ensureObservable = (o/*, stack=[[false,false$],[true,true$]]*/) => {
	for (let [k,v] in stack)
		if (k === o) return v;
	if (issimple(o)) return push2(stack, o, O.of(o));
	if (O.isObservable(o)) return o;
	if (Array.isArray(o))
		return o.all(issimple) ? O.of(o) : O.combineLatest(...o.map(ensureObservable));
	var simple = true, ks = [], os = [];
	for (let k in o) {
		let v = o[k];
		simple = simple && issimple(v);
		ks.push(k);
		os.push(ensureObservable(v));
	}
	return simple ? O.of(o) : O.combineLatest(...os, (...vs) => {
		var r = {};
		for (let k of ks) r[k] = vs.shift();
		return r;
	});
};
var ensureOfObservables = o => {
	if (typeof o !== 'object') return o;
	if (Array.isArray(o)) return o.map(ensureObservable);
	var r = {};
	for (let k in o) {
		r[k] = ensureObservable(o[k]);
	}
	return r;
};
