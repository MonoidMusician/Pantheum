var stampit = require('stampit');
var common = require('./common');

var model = {};

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var methods = {
		// Required API
		toData(visited) {
			var data = {};
			for (let d of [...columns, "id", "tag"])
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
			this._tag = tag && (model.Path ? model.Path(Object.assign({word:this.word}, tag), !!this.cacheable) : tag);
		},
		// Special SQL pseudo-member access
		set word_id(word_id) {
			this.word = {id:word_id};
		},
		get word_id() {
			return this.word && this.word.id;
		},
		set form_tag(form_tag) {
			this._tag = model.Path ? model.Path({tag:form_tag, word:this.word}, !!this.cacheable) : form_tag;
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

