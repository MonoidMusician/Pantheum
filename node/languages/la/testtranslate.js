var Promise = require('bluebird');

var model = require('../../model');
var format = require('../../lib/string').normalize_punctuation;
var {translate: { en }} = require('./');

model.Word({id:9541}, true).pullall().then(Promise.coroutine(function*(word) {
	word.close();
	var sorted = []; sorted.length = word.mgr.length;
	for (let f of word.forms)
		sorted[f.ord()] = f;
	for (let f of sorted) {
		if (!f) continue;
		console.log(f.toString());
		let tr = en(f).simplify();
		let trl = [...tr].map(format).join(', ');
		if (!trl) continue;
		console.log('- '+trl);
		console.log();
		//console.log([...tr].map(format), tr.repr());
		//console.log(tr.single, tr.data);
	}
}));
