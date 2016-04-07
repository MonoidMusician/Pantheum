var stampit = require('stampit');
var common = require('./common');
var Definition = require('./definition');

(function(model) {
	"use strict";
	var prefix = "word_";
	var columns = ["spart","name","lang","last_changed"];
	var methods = {
		// Required API
		toData() {
			var data = {};
			for (let d of [...columns, "id"])
				if (this[d] != null)
					data[d] = this[d];
			for (let cls of this.references) {
				let m = {};
				for (let d of this[cls.table])
					m[d.id] = d.toData();
				data[cls.table] = m;
			}
			return data;
		},
		fromData(data, overwrite) {
			for (let d of [...columns, "id"])
				if (overwrite || data[d] != null)
					this[d] = data[d];
			for (let cls of this.references) {
				let m = [];
				for (let id in data[cls.table])
					m.push(new cls(id).fromData(data[cls.table][id]));
				this[cls.table] = m;
			}
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
					row[prefix+d] = this["_"+d];
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
			if (this._path === null) {
				this._path = PATH(this.mgr);
				this._path.set_word(this);
			}
			return this._path;
		},
		get mgr() {
			if (this._mgr === null)
				this._mgr = this.db.get_mgrW(this);
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
		references: [Definition],
	};
	var Word = stampit({
		methods: common.methods(methods),
		refs: statics,
		static: statics,
		init: function initWord({instance, args: [data, cacheable], stamp}) {
			// Data argument disappeared, holds value of cacheable
			if (data === true || data === false) {
				cacheable = data;
				data = null;
			}

			// Our data is part of the instance
			if (data == null)
				data = Object.assign({}, instance);

			// Tell cached stamp to cache this or not
			instance.cacheable = cacheable;

			// Copy accessors to the instance, create missing accessors
			common.defprops(instance, [...columns, "id", "path", "mgr"]);

			// Initialize using accessors
			if (typeof data === 'number') {
				instance.id = data;
			} else {
				instance.fromData(data, true);
			}
		}
	});
	Word.table = "words";
	Word.key = "word_id";
	Word.references = [Definition];

	model.Word = common.stamp(Word);
})(!exports?pantheum.model:this);
if (exports) module.exports = this.Word;
