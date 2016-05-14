function _do_ignore(l,ignore) {
	if (!ignore || !l) return l;
	var vec = Array.isArray(l);
	ignore = ignore.map(p=>p.toString()).filter(p=>!p.includes('/'));
	if (Array.isArray(l)) {
		var m = l.map((v,k) => {
			if (typeof v === 'object') {
				v = _do_ignore(v,ignore);
				if (('length' in v) ? v.length : Object.keys(v).length)
					return v;
			} else if (!ignore.contains(v)) {
				return v;
			}
		}).filter(v=>v!==undefined);
		console.log(l,m);
		for (let k of l.keys()) delete l[k];
		l.length = 0;
		for (let n of m) l.push(n);
		return l;
	}
	for (let k in l) {
		let v = l[k];
		if (typeof v === 'object') {
			v = l[k] = _do_ignore(v,ignore);
			if (!v || ignore.includes(k))
				delete l[k];
		} else if (ignore.contains(v))
			delete l[k];
	}
	return l;
}

function _in_ignore(p,ignore) {
	if (!ignore) return false;
	var base = model.Path(p);
	return ignore.some(ig => p.issub(base.reset().add2(ig), true));
}

function _filter_ignore(values, ignore, p, empty=true, prev=null) {
	var ret = [];
	if (values !== null && values !== false) {
		if (prev) {
			if (!prev[0]) prev[0]=[false];
			var count = [];
			ret['_'] = [];
			for (var k of prev[0]) {
				ret[k] = _filter_ignore(values, ignore, model.Path(p,k), empty, array_slice(prev, 1));
				// TODO: only works when prev.length === 1
				count.push(...ret[k]);
			}
			for (let v of values) {
				if (count.includes(v))
					ret['_'].push(v);
			}
		} else {
			for (var v of values) {
				if (!v || !_in_ignore(model.Path(p,v),ignore))
					ret.push(v);
			}
		}
	}
	if (empty || ret)
		return ret;
	return [false];
}
function _filter_ignore2(values, ignore, p, parallel, prev=null) {
	for (let key in values)
		values[key] = _filter_ignore(values[key], ignore, model.Path(p,key), true, prev?[prev[key]]:null);
}
function _fill(values, parallel) {
	var ret = [];
	for (var k of parallel) {
		ret[k] = values;
	}
	return ret;
}
function is_fillable(v) {
	if (!Array.isArray(v)) return false;
	return !v.some(Array.isArray);
	for (var k of v) {
		if (Array.isArray(k)) return false;
	}
	return true;
}

var model = pantheum.model;

// Parse the depath into the necessary row/column values for the table
function word_table_values(w,ignore=null) {
	//if (!w.read_paths()) return [null,null,null,null,null];
	var lang = w.lang;
	var spart = w.spart;
	var values = [];
	values.length = 5;
	var path = model.Path({word:w});
	// values[0] : table name
	// values[1] : major vertical
	// values[2] : minor vertical
	// values[3] : major horizontal
	// values[4] : minor horizontal
	if (lang === "la" || lang === "grc") {
		if ((spart === "noun") ||
			(spart === "adjective") ||
			(spart === "pronoun")) {
			if (spart === "adjective")
				values[1] = path.iterate("degree");
			else values[1] = [];
			values[2] = path.iterate("case");
			values[3] = path.iterate("number");
			values[4] = path.iterate("gender");
			values[0] = [];
		} else if (spart === "verb") {
			var moods = values[0] = path.iterate("mood");
			values[1] = [];
			values[2] = [];
			values[3] = [];
			values[4] = [];

			var hspan4 = [];
			var persons = model.Path({word:w},"indicative").iterate("person");
			var persons = _filter_ignore(persons, ignore, model.Path({word:w},"indicative"));
			for (var _ of persons) hspan4.push(false);

			var hacked = null;

			for (let _0 of moods) {
				if (ignore !== null && ignore.includes(_0))
					continue;
				values[_0] = [values[0]];
				values[_0].length = values.length;
				var path = model.Path({word:w},_0);
				if (_0 === "indicative" ||
				    _0 === "subjunctive" ||
				    _0 === "imperative") {
					values[_0][1] = path.iterate("voice");
					values[_0][2] = path.iterate("tense");
					values[_0][3] = path.iterate("number");
					values[_0][4] = model.Path({word:w},"indicative").iterate("person");
					if (!values[1]) values[1] = [false];
				} else if (_0 === "participle") {
					//values[_0][1] = [""];
					values[_0][2] = path.iterate("tense");
					values[_0][3] = path.iterate("voice");
					values[_0][4] = hspan4;
				} else if (_0 === "infinitive") {
					//values[_0][1] = [""];
					values[_0][2] = path.iterate("tense");
					values[_0][3] = path.iterate("voice");
					values[_0][4] = hspan4;
				} else if (_0 === "supine" ||
				           _0 === "gerund") {
					let i = values[0].indexOf(_0);
					if (hacked !== null) {
						if (i > -1) values[0].splice(i,1);
						values[3][hacked].push(_0);
						continue;
					}
					var hacked = "";
					if (i > -1) values[0][i] = hacked;

					values[_0][1] = [""];
					values[_0][2] = model.Path({word:w},"gerund").iterate("case");
					if (!values[_0][2])
						values[_0][2] = model.Path({word:w}, "supine").iterate("case");
					values[_0][3] = [_0];
					values[_0][4] = hspan4;

					_0 = hacked;
				}
			}
		} else if (spart === "adverb") {
			values[1] = path.iterate("degree");
		}






	} else
	if (lang === "fr") {
		if ((spart === "noun") ||
			(spart === "adjective") ||
			(spart === "pronoun")) {
			if (spart === "adjective")
				values[1] = path.iterate("degree");
			else values[1] = [];
			values[3] = path.iterate("number");
			values[4] = path.iterate("gender");
			values[0] = [];
		} else if (spart === "verb") {
			var moods = path.iterate("mood");
			values[0] = moods;
			values[1] = [];
			values[2] = [];
			values[3] = [];
			values[4] = [];
			for (let _0 of moods) {
				if (ignore !== null && in_array(_0,ignore))
					continue;
				values[_0] = [values[0]];
				values[_0].length = values.length;
				var path = model.Path({word:w},_0);
				if (_0 === "indicative" ||
					_0 === "subjunctive") {
					if (!values[_0][1]) values[_0][1] = [false];
					values[_0][2] = path.iterate("tense");
					values[_0][3] = path.iterate("number");
					values[_0][4] = path.iterate("person");
				} else if (_0 === "infinitive" ||
				           _0 === "gerund") {
					values[_0][1] = [""];
					values[_0][2] = [""];
					values[_0][3] = path.iterate("type");
					values[_0][4] = [false,false,false];
				} else if (_0 === "imperative") {
					values[_0][1] = [""];
					values[_0][2] = [""];
					values[_0][3] = path.iterate("number");
					values[_0][4] = path.iterate("person");
				} else if (_0 === "participle") {
					values[_0][1] = [""];
					values[_0][2] = path.iterate("gender");
					values[_0][3] = path.iterate("tense");
					values[_0][4] = path.iterate("number");
				}/**/
				values[1][_0] = values[_0][1];
				values[2][_0] = values[_0][2];
				values[3][_0] = values[_0][3];
				values[4][_0] = values[_0][4];
			}
		} else if (spart === "adverb") {
			values[1] = path.iterate("degree");
		}
	} else
	if (lang === "es") {
		if ((spart === "noun") ||
			(spart === "adjective") ||
			(spart === "pronoun")) {
			if (spart === "adjective")
				values[1] = path.iterate("degree");
			else values[1] = [];
			values[3] = path.iterate("number");
			values[4] = path.iterate("gender");
			values[0] = [];
		} else if (spart === "verb") {
			var moods = path.iterate("mood");
			values[0] = [];
			for (let _0 of moods) {
				if (ignore !== null && in_array(_0,ignore))
					continue;
				var path = model.Path({word:w},_0);
				if (_0 === "indicative" ||
					_0 === "subjunctive") {
					if (!values[1]) values[1] = [false];
					values[2] = path.iterate("tense");
					values[3] = path.iterate("number");
					values[4] = path.iterate("person");
				} else if (_0 === "infinitive" ||
				           _0 === "gerund") {
					values[1] = [""];
					values[2] = [""];
					values[3] = [""];
					values[4] = [false,false,false];
				} else if (_0 === "imperative") {
					values[1] = [""];
					values[2] = path.iterate("imperative-mood");
					values[3] = path.iterate("number");
					values[4] = path.iterate("person");
				} else if (_0 === "past-participle") {
					values[1] = [""];
					values[2] = path.iterate("gender");
					values[3] = path.iterate("number");
					values[4] = [false,false,false];
				}
				values[0][_0] = [values[1],values[2],values[3],values[4]];
			}
		} else if (spart === "adverb") {
			values[1] = path.iterate("degree");
		}
	} else
	if (lang === "eo") {
		if ((spart === "noun") ||
			(spart === "adjective") ||
			(spart === "pronoun")) {
			values[0] = [];
			values[1] = [];
			values[2] = path.iterate("case");
			values[3] = path.iterate("number");
			values[4] = [];
		} else if (spart === "verb") {
			var moods = path.iterate("mood");
			values[0] = [];
			var hacked = null;
			for (let _0 of moods) {
				if (ignore !== null && in_array(_0,ignore))
					continue;
				var path = model.Path({word:w},_0);
				var name = null;
				if (_0 === "indicative") {
					values[1] = [""];
					values[2] = [""];
					values[3] = path.iterate("tense");
					values[4] = [false,false];
				} else if (_0 === "infinitive" ||
				           _0 === "conditional" ||
				           _0 === "imperative") {
					if (hacked) {
						values[0][hacked][2].push(_0);
						continue;
					}
					var hacked = _0;
					var name = "";
					values[1] = [""];
					values[2] = [""];
					values[3] = [_0];
					values[4] = [false,false];
				} else if (_0 === "adjectival-participle") {
					values[1] = path.iterate("voice");
					values[2] = path.iterate("case");
					values[3] = path.iterate("tense");
					values[4] = path.iterate("number");
				} else if (_0 === "nominal-participle") {
					values[1] = path.iterate("voice");
					values[2] = path.iterate("case");
					values[3] = path.iterate("tense");
					values[4] = path.iterate("number");
				} else if (_0 === "adverbial-participle") {
					values[1] = path.iterate("voice");
					values[2] = [""];
					values[3] = path.iterate("tense");
					values[4] = [false,false];
				}
				values[0][_0] = [values[1],values[2],values[3],values[4]];
				if (name !== null)
					values[0][_0].push(name);
			}
		}
	} else
	if (lang === "ith" && spart === "root") {
		values[0] = path.iterate("complement");
		values[3] = path.iterate("formality");
		values[2] = path.iterate("stem");
	}
	console.log(values);
	// values[0] : table name
	// values[1] : major vertical
	// values[2] : minor vertical
	// values[3] : major horizontal
	// values[4] : minor horizontal
	// //1,2,3,4 may depend on //0 (already done)
	// //2       may depend on //1
	// //4       may depend on //3
	values[0] = _do_ignore(values[0],ignore);
	if (!values[0]) values[0] = [false];
	if (Array.isArray(values[1])) values[1] = _fill(values[1], values[0]);
	if (Array.isArray(values[2])) values[2] = _fill(values[2], values[0]);
	if (Array.isArray(values[3])) values[3] = _fill(values[3], values[0]);
	if (Array.isArray(values[4])) values[4] = _fill(values[4], values[0]);
	_filter_ignore2(values[1],ignore,model.Path({word:w}),values[0]);
	_filter_ignore2(values[2],ignore,model.Path({word:w}),values[0],values[1]);
	_filter_ignore2(values[3],ignore,model.Path({word:w}),values[0]);
	_filter_ignore2(values[4],ignore,model.Path({word:w}),values[0],values[3]);
	/*var_dump(values[0]);
	var_dump(values[1]);
	var_dump(values[2]);
	var_dump(values[3]);
	var_dump(values[4]);*/
	return values;
}

(function(view) {
	var createClass = function(c) {
		var r = React.createClass(c);
		r.h = h.bind(undefined, r);
		return r;
	};
	var reduconcat = (a,b)=>a.concat(b);
	function is_element(el, ...types) {
		return React.isValidElement(el) && (!types || types.includes(el.type));
	}

	view.Scrollable = createClass({
		displayName: 'view.Scrollable',
		render: function() {
			return h('div', {...this.props, className:'scrollable'}, this.children);
		},
	});

	view.table = {
		th: {
			_notProps: true,
		},
		td: {
			_notProps: true,
		},
		tr: {
			_notProps: true,
			_wrap: function(fn) {
				return function() {
					return h('tr', {}, fn.apply(this, arguments));
				}
			},
		},
	};
	if (!view.format_value) view.format_value = function(...arg) {
		//console.log('value', ...arg);
		return h('span', arg);
	};
	if (!view.format_word) view.format_word = function(...arg) {
		if (arg.length === 1 && arg[0] && 'value' in arg[0]) {
			//console.log('word/path', arg[0].tag, arg[0].value);
			return h('span', arg[0].value);
		}
		console.log('word', ...arg);
		return h('span', arg);
	};
	var {th,td,tr} = view.table;
	var nbsp = '\u00A0';
	th.blank = function(props={}) {
		return h('th', props, nbsp);
	};
	th.space = function(props={}) {
		return h('th', props, nbsp+nbsp+nbsp);
	};
	th.format_value = function(...arg) {
		if (this && !this['_notProps']) var props = Object.assign({}, this);
		return h('th', props, view.format_value(...arg));
	};
	th.format_value.minor = function(...arg) {
		var props = {
			className:'minor',
			style: {
				fontWeight: 'normal',
				textAlign: 'left',
			}
		};
		if (this && !this['_notProps'])
			props = Object.assign(props, this);
		return th.format_value.call(props, ...arg);
	};
	th.format_value.major = function(...arg) {
		var props = {
			className:'major',
			style: {
				fontWeight: 'bold',
				textAlign: 'left',
			}
		};
		if (this && !this['_notProps'])
			props = Object.assign(props, this);
		return th.format_value.call(props, ...arg);
	};
	th.format_value.greatest = function(...arg) {
		var props = {
			className:'greatest',
			style: {
				fontWeight: 'bold',
				textAlign: 'left',
			}
		};
		if (this && !this['_notProps'])
			props = Object.assign(props, this);
		return th.format_value.call(props, ...arg);
	};
	td.format_word = function(...arg) {
		if (this && !this['_notProps'])
			var props = Object.assign({}, this);
		return h('td', props, view.format_word(...arg));
	};
	tr.blank = tr._wrap(th.blank);
	tr.format_value = tr._wrap(th.format_value);
	tr.format_value.major = tr._wrap(th.format_value.major);
	tr.format_value.minor = tr._wrap(th.format_value.minor);
	tr.format_word = tr._wrap(td.format_word);
	tr.H = function({w, ignore, values}) {
		return values[3].map(
			v => th.format_value.major.call({
				colSpan: (v in values ? values[v] : values)[4].length
			}, v)
		);
	};
	tr.h = function({ignore, values}) {
		return values[3].map(
			v => (v in values ? values[v] : values)[4].map(
				c => th.format_value.minor(c)
			)
		).reduce(reduconcat);
	};
	function getgroups({p:basepath, optimization, ignore, values}) {
		var getv = (v,j) => v in values ? values[v][j] : values[j];
		var groups = [], last;
		for (let _3 of values[3]) {
			for (let _4 of getv(_3,4)) {
				let p = model.Path(basepath).add2(_3, _4);
				let g = {p, _3, _4};
				if (last && !_in_ignore(p, ignore) && optimization & 2 && p.value === last) {
					groups[groups.length-1].push(g);
				} else {
					groups.push([g]);
					last = p.value;
				}
			}
		}
		return groups;
	};
	tr.body = function({p:basepath, optimization, ignore, i, values, gutter, rows=[]}) {
		let prevv;
		for (let v of values[2]) {
			let path = model.Path(basepath).add(v);
			let row = [];
			if (gutter > 1) {};row.push(th.space());
			row.push(th.format_value.minor(v));
			let getv = j => v in values ? values[v][j] : values[j];
			let groups = getgroups({p:path, optimization, ignore, values:values.slice(0,3).concat([3,4].map(getv))});
			//console.log(path.toString(), groups, groups.map(g=>g[0].p.map));
			for (let group of groups) {
				let ditto, _ = group.length-1, p = group[0].p;
				let {_3:_30, _4:_40} = group[0];
				let {_3:_3_, _4:_4_} = group[_];
				let {_3,_4} = group[0]; if (_) _3 = _4 = undefined;
				if (!_) ditto = (prevv && p.value && p.value === model.Path(p, prevv).value);
				let val;
				if (_in_ignore(p, ignore) && p.hasvalue())
					val = h('abbr', {className:'symbolic', title:"Not learned yet"}, '—');
				else val = view.format_word(p);
				let link;
				//if (getlink) link = getlink(p);
				if (link) val = view.make_link(link, val);
				if (_) val = h('span', [view.arrow.float.left, view.arrow.float.right, val]);
				else if (ditto && optimization & 1) val = nbsp+'\u2044'+nbsp+'\u2044';
				row.push(h('td', {key:p.toString()}, val));
				//if (extras) row.push(...extras(p));
			}
			rows.push(row);
			prevv = v;
		}
		return rows;
	};
	tr.bodysection = function({p:basepath, optimization, ignore, i, values, gutter, rows=[]}) {
		var p = model.Path(basepath);
		if (values[1]) {
			for (let v of values[1]) {
				rows.push(tr.format_value.major.call({
					colSpan: 2,
				}, v));
				if (v) p.add(v);
				let getv = j => v in values ? values[v][j] : values[j];
				let vals = values.slice(0,2).concat([2,3,4].map(getv));
				rows = tr.body({p, optimization, ignore, values:vals, gutter, rows});
			}
			return rows;
		}
		return tr.body({p, optimization, ignore, values, gutter, rows});
	};
	tr.subtable = function({p, optimization, ignore, i, values, gutter, heading, rows=[]}) {
		if (!heading)
			heading = th.blank({colSpan:gutter});
		else if (!is_element(heading, 'th', 'td'))
			heading = th.format_value.greatest.call({colSpan:gutter}, heading);
		rows.push([heading, ...tr.H({p,ignore,values})]);
		heading = th.blank({colSpan:gutter});
		if (values[4] && values[4].some(Boolean))
			rows.push([heading, ...tr.h({p,ignore,values})]);
		return tr.bodysection({p, optimization, ignore, values, gutter, rows});
	};

	view.do_table = function(w, values, ignore, optimization=0) {
		// values.every((v,i)=>(!v||!v.length)!==(i===1));
		if (values[1] &&
		   !values[2] &&
		   !values[3] &&
		   !values[4] &&
		   !values[0]) {
			return values[1].map(_1 => [
				tr.format_value(_1),
				tr.format_word(w.path(_1), w.lang, true)
			]).reduce(reduconcat);
		}
		var path = model.Path({word:w});
		var gutter = 1;
		if (values[0]) {
			var getv = (v,j) => v in values ? values[v][j] : values[j];
			var gutter = 1+values[0].some(v=>getv(v,1)&&getv(v,1).length);
			return values[0].map((v,i) => [
				...(i?[tr.blank({key:'blank'+i})]:[]),
				...tr.subtable({
					heading: v,
					p: path.reset().add(v),
					values: [
						values[0],
						getv(v,1),
						getv(v,2),
						getv(v,3),
						getv(v,4),
					], gutter,
					ignore, i,
				})
			]).reduce(reduconcat);
		}
		return tr.subtable({
			p: path, values, gutter,
			optimization, ignore,
		});
	};
	view.render = function() {
		var w = pantheum.view.word;
		w.pull().then(function() {
			var values = word_table_values(w);
			ReactDOM.render(pantheum.view.create_table(pantheum.view.do_table(w, values)), document.getElementById('inflection2'));
		});
	};
})(pantheum.view);


window.word_table_values = word_table_values;


