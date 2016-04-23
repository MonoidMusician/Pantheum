"use strict";
var stampit = require('stampit');
var queryAPI = require('./queryAPI.js');
var cycle = require('cycle');

// Each derive class must have
//  - table Table name in database
//  - key   Column name for id in database
//  - id    ID value for instance
//  - references
var methods = {
	toJSON(...arg) {
		return cycle.decycle(this.toData(...arg));
	},
	fromJSON(data) {
		return this.fromData(cycle.retrocycle(data));
	},
	_query(method, ...arg) {
		return queryAPI({data:this.toJSON(), arg}, this.table, '_do', method);
	},
	_query_merge(...arg) {
		return this._query(...arg).then(result =>
			typeof result === 'object'
				? this.fromJSON(result)
				: result
		);
	},
	exists() {
		if (this.id == null)
			return Promise.resolve(false);
		return this._query('exists');
	},
	delete() {
		if (this.id == null)
			return Promise.resolve(null);
		return this._query('delete').then(r=>r===null?r:this);
	},
	pull() {
		if (this.id == null)
			return Promise.reject(new Error("No model id"));
		return this._query_merge('pull');
	},
	pullchildren(classes) {
		if (!classes)
			classes = this.references || [];
		if (!classes.length) return Promise.resolve(this);
		return this._query_merge('pullchildren', classes);
	},
	pullchildrenscarce(classes) {
		if (!classes)
			classes = this.references || [];
		if (!classes.length) return Promise.resolve(this);
		return this._query_merge('pullchildrenscarce', classes);
	},
	pullall() {
		return this._query_merge('pullall');
	},
	insert() {
		return this._query_merge('insert');
	},
	push() {
		return this._query_merge('push');
	},
	update() {
		return this._query_merge('update');
	},
	/*push_id(newid) {
		if (typeof newid !== 'number')
			return Promise.reject(new Error("Model id must be integer"));
		return queryAPI({data:this.toJSON(), arg}, this.table, 'do', 'push_id').then(result => {
			this.uncache();
			this.id = newid;
			this.cache();
			return this;
		});
	},*/
};
function getchildren() {
	"use strict";
	if (!this.references) return;
	var children = {};
	for (let c of this.references)
		children[c.table] = this[c.table];
	return children;
}
function setchildren(children) {
	"use strict";
	if (!this.references) return;
	for (let c of this.references) {
		if (children[c.table])
			this[c.table] = children[c.table];
	}
}
var common = stampit({methods, init: function({instance}) {
	Object.defineProperty(instance, 'children', {
		enumerable: true, configurable: true,
		get: getchildren, set: setchildren,
	});
}});
common.mode = 'client';

module.exports = common;
