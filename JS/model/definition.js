var connection = require('./mysql');
var Promise = require('promise');

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var table = "definitions";
	var cache = {};
	class Definition {
		constructor(data, cached) {
			if (data instanceof Definition) return data;
			if (typeof data === 'number') {
				if (cached && cache[data] instanceof Definition)
					return cache[data];
				this._id = data;
			} else this.update(data, true);
			if (cached && this._id != null)
				this.cache();
		}
		cache() {
			cache[this._id] = this;
		}
		uncache() {
			delete cache[this._id];
		}
		update(data, overwrite) {
			for (let d of columns)
				if (overwrite || data[d] != null)
					this["_"+d] = data[d];
			if (overwrite || data.id != null)
				this.id = data.id;
			if (overwrite || data.word != null)
				this.word = data.word;
			if (overwrite || data.tag != null)
				this.tag = data.tag;
		}
		insert() {
			var row = {};
			if (this._word) row.word_id = this._word.id;
			if (this._tag) row.form_tag = this._tag.toString();
			for (let d of columns)
				// Leave out null/undefined values
				if (this["_"+d] != null)
					row[prefix+d] = this["_"+d];
			if (!Object.keys(row).length) return Promise.reject(new Error("No fields to insert"));
			if (this._id) row.def_id = this._id;
			return new Promise((resolve, reject) => {
				connection.query("INSERT INTO definitions SET ?", row, (err, result) => {
					if (err) return reject(err);
					if (!this._id) this._id = result.insertId;
					resolve(this);
				});
			});
		}
		pull() {
			var row = {};
			if (this._id == null) return Promise.reject(new Error("No definition id"));
			return new Promise((resolve, reject) => {
				connection.query("SELECT * FROM definitions WHERE def_id = ?", this._id, (err, rows, fields) => {
					if (err) return reject(err);
					if (rows.length !== 1) return reject(new Error("Definition not found"));
					var row = rows[0];
					for (let d of columns)
						if (row[prefix+d] !== undefined)
							this["_"+d] = row[prefix+d];
					if (row.word_id)
						this.word_id = row.word_id;
					if (row.form_tag)
						this.form_tag = row.form_tag;
					resolve(this);
				});
			});
		}
		push(...fields) {
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
				// Include if null (we could be resetting a field)
				if (this["_"+d] !== undefined)
					row[prefix+d] = this["_"+d];
			if (!Object.keys(row).length)
				return  Promise.resolve(this);
			return new Promise((resolve, reject) => {
				connection.query("UPDATE definitions SET ? WHERE def_id = ?", [row, this._id], (err, result) => {
					if (err) return reject(err);
					return resolve(this);
				});
			});
		}
		get id() {
			return this._id;
		}
		set id(id) {
			if (typeof id !== 'number')
				throw new Error("Definition id must be integer");
			this._id = id;
		}
		push_id(newid) {
			if (typeof newid !== 'number')
				return Promise.reject(new Error("Definition id must be integer"));
			this.uncache();
			return new Promise((resolve, reject) => {
				connection.query("UPDATE definitions SET def_id = ? WHERE def_id = ?", [newid, this._id], (err, result) => {
					if (err) {
						this.cache();
						return reject(err);
					}
					this.id = newid;
					this.cache();
					resolve(this);
				});
			});
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

	model.Definition = Definition;
})(!exports?pantheum.model:this);
