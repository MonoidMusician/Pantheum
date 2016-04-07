var stampit = require('stampit');
var common = require('./common');

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var methods = {
		// Required API
		toData() {
			var data = {};
			for (let d of [...columns, "id", "word", "tag"])
				if (this[d] != null)
					data[d] = this[d];
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
			this._word = word && (model.Word ? new model.Word(word) : word);
		},
		set tag(tag) {
			this._tag = tag && (model.Path ? new model.Path(this.word, tag) : tag);
		},
		// Special SQL pseudo-member access
		set word_id(word_id) {
			this._word = model.Word ? new model.Word(word_id) : {id:word_id};
		},
		get word_id() {
			return this._word && this._word.id;
		},
		set form_tag(form_tag) {
			this._tag = model.Path ? new model.Path(this._word, form_tag) : form_tag;
		},
		get form_tag() {
			return this._tag && this._tag.toString();
		},
	};
	var statics = {
		table: "definitions",
		key: prefix+"id",
	};
	var Definition = stampit({
		methods: common.methods(methods),
		refs: statics,
		static: statics,
		init: function initDefinition({instance, args: [data, cacheable], stamp}) {
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
			common.defprops(instance, [...columns, "id", "word", "tag", "word_id", "form_tag"], methods);

			// Initialize using accessors
			if (typeof data === 'number') {
				instance.id = data;
			} else {
				instance.fromData(data, true);
			}
		}
	});
	model.Definition = common.stamp(Definition);
})(!exports?pantheum.model:this);
if (exports) module.exports = this.Definition;
