var stampit = require('stampit');
var common = require('./common');

var model = {};

(function(model) {
	"use strict";
	var _unique = (val,idx,self) => self.indexOf(val) === idx;
	function _subscript(obj, k) {
		if (typeof obj === 'function')
			return obj(k);
		return obj[k];
	}
	// Three helpers for initializing a depath+
	function register(hic, k, v) {
		if (typeof k !== 'string' || typeof v !== 'string') {
			throw new TypeError("register: key and value must be string");
		}
		if (v in hic.key2values)
			throw new Error("value '"+v+"' is already present as a key");
		else if (k in hic.value2key)
			throw new Error("key '"+k+"' is already present as a value");
		else if (!(k in hic.key2values))
			hic.key2values[k] = [];
		hic.key2values[k].push(v);
		hic.all_sub_keys.push(k);
		if (v in hic.value2key && hic.value2key[v] != k)
			throw new Error("value '$v' already added with a different key");
		hic.value2key[v] = k;
	}
	function register2(hic, k, vec) {
		return vec.map(register.bind(null, hic, k));
		for (let _ of vec) register(hic, k, _);
	}
	function register3(hic, hash) {
		for (let k in hash)
			register2(hic, k, hash[k]);
	}
	/**
	 * Depath "manager" for linking specific forms of a word.
	 */
	class Depath {
		constructor(name, init, aliases) {
			this.level = {};
			this.key2values = {};
			this.value2key = {};
			this.all_sub_keys = [];
			this.simple_keys = [];
			this.recursive_keys = [];
			this.name = name;
			if (aliases == undefined) aliases = [];
			this.aliases = aliases;
			if (init != undefined)
			for (let k in init) {
				let v = init[k];
				let copied = false;
				if (Array.isArray(v)) {
					this.simple_keys.push(k);
					register2(this,k,v);
				} else {
					// Avoid mutating init object!
					let os = v;
					v = {};
					this.recursive_keys.push(k);
					for (let i in os) {
						let o = os[i];
						register(this,k,i);
						if (!(o instanceof Depath)) {
							o = new Depath(null, o, aliases);
						}
						this.all_sub_keys.push(...o.all_sub_keys);
						register3(this, o.key2values);
						v[i] = o;
					}
				}
				this.level[k] = v;
			}
			// Sort && remove duplicates
			this.all_sub_keys = this.all_sub_keys.filter(_unique);
			for (var k of Object.keys(this.key2values))
				this.key2values[k] = this.key2values[k].filter(_unique);
		}
		is_key(key) {
			return key in this.key2values;
		}
		find_key(val) { return this.value2key[val]; }
		key_index(key) {
			var k = this.all_sub_keys.indexOf(key);
			if (k !== -1) return k;
			throw new Error("key '"+key+"' not found");
		}
		is_value(value) {
			return value in this.value2key;
		}
		value_index(value) {
			var key = this.find_key(value);
			if (key != null) {
				var v = this.key2values[key].indexOf(value);
				if (v >= 0) return v;
			}
			throw new Error("value '"+value+"' not found");
		}
		resolve_alias(value, key) {
			// Maybe it is a value already?
			if (value in this.value2key) return value;
			var ret = key ? _subscript(_subscript(this.aliases, key), value) : null;
			if (typeof ret !== 'string')
				ret = _subscript(this.aliases, value);
			if (typeof ret !== 'string')
				throw new Error("value '"+value+"' has no alias for key "+(key==null?'nil':"'"+key+"'")+" depath name "+this.name);
			return ret;
		}
		add_alias(alias, value, key) {
			if (!(value in this.value2key))
				throw new Error("bad value '"+value+"'");
			if (key != null)
				this.aliases[key][alias] = value;
			else this.aliases[alias] = value;
		}
	}
	Depath.depaths = {};
	Depath.of = function(word) {
		return this.for(word.lang, word.spart);
	};
	Depath.for = function(lang, spart) {
		return this.depaths[lang] && this.depaths[lang][spart];
	};
	Depath.add = function(lang, spart, depath) {
		if (!this.depaths[lang]) this.depaths[lang] = {};
		return this.depaths[lang][spart] = depath;
	};
	model.Depath = Depath;
}(model));

(function(model) {
	"use strict";
	var FLAT_STORAGE = true;
	var SEP = '/';
	var prefix = "form_";
	var columns = ["tag", "value", "usage"];
	var methods = {
		// Accessors
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Definition id must be integer or null/undefined");
			this._id = id;
		},
		get id() {
			return this._id;
		},
		set mgr(mgr) {
			this._mgr = mgr;
			if (this._mgr)
				this.map.length = mgr.all_sub_keys.length;
		},
		get mgr() {
			if (!this._mgr && this.word)
				this.mgr = this.word.mgr;
			return this._mgr;
		},
		set word(word) {
			this._word = common.construct(model.Word, word, !!this.cacheable);
			this.mgr = this.word.mgr;
			this.add2(this.word.df_path_values);
		},
		get word() {
			return this._word;
		},
		// Special SQL pseudo-member access
		get value() {
			return this.valid() ? this.get() : undefined;
		},
		set value(value) {
			this.validate().set(value);
		},
		get tag() {
			if (!this.map) return;
			return this.toString();
		},
		set tag(tag) {
			this.reset().add2(tag);
		},
		set word_id(word_id) {
			this.word = {id:word_id}; // fall through to word setter
		},
		get word_id() {
			return this.word && this.word.id;
		},
		// Required API
		fromData(data, overwrite) {
			for (let d of [...columns, "id"])
				if (overwrite || data[d] != null)
					this[d] = data[d];
			return this;
		},
		toData(visited) {
			var data = {};
			for (let d of [...columns, "id"])
				if (this[d] != null)
					data[d] = this[d];

			// Serialize recursive structures
			if (!visited) visited = [];
			visited.push([this, data]);
			data.word = this.word && (this.word.toData ? common.visit(visited, this.word, this.word.toData) : this.word);
			return data;
		},
		fromSQL(row) {
			if (row.word_id != null) {
				this.word_id = row.word_id;
				if (!this.mgr) {
					var row2 = Object.assign({}, row, {word_id:undefined});
					return this.word.pull().then(word=> {
						return this.fromSQL(row2);
					});
				}
			}
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this[d] = row[prefix+d];
			return this;
		},
		toSQL() {
			var row = {};
			if (this.word_id !== undefined)
				row.word_id = this.word_id;
			for (let d of columns)
				if (this[d] !== undefined)
					row[prefix+d] = this[d];
			return row;
		},
		// Count how many keys are used with values
		get keylength() {
			return this.map && this.map.filter(Boolean).length;
		},
		// API
		resolve_hash(hash) {
			if (hash && "path_storage" in hash) return hash.path_storage;
			if (!hash && this.word) {
				if (this.word.path_storage)
					return this.word.path_storage;
			}
			return hash;
		},
		resolve_key_value(arg) {
			var key = null; var value = null;
			if (arg.length === 1) {
				value = this.mgr.resolve_alias(arg[0]);
				if (value != null)
					key = this.mgr.find_key(value);
			} else if (arg.length === 2) {
				key = arg[0]; value = arg[1];
				value = this.mgr.resolve_alias(value, key);
				// In case key was "" or to catch incorrect value:
				key = this.mgr.find_key(value);
			}
			return [key,value];
		},
		add(...arg) {
			this._map_dirty = true;
			if (arg.length === 1 && Array.isArray(arg[0])) arg = arg[0];
			var [key, value] = this.resolve_key_value(arg);
			var key_index = this.mgr.key_index(key);
			this.map[key_index] = value;
			return this;
		},
		add2(...arg) {
			for (var a of arg) {
				if (typeof a === 'function') var a = a();
				if (a && !Array.isArray(a) && a.toString)
					a = a.toString();
				if (!a) continue;
				if (typeof a === 'string')
					a.trim().split(SEP).filter(Boolean).map(b=>this.add(b));
				else if (Array.isArray(a))
					if (a.length === 2 && typeof a[0] === 'string' && this.mgr.is_key(a[0]))
						this.add(a);
					else this.add2(...a);
				else throw new TypeError("Path.add2 requires a string or vector");
			}
			return this;
		},
		addp(basepath, overwrite) {
			for (var k of this.mgr.all_sub_keys) {
				if (!basepath.key_exists(k)) continue;
				if (overwrite || !this.key_exists(k)) {
					this.add(basepath.key_value(k));
					this._map_dirty = true;
				}
			}
			return this;
		},
		addifvalid(...arg) {
			if (arg.length === 1 && Array.isArray(arg[0])) arg = arg[0];
			var [key, value] = this.resolve_key_value(arg);
			var key_index = this.mgr.key_index(key);
			var valids = this.values(key);

			if (valids && valids.includes(value)) {
				var prev = this.map[key_index];
				if (prev == value) return this;
				this.map[key_index] = value;
				this._map_dirty = true;

				// Test if change is OK
				if (key in this.mgr.simple_keys || this.valid())
					return this;

				// Revert change
				this._map_dirty = true;
				this.map[key_index] = prev;
			}
			return false;
		},
		take(key) {
			this._map_dirty = true;
			this.map[this.mgr.key_index(key)] = null;
			return this;
		},
		take2(...arg) {
			for (var a of arg) {
				if (!a) continue;
				if (typeof a === 'string')
					this.take(a);
				else if (Array.isArray(a))
					if (a.length === 2 && this.mgr.is_key(a[0]) && this.mgr.is_value(a[1]))
						this.take(a);
					else this.take2(...a);
				else throw new TypeError("Path.take2 requires a string or vector");
			}
			return this;
		},
		toString() {
			return this.map.filter(Boolean).join(SEP);
		},
		reset() {
			for (var i in this.map)
				this.map[i] = null;
			return this;
		},
		key_exists(key) {
			return !!this.map[this.mgr.key_index(key)];
		},
		key_value(key) {
			return this.map[this.mgr.key_index(key)];
		},
		walk(hash, create) {
			this.validate();
			var hash = this.resolve_hash(hash);
			for (let p of this.map) {
				if (!hash) break;
				if (!p) continue;
				if (create && !hash[p]) hash[p] = {};
				hash = hash[p];
			}
			return hash;
		},
		walk_part(hash, max, min, create) {
			if (!max) max=0; if (!min) min=0;
			this.validate();
			var hash = this.resolve_hash(hash);
			var i = 0;
			for (let p of this.map) {
				if (min) {min -= 1;continue;}
				if (!hash) return hash;
				i += 1;
				if (!p) continue;
				if (create && !hash[p]) hash[p] = {};
				hash = hash[p];
				if (!(max -= 1)) break;
			}
			return [hash,i,this.map[i]];
		},
		walk_all(hash) {
			this.validate();
			var hash = this.resolve_hash(hash);
			var res = [hash];
			var found = {};
			for (let i in this.map) {
				let p = this.map[i];
				if (!res.length) break;
				found[p] = true;
				if (p) res = res.map(h => h[p]).filter(Boolean);
				else {
					let wen = [];
					let k = this.mgr.all_sub_keys[i];
					let vals = ["", ...this.values(k)];
					for (let h of res) {
						wen.push(...vals.map(v => h[v])).filter(Boolean);
					}
					res = wen;
				}
			}
			res.push(found);
			return res;
		},
		values(key) {
			if (!this._map_dirty) return key ? this._valid_values[key] : this._valid_values;
			var ret = {};
			var recurse = dp => {
				if (!dp) return false;
				for (var k of dp.simple_keys) {
					if (ret[k]) throw new Error("duplicate key");
					ret[k] = dp.level[k];
					if (k === key) return true;
				}
				for (var k of dp.recursive_keys) {
					if (ret[k]) throw new Error("duplicate key");
					ret[k] = Object.keys(dp.level[k]);
					var i = dp.key_index(k);
					var v = this.map[i];
					if (v && recurse(dp.level[k][v])) return true;
				}
				return false;
			};
			recurse(this.mgr);
			if (!key) {
				this._valid_values = ret;
				this._map_dirty = false;
			}
			return key ? ret[key] : ret;
		},
		valid(msg) {
			if (!this.mgr || !this.map) return msg ? "missing field(s)" : false;
			var dp = this.mgr;
			var vals = this.values();
			for (let k of this.mgr.all_sub_keys) {
				var i = dp.key_index(k);
				var vs = vals[k];
				var v = this.map[i];
				if (v == null) continue;
				if (!vs || !vs.includes(v))
					return msg ? "value '"+v+"' of key '"+k+"' was not in set "+vs : false;
			}
			return msg ? null : true;
		},
		validate() {
			var msg = this.valid(true);
			if (msg) throw new Error("invalid path: "+msg);
			return this;
		},
		set(val, hash) {
			this._map_dirty = true;
			if(FLAT_STORAGE) {
				var h = this.resolve_hash(hash);
				return h[this.toString()] = val;
			}
			var h = this.walk(hash,1);
			return h[""] = val;
		},
		get(hash) {
			if(FLAT_STORAGE) {
				var h = this.resolve_hash(hash);
				return h[this.toString()];
			}
			var h = this.walk(hash,0);
			if (h == null || !("" in h)) return;
			return h[""];
		},
		exists(hash) {
			if(FLAT_STORAGE) {
				var h = this.resolve_hash(hash);
				return h && this.toString() in h;
			}
			var h = this.walk(hash,0);
			return h != null && "" in h;
		},
		iterate(k, hash) {
			var h = this.resolve_hash(hash);
			var valids = this.key_exists(k) ? [this.key_value(k)] : this.values(k);
			if (h == null || !valids || (this.exists(h) && this.key_exists(k))) return valids;
			if(FLAT_STORAGE) {
				// Cache a path object to use while comparing
				var p = Path({mgr:this.mgr});
				return valids.filter(
					i => Object.keys(h).some(
						j => p.reset().add2(j).map.includes(i) && p.issub(this)
					)
				);
			}
			function array_key_exists_r(key, obj) {
				if (typeof obj !== 'object') return false;
				if (key in obj) return true;
				for (let k in obj)
					if (array_key_exists_r(key, obj[k])) return true;
				return false;
			}
			var all = this.walk_all(h);
			return valids.filter(i => all.some(array_key_exists_r.bind(null, i)));
		},
		issub(other,ret) {
			for (var k of this.mgr.all_sub_keys) {
				if (other.key_value(k) && this.key_value(k) != other.key_value(k))
					return false;
				if (this.key_value(k) && !other.key_value(k))
					ret = true;
			}
			return !!ret;
		},
		hasvalue(hash) { return this.get(hash) != null; },
		remove(hash) {
			var hash = this.resolve_hash(hash);
			if(FLAT_STORAGE) {
				this._map_dirty = true;
				var ret = hash[this.toString()];
				delete hash[this.toString()];
				return ret;
			}
			var h = this.walk(hash,0);
			if (h == null) return h;
			this._map_dirty = true;
			var ret = h[""];
			delete h[""];
			// Clean up dead branches if we have killed them:
			while (!Object.keys(h).length && this.keylength) {
				let max = this.keylength-1;
				let [h2,i,k] = this.walk_part(hash, max);
				delete h2[k];
				h = h2;
			}
			return ret;
		},
		remove_all(hash) {
			var hash = this.resolve_hash(hash);
			for (var k in hash)
				delete hash[k];
			return this;
		},
		move(wen, hash) {return wen.set(this.remove(hash), hash);},
	};
	var statics = {
		table: "forms",
		key: prefix+"id",
		reference: "word",
		columns
	};
	var Path = stampit({
		methods: common.methods(methods),
		refs: statics,
		static: statics,
		props: {
			_map_dirty: true,
			map: []
		},
		init: function initPath({instance, args, stamp}) {
			if (args.length && args[0] === true) {
				instance.cacheable = true;
				args = args.slice(1);
			}

			// Copy accessors to the instance
			common.defprops(instance, ["id", "mgr", "word_id", "word", ...columns], methods);
			// Avoid default setter on this one
			for (let n of ["keylength"]) {
				Object.defineProperty(instance, n, Object.getOwnPropertyDescriptor(methods, n));
			}

			// Special method: add.ifvalid
			instance.add = function add() {
				return methods.add.apply(this, arguments);
			};
			instance.add.ifvalid = instance.addifvalid.bind(instance);

			instance.add2(...args);
		}
	});
	Path = common.stamp(Path);
	Path.normalize = function normalize(mgr, tag) {
		return Path({mgr,tag}).toString();
	};
	Object.defineProperty(Path, 'FLAT_STORAGE', {
		get: function() {return FLAT_STORAGE},
		set: function(v) {FLAT_STORAGE = v},
	});
	model.Path = Path;
	model.Form = Path;
}(model));

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var methods = {
		// Member access
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Definition id must be integer or null/undefined");
			this._id = id;
		},
		set word(word) {
			this._word = common.construct(model.Word, word, !!this.cacheable);
		},
		set tag(tag) {
			this._tag = common.construct(model.Path, Object.assign({word:this.word}, tag));
		},
		// Special SQL pseudo-member access
		set word_id(word_id) {
			this.word = {id:word_id};
		},
		get word_id() {
			return this.word && this.word.id;
		},
		set form_tag(form_tag) {
			this._tag = model.Path ? model.Path({word:this.word}, form_tag) : form_tag;
		},
		get form_tag() {
			return this.tag && this.tag.toString();
		},
		// Required API
		fromData(data, overwrite) {
			for (let d of [...columns, "id", "word", "tag"])
				if (overwrite || data[d] != null)
					this[d] = data[d];
			return this;
		},
		toData(visited) {
			var data = {};
			for (let d of [...columns, "id", "form_tag"])
				if (this[d] != null)
					data[d] = this[d];

			// Serialize recursive structures
			if (!visited) visited = [];
			visited.push([this, data]);
			data.word = this.word && (this.word.toData ? common.visit(visited, this.word, this.word.toData) : this.word);
			return data;
		},
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this[d] = row[prefix+d];
			if (row.word_id != null)
				this.word_id = row.word_id;
			if (row.form_tag != null) {
				if (!this.word.mgr) {
					return this.word.pull().then(
						w => {
							this.form_tag = row.form_tag;
							return this;
						}
					);
				}
				this.form_tag = row.form_tag;
			}
			return this;
		},
		toSQL() {
			var row = {};
			for (let d of columns)
				if (this[d] !== undefined)
					row[prefix+d] = this[d];
			if (this.word_id !== undefined)
				row.word_id = this.word_id;
			if (this.form_tag !== undefined)
				row.form_tag = this.form_tag;
			return row;
		},
	};
	var statics = {
		table: "definitions",
		key: prefix+"id",
		reference: "word",
		columns
	};
	var Definition = stampit({
		methods: common.methods(methods),
		refs: statics,
		static: statics,
		init: function initDefinition({instance, args: [cacheable], stamp}) {
			// Tell cacheable stamp to cache this or not
			if (cacheable !== undefined)
				instance.cacheable = cacheable;

			// Copy accessors to the instance, create missing accessors
			common.defprops(instance, [...columns, "id", "word", "tag", "word_id", "form_tag"], methods);

			var pullall = instance.pullall;
			instance.pullall = function(...arg) {
				return pullall.call(this, ...arg).then(a=>a.word.pull()).then(a=>this);
			};
		}
	});
	model.Definition = common.stamp(Definition);
}(model));

(function(model) {
	"use strict";
	var prefix = "word_";
	var columns = ["spart","name","lang","last_changed"];
	var methods = {
		// Member access
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Word id must be integer or null/undefined");
			this._id = id;
		},
		get mgr() {
			if (!this._mgr && model.Depath) {
				this._mgr = model.Depath.of(this);
			}
			return this._mgr;
		},
		// Required API
		fromData(data, overwrite) {
			for (let d of [...columns, "id"])
				if (overwrite || data[d] != null)
					this[d] = data[d];
			for (let cls of this.references)
				if (data[cls.table])
					this[cls.table] = data[cls.table].map(d => cls(d, !!this.cacheable));
			return this;
		},
		toData(visited) {
			var data = {};
			for (let d of [...columns, "id"])
				if (this[d] != null)
					data[d] = this[d];

			// Serialize recursive structures
			if (!visited) visited = [];
			visited.push([this, data]);
			var children = this.children;
			for (let c in children)
				if (children[c])
					data[c] = children[c].map(d => common.visit(visited, d, d.toData));
			return data;
		},
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this[d] = row[prefix+d];
			return this;
		},
		toSQL() {
			var row = {};
			for (let d of columns)
				if (this[d] !== undefined)
					row[prefix+d] = this[d];
			return row;
		},
		// Display
		toString() {
			return this.id+this.name;
		},
		// Extra methods
		path(tag, value) {
			if (typeof tag === 'string') tag = {tag};
			tag = model.Path(Object.assign({}, tag, {word:this}));
			if (value) tag.value = value;
			return tag.value;
		},
		has_attr(attr) {
			if (attr.value() == null)
				return !!attr.get(this);
			else return attr.get(this) == attr.value();
		},
	};
	var statics = {
		table: "words",
		key: "word_id",
		references: [model.Definition, model.Form],
		columns
	};
	var Word = stampit({
		methods: common.methods(methods),
		refs: statics,
		static: statics,
		props: {
			path_storage: {}
		},
		init: function initWord({instance, args: [cacheable], stamp}) {
			// Tell cacheable stamp to cache this or not
			if (cacheable !== undefined)
				instance.cacheable = cacheable;

			// Copy accessors to the instance, create missing accessors
			common.defprops(instance, [...columns, "id", "mgr"], methods);
		}
	});

	model.Word = common.stamp(Word);
}(model));

module.exports = model;

