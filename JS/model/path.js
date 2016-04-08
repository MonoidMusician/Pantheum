(function(model) {
	"use strict";
	class PATH {
		constructor(init, ...path) {
			this.issql = false;
			this._id = null;
			this._mgr = null;
			this._word = null;
			this._tag = null;
			this._value = null;
			this.map = [];
			if (ISWORD(init)) {
				this.issql = init.issql;
				var p = init.path();
				this._mgr = p.mgr();
				this.map.length = p.map.length;
				this._word = init;
				this.add2(init.df_path_values);
			} else if (ISPATH(init)) {
				this.issql = init.issql;
				this._mgr = init.mgr();
				this.map.length = init.map.length;
			} else {
				if (!ISDEPATH(init)) _die("bad init object ".var_export(init,1));
				this._mgr = init;
				this.map.length = init.all_sub_keys;
			}
			if (typeof path === 'number') {
				this.issql = true;
				this._id = path;
				path = this.tag();
			}
			this.add2(path);
		}
		resolve_hash(hash) {
			if (ISWORD(hash)) return hash.path_storage;
			if (!hash && this.word()) {
				if (this.word().path_storage)
					return this.word().path_storage;
				//else if (this.word().db() !== null)
				//	return this.word().db();
			}
			return hash;
		}
		get id() { return this._id; }
		set id(id) { this._id = id; }
		get mgr() { return this._mgr; }
		set mgr(m) { return this._mgr = m; }
		get word() { return this._word; }
		set word(w) { return this._word = w; }
		get tag() {
			return this._tag;
		}
		get value() {
			return this._value;
		}
		set value(value) {
			this._value = value;
		}
		resolve_key_value(arg) {
			var key = null; var value = null;
			if (arg.length === 1) {
				value = this.mgr().resolve_alias(arg[0]);
				if (value !== null)
					key = this.mgr().find_key(value);
			} else if (arg.length === 2) {
				[key,value] = arg;
				value = this.mgr().resolve_alias(value, key);
				// In case key was "" or to catch incorrect value:
				key = this.mgr().find_key(value);
			}
			return [key,value];
		}
		add(...arg) {
			this._map_dirty = true;
			if (arg.length === 1 && Array.isArray(arg[0])) arg = arg[0];
			var [key, value] = this.resolve_key_value(arg);
			var key_index = this.mgr().key_index(key);
			this.map[key_index] = value;
			return this;
		}
		add2(...arg) {
			for (var a of arg) {
				if (typeof a === 'function') var a = a();
				if (ISPATH(a)) var a = a.toString();
				if (!a) {}
				else if (typeof a === 'string') {
					for (var _ of a.trim().split("/"))
						if (_) this.add(_);
				} else if (Array.isArray(a))
					if (a.length === 2 && typeof a[0] === 'string' && this.mgr().is_key(a[0]))
						this.add(a);
					else this.add2(...a);
				else throw new TypeError("PATH.add2 requires a string or vector");
			}
			return this;
		}
		addp(basepath) {
			for (var k of this.mgr().all_sub_keys) {
				if (!this.key_exists(k))
					this.add(basepath.key_value(k));
			}
			this._map_dirty = true;
			return this;
		}
		take(key) {
			this._map_dirty = true;
			this.map[this.mgr().key_index(key)] = null;
			return this;
		}
		take2(...arg) {
			for (var a of vec) {
				if (!a) {}
				else if (typeof a === 'string')
					this.take(a);
				else if (Array.isArray(a))
					if (a.length === 2 && this.mgr().is_key(a[0])
						&& this.mgr().is_value(a[1]))
						this.take(a);
					else this.take2(...arg);
				else throw new TypeError("PATH.take2 requires a string or vector");
			}
			return this;
		}
		toString() {
			return this.map.filter(_=>_).join("/");
		}
		reset() {
			for (var i of Object.keys(this.path))
				this.path[i] = null;
			return this;
		}
		key_exists(key) {
			return !!this.map[this.mgr().key_index(key)];
		}
		key_value(key) {
			return this.map[this.mgr().key_index(key)];
		}
		walk(hash, create=false) {
			this.validate();
			var hash = this.resolve_hash(hash);
			for (var p of this.map) {
				if (!hash) break;
				if (!p) continue;
				if (create && !hash[p]) hash[p] = [];
				hash = hash[p];
			}
			return hash;
		}
		walk_part(hash, max=0, min=0, create=false) {
			this.validate();
			var hash = this.resolve_hash(hash);
			var i = 0;
			for (var p of this.map) {
				if (min) {min -= 1;continue;}
				if (!hash) return hash;
				i += 1;
				if (!p) continue;
				if (create && !hash[p]) hash[p] = [];
				hash = hash[p];
				if (!(max -= 1)) break;
			}
			return [hash,i,this.map[i]];
		}
		_calculate_valid_values() {
			if (!this._map_dirty) return this._valid_values;
			this._map_dirty = false;
			var ret = [];
			var recurse = function(dp) {
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
			recurse(this.mgr());
			this._valid_values = ret;
			return ret;
		}
		valid(msg=false) {
			var dp = this.mgr();
			var vals = this._calculate_valid_values();
			for (var k of Object.keys(vals)) {
				var i = dp.key_index(k);
				var vs = vals[k];
				var v = this.map[i];
				if (v === null) continue;
				var match = 0;
				for (var v_p of vs)
					if (v_p == v) { var match=1; break; }
				if (!match) return msg ? "value '$v' of key '$k' was not in set "+var_export(vs, true) : false;
			}
			return msg ? null : true;
		}
		validate() {
			var msg = this.valid(true);
			if (msg) throw new Error("invalid path: "+msg);
		}
		// Count how many values there are
		get length() {
			var s=0;
			for (var p of this.map)
				if(p) s+=1;
			return s;
		}
		set(val, hash) {
			this._map_dirty = true;
			if(FLAT_STORAGE) {
				var h = this.resolve_hash(hash);
				return h[this.toString()] = val;
			}
			var h = this.walk(hash,1);
			return h[""] = val;
		}
		get(hash) {
			if(FLAT_STORAGE) {
				var h = this.resolve_hash(hash);
				if (this.exists(h)) {
					return h[this.toString()];
				} else return;
			}
			var h = this.walk(hash,0);
			if (h === null || !("" in h)) return null;
			return h[""];
		}
		exists(hash) {
			if(FLAT_STORAGE) {
				var h = this.resolve_hash(hash);
				return h && this.toString() in h;
			}
			var h = this.walk(hash,0);
			return h !== null;
		}
		iterate(k, hash) {
			var h = this.resolve_hash(hash);
			var vals = this._calculate_valid_values();
			if (h !== null)
				if(FLAT_STORAGE)
					return vals[k].filter(function(i) {
						return Object.keys(h).some(function(j) {
							if (!new PATH(this._mgr, j).issub(this)) return false;
							return j.split("/").includes(i);
						});
					});
				else
					return vals[k].filter(function(i) {
						return array_key_exists_r(i, h);
					});
			return vals[k];
		}
		issub(other,ret) {
			for (var k of this.mgr().all_sub_keys) {
				if (other.key_value(k) && this.key_value(k) != other.key_value(k)) {
					return false;
				} else if (this.key_value(k)) {
					ret = true;
				}
			}
			return !!ret;
		}
		hasvalue(hash) { return this.get(hash) !== null; }
		remove(hash) {
			if (this.issql && this._id !== null)
				sql_exec(sql_stmt("form_id->delete from forms"), ["i", this._id]);
			var hash = this.resolve_hash(hash);
			if(FLAT_STORAGE) {
				this._map_dirty = true;
				var ret = hash[this.toString()];
				delete hash[this.toString()];
				return ret;
			}
			var h = this.walk(hash,0);
			if (h === null) return null;
			this._map_dirty = true;
			var ret = h[""];
			delete h[""];
			// Clean up dead branches if we have killed them:
			while (!h.length && this.length) {
				var max = this.length-1;
				var [h2,i,k] = this.walk_part(hash, max);
				delete h2[k];
				h = h2;
			}
			return ret;
		}
		remove_all(hash) {
			var hash = this.resolve_hash(hash);
			for (var k of Object.keys(hash))
				delete hash[k];
			return this;
		}
		move(wen, hash) {return wen.set(this.remove(hash), hash);}
		values(key) {
			return this._calculate_valid_values()[key];
		}
	}
	model.PATH = PATH;
})(pantheum.model);
