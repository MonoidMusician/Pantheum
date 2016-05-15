
module.exports = function(model) {
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
			this.length = 1;
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
				let len = 0;
				if (Array.isArray(v)) {
					this.simple_keys.push(k);
					register2(this,k,v);
					len = v.length;
				} else {
					// Avoid mutating init object!
					let os = v;
					v = {};
					this.recursive_keys.push(k);
					for (let i in os) {
						let o = os[i];
						register(this,k,i);
						if (!(o instanceof Depath)) {
							o = new Depath(name+'/'+k, o, aliases);
						}
						this.all_sub_keys.push(...o.all_sub_keys);
						register3(this, o.key2values);
						Object.assign(this.level, o.level);
						v[i] = o;
						len += o.length;
					}
				}
				this.level[k] = v;
				this.length *= len+1;
			}
			// Sort && remove duplicates
			this.own_keys = this.recursive_keys.concat(this.simple_keys).filter(_unique);
			var simple = this.all_sub_keys.filter(k=>Array.isArray(this.level[k]));
			var recursive = this.all_sub_keys.filter(k=>!Array.isArray(this.level[k]));
			this.all_sub_keys = recursive.concat(simple).filter(_unique);
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
};
