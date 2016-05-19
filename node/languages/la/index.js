var model = require('../../model');

var depaths = require('./depaths');

for (let spart in depaths) {
	var depath = depaths[spart];
	if (!(depath instanceof model.Depath)) depath = new model.Depath('la/'+spart, depaths[spart]);
}

var translate = require('./translate');

module.exports = {depaths, translate};
