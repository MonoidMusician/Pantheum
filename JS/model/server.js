var queryP = require('./mysqlpromise');

// Each derive class must have
//  - table Table name in database
//  - key   Column name for id in database
//  - id    ID value for instance
//  - referenced
class common {
	exists() {
		if (this.id == null)
			return Promise.resolve(false);
		return queryP("SELECT * FROM ?? WHERE ?? = ?", [this.constructor.table, this.constructor.key, this.id]).then(rows=>rows.length===1);
	}
	pull() {
		if (this.id == null)
			return Promise.reject(new Error("No model id"));
		return queryP("SELECT * FROM ?? WHERE ?? = ?", [this.constructor.table, this.constructor.key, this.id]).then(rows => {
			if (rows.length !== 1)
				return Promise.reject(new Error("Model not found"));
			this.fromSQL(rows[0]);
			return this;
		});
	}
	pullchildren() {
		var classes = this.constructor.referenced || [];
		if (!classes.length) return Promise.resolve(this);
		var pulls = [];
		for (let cls of classes) {
			let table = cls.table;
			pulls.push(queryP("SELECT * FROM ?? WHERE ?? = ?", [table, this.constructor.key, this.id]).then(rows => {
				return {[table]:rows.map(row => {
					return new cls(row[cls.key]).fromSQL(row);
				})};
			}));
		}
		return Promise.all(pulls).then(results => {
			var referenced = {};
			Object.assign(referenced, ...results);
			this.referenced = referenced;
			return this;
		});
	}
	pullall() {
		return Promise.all([this.pull(),this.pullchildren()]).then(results=>this);
	}
	insert() {
		var row = this.toSQL();
		if (!Object.keys(row).length)
			return Promise.reject(new Error("No model fields to insert"));
		if (this.id != null) row[this.constructor.key] = this.id;
		var refs = this.referenced;
		return queryP("INSERT INTO ?? SET ?", [this.constructor.table, row]).then(result => {
			if (this.id == null) this.id = result.insertId;
			var inserts = [];
			for (let r in refs) {
				inserts.push(...refs[r].map(a=>a.insert()));
			}
			if (inserts.length)
				return Promise.all(inserts).then(results=>this);
			else return Promise.resolve(this);
		});
	}
	push() {
		var row = this.toSQL();
		if (!Object.keys(row).length)
			return  Promise.resolve(this);
		var referenced = this.constructor.referenced || [];
		var update = queryP("UPDATE ?? SET ? WHERE ?? = ?", [this.constructor.table, row, this.constructor.key, this.id]);
		var cull = []; var update2 = [];
		for (let cls of referenced) {
			let {table, key} = cls;
			let refs = this[table];
			if (!refs || !refs.length) {
				cull.push(queryP("DELETE FROM ?? WHERE ?? = ?", [table, this.constructor.key, this.id]));
				continue;
			}
			cull.push(queryP("DELETE FROM ?? WHERE ?? = ? AND ?? NOT IN (?)", [table, this.constructor.key, this.id, key, refs.map(a=>a.id).filter(a=>a!=null)]));
			update2.push(...refs.map(a=>a.update()));
		};
		return Promise.all([update, ...cull, ...update2]).then(results=>this);
	}
	update() {
		var referenced = this.constructor.referenced;
		if (referenced && referenced.length)
			return this.exists().then(c => c ? this.push() : this.insert());
		var row = this.toSQL();
		row[this.constructor.key] = this.id;
		return queryP("INSERT INTO ?? SET ? ON DUPLICATE KEY UPDATE ?", [this.constructor.table, row, row]).then(result=>this);
	}
	push_id(newid) {
		if (typeof newid !== 'number')
			return Promise.reject(new Error("Model id must be integer"));
		this.uncache();
		return queryP("UPDATE ?? SET ?? = ? WHERE ?? = ?", [this.constructor.table, this.constructor.key, newid, this.constructor.key, this.id]).then(result => {
			this.id = newid;
			this.cache();
			return this;
		}, err => {
			this.cache();
			throw err;
		});
	}
	get referenced() {
		if (this.constructor.referenced == null) return;
		var referenced = {};
		for (let c of this.constructor.referenced)
			referenced[c.table] = this[c.table];
		return referenced;
	}
	set referenced(referenced) {
		if (this.constructor.referenced == null) return;
		for (let c of this.constructor.referenced)
			if (referenced[c.table])
				this[c.table] = referenced[c.table];
	}
}

module.exports = common;
