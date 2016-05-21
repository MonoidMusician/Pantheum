var stampit = require('stampit');

var languages = require('../languages');
var common = require('./common');

module.exports = function(model) {
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var methods = {
		// Member access
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Definition id must be integer or null/undefined");
			this._id = id;
		},
		set word(word) {
			this._word = common.construct(model.Word, word, this.cacheable);
		},
		set tag(tag) {
			this._tag = common.construct(model.Path, Object.assign({word:this.word}, tag));
		},
		// Special SQL pseudo-member access
		set word_id(word_id) {
			this.word = {id:word_id};
		},
		get word_id() {
			return this.word && this.word.id;
		},
		set form_tag(form_tag) {
			this._tag = model.Path ? model.Path({word:this.word}, form_tag) : form_tag;
		},
		get form_tag() {
			return this.tag && this.tag.toString();
		},
		// Required API
		fromData(data, visited) {
			// Reconstruct recursive structures
			if (!visited) visited = [];
			visited.push([data, this]);
			this.word = data.word && common.visit(visited, data.word, model.Word.fromData.cacheable(this.cacheable));

			for (let d of [...columns, "id", "form_tag"])
				if (data[d] != null)
					this[d] = data[d];
			if (this.lang in languages)
				this.lang = languages[this.lang];

			return this;
		},
		toData(visited) {
			var data = {};
			for (let d of [...columns, "id", "form_tag"])
				if (this[d] != null)
					data[d] = this[d];

			// Serialize recursive structures
			if (!visited) visited = [];
			visited.push([this, data]);
			data.word = this.word && (this.word.toData ? common.visit(visited, this.word, this.word.toData) : this.word);

			return data;
		},
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this[d] = row[prefix+d];
			if (this.lang in languages)
				this.lang = languages[this.lang];
			if (row.word_id != null)
				this.word_id = row.word_id;
			if (row.form_tag != null) {
				if (!this.word.mgr) {
					return this.word.pull().then(
						w => {
							this.form_tag = row.form_tag;
							return this;
						}
					);
				}
				this.form_tag = row.form_tag;
			}
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
};
