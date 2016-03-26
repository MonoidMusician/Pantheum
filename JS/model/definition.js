var connection = require('./mysql');
var Promise = require('promise');

(function(model) {
	"use strict";
	var prefix = "def_";
	var columns = ["id","value","type","sense","lang"];
	var table = "definitions";
	class Definition {
		constructor(data) {
			if (typeof data === 'number') {
				this._id = data;
				this.fetch(this.id);
			} else {
				for (let d of columns)
					this["_"+d] = data[d];
				this._word = data.word && (model.Word ? new model.Word(data.word) : data.word);
				this._tag = data.tag && (model.Path ? new model.Path(data.word, data.tag) : data.tag);
			}
		}
		create() {
			var def = this;
			var row = {};
			if (def._word) row.word_id = def._word.id;
			if (def._tag) row.word_id = def._tag.toString();
			for (let d of columns)
				if (def["_"+d] != null)
					row[prefix+d] = def["_"+d];
			if (!Object.keys(row).length) return;
			return new Promise(function(resolve, reject) {
				connection.query("INSERT INTO definitions SET ?", row, function(err, result) {
					if (err) {reject(err);return}
					if (!def._id) def._id = result.insertId;
					resolve(def);
				});
			});
		}
	}

	model.Definition = Definition;
})(!exports?pantheum.model:this);
