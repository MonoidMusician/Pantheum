var stampit = require('stampit');
var queryAPI = require('./');
require('../lib/cycle.js');

// Each derive class must have
//  - table Table name in database
//  - key   Column name for id in database
//  - id    ID value for instance
//  - references
var methods = {
	toJSON() {
		return JSON.decycle(this.toData());
	},
	fromJSON(data) {
		return this.fromData(JSON.retrocycle(data));
	},
};
for (let method of [
	'exists','delete','insert','push','update','push_id',
	'pull','pullchildren','pullchildrenscarce','pullall',
]) {
	methods[method] = function(...arg) {
		queryAPI(this.table, this.id, '_api', method, {this, arg});
	};
}
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

module.exports = common;
