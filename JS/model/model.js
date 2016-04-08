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
				if (Array.isArray(v)) {
					this.simple_keys.push(k);
					register2(this,k,v);
				} else {
					this.recursive_keys.push(k);
					for (let _ in v) {
						let v_ = v[_];
						register(this,k,_);
						if (!(v_ instanceof Depath))
							v[_] = v_ = new Depath(null, v_, aliases);
						this.all_sub_keys.push(...v_.all_sub_keys);
						register3(this, v_.key2values);
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
			for (let k in this.all_sub_keys)
				if (this.all_sub_keys[k] == key) return k;
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
	var methods = {
		// Accessors
		set mgr(m) {
			this._mgr = m;
			this.map.length = m.all_sub_keys.length;
		},
		get mgr() {
			return this._mgr;
		},
		set word(w) {
			this._word = w;
			this.mgr = w.mgr;
			this.add2(w.df_path_values);
		},
		get word() {
			return this._word;
		},
		// Count how many keys are used with values
		get keylength() {
			return this.map && this.map.filter(Boolean).length;
		},
		// API
		resolve_hash(hash) {
			if ("path_storage" in hash) return hash.path_storage;
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
				if (!a) {}
				else if (typeof a === 'string')
					a.trim().split("/").filter(Boolean).map(b=>this.add(b))
				else if (Array.isArray(a))
					if (a.length === 2 && typeof a[0] === 'string' && this.mgr.is_key(a[0]))
						this.add(a);
					else this.add2(...a);
				else throw new TypeError("Path.add2 requires a string or vector");
			}
			return this;
		},
		addp(basepath) {
			for (var k of this.mgr.all_sub_keys) {
				if (!this.key_exists(k))
					this.add(basepath.key_value(k));
			}
			this._map_dirty = true;
			return this;
		},
		take(key) {
			this._map_dirty = true;
			this.map[this.mgr.key_index(key)] = null;
			return this;
		},
		take2(...arg) {
			for (var a of vec) {
				if (!a) {}
				else if (typeof a === 'string')
					this.take(a);
				else if (Array.isArray(a))
					if (a.length === 2 && this.mgr.is_key(a[0])
						&& this.mgr.is_value(a[1]))
						this.take(a);
					else this.take2(...arg);
				else throw new TypeError("Path.take2 requires a string or vector");
			}
			return this;
		},
		toString() {
			return this.map.filter(Boolean).join("/");
		},
		reset() {
			for (var i of Object.keys(this.path))
				this.path[i] = null;
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
			for (var p of this.map) {
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
			for (var p of this.map) {
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
		_calculate_valid_values() {
			if (!this._map_dirty) return this._valid_values;
			var ret = {};
			var recurse = dp => {
				if (!dp) return ret;
				for (var k of dp.simple_keys) {
					if (ret[k]) throw new Error("duplicate key");
					ret[k] = dp.level[k];
				}
				for (var k of dp.recursive_keys) {
					if (ret[k]) throw new Error("duplicate key");
					ret[k] = Object.keys(dp.level[k]);
					var i = dp.key_index(k);
					var v = this.map[i];
					if (v)
						recurse(dp.level[k][v]);
				}
				return ret;
			};
			recurse(this.mgr);
			this._valid_values = ret;
			this._map_dirty = false;
			return ret;
		},
		valid(msg) {
			var dp = this.mgr;
			var vals = this._calculate_valid_values();
			for (let k of Object.keys(vals)) {
				var i = dp.key_index(k);
				var vs = vals[k];
				var v = this.map[i];
				if (v == null) continue;
				if (!vs.includes(v)) return msg ? "value '"+v+"' of key '"+k+"' was not in set "+vs : false;
			}
			return msg ? null : true;
		},
		validate() {
			var msg = this.valid(true);
			if (msg) throw new Error("invalid path: "+msg);
		},
		set(val, hash) {
			this._map_dirty = true;
			var h = this.walk(hash,1);
			return h[""] = val;
		},
		get(hash) {
			var h = this.walk(hash,0);
			if (h == null || !("" in h)) return null;
			return h[""];
		},
		exists(hash) {
			var h = this.walk(hash,0);
			return h != null;
		},
		iterate(k, hash) {
			var h = this.resolve_hash(hash);
			var vals = this._calculate_valid_values();
			if (h != null)
				return vals[k].filter(function(i) {
					return array_key_exists_r(i, h);
				});
			return vals[k];
		},
		issub(other,ret) {
			for (var k of this.mgr.all_sub_keys) {
				if (other.key_value(k) && this.key_value(k) != other.key_value(k)) {
					return false;
				} else if (this.key_value(k) && !other.key_value(k)) {
					ret = true;
				}
			}
			return !!ret;
		},
		hasvalue(hash) { return this.get(hash) != null; },
		remove(hash) {
			if (this.issql && this._id != null)
				sql_exec(sql_stmt("form_id->delete from forms"), ["i", this._id]);
			var hash = this.resolve_hash(hash);
			var h = this.walk(hash,0);
			if (h == null) return h;
			this._map_dirty = true;
			var ret = h[""];
			delete h[""];
			// Clean up dead branches if we have killed them:
			while (!h.length && this.keylength) {
				var max = this.keylength-1;
				var [h2,i,k] = this.walk_part(hash, max);
				delete h2[k];
				h = h2;
			}
			return ret;
		},
		remove_all(hash) {
			var hash = this.resolve_hash(hash);
			for (var k of Object.keys(hash))
				delete hash[k];
			return this;
		},
		move(wen, hash) {return wen.set(this.remove(hash), hash);},
		values(key) {
			return this._calculate_valid_values()[key];
		},
	};
	var Path = stampit({
		methods: methods,
		props: {
			_map_dirty: true,
			map: []
		},
		init: function initPath({instance, args, stamp}) {
			// Copy accessors to the instance
			for (let n of ["word","mgr","keylength"]) {
				let v = instance[n];
				Object.defineProperty(instance, n, Object.getOwnPropertyDescriptor(methods, n));
				if (v) instance[n] = v;
			}
			instance.add2(args);
		}
	});
	model.Path = Path;
}(model));

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var methods = {
		// Required API
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
		fromData(data, overwrite) {
			for (let d of [...columns, "id", "word", "tag"])
				if (overwrite || data[d] != null)
					this[d] = data[d];
			return this;
		},
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this[d] = row[prefix+d];
			if (row.word_id != null)
				this.word_id = row.word_id;
			if (row.form_tag != null)
				this.form_tag = row.form_tag;
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
		// Member access
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Definition id must be integer or null/undefined");
			this._id = id;
		},
		set word(word) {
			this._word = word && (model.Word ? model.Word(word, !!this.cacheable) : word);
		},
		set tag(tag) {
			this._tag = tag && (model.Path ? model.Path(Object.assign({word:this.word}, tag)) : tag);
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
		}
	});
	model.Definition = common.stamp(Definition);
}(model));

(function(model) {
	"use strict";
	var prefix = "word_";
	var columns = ["spart","name","lang","last_changed"];
	var methods = {
		// Required API
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
		fromData(data, overwrite) {
			for (let d of [...columns, "id"])
				if (overwrite || data[d] != null)
					this[d] = data[d];
			for (let cls of this.references)
				if (data[cls.table])
					this[cls.table] = data[cls.table].map(d => cls(d, !!this.cacheable));
			return this;
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
		// Member access
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Word id must be integer or null/undefined");
			this._id = id;
		},
		get path() {
			if (this._path == null)
				this._path = model.Path({word:this});
			return this._path;
		},
		get mgr() {
			if (this._mgr == null && model.Depath)
				this._mgr = model.Depath.of(this);
			return this._mgr;
		},
		// Extra methods
		has_attr(attr) {
			if (attr.value() == null)
				return !!attr.get(this);
			else return attr.get(this) == attr.value();
		}
	};
	var statics = {
		table: "words",
		key: "word_id",
		references: [model.Definition],
		columns
	};
	var Word = stampit({
		methods: common.methods(methods),
		refs: statics,
		static: statics,
		init: function initWord({instance, args: [cacheable], stamp}) {
			// Tell cacheable stamp to cache this or not
			if (cacheable !== undefined)
				instance.cacheable = cacheable;

			// Copy accessors to the instance, create missing accessors
			common.defprops(instance, [...columns, "id", "path", "mgr"], methods);
		}
	});

	model.Word = common.stamp(Word);
}(model));

module.exports = model;

