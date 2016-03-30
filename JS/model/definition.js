var common = require('./common');

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var cache = {};
	class Definition extends common {
		constructor(data, cached) {
			super();
			if (data instanceof Definition) return data;
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
			if (this.word != null)
				data.word = this.word;
			if (this.tag != null)
				data.tag = this.tag;
			return data;
		}
		fromData(data, overwrite) {
			for (let d of columns)
				if (overwrite || data[d] != null)
					this["_"+d] = data[d];
			if (overwrite || data.id != null)
				this.id = data.id;
			if (overwrite || data.word != null)
				this.word = data.word;
			if (overwrite || data.tag != null)
				this.tag = data.tag;
			return this;
		}
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this["_"+d] = row[prefix+d];
			if (row.word_id)
				this.word_id = row.word_id;
			if (row.form_tag)
				this.form_tag = row.form_tag;
			return this;
		}
		toSQL(...fields) {
			var cols = new Set(["word","tag", ...columns]);
			if (fields.length === 1 && (Array.isArray(fields[0]) || fields[0] instanceof Set))
				fields = fields[0];
			fields = [...fields].filter(cols.has.bind(cols));
			if (!fields.length) fields = cols;
			else fields = new Set(fields);
			var row = {};
			if (fields.delete("word") && this.word_id  !== undefined) row.word_id  = this.word_id;
			if (fields.delete("tag")  && this.form_tag !== undefined) row.form_tag = this.form_tag;
			for (let d of fields)
				if (this["_"+d] !== undefined)
					row[prefix+d] = this["_"+d];
			return row;
		}
		get id() {
			return this._id;
		}
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Definition id must be integer or null/undefined");
			this._id = id;
		}
		get value() {
			return this._value;
		}
		set value(value) {
			this._value = value;
		}
		get type() {
			return this._type;
		}
		set type(type) {
			this._type = type;
		}
		get sense() {
			return this._sense;
		}
		set sense(sense) {
			this._sense = sense;
		}
		get lang() {
			return this._lang;
		}
		set lang(lang) {
			this._lang = lang;
		}
		get word() {
			return this._word;
		}
		set word(word) {
			this._word = word && (model.Word ? new model.Word(word) : word);
		}
		get word_id() {
			return this._word && this._word.id;
		}
		set word_id(word_id) {
			this._word = model.Word ? new model.Word(word_id) : {id:word_id};
		}
		get tag() {
			return this._tag;
		}
		set tag(tag) {
			this._tag = tag && (model.Path ? new model.Path(this.word, tag) : tag);
		}
		get form_tag() {
			return this._tag && this._tag.toString();
		}
		set form_tag(form_tag) {
			this._tag = model.Path ? new model.Path(this._word, form_tag) : form_tag;
		}
	}
	Definition.table = "definitions";
	Definition.key = prefix+"id";

	model.Definition = Definition;
})(!exports?pantheum.model:this);
