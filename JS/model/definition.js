var stampit = require('stampit');
var server = require('./server');
var cached = require('./cached');

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["value","type","sense","lang"];
	var global_enumerable = false;
	function defprop(obj, name, src) {
		var _name = "_"+name;
		function get() {return this[_name];}
		function set(v) {this[_name] = v;}
		var props = Object.getOwnPropertyDescriptor(src||obj, name);
		if (src === obj && props && "get" in props && "set" in props) return;
		var config = {
			get: get, set: set,
			configurable: true,
		};
		if (props && !props.set) delete props.set;
		if (props && !props.get) delete props.get;
		Object.assign(config, props);
		config.enumerable = global_enumerable;
		Object.defineProperty(obj, name, config);
	}
	function defprops(obj, names, src) {
		for (let n of names) defprop(obj, n, src);
	}
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
	var config = {enumerable: true};
	Object.defineProperties(methods, {
		toData: config, fromData: config,
		toSQL: config, fromSQL: config,
	});
	var Definition = stampit({
		methods,
		refs: {
			table: "definitions",
			key: prefix+"id",
		},
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
			defprops(instance, [...columns, "id", "word", "tag", "word_id", "form_tag"], methods);

			// Initialize using accessors
			if (typeof data === 'number') {
				instance.id = data;
			} else {
				instance.fromData(data, true);
			}
		}
	});
	model.Definition = stampit.compose(server, Definition, cached);
})(!exports?pantheum.model:this);
