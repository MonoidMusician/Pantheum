var common = require('./common');

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	class Definition {
		constructor(data, cached) {
			if (data instanceof Definition) return data;
			if (typeof data === 'number') {
				if (cached && cache[data] instanceof Definition)
					return cache[data];
				this._id = data;
			} else this.fromData(data, true);
			if (cached && this._id != null)
				this.cache();
		}
		toData() {
			var data = {};
			for (let d of [...columns, "id","word","tag"])
				if (this[d] != null)
					data[d] = this[d];
			return data;
		}
		fromData(data, overwrite) {
			for (let d of [...columns, "id","word","tag"])
				if (overwrite || data[d] != null)
					this[d] = data[d];
			return this;
		}
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this[d] = row[prefix+d];
			if (row.word_id)
				this.word_id = row.word_id;
			if (row.form_tag)
				this.form_tag = row.form_tag;
			return this;
		}
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
	Definition = stampit.compose(common, cached, {
		methods: Definition.prototype
	});

	model.Definition = Definition;
})(!exports?pantheum.model:this);
