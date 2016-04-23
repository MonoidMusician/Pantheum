"use strict";
var stampit = require('stampit');
var Promise = require('bluebird');
var queryP = require('./mysqlpromise');
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
	exists() {
		if (this.id == null)
			return Promise.resolve(false);
		return queryP("SELECT 1 FROM ?? WHERE ?? = ?", [this.table, this.key, this.id]).then(rows=>rows.length===1);
	},
	delete() {
		if (this.id == null)
			return Promise.resolve(null);
		return queryP("DELETE FROM ?? WHERE ?? = ?", [this.table, this.key, this.id]).then(({affectedRows:r})=>r?this:null);
	},
	pull() {
		if (this.id == null)
			return Promise.reject(new Error("No model id"));
		return queryP("SELECT * FROM ?? WHERE ?? = ?", [this.table, this.key, this.id]).then(rows => {
			if (rows.length !== 1)
				return Promise.reject(new Error("Model not found"));
			return this.fromSQL(rows[0]);
		});
	},
	pullchildren(classes) {
		if (!classes)
			classes = this.references || [];
		if (!classes.length) return Promise.resolve(this);
		var pulls = [];
		for (let cls of classes) {
			let {table, key} = cls;
			pulls.push(queryP("SELECT * FROM ?? WHERE ?? = ?", [table, this.key, this.id]).then(rows => {
				return Promise.all(rows.map(
					row => cls({id:row[key], cacheable:this.cacheable}).fromSQL(row)
				)).then(a => ({[table]:a}));
			}));
		}
		return Promise.all(pulls).then(results => {
			var children = {};
			Object.assign(children, ...results);
			this.children = children;
			return this;
		});
	},
	pullchildrenscarce(classes) {
		if (!classes)
			classes = this.references || [];
		if (!classes.length) return Promise.resolve(this);
		var pulls = [];
		for (let cls of classes) {
			let {table, key} = cls;
			pulls.push(queryP("SELECT ?? FROM ?? WHERE ?? = ?", [key, table, this.key, this.id]).then(rows => {
				return {[table]:rows.map(row => {
					return cls({id:row[key], cacheable:this.cacheable});
				})};
			}));
		}
		return Promise.all(pulls).then(results => {
			var children = {};
			Object.assign(children, ...results);
			this.children = children;
			return this;
		});
	},
	pullall() {
		var prom = Promise.all([this.pull(),this.pullchildren()]).then(results=>this);
		if (this.reference)
			prom.then(r=>this[this.reference].pull());
		return prom.then(r=>this);
	},
	insert() {
		var row = this.toSQL();
		if (!Object.keys(row).length)
			return Promise.reject(new Error("No model fields to insert"));
		if (this.id != null) row[this.key] = this.id;
		var children = this.children;
		return queryP("INSERT INTO ?? SET ?", [this.table, row]).then(result => {
			if (this.id == null) this.id = result.insertId;
			var inserts = [];
			for (let r in children) {
				inserts.push(...children[r].map(a=>a.insert()));
			}
			if (inserts.length)
				return Promise.all(inserts).then(results=>this);
			else return Promise.resolve(this);
		});
	},
	push() {
		var references = this.references || [];
		var row = this.toSQL();
		if (!Object.keys(row).length)
			var update = Promise.resolve(this);
		else
			var update = queryP("UPDATE ?? SET ? WHERE ?? = ?", [this.table, row, this.key, this.id]);
		var cull = []; var update2 = [];
		for (let {table,key} of references) {
			let children = this[table];
			if (!children || !children.length) {
				cull.push(queryP("DELETE FROM ?? WHERE ?? = ?", [table, this.key, this.id]));
				continue;
			}
			let existing_ids = children.map(a=>a.id).filter(a=>a!=null);
			cull.push(queryP("DELETE FROM ?? WHERE ?? = ? AND ?? NOT IN (?)", [table, this.key, this.id, key, existing_ids]));
			update2.push(...children.map(a=>a.update()));
		};
		return Promise.all([update, ...cull, ...update2]).then(results=>this);
	},
	update() {
		var references = this.references;
		if (references && references.length)
			return this.exists().then(c => c ? this.push() : this.insert());
		var row = this.toSQL();
		row[this.key] = this.id;
		return queryP("INSERT INTO ?? SET ? ON DUPLICATE KEY UPDATE ?", [this.table, row, row]).then(result=>this);
	},
	push_id(newid) {
		if (typeof newid !== 'number')
			return Promise.reject(new Error("Model id must be integer"));
		return queryP("UPDATE ?? SET ?? = ? WHERE ?? = ?", [this.table, this.key, newid, this.key, this.id]).then(result => {
			this.uncache();
			this.id = newid;
			this.cache();
			return this;
		});
	},
};
function getchildren() {
	if (!this.references) return;
	var children = {};
	for (let c of this.references)
		children[c.table] = this[c.table];
	return children;
}
function setchildren(children) {
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
common.mode = 'server';

module.exports = common;
