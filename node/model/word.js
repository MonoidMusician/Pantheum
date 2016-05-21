var stampit = require('stampit');

var languages = require('../languages');
var common = require('./common');

module.exports = function(model) {
	var prefix = "word_";
	var columns = ["spart","name","lang","last_changed"];
	var methods = {
		// Member access
		set id(id) {
			if (id != null && typeof id !== 'number')
				throw new TypeError("Word id must be integer or null/undefined");
			this._id = id;
		},
		get mgr() {
			if (!this._mgr && model.Depath) {
				this._mgr = model.Depath.of(this);
			}
			return this._mgr;
		},
		// Required API
		fromData(data, visited) {
			for (let d of [...columns, "id"])
				if (data[d] != null)
					this[d] = data[d];
			if (this.lang in languages)
				this.lang = languages[this.lang];

			// Reconstruct recursive structures
			if (!visited) visited = [];
			visited.push([data, this]);
			for (let cls of this.references) {
				if (!data[cls.table]) continue;
				this[cls.table] = data[cls.table].map(
					d => common.visit(visited, d, cls.fromData.cacheable(this.cacheable))
				);
			}

			return this;
		},
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
		fromSQL(row) {
			for (let d of columns)
				if (row[prefix+d] !== undefined)
					this[d] = row[prefix+d];
			if (this.lang in languages)
				this.lang = languages[this.lang];
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
		// Extra methods
		path(tag, value) {
			if (typeof tag === 'string') tag = {tag};
			tag = model.Path(Object.assign({}, tag, {word:this}));
			if (value) tag.value = value;
			return tag.value;
		},
		has_attr(attr) {
			if (attr.value() == null)
				return !!attr.get(this);
			else return attr.get(this) == attr.value();
		},
	};
	var statics = {
		table: "words",
		key: "word_id",
		references: [model.Definition, model.Form],
		columns
	};
	var Word = stampit({
		methods: common.methods(methods),
		refs: statics,
		static: statics,
		props: {
			path_storage: {}
		},
		init: function initWord({instance, args: [cacheable], stamp}) {
			// Tell cacheable stamp to cache this or not
			if (cacheable !== undefined)
				instance.cacheable = cacheable;

			// Copy accessors to the instance, create missing accessors
			common.defprops(instance, [...columns, "id", "mgr"], methods);
		}
	});
	model.Word = common.stamp(Word);
};
