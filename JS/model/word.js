var common = require('./common');
var Definition = require('./definition').Definition;

(function(model) {
	"use strict";
	var prefix = "word_";
	var columns = ["spart","name","lang"];
	var cache = {};
	class Word extends common {
		constructor(data, cached) {
			super();
			if (data instanceof Word) return data;
			if (typeof data === 'number') {
				if (cached && cache[data] instanceof Definition)
					return cache[data];
				this._id = data;
			} else this.fromData(data, true);
			if (cached && this._id != null)
				this.cache();
		}
		cache() {
			cache[this._id] = this;
		}
		uncache() {
			delete cache[this._id];
		}
		toData() {
			var data = {};
			for (let d of columns)
				if (this["_"+d] != null)
					data[d] = this["_"+d];
			if (this.id != null)
				data.id = this.id;
			for (let cls of this.constructor.referenced) {
				let m = {};
				for (let d of this[cls.table])
					m[d.id] = d.toData();
				data[cls.table] = m;
			}
			return data;
		}
		fromData(data, overwrite) {
			for (let d of columns)
				if (overwrite || data[d] != null)
					this["_"+d] = data[d];
			if (overwrite || data.id != null)
				this.id = data.id;
			for (let cls of this.constructor.referenced) {
				let m = [];
				for (let id in data[cls.table])
					m.push(new cls(id).fromData(data[cls.table][id]));
				this[cls.table] = m;
			}
			return this;
		}
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this["_"+d] = row[prefix+d];
			return this;
		}
		toSQL() {
			var row = {};
			for (let d of columns)
				if (this["_"+d] !== undefined)
					row[prefix+d] = this["_"+d];
			return row;
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
		toString() {return this.id+this.name;}
		get id() {
			return this._id;
		}
		set id(id) {
			if (typeof id !== 'number')
				throw new TypeError("Definition id must be integer");
			this._id = id;
		}
		get name() {
			return this._name;
		}
		set name(name) {
			this._name = name;
		}
		get cached() {
			return this._cached;
		}
		set cached(cached) {
			this._cached = cached;
		}
		get speechpart() {
			return this._speechpart;
		}
		set speechpart(spart) {
			this._speechpart = spart;
		}
		get lang() {
			return this._lang;
		}
		set lang(lang) {
			this._lang = lang;
		}
		get last_changed() {
			return this._last_changed;
		}
		get info() {
			return this._info;
		}


		has_attr(attr) {
			if (attr.value() == null)
				return !!attr.get(this);
			else return attr.get(this) == attr.value();
		}
	}
	Word.table = "words";
	Word.key = "word_id";
	Word.referenced = [Definition];

	model.Word = Word;
})(!exports?pantheum.model:this);
