(function(model) {
	"use strict";
	var _unique = (val,idx,self) => self.indexOf(val) === idx;
	function _subscript(obj, k) {
		if (Array.isArray(obj)) return obj[k];
		return obj(k);
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
		else if (!k in hic.key2values)
			hic.key2values[k] = [];
		hic.key2values[k].push(v);
		hic.all_sub_keys.push(k);
		if (v in hic.value2key && hic.value2key[v] != k)
			throw new Error("value '$v' already added with a different key");
		hic.value2key[v] = k;
	}
	function register2(hic, k, vec) {
		for (let _ of vec) register(hic, k, _);
	}
	function register3(hic, hash) {
		for (let k in hash)
			register2(hic, k, hash[k]);
	}
	////
	// DEPATH "manager" for linking specific forms of a word+
	//
	class DEPATH {
		construct(init, aliases, name) {
			this.key2values = [];
			this.value2key = [];
			this.all_sub_keys = [];
			this.level = [];
			this.simple_keys = [];
			this.recursive_keys = [];
			this.name = name;
			if (aliases == undefined) var aliases = [];
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
						if (!ISDEPATH(v_))
							v_ = DEPATH(v_, aliases);
						this.all_sub_keys = this.all_sub_keys.concat(...v_.all_sub_keys);
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
			for (var k of Object.keys(this.all_sub_keys))
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
				var ret = _subscript(this.aliases, value);
			if (typeof ret !== 'string')
				throw new Error("value '"+value+"' has no alias for key "+(key==null?'nil':"'"+key+"'")+" depath name "+this.name);
			return ret;
		}
		add_alias(alias, value, key) {
			if (!value in this.value2key)
				throw new Error("bad value '"+value+"'");
			if (key != null)
				this.aliases[key][alias] = value;
			else this.aliases[alias] = value;
		}
	}

	model.DEPATH = DEPATH;
})(pantheum.model);
