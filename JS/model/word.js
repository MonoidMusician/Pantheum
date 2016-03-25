(function(model) {
	"use strict";
	class WORD {
		construct(db, id, mgr, lang) {
			this.path_storage = {};
			this.attr_storage = {};
			this.df_path_values = [];
			if (Array.isArray(id) && mgr !== null) {
				id = db.new_word(mgr, id, lang);
			}
			this._db = db;
			this.issql = ISSQLDB(db);
			if (this.issql && typeof id === 'string')
				id = db.id_find_word(id, lang);
			this._id = id;
			if (mgr === null) {
				if(!is_scalar(id)) throw new TypeError("_WORD.id needs to be scalar");
				// defer this._mgr = db.get_mgrW(this); to this.mgr
			}
			this._mgr = mgr;
			this._path = null;
			return this;
		}
		get path() {
			if (this._path === null) {
				this._path = PATH(this.mgr);
				this._path.set_word(this);
			}
			return this._path;
		}
		get mgr() {
			if (this._mgr === null)
				this._mgr = this.db.get_mgrW(this);
			return this._mgr;
		}
		get db() { return this._db; }
		toString() {return this.id+this.name;}
		get id() { return this._id; }
		set id(id) {
			if (this.id === null)
				this._id = id;
			else _die("already had id, could not set to $id");
		}
		get name() {
			if (this.issql && this._id !== null)
				sql_getone(sql_stmt("word_id->word_name"), this._name, ["i", this._id]); // still null if not found
			return this._name;
		}
		set name(name) {
			this._name = name;
			if (this.issql && this._id !== null)
				sql_set(sql_stmt("word_id->word_name="), this._name, ["i", this._id]);
		}
		get cached() {
			if (this.issql && this._id !== null)
				sql_getone(sql_stmt("word_id->inflection_cache"), this._cached, ["i", this._id]); // still null if not found
			return this._cached;
		}
		set cached(cached) {
			this._cached = cached;
			if (this.issql && this._id !== null)
				sql_set(sql_stmt("word_id->inflection_cache="), this._cached, ["i", this._id]);
		}
		get speechpart() {
			if (this.issql && this._id !== null)
				sql_getone(sql_stmt("word_id->word_spart"), this._speechpart, ["i", this._id]); // still null if not found
			return this._speechpart;
		}
		set speechpart(spart) {
			this._speechpart = spart;
			if (this.issql && this._id !== null)
				sql_set(sql_stmt("word_id->word_spart="), this._speechpart, ["i", this._id]);
		}
		get lang() {
			if (this.issql && this._id !== null)
				sql_getone(sql_stmt("word_id->word_lang"), this._lang, ["i", this._id]); // still null if not found
			return this._lang;
		}
		set lang(lang) {
			this._lang = lang;
			if (this.issql && this._id !== null)
				sql_set(sql_stmt("word_id->word_lang="), this._lang, ["i", this._id]);
		}
		get last_changed() {
			if (this.issql && this._id !== null)
				sql_getone(sql_stmt("word_id->last_changed"), this._last_changed, ["i", this._id]); // still null if not found
			return this._last_changed;
		}
		get info() {
			if (this.issql && this._id !== null)
				sql_getone(sql_stmt("word_id->word_info_formatted"), this._info, ["i", this._id]); // still null if not found
			return this._info;
		}

		clear_definitions() {
			this._definitions = [];
		}
		get_def(def) {
			if (this.issql && this._id !== null) {
				var id = null;
				sql_getone(sql_stmt("word_id,def_lang,def_value->def_id"), id, ["iss", this._id, def.lang(), def.value()]);
				var def = DEFINITION(this.db(), id, this);
			}
			return def;
		}
		definitions() {
			if (this.issql && this._id !== null) {
				var added = [];  // ids returned
				sql_getmany(sql_stmt("word_id->def_id"), added, ["i", this._id]);
				for (var def of added) {
					var cont=false;
					for (var _def of this._definitions) {
						if (ISDEFINITION(_def) ? _def.id() == def : _def == def) {var cont=true;break;}
					}
					if (!cont)
						this._definitions.push(DEFINITION(this.db(), def, this));
				}
			}
			return this._definitions;
		}
		add_definition(def) {
			if (this.issql && this._id !== null) {
				if (def.type())
					sql_exec(sql_stmt("word_id,def_lang,def_value,form_tag,def_type->new in definitions"), ["issss", this._id, def.lang(), def.value(), def.toString().path(), def.type()]);
				else sql_exec(sql_stmt("word_id,def_lang,def_value,form_tag->new in definitions"), ["isss", this._id, def.lang(), def.value(), def.toString().path()]);
				var def = this.get_def(def);
			}
			this._definitions.push(def);
			return def;
		}

		clear_pronunciations() {
			this._pronunciations = [];
		}
		get_pron(pron) {
			if (this.issql && this._id !== null) {
				var id = null;
				sql_getone(sql_stmt("word_id,pron_type,pron_value->pron_id"), id, ["iss", this._id, pron.type(), pron.value()]);
				if (id !== null) var pron = PRONUNCIATION(this.db(), id, this);
			}
			return pron;
		}
		pronunciations() {
			if (this.issql && this._id !== null) {
				var added = []; // ids returned
				sql_getmany(sql_stmt("word_id->pron_id"), added, ["i", this._id]);
				for (var pron of added) {
					var cont=false;
					for (var _pron of this._pronunciations) {
						if (ISPRONUNCIATION(_pron) ? _pron.id() == pron : _pron == pron) {var cont=true;break;}
					}
					if (!cont)
						this._pronunciations.push(PRONUNCIATION(this.db(), pron, this));
				}
			}
			return this._pronunciations;
		}
		add_pronuncation(pron) {
			if (this.issql && this._id !== null) {
				sql_exec(sql_stmt("word_id,pron_type,pron_value,form_tag->new in pronunciations"), ["isss", this._id, pron.type(), pron.value(), pron.toString().path()]);
				var pron = this.get_pron(pron);
				console.log(var_export(pron.id(),1));
			}
			this._pronunciations.push(pron);
			return pron;
		}


		clear_connections() {
			this._connections = [];
		}
		connections() {
			if (this.issql && this._id !== null) {
				var stmt = sql_stmt("from_word_id->to_word_id,connect_type");
				if (!stmt.bind_param("i", this._id)) {
					throw new Error("Binding parameters failed: (" + stmt.errno + ") " + stmt.error);
					return this._connections;
				}
				stmt.bind_result(to_word_id, type);
				if (!stmt.execute()) {
					throw new Error("Execute failed ("+__FILE__+"@"+__LINE__+"): (" + stmt.errno + ") " + stmt.error);
					return result;
				}
				var added = [];
				while (stmt.fetch()) {
					added.push([to_word_id,type]);
				}
				for (var connect of added) {
					this._connections.push(CONNECTION(
						this,
						WORD(this.db(),connect[0]),
						connect[1]
					));
				}
				stmt.free_result();
				stmt.reset();
			}
			return this._connections;
		}
		add_connection(connect) {
			if (this.issql && this._id !== null) {
				sql_exec(sql_stmt("from_word_id,to_word_id,connect_type->new in connections"), ["iis", this._id, connect.to().id(), connect.type()]);
			}
			this._connections.push(connect);
			return connect;
		}
		remove_connection(connect) {
			if (this.issql && this._id !== null) {
				sql_exec(sql_stmt("from_word_id,to_word_id,connect_type->delete from connections"), ["iis", this._id, connect.to().id(), connect.type()]);
			}
		}



		clear_paths() {
			this._paths = [];
		}
		paths() {
			if (this.issql && this._id !== null) {
				var added = []; // ids returned
				sql_getmany(sql_stmt("word_id->form_id"), added, ["i", this._id]);
				for (var path of added) {
					var cont=false;
					for (var _path of this._paths) {
						if (ISPATH(_path) ? _path.id() == path : _path == path) {var cont=true;break;}
					}
					if (!cont)
						this._paths.push(PATH(this, path));
				}
			}
			return this._paths;
		}
		get_path(path) {
			if (this.issql && this._id !== null) {
				var id = null;
				sql_getone(sql_stmt("word_id,form_tag,form_value->form_id"), id, ["iss", this._id, path.toString(), path.get()]);
				if (id !== null) var path = PATH(path, id);
			}
			return path;
		}
		add_path(path) {
			if (this.issql && this._id !== null) {
				sql_exec(sql_stmt("word_id,form_tag,form_value->new in forms"), ["iss", this._id, path.toString(), path.get()]);
				var path = this.get_path(path);
			}
			this._paths.push(path);
			return path;
		}

		// Ugly interaction with forms/paths
		path_by_tag(...tag) {
			var _id = null;
			var p = PATH(this, tag);
			if (this.issql) {
				var tag = p.toString();
				sql_getone(sql_stmt("word_id,form_tag->form_id"), _id, ["is", this._id, tag]);
				if (_id !== null)
					return PATH(this, _id);
			}
			if (p.exists())
				return p;
		}
		read_paths() {
			this.clear_paths();
			for (var p of this.paths()) {
				p.set(p.value());
			}
			return this._paths;
		}


		clear_attrs() {
			this._attrs = [];
		}
		attrs() {
			if (this.issql && this._id !== null) {
				var added = []; // ids returned
				sql_getmany(sql_stmt("word_id->attr_tag"), added, ["i", this._id]);
				for (var attr of added) {
					var cont=false;
					for (var _attr of this._attrs) {
						if (ISPATH(_attr) ? _attr.tag() == attr : _attr == attr) {var cont=true;break;}
					}
					if (!cont)
						this._attrs.push(ATTR(this, attr));
				}
			}
			return this._attrs;
		}
		add_attr(attr) {
			this._attrs.push(attr);
			if (this.issql && this._id !== null) {
				sql_exec(sql_stmt("set attr"), ["iss", this._id, attr.tag(), attr.value()]);
			}
		}
		remove_attr(attr) {
			var a = this._attrs;
			for (let k in a) {
				if (a[k].tag() === attr.tag())
					delete this._attrs[k];
			}
			if (this.issql && this._id !== null) {
				sql_exec(sql_stmt("word_id,attr_tag->delete from attributes"), ["is", this._id, attr.tag()]);
			}
		}
		read_attrs() {
			this.clear_attrs();
			for (var p of this.attrs()) {
				p.set(p.value());
			}
			return this._attrs;
		}


		has_attr(attr) {
			if (attr.value() == null)
				return !!attr.get(this);
			else return attr.get(this) == attr.value();
		}

		remove() {
			if (this.issql && this._id != null)
				return sql_exec(sql_stmt("word_id->delete from words"), ["i", this._id]); // still null if not found
		}
	}

	model.WORD = WORD;
})(pantheum.model);
